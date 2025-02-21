<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('band_invitation_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('band_id');
            $table->foreign('band_id')->references('id')->on('bands')->cascadeOnDelete();
            $table->string('email');
            $table->timestamp('invited_at');
            $table->timestamps();

            // Add index for faster lookups
            $table->index(['band_id', 'email', 'invited_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('band_invitation_history');
    }
}; 