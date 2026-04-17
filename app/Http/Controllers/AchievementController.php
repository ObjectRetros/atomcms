<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AchievementController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Get all achievements grouped by category, with the user's progress
        $rawAchievements = DB::table('achievements as a')
            ->leftJoin('users_achievements as ua', function ($join) use ($user) {
                $join->on('ua.achievement_name', '=', 'a.name')
                     ->where('ua.user_id', '=', $user->id);
            })
            ->select(
                'a.id',
                'a.name',
                'a.category',
                'a.level',
                'a.reward_amount',
                'a.reward_type',
                'a.points',
                'a.progress_needed',
                DB::raw('COALESCE(ua.progress, 0) as user_progress')
            )
            ->orderBy('a.category')
            ->orderBy('a.name')
            ->orderBy('a.level')
            ->get();

        // Group by base name (strip level suffix) then by category
        $categories = [];
        $totalPoints = 0;
        $earnedPoints = 0;

        foreach ($rawAchievements as $ach) {
            $cat = $ach->category ?? 'identity';
            $isEarned = $ach->user_progress >= $ach->progress_needed;

            if (!isset($categories[$cat])) {
                $categories[$cat] = [
                    'label'    => Achievement::getLabelFor($cat),
                    'icon'     => Achievement::getIconFor($cat),
                    'items'    => [],
                    'earned'   => 0,
                    'total'    => 0,
                ];
            }

            $categories[$cat]['items'][] = $ach;
            $categories[$cat]['total']++;
            $totalPoints += $ach->points ?? 0;

            if ($isEarned) {
                $categories[$cat]['earned']++;
                $earnedPoints += $ach->points ?? 0;
            }
        }

        // Overall stats
        $totalAchievements = $rawAchievements->count();
        $earnedCount = $rawAchievements->filter(
            fn($a) => $a->user_progress >= $a->progress_needed
        )->count();

        // Achievement score from user settings
        $achievementScore = DB::table('users_settings')
            ->where('user_id', $user->id)
            ->value('achievement_score') ?? 0;

        // Top achievement earners for sidebar
        $topEarners = DB::table('users_settings as us')
            ->join('users as u', 'u.id', '=', 'us.user_id')
            ->orderByDesc('us.achievement_score')
            ->limit(5)
            ->select('u.username', 'u.look', 'us.achievement_score')
            ->get();

        return view('achievements.index', compact(
            'categories',
            'earnedCount',
            'totalAchievements',
            'earnedPoints',
            'totalPoints',
            'achievementScore',
            'topEarners'
        ));
    }

    // Profile page: achievements for a specific user
    public function profile(User $user)
    {
        $earned = DB::table('achievements as a')
            ->join('users_achievements as ua', function ($join) use ($user) {
                $join->on('ua.achievement_name', '=', 'a.name')
                     ->where('ua.user_id', '=', $user->id);
            })
            ->where('ua.progress', '>=', DB::raw('a.progress_needed'))
            ->select('a.name', 'a.category', 'a.level', 'a.points')
            ->orderByDesc('a.points')
            ->limit(12)
            ->get();

        $score = DB::table('users_settings')
            ->where('user_id', $user->id)
            ->value('achievement_score') ?? 0;

        return view('achievements.profile', compact('user', 'earned', 'score'));
    }
}
