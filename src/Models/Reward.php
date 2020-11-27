<?php

namespace Azuriom\Plugin\Vote\Models;

use Azuriom\Models\Server;
use Azuriom\Models\Traits\HasTablePrefix;
use Azuriom\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property int $server_id
 * @property int $chances
 * @property int|null $money
 * @property bool $need_online
 * @property array $commands
 * @property bool $is_enabled
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property \Azuriom\Models\Server|null $server
 * @property \Illuminate\Support\Collection|\Azuriom\Plugin\Vote\Models\Vote[] $votes
 *
 * @method static \Illuminate\Database\Eloquent\Builder enabled()
 */
class Reward extends Model
{
    use HasTablePrefix;

    /**
     * The table prefix associated with the model.
     *
     * @var string
     */
    protected $prefix = 'vote_';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'server_id', 'chances', 'money', 'commands', 'need_online', 'is_enabled',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'commands' => 'array',
        'is_enabled' => 'boolean',
    ];

    public function sites()
    {
        return $this->belongsToMany(Site::class, 'vote_reward_site');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function giveTo(User $user)
    {
        if ($this->money > 0) {
            $user->addMoney($this->money);
            $user->save();
        }

        $commands = $this->commands ?? [];

        if ($globalCommands = setting('vote.commands')) {
            $commands = array_merge($commands, json_decode($globalCommands));
        }

        $commands = array_map(function ($el) {
            return str_replace('{reward}', $this->name, $el);
        }, $commands);

        if ($this->server !== null && ! empty($commands)) {
            $this->server->bridge()->executeCommands($commands, $user->name, $this->need_online);
        }
    }

    /**
     * Scope a query to only include enabled vote rewards.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnabled(Builder $query)
    {
        return $query->where('is_enabled', true);
    }
}
