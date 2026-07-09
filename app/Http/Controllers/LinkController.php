<?php

namespace App\Http\Controllers;

use App\Models\LinkClick;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LinkController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->get('range', 'all');

        $query = LinkClick::query();
        $recentQuery = LinkClick::query();

        if ($range === 'today') {
            $query->whereDate('created_at', today());
            $recentQuery->whereDate('created_at', today());
        } elseif ($range === '7d') {
            $query->where('created_at', '>=', now()->subDays(7));
            $recentQuery->where('created_at', '>=', now()->subDays(7));
        } elseif ($range === '30d') {
            $query->where('created_at', '>=', now()->subDays(30));
            $recentQuery->where('created_at', '>=', now()->subDays(30));
        }

        $totalClicks = (clone $query)->count();
        $clicksByLink = (clone $query)
            ->select('link_name', DB::raw('count(*) as total'))
            ->groupBy('link_name')
            ->get();
        $recentClicks = $recentQuery
            ->latest()
            ->limit(50)
            ->get();

        // Daily clicks for trend chart (use raw SQL compatible with SQLite)
        $dailyClicks = (clone $query)
            ->select(
                DB::raw("date(created_at) as date"),
                DB::raw('count(*) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Traffic sources
        $trafficSources = (clone $query)
            ->select('source', DB::raw('count(*) as total'))
            ->whereNotNull('source')
            ->groupBy('source')
            ->orderByDesc('total')
            ->get()
            ->toArray();

        return view('analytics', compact(
            'totalClicks', 'clicksByLink', 'recentClicks',
            'range', 'dailyClicks', 'trafficSources'
        ));
    }

    public function track(Request $request)
    {
        $validated = $request->validate([
            'link_name' => 'required|string|max:255',
            'link_url' => 'nullable|string|max:1000',
        ]);

        LinkClick::create([
            'link_name' => $validated['link_name'],
            'link_url' => $validated['link_url'] ?? '',
            'referer' => $request->header('referer', ''),
            'user_agent' => $request->header('user-agent', ''),
            'ip' => $request->ip(),
            'source' => $request->input('source', ''),
        ]);

        return response()->json(['status' => 'ok']);
    }

    public function clearTest()
    {
        $deleted = LinkClick::whereIn('link_name', ['test', 'v'])->delete();
        return redirect('/analytics')->with('cleared', true);
    }
}