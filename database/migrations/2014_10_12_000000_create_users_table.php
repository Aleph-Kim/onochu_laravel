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
            $table->string('kakao_id', 50)->unique()->comment('카카오 고유 ID');
            $table->string('nickname', 50)->comment('유저 닉네임');
            $table->unsignedBigInteger('profile_album_id')->nullable()->comment('유저 프로필 앨범');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
