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
        Schema::table('setlist_song', function (Blueprint $table) {
            $table->enum('transition_type', ['direct', 'fade', 'stop'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setlist_song', function (Blueprint $table) {
            $table->dropColumn('transition_type');
        });
    }
};
