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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->index();
            $table->foreignId('school_id')
                ->constrained('users', 'id')
                ->onDelete('cascade')
                ->comment('primary ID of user table')
                ->index('user_school_id');

            $table->string('roll_number', 20)->nullable()->index();
            $table->date('dob')->nullable();
            $table->string('parents_name', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
