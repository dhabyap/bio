<?php

namespace App\Http\Controllers;

use App\Models\LinkContent;
use Illuminate\Http\Request;

class EditorController extends Controller
{
    public function index()
    {
        $content = LinkContent::first();
        $initialState = $content ? $content->state : null;
        return view('editor', compact('initialState'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'state_json' => 'required|json',
        ]);

        $state = json_decode($data['state_json'], true);

        $content = LinkContent::first();
        if ($content) {
            $content->update(['state' => $state]);
        } else {
            LinkContent::create(['state' => $state]);
        }

        return redirect('/editor')->with('saved', true);
    }
}