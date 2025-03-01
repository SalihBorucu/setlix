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
            $columns = [
                'stripe_subscription_id',
                'subscription_status',
                'subscription_ends_at',
                'trial_ends_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('bands', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bands', function (Blueprint $table) {
            if (!Schema::hasColumn('bands', 'stripe_subscription_id')) {
                $table->string('stripe_subscription_id')->nullable();
            }
            if (!Schema::hasColumn('bands', 'subscription_status')) {
                $table->string('subscription_status')->nullable();
            }
            if (!Schema::hasColumn('bands', 'subscription_ends_at')) {
                $table->timestamp('subscription_ends_at')->nullable();
            }
            if (!Schema::hasColumn('bands', 'trial_ends_at')) {
                $table->timestamp('trial_ends_at')->nullable();
            }
        });
    }
};
