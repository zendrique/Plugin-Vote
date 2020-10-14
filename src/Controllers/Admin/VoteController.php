<?php

namespace Azuriom\Plugin\Vote\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\User;
use Azuriom\Plugin\Vote\Models\Vote;
use Azuriom\Support\Charts;

class VoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $votes = collect();
        $date = now()->startOfMonth()->subYear();

        while ($date->isPast()) {
            $date->addMonth();

            $votes->put($date->format('m/Y'), Vote::getRawTopVoters($date, $date->clone()->endOfMonth()));
        }

        $users = User::findMany($votes->flatMap(function ($votes) {
            return $votes->pluck('user_id');
        })->unique())->keyBy('id');

        $votes = $votes->map(function ($voteValues) use ($users) {
            return $voteValues->mapWithKeys(function ($vote, $position) use ($users) {
                return [
                    $position + 1 => (object) [
                        'user' => $users->get($vote->user_id),
                        'votes' => $vote->count,
                    ],
                ];
            });
        });

        return view('vote::admin.votes', [
            'votes' => $votes,

            'votesCount' => Vote::count(),
            'votesCountMonth' => Vote::where('created_at', now()->startOfMonth())->count(),
            'votesCountWeek' => Vote::where('created_at', now()->startOfWeek())->count(),
            'votesCountDay' => Vote::where('created_at', today())->count(),
            'votesPerMonths' => Charts::countByMonths(Vote::query()),
            'votesPerDays' => Charts::countByDays(Vote::query()),

            'now' => now()->format('m/Y'),
        ]);
    }
}
