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
        // SQLite(테스트 DB)는 컬럼 타입을 엄격히 구분하지 않아 int/bigint 차이가 없고,
        // dropForeign·raw MODIFY 구문도 미지원 -> 운영 MySQL 서버에서만 실행
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        // 서버 DB는 레거시 스키마라 FK가 라라벨 명명 규칙이 아닌 MySQL 기본 이름(예: recommends_ibfk_2)으로 걸려있어,
        // 컬럼 타입 변경 전 information_schema에서 실제 제약 이름을 조회 후 제거
        foreach ([
            ['songs', 'album_id'],
            ['song_artists', 'song_id'],
            ['song_artists', 'artist_id'],
            ['new_album_artists', 'new_album_id'],
            ['recommends', 'song_id'],
            ['recommends', 'user_id'],
        ] as [$tableName, $columnName]) {
            $this->dropExistingForeignKey($tableName, $columnName);
        }

        // 부모 테이블 id: int → bigint unsigned (auto increment, 라라벨 표준 $table->id())
        // Blueprint::change()는 이미 PRIMARY KEY인 컬럼에도 auto_increment primary key를 다시 붙여 Multiple primary key defined 에러가 나므로 raw SQL로 처리
        foreach ([
            'users', 'artists', 'albums', 'songs', 'song_artists', 'recommends',
            'new_albums', 'new_album_artists',
        ] as $tableName) {
            DB::statement("ALTER TABLE `{$tableName}` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT");
        }

        // 외래키 컬럼: int → bigint unsigned (라라벨 표준 $table->foreignId())
        Schema::table('songs', fn (Blueprint $table) => $table->unsignedBigInteger('album_id')->change());
        Schema::table('song_artists', function (Blueprint $table) {
            $table->unsignedBigInteger('song_id')->change();
            $table->unsignedBigInteger('artist_id')->change();
        });
        Schema::table('new_album_artists', fn (Blueprint $table) => $table->unsignedBigInteger('new_album_id')->change());
        Schema::table('recommends', function (Blueprint $table) {
            $table->unsignedBigInteger('song_id')->change();
            $table->unsignedBigInteger('user_id')->change();
        });

        // FK 제약 재생성
        Schema::table('songs', function (Blueprint $table) {
            $table->foreign('album_id')->references('id')->on('albums');
        });
        Schema::table('song_artists', function (Blueprint $table) {
            $table->foreign('song_id')->references('id')->on('songs');
            $table->foreign('artist_id')->references('id')->on('artists');
        });
        Schema::table('new_album_artists', function (Blueprint $table) {
            $table->foreign('new_album_id')->references('id')->on('new_albums');
        });
        Schema::table('recommends', function (Blueprint $table) {
            $table->foreign('song_id')->references('id')->on('songs');
            $table->foreign('user_id')->references('id')->on('users');
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

        // FK 제약 제거 (실제 제약 이름을 information_schema에서 조회해 제거)
        foreach ([
            ['songs', 'album_id'],
            ['song_artists', 'song_id'],
            ['song_artists', 'artist_id'],
            ['new_album_artists', 'new_album_id'],
            ['recommends', 'song_id'],
            ['recommends', 'user_id'],
        ] as [$tableName, $columnName]) {
            $this->dropExistingForeignKey($tableName, $columnName);
        }

        // 외래키 컬럼: bigint → int unsigned
        Schema::table('songs', fn (Blueprint $table) => $table->unsignedInteger('album_id')->change());
        Schema::table('song_artists', function (Blueprint $table) {
            $table->unsignedInteger('song_id')->change();
            $table->unsignedInteger('artist_id')->change();
        });
        Schema::table('new_album_artists', fn (Blueprint $table) => $table->unsignedInteger('new_album_id')->change());
        Schema::table('recommends', function (Blueprint $table) {
            $table->unsignedInteger('song_id')->change();
            $table->unsignedInteger('user_id')->change();
        });

        // 부모 테이블 id: bigint → int unsigned
        foreach ([
            'users', 'artists', 'albums', 'songs', 'song_artists', 'recommends',
            'new_albums', 'new_album_artists',
        ] as $tableName) {
            DB::statement("ALTER TABLE `{$tableName}` MODIFY `id` INT UNSIGNED NOT NULL AUTO_INCREMENT");
        }

        // FK 제약 재생성
        Schema::table('songs', function (Blueprint $table) {
            $table->foreign('album_id')->references('id')->on('albums');
        });
        Schema::table('song_artists', function (Blueprint $table) {
            $table->foreign('song_id')->references('id')->on('songs');
            $table->foreign('artist_id')->references('id')->on('artists');
        });
        Schema::table('new_album_artists', function (Blueprint $table) {
            $table->foreign('new_album_id')->references('id')->on('new_albums');
        });
        Schema::table('recommends', function (Blueprint $table) {
            $table->foreign('song_id')->references('id')->on('songs');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
    * 라라벨 명명 규칙에 의존하지 않고 실제 FK 제약 이름을 조회해 제거
    */
    private function dropExistingForeignKey(string $table, string $column): void
    {
        $constraintName = DB::selectOne(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$table, $column]
        )?->CONSTRAINT_NAME;

        if ($constraintName) {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraintName}`");
        }
    }
};
