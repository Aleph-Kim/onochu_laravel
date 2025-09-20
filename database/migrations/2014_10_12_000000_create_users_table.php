<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('type')->comment('타입(공급자, 수요자)');
            $table->integer('status')->comment('상태(승인 대기, 승인, 탈퇴, 거절)');
            $table->string('login_id')->comment('아이디');
            $table->string('password')->comment('비밀번호');
            $table->string('email')->comment('이메일');
            $table->string('name')->comment('담당자명');
            $table->string('phone')->comment('담당자 연락처');
            $table->string('company')->comment('업체명/기관명')->nullable();
            $table->date('birth')->comment('생년월일')->nullable();
            $table->boolean('is_marketing_email_allowed')->comment('마케팅 메일 수신 여부')->default(false);
            $table->timestamp('confirmed_at')->comment('승인일')->nullable();
            $table->timestamp('expired_at')->comment('탈퇴일')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        // 외래 키 제약 조건 비활성화 후 테이블 삭제
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('users');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
