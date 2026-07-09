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
        // Ensure $raw is an array — model cast may not apply depending on storage
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : [];
        }
        if (!is_array($raw) || empty($raw)) {
            $raw = [];
        }

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

        // Build LLM system prompt
        $systemPrompt = "You are a Link-in-bio page generator. Generate a JSON response for a personal link page based on the user's description. Return ONLY valid JSON, no markdown, no explanations.

The JSON structure must be:
{
  \"profile\": {
    \"name\": \"...\",
    \"handle\": \"@...\",
    \"bio\": \"...\",
    \"avatar\": \"\"
  },
  \"sections\": [
    {
      \"key\": \"section-xxx\",
      \"label\": \"SECTION LABEL\",
      \"links\": [
        {
          \"icon\": \"emoji or symbol\",
          \"title\": \"Link title\",
          \"subtitle\": \"Short description\",
          \"url\": \"https://...\"
        }
      ]
    }
  ]
}

Guidelines:
- Profile name from context, handle @username consistent with name
- Bio: 1-2 sentences max
- Sections: 2-4 sections max, each with 1-5 links
- Section keys: use kebab-case unique ids
- Icons: relevant emoji for each link
- URLs: use real URLs when known (GitHub, X/Twitter, LinkedIn, etc.), placeholder # for unknown
- Subtitles: short, descriptive
- First section should be most important (Highlight/Featured)
- Use real platform URLs when possible";

        $payload = json_encode([
            'model' => 'test',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => "Generate my link-in-bio page. $prompt"],
            ],
            'temperature' => 0.7,
            'max_tokens' => 2000,
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

        $decoded = json_decode($response, true);
        $content = $decoded['choices'][0]['message']['content'] ?? null;

        if (!$content) {
            return response()->json(['error' => 'Empty AI response'], 500);
        }

        // Strip markdown code blocks if present
        $content = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($content));

        $state = json_decode($content, true);
        if (!$state || !isset($state['profile'])) {
            return response()->json(['error' => 'AI returned invalid format'], 500);
        }

        // Ensure handle
        if (empty($state['profile']['handle'])) {
            $state['profile']['handle'] = '@' . $user->username;
        }

        return response()->json($state);
    }
}