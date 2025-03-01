<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrialFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('trial_ends_at')->after('remember_token')->nullable();
            $table->timestamp('trial_started_at')->after('remember_token')->nullable();
            $table->boolean('is_trial')->after('remember_token')->default(false);
            $table->timestamp('soft_deleted_at')->after('remember_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['trial_ends_at', 'trial_started_at', 'is_subscribed', 'soft_deleted_at']);
        });
    }
}
