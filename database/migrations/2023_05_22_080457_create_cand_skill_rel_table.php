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
        Schema::create('cand_skill_rel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cand_info_id');
            $table->unsignedBigInteger('skill_id');
            $table->timestamps();

            // Define foreign key relationships
            $table->foreign('cand_info_id')->references('id')->on('candidate_info')->onDelete('cascade');
            $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cand_skill_rel');
    }
};
