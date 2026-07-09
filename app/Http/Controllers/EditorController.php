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

        return view('editor', compact('content'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'state' => 'required|array',
            'state.name' => 'required|string|max:255',
            'state.bio' => 'nullable|string|max:1000',
            'state.avatar' => 'nullable|string|max:500',
            'state.links' => 'required|array',
            'state.links.*.title' => 'required|string|max:255',
            'state.links.*.url' => 'required|url|max:500',
            'state.links.*.icon' => 'nullable|string|max:20',
        ]);

        $content = LinkContent::updateOrCreate(
            ['user_id' => $user->id],
            ['state' => json_encode($request->state)]
        );

        return redirect('/editor')->with('status', 'Saved!');
    }
}