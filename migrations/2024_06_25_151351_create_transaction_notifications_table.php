<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

use function Hyperf\Config\config;

class CreateTransactionNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction_notifications', static function (Blueprint $table) {
            $table->uuid('id')->unique();
            $table->uuid('transaction_id');
            $table->enum('status', ['done', 'error']);
            $table->dateTime('date');

            $table->primary('id');
            if (config('databases.default.driver') !== 'sqlite') {
                $table->foreign('transaction_id')->references('id')->on('transactions');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_notifications');
    }
}
