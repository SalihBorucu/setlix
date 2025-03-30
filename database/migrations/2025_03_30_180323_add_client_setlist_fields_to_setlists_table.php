<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('setlists', function (Blueprint $table) {
            $table->boolean('is_public')->after('song_order')->default(false);
            $table->string('public_slug')->after('song_order')->nullable()->unique();
            $table->integer('target_duration')->after('song_order')->nullable();
            $table->string('client_name')->after('song_order')->nullable();
            $table->string('client_email')->after('song_order')->nullable();
            $table->timestamp('submitted_at')->after('song_order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setlists', function (Blueprint $table) {
            $table->dropColumn([
                'is_public',
                'public_slug',
                'target_duration',
                'client_name',
                'client_email',
                'submitted_at'
            ]);
        });
    }
};
