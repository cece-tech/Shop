<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Stripe Cashier fields
            $table->string('stripe_id')->nullable()->index();
            $table->string('pm_type')->nullable();
            $table->string('pm_last_four', 4)->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            
            // Stripe Connect fields for sellers
            $table->string('stripe_connect_id')->nullable();
            $table->boolean('stripe_connect_verified')->default(false);
            $table->boolean('payouts_enabled')->default(false);
            
            // Seller metrics
            $table->integer('total_sales')->default(0);
            $table->decimal('total_earnings', 10, 2)->default(0);
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
            $table->dropColumn([
                'stripe_id',
                'pm_type',
                'pm_last_four',
                'trial_ends_at',
                'stripe_connect_id',
                'stripe_connect_verified',
                'payouts_enabled',
                'total_sales',
                'total_earnings',
            ]);
        });
    }
};