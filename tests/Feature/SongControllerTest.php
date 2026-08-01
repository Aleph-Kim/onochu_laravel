<?php

namespace Tests\Feature;

use App\Services\FloApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Traits\FakesFloApi;
use Tests\TestCase;

class SongControllerTest extends TestCase
{
    use RefreshDatabase, FakesFloApi;

    #[TestDox('id가 없으면 400을 반환한다')]
    public function test_missing_id_returns_400(): void
    {
        $response = $this->get('/song/detail');

        $response->assertStatus(400);
    }

    #[TestDox('id가 정수가 아니면 400을 반환한다')]
    public function test_non_integer_id_returns_400(): void
    {
        $response = $this->get('/song/detail?id=abc');

        $response->assertStatus(400);
    }

    #[TestDox('유효한 id면 곡 상세 정보를 반환한다')]
    public function test_valid_id_returns_song_detail(): void
    {
        $this->mock(FloApiService::class, function ($mock) {
            $mock->shouldReceive('getSongByFloId')
                ->with(123)
                ->andReturn($this->fakeTrackInfo(['flo_id' => 123]));
        });

        $response = $this->get('/song/detail?id=123');

        $response->assertOk();
        $response->assertViewIs('song.detail');
        $response->assertViewHas('songInfo.song.flo_id', 123);
    }
}
