<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('new_albums', function (Blueprint $table) {
            $table->id();
            $table->string('album_title')->comment('앨범 제목');
            $table->string('album_img_url')->nullable()->comment('앨범 커버 사진 URL');
            $table->integer('flo_id')->comment('FLO music에서 사용하는 앨범 고유 ID');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('new_albums');
    }
};
