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
        Schema::table('bands', function (Blueprint $table) {
            $table->string('cover_image_path')->nullable();
            $table->string('cover_image_thumbnail_path')->nullable();
            $table->string('cover_image_small_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bands', function (Blueprint $table) {
            $table->dropColumn([
                'cover_image_path',
                'cover_image_thumbnail_path',
                'cover_image_small_path'
            ]);
        });
    }
};
