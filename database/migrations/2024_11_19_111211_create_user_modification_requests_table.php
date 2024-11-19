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
            $table->enum('type', ['edit', 'delete']);
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('requested_to')->constrained('users')->onDelete('cascade');
            $table->foreignId('target_id')->constrained('users')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable()->unique();
            $table->string('profile')->nullable();
            $table->string('address')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('roll_number')->nullable();
            $table->string('parents_name')->nullable();
            $table->string('experience')->nullable();
            $table->string('expertises')->nullable();
            $table->enum('user_status', ['Active', 'Inactive'])->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
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
