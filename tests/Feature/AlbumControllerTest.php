<?php

namespace Tests\Feature;

use App\Services\FloApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Traits\FakesFloApi;
use Tests\TestCase;

class AlbumControllerTest extends TestCase
{
    use RefreshDatabase, FakesFloApi;

    #[TestDox('id가 없으면 400을 반환한다')]
    public function test_missing_id_returns_400(): void
    {
        $response = $this->get('/album/detail');

        $response->assertStatus(400);
    }

    #[TestDox('유효한 id면 앨범 상세 정보를 반환한다')]
    public function test_valid_id_returns_album_detail(): void
    {
        $this->mock(FloApiService::class, function ($mock) {
            $mock->shouldReceive('getAlbumByFloId')
                ->with(456)
                ->andReturn([
                    'album_info' => $this->fakeAlbumInfo(['flo_id' => 456]),
                    'songs_info' => [$this->fakeTrackInfo()],
                ]);
        });

        $response = $this->get('/album/detail?id=456');

        $response->assertOk();
        $response->assertViewIs('album.detail');
        $response->assertViewHas('albumInfo.flo_id', 456);
    }
}
