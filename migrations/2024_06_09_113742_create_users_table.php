<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('name', 100);
            $table->string('identify', 11)->unique();
            $table->string('email', 150)->unique();
            $table->string('password');
            $table->enum('user_type', ['user', 'salesman'])->default('user');
            $table->datetimes();

            $table->primary('id');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}
