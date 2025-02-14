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
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('band_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('duration_seconds'); // Duration in seconds for easier calculations
            $table->text('notes')->nullable();
            $table->string('url')->nullable();
            $table->integer('bpm')->nullable();
            $table->text('artist')->nullable();
            $table->string('document_path')->nullable(); // For attached files
            $table->string('document_type')->nullable(); // PDF or TXT
            $table->timestamps();
            $table->softDeletes(); // Allow soft deletes for songs
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
