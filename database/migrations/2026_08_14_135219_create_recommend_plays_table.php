<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recommend_plays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->comment('재생한(공유받은) 유저 ID');
            $table->foreignId('recommend_id')->constrained()->comment('재생된 공유(추천) ID');
            $table->timestamp('notified_at')->nullable()->comment('모달 노출 완료 시각');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommend_plays');
    }
};
