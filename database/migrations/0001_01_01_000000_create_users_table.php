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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id')->nullable()->index();

            // $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('set null');
            $table->string('first_name', 50)->nullable()->index('users_first_name_index');
            $table->string('last_name', 50)->nullable()->index('users_last_name_index');
            $table->string('email', 100)->unique()->index();
            $table->string('phone', 12)->unique()->nullable()->index();
            $table->string('profile', 50)->nullable();
            $table->string('password');
            $table->enum('status', ['PENDING', 'ACTIVE', 'INACTIVE'])->default('PENDING');
            $table->unsignedBigInteger('address_id')->nullable();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};
