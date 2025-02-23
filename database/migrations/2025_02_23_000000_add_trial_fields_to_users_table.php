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
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('trial_started_at')->nullable();
            $table->boolean('is_subscribed')->default(false);
            $table->timestamp('soft_deleted_at')->nullable();
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