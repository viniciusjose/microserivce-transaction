<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', static function (Blueprint $table) {
            $table->uuid('id')->unique();
            $table->uuid('wallet_payer_id');
            $table->uuid('wallet_payee_id');
            $table->decimal('value', 10, 2);
            $table->datetimes();

            $table->primary('id');
            $table->foreign('wallet_payer_id')->references('id')->on('wallets');
            $table->foreign('wallet_payee_id')->references('id')->on('wallets');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
}
