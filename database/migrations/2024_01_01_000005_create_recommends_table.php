<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('song_id')->constrained()->comment('추천 노래 ID');
            $table->foreignId('user_id')->constrained()->comment('추천 유저 ID');
            $table->integer('score')->default(1)->comment('추천 점수 (1 ~ 5)');
            $table->text('comment')->nullable()->comment('추천 관련 추가 설명');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommends');
    }
};
