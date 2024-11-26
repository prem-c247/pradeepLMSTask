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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('address_1', 100);
            $table->string('address_2', 100)->nullable();
            $table->string('street', 50)->nullable();
            $table->string('city', 50);
            $table->string('state', 50);
            $table->string('postal_code', 10);
            $table->string('country', 50);
            $table->string('latitude', 20)->nullable();
            $table->string('longitude', 20)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
