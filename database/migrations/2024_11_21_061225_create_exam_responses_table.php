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
        Schema::create('exam_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')
                ->constrained()
                ->onDelete('cascade')
                ->index('exam_responses_exam_id_index');

            $table->unsignedBigInteger('question_id')
                ->index('exam_responses_question_id_index');

            $table->integer('chosen_option');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_responses');
    }
};
