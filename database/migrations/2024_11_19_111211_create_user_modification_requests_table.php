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
        Schema::create('user_modification_requests', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['EDIT', 'DELETE']);
            $table->unsignedBigInteger('requested_by')->index();
            $table->unsignedBigInteger('requested_to')->index();
            $table->unsignedBigInteger('target_id')->index();
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('email', 50)->nullable()->unique();
            $table->string('phone', 12)->nullable()->unique();
            $table->string('profile', 50)->nullable();
            $table->json('address')->nullable()->comment('full address in json');
            $table->string('owner_name', 50)->nullable();
            $table->string('roll_number', 20)->nullable();
            $table->string('parents_name', 50)->nullable();
            $table->decimal('experience', 2, 1)->nullable();
            $table->json('expertises')->nullable();
            $table->enum('user_status', ['ACTIVE', 'INACTIVE'])->nullable();
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_modification_requests');
    }
};
