<?php

use App\Models\Recommend;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recommends', function (Blueprint $table) {
            $table->integer('score')->nullable()->default(null)->comment('추천 점수 (1 ~ 5, nullable)')->change();
        });
    }

    public function down(): void
    {
        Recommend::whereNull('score')->update(['score' => 1]);

        Schema::table('recommends', function (Blueprint $table) {
            $table->integer('score')->nullable(false)->default(1)->comment('추천 점수 (1 ~ 5)')->change();
        });
    }
};
