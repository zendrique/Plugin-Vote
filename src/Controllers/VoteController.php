<?php

namespace Azuriom\Plugin\Vote\Controllers;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\User;
use Azuriom\Plugin\Vote\Models\Reward;
use Azuriom\Plugin\Vote\Models\Site;
use Azuriom\Plugin\Vote\Models\Vote;
use Azuriom\Plugin\Vote\Verification\VoteChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoteController extends Controller
{
    /**
     * Display the vote home page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $votes = DB::table((new Vote())->getTable())
            ->select(['user_id', DB::raw('COUNT(user_id) AS count')])
            ->where('created_at', '>', now()->startOfMonth())
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->take(setting('vote.top-players-count', 10))
            ->get();

        $users = User::findMany($votes->pluck('user_id'))->keyBy('id');

        $votes = $votes->mapWithKeys(function ($vote, $position) use ($users) {
            return [
                $position + 1 => [
                    'user' => $users->get($vote->user_id),
                    'votes' => $vote->count,
                ],
            ];
        });

        return view('vote::index', [
            'sites' => Site::enabled()->whereHas('rewards')->get(),
            'rewards' => Reward::orderByDesc('chances')->get(),
            'votes' => $votes,
        ]);
    }

    public function verifyUser(string $name)
    {
        if (! User::where('name', $name)->exists()) {
            return response()->json([
                'message' => trans('vote::messages.unknown-user'),
            ], 422);
        }

        return response()->noContent();
    }

    public function canVote(Request $request, Site $site)
    {
        $user = $request->user() ?? User::firstWhere('name', $request->input('user'));

        if ($user === null) {
            abort(401);
        }

        $nextVoteTime = $this->getNextVoteTime($site, $user);

        if ($nextVoteTime !== null) {
            return response()->json([
                'message' => trans('vote::messages.vote-delay', ['time' => $nextVoteTime]),
            ], 422);
        }

        if ($site->rewards->isEmpty()) {
            return response()->json([
                'message' => trans('vote::messages.site-no-rewards'),
            ], 422);
        }

        return response()->noContent();
    }

    public function done(Request $request, Site $site)
    {
        $user = $request->user() ?? User::firstWhere('name', $request->input('user'));

        if ($user === null) {
            abort(401);
        }

        $nextVoteTime = $this->getNextVoteTime($site, $user);

        if ($nextVoteTime !== null) {
            return response()->json([
                'message' => trans('vote::messages.vote-delay', ['time' => $nextVoteTime]),
            ], 422);
        }

        if ($site->rewards->isEmpty()) {
            return response()->json([
                'message' => trans('vote::messages.site-no-rewards'),
            ], 422);
        }

        $voteChecker = app(VoteChecker::class);

        if ($site->has_verification && ! $voteChecker->verifyVote($site, $user, $request->ip())) {
            return response()->json([
                'status' => 'pending',
            ]);
        }

        $reward = $this->getRandomReward($site);

        $site->votes()->create([
            'user_id' => $user->id,
            'reward_id' => $reward->id,
        ]);

        if ($reward->money > 0) {
            $user->addMoney($reward->money);
            $user->save();
        }

        $commands = array_map(function ($el) use ($reward) {
            return str_replace('{reward}', $reward->name, $el);
        }, $reward->commands ?? []);

        if ($reward->server !== null) {
            $reward->server->bridge()->executeCommands($commands, $user->name, $reward->need_online);
        }

        return response()->json(['message' => trans('vote::messages.vote-success')]);
    }

    private function getNextVoteTime(Site $site, User $user)
    {
        $lastVoteTime = $site->votes()
            ->where('user_id', $user->id)
            ->where('created_at', '>', now()->subMinutes($site->vote_delay))
            ->latest()
            ->value('created_at');

        if ($lastVoteTime === null) {
            return null;
        }

        return $lastVoteTime->addMinutes($site->vote_delay)->diffForHumans();
    }

    private function getRandomReward(Site $site)
    {
        $rewards = $site->rewards;

        $total = $rewards->sum('chances');
        $random = random_int(0, $total);

        $sum = 0;

        foreach ($rewards as $reward) {
            $sum += $reward->chances;

            if ($sum >= $random) {
                return $reward;
            }
        }

        return $rewards->first();
    }
}
