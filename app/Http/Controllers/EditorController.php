<?php

namespace App\Http\Controllers;

use App\Models\LinkContent;
use Illuminate\Http\Request;

class EditorController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $content = LinkContent::firstOrCreate(
            ['user_id' => $user->id],
            ['state' => json_encode([
                'name' => $user->name,
                'bio' => $user->bio ?? '',
                'avatar' => $user->avatar ?? '',
                'links' => [],
            ])]
        );

        $raw = $content->state;
        // Deep decode: if state is string, decode. If nested values (like links) are strings, decode them too.
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        
        // Handle potential double-encoded JSON inside (if MySQL stored string as JSON)
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        
        if (!is_array($raw)) $raw = [];


        if (isset($raw['name'])) {
            // old flat format —> migrate to profile + sections
            $initialState = [
                'profile' => [
                    'name' => $raw['name'] ?? $user->name,
                    'handle' => '@' . $user->username,
                    'bio' => $raw['bio'] ?? '',
                    'avatar' => $raw['avatar'] ?? '',
                ],
                'sections' => [],
            ];
            if (!empty($raw['links'])) {
                $initialState['sections'][] = [
                    'key' => 'links',
                    'label' => 'Links',
                    'links' => $raw['links'],
                ];
            }
        } elseif (isset($raw['profile']) && is_array($raw['profile'])) {
            // already new format with profile + sections
            $initialState = $raw;
            if (!isset($initialState['profile']['handle']) || empty($initialState['profile']['handle'])) {
                $initialState['profile']['handle'] = '@' . $user->username;
            }
        } else {
            // completely empty/unrecognized — give default
            $initialState = [
                'profile' => [
                    'name' => $user->name,
                    'handle' => '@' . $user->username,
                    'bio' => $user->bio ?? '',
                    'avatar' => '',
                ],
                'sections' => [],
            ];
        }

        return view('editor', compact('initialState'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'state_json' => 'required|string',
        ]);

        $parsed = json_decode($request->state_json, true);
        if (!$parsed || !isset($parsed['profile'])) {
            return back()->withErrors(['state_json' => 'Invalid data format']);
        }

        $content = LinkContent::updateOrCreate(
            ['user_id' => $user->id],
            ['state' => json_encode($parsed)]
        );

        return redirect('/editor')->with('status', 'Saved!');
    }

    public function aiGenerate(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
        ]);

        $prompt = $request->input('prompt');
        $user = auth()->user();

        // Compact system+user prompt — model has reasoning overhead
        $userContent = "Generate a JSON link-in-bio page. Name/Handle from context. Bio 1-2 sentences.
Sections: 2-4, each with 1-5 links. Use real URLs (GitHub, X, etc) when known, else #.
Return ONLY this JSON structure, no other text:
{\"profile\":{\"name\":\"\",\"handle\":\"\",\"bio\":\"\",\"avatar\":\"\"},\"sections\":[{\"key\":\"\",\"label\":\"\",\"links\":[{\"icon\":\"\",\"title\":\"\",\"subtitle\":\"\",\"url\":\"\"}]}]}

$prompt";

        $payload = json_encode([
            'model' => 'test',
            'messages' => [
                ['role' => 'user', 'content' => $userContent],
            ],
            'temperature' => 0.1,
            'max_tokens' => 8000,
        ]);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'http://127.0.0.1:20128/v1/chat/completions',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error || $httpCode !== 200) {
            return response()->json([
                'error' => 'AI generation failed: ' . ($error ?: "HTTP $httpCode"),
            ], 500);
        }

        // Strip trailing data: [DONE] that 9Router appends
        $response = preg_replace('/\s*data:\s*\[DONE\]\s*$/', '', trim($response));

        $decoded = json_decode($response, true);
        if (!$decoded || !isset($decoded['choices'][0]['message'])) {
            $err = json_last_error_msg();
            return response()->json(['error' => "Failed to parse LLM response: $err"], 500);
        }

        $message = $decoded['choices'][0]['message'];

        // Try content first, then reasoning_content
        $content = $message['content'] ?? '';
        if (empty(trim($content))) {
            $content = $message['reasoning_content'] ?? '';
        }

        if (empty(trim($content))) {
            return response()->json(['error' => 'Empty AI response'], 500);
        }

        // Strip markdown code blocks
        $content = preg_replace('/^```(?:json)?\s*\n?|\n?\s*```$/i', '', trim($content));

        // Try to extract JSON from response — find first { and last }
        $jsonStart = strpos($content, '{');
        $jsonEnd = strrpos($content, '}');
        if ($jsonStart !== false && $jsonEnd !== false && $jsonEnd > $jsonStart) {
            $content = substr($content, $jsonStart, $jsonEnd - $jsonStart + 1);
        }

        $state = json_decode($content, true);
        if (!$state || !isset($state['profile'])) {
            $err = json_last_error_msg();
            return response()->json(['error' => "AI returned invalid format: $err"], 500);
        }

        // Ensure handle
        if (empty($state['profile']['handle'])) {
            $state['profile']['handle'] = '@' . $user->username;
        }

        return response()->json($state);
    }
}