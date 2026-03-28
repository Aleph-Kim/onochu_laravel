<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('아티스트 이름');
            $table->string('genre', 50)->nullable()->comment('장르 정보');
            $table->string('group_type', 50)->nullable()->comment('그룹 정보');
            $table->string('img_url')->nullable()->comment('아티스트 사진 URL');
            $table->integer('flo_id')->unique()->nullable()->comment('FLO music에서 사용하는 고유 ID');
            $table->string('flo_img_url')->nullable()->comment('FLO music에서 사용하는 아티스트 사진 URL');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artists');
    }
};
