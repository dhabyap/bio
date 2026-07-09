<?php

namespace App\Http\Controllers;

use App\Models\LinkClick;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index()
    {
        $totalClicks = LinkClick::count();
        $clicksByLink = LinkClick::selectRaw('link_name, count(*) as total')
            ->groupBy('link_name')
            ->orderByDesc('total')
            ->get();
        $recentClicks = LinkClick::latest()->take(50)->get();

        return view('analytics', compact('totalClicks', 'clicksByLink', 'recentClicks'));
    }

    public function track(Request $request)
    {
        $validated = $request->validate([
            'link_name' => 'required|string|max:255',
            'link_url' => 'nullable|string|max:500',
        ]);

        LinkClick::create([
            'link_name' => $validated['link_name'],
            'link_url' => $validated['link_url'] ?? '',
            'referer' => $request->header('referer'),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
        ]);

        return response()->json(['ok' => true]);
    }
}
