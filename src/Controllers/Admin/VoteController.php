<?php

namespace Azuriom\Plugin\Vote\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\User;
use Azuriom\Plugin\Vote\Models\Vote;

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
            'now' => now()->format('m/Y'),
        ]);
    }
}
