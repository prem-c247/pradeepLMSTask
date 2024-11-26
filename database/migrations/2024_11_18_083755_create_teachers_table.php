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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->index('user_id_index');

            $table->foreignId('school_id')
                ->constrained('users', 'id')
                ->onDelete('cascade')
                ->comment('primary ID of user table')
                ->index('school_user_id_index');

            $table->decimal('experience', 2, 1)->nullable();
            $table->json('expertises')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
