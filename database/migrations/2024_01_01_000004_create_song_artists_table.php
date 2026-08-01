<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('song_artists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('song_id')->constrained()->comment('노래 ID');
            $table->foreignId('artist_id')->constrained()->comment('아티스트 ID');
            $table->timestamps();
            $table->unique(['song_id', 'artist_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('song_artists');
    }
};
