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
        Schema::create('band_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('band_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('stripe_subscription_id')->unique()->nullable();
            $table->string('stripe_price_id')->nullable();
            $table->string('stripe_status')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            // Ensure a band can only have one active subscription
            $table->unique(['band_id', 'stripe_status'], 'unique_active_subscription');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('band_subscriptions');
    }
};
