<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite(테스트 DB)는 기존 FK 변경 미지원이라 생성 마이그레이션에 반영, 운영 MySQL에서만 실행
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('recommend_plays', function (Blueprint $table) {
            $table->dropForeign(['recommend_id']);
            $table->foreign('recommend_id')->references('id')->on('recommends')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('recommend_plays', function (Blueprint $table) {
            $table->dropForeign(['recommend_id']);
            $table->foreign('recommend_id')->references('id')->on('recommends');
        });
    }
};
