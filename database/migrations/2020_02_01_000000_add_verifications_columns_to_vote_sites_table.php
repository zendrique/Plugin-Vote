<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerificationsColumnsToVoteSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vote_sites', function (Blueprint $table) {
            $table->string('verification_key')->nullable()->after('url');
            $table->boolean('has_verification')->default(true)->after('verification_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vote_sites', function (Blueprint $table) {
            $table->dropColumn(['verification_key', 'has_verification']);
        });
    }
}
