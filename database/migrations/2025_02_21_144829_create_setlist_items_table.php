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
        Schema::create('setlist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setlist_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['song', 'break']);
            $table->foreignId('song_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->integer('duration_seconds');
            $table->text('notes')->nullable();
            $table->integer('order');
            $table->timestamps();

            // Add index for ordering
            $table->index(['setlist_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setlist_items');
    }
};
