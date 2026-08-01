<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->constrained()->comment('연관 앨범 ID');
            $table->string('title')->comment('노래 제목');
            $table->string('genre', 50)->nullable()->comment('장르 정보');
            $table->string('title_yn', 50)->nullable()->comment('타이틀 여부');
            $table->string('play_time', 50)->nullable()->comment('재생 시간');
            $table->text('lyrics')->nullable()->comment('노래 가사');
            $table->string('composer', 100)->nullable()->comment('작곡가');
            $table->string('lyricist', 100)->nullable()->comment('작사가');
            $table->string('arranger', 100)->nullable()->comment('편곡가');
            $table->integer('flo_id')->unique()->nullable()->comment('FLO music에서 사용하는 고유 ID');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
