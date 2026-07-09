<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LinkContent;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $range = $request->get('range', 'all');
        $query = \App\Models\LinkClick::query();

        if ($user) {
            $contentIds = LinkContent::where('user_id', $user->id)->pluck('id');
            $query->whereIn('link_content_id', $contentIds);
        }

        if ($range === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($range === '7d') {
            $query->where('created_at', '>=', now()->subDays(7));
        } elseif ($range === '30d') {
            $query->where('created_at', '>=', now()->subDays(30));
        }

        $totalClicks = (clone $query)->count();
        $clicksByLink = (clone $query)
            ->select('link_name', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('link_name')
            ->orderByDesc('total')
            ->get();
        $dailyClicks = (clone $query)
            ->select(\Illuminate\Support\Facades\DB::raw("date(created_at) as date"), \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        $trafficSources = (clone $query)
            ->whereNotNull('source')->where('source', '!=', '')
            ->select('source', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('source')->orderByDesc('total')
            ->get();
        $recentClicks = (clone $query)
            ->where('link_name', '!=', '')
            ->latest()->limit(50)->get();

        return view('analytics', compact(
            'totalClicks', 'clicksByLink', 'dailyClicks', 'trafficSources',
            'recentClicks', 'range'
        ));
    }

    public function track(Request $request)
    {
        $validated = $request->validate([
            'link_content_id' => 'required|exists:link_contents,id',
            'link_name' => 'required|string|max:255',
            'link_url' => 'nullable|string|max:500',
            'source' => 'nullable|string|max:500',
        ]);

        \App\Models\LinkClick::create([
            'link_content_id' => $validated['link_content_id'],
            'link_name' => $validated['link_name'],
            'link_url' => $validated['link_url'] ?? null,
            'source' => $validated['source'] ?? $request->header('referer'),
            'referer' => $request->header('referer'),
            'user_agent' => $request->header('user-agent'),
            'ip' => $request->ip(),
        ]);

        return response()->json(['ok' => true]);
    }

    public function show($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $content = $user->linkContent;

        // ensure state is array
        $raw = null;
        if ($content && $content->state) {
            $raw = $content->state;
            if (is_string($raw)) {
                $decoded = json_decode($raw, true);
                $raw = is_array($decoded) ? $decoded : null;
            }
        }

        if (!$raw) {
            $state = [
                'name' => $user->name,
                'handle' => '@' . $user->username,
                'bio' => $user->bio ?? '',
                'avatar' => $user->avatar ?? '',
                'links' => [],
            ];
        } elseif (isset($raw['name'])) {
            $state = [
                'name' => $raw['name'] ?? $user->name,
                'handle' => '@' . $user->username,
                'bio' => $raw['bio'] ?? '',
                'avatar' => $raw['avatar'] ?? '',
                'links' => $raw['links'] ?? [],
            ];
        } elseif (isset($raw['profile'])) {
            $state = [
                'name' => $raw['profile']['name'] ?? $user->name,
                'handle' => $raw['profile']['handle'] ?? '@' . $user->username,
                'bio' => $raw['profile']['bio'] ?? '',
                'avatar' => $raw['profile']['avatar'] ?? '',
                'links' => [],
            ];
            foreach ($raw['sections'] ?? [] as $sec) {
                foreach ($sec['links'] ?? [] as $lk) {
                    $state['links'][] = $lk;
                }
            }
        } else {
            $state = [
                'name' => $user->name,
                'handle' => '@' . $user->username,
                'bio' => $user->bio ?? '',
                'avatar' => $user->avatar ?? '',
                'links' => [],
            ];
        }

        return view('links', compact('user', 'state'));
    }

    public function clearTest()
    {
        $user = auth()->user();
        $contentIds = LinkContent::where('user_id', $user->id)->pluck('id');
        \App\Models\LinkClick::whereIn('link_content_id', $contentIds)->delete();
        return back()->with('status', 'Test data cleared');
    }
}