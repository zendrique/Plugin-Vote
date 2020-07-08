<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveForeignKeysFromVotesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            Schema::table('vote_rewards', function (Blueprint $table) {
                $table->dropForeign(['server_id']);
            });

            Schema::table('vote_votes', function (Blueprint $table) {
                $table->dropForeign(['site_id']);
                $table->dropForeign(['reward_id']);
            });
        } catch (Exception $e) {
            // ignore, SQLite doesn't support dropping foreign keys.
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
