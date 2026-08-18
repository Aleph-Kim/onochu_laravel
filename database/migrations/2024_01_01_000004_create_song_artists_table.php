<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('song_artists', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('song_id')->comment('노래 ID');
            $table->unsignedInteger('artist_id')->comment('아티스트 ID');
            $table->foreign('song_id')->references('id')->on('songs');
            $table->foreign('artist_id')->references('id')->on('artists');
            $table->timestamps();
            $table->unique(['song_id', 'artist_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('song_artists');
    }
};
