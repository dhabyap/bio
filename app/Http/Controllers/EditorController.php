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
            // keep old links as one section
            if (!empty($raw['links'])) {
                $initialState['sections'][] = [
                    'key' => 'links',
                    'label' => 'Links',
                    'links' => $raw['links'],
                ];
            }
        } else {
            // already new format with profile + sections
            $initialState = $raw;
            // ensure handle
            if (!isset($initialState['profile']['handle']) || empty($initialState['profile']['handle'])) {
                $initialState['profile']['handle'] = '@' . $user->username;
            }
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
}