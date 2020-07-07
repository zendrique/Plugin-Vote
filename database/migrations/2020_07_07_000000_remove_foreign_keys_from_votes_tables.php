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
        Schema::table('vote_rewards', function (Blueprint $table) {
            $table->dropForeign(['server_id']);
        });

        Schema::table('vote_votes', function (Blueprint $table) {
            $table->dropForeign(['site_id']);
            $table->dropForeign(['reward_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('vote_rewards', function (Blueprint $table) {
            $table->foreign('server_id')->references('id')->on('servers')->onDelete('cascade');
        });

        Schema::table('vote_votes', function (Blueprint $table) {
            $table->foreign('site_id')->references('id')->on('vote_sites')->onDelete('cascade');
            $table->foreign('reward_id')->references('id')->on('vote_rewards')->onDelete('cascade');
        });
    }
}
