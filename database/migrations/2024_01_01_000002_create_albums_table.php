<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('앨범 제목');
            $table->date('release_date')->nullable()->comment('발매일');
            $table->string('genre', 50)->nullable()->comment('장르 정보');
            $table->string('type', 50)->nullable()->comment('타입');
            $table->string('img_url')->nullable()->comment('앨범 커버 사진 URL');
            $table->integer('flo_id')->unique()->nullable()->comment('FLO music에서 사용하는 고유 ID');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
