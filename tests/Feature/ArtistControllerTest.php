<?php

namespace Tests\Feature;

use App\Services\FloApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Traits\FakesFloApi;
use Tests\TestCase;

class ArtistControllerTest extends TestCase
{
    use RefreshDatabase, FakesFloApi;

    #[TestDox('id가 없으면 400을 반환한다')]
    public function test_missing_id_returns_400(): void
    {
        $response = $this->get('/artist/detail');

        $response->assertStatus(400);
    }

    #[TestDox('유효한 id면 아티스트 상세 정보를 반환한다')]
    public function test_valid_id_returns_artist_detail(): void
    {
        $this->mock(FloApiService::class, function ($mock) {
            $mock->shouldReceive('getArtistByFloId')
                ->with(789)
                ->andReturn($this->fakeArtistInfo(['flo_id' => 789]));
            $mock->shouldReceive('getAlbumsByArtistFloId')
                ->with(789)
                ->andReturn([$this->fakeAlbumInfo() + ['artists' => [$this->fakeArtistInfo()]]]);
        });

        $response = $this->get('/artist/detail?id=789');

        $response->assertOk();
        $response->assertViewIs('artist.detail');
        $response->assertViewHas('artistInfo.flo_id', 789);
    }
}
