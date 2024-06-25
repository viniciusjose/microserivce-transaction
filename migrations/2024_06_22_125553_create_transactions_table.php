<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

use function Hyperf\Config\config;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', static function (Blueprint $table) {
            $table->uuid('id')->unique();
            $table->string('wallet_payer_id');
            $table->string('wallet_payee_id');
            $table->decimal('value', 10, 2);
            $table->datetimes();

            $table->primary('id');
            if (config('databases.default.driver') !== 'sqlite') {
                $table->foreign('wallet_payer_id')->references('id')->on('wallets');
                $table->foreign('wallet_payee_id')->references('id')->on('wallets');
            }
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
