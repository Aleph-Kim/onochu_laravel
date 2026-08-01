<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('new_album_artists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('new_album_id')->constrained('new_albums')->comment('새 앨범 ID');
            $table->string('artist_name')->comment('아티스트 이름');
            $table->integer('flo_id')->comment('FLO music에서 사용하는 고유 ID');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('new_album_artists');
    }
};
