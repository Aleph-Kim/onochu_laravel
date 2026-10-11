<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\NewAlbum;
use App\Models\Recommend;
use App\Models\Song;
use App\Models\User;
use App\Services\FloApiService;
use App\Services\ImageService;
use App\Services\WebPushService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class UpdateNewAlbumsTest extends TestCase
{
    use RefreshDatabase;

    #[TestDox('아티스트 이미지 갱신이 실패해도 새 앨범 저장과 알림은 완료된다')]
    public function test_artist_image_update_failure_does_not_drop_notifications(): void
    {
        $user = User::factory()->create();
        $artist = $this->createRecommendedArtist($user);

        $this->mockFloApi([$this->newAlbumData(1001, $artist)], function ($mock) {
            $mock->shouldReceive('getArtistByFloId')->andThrow(new HttpException(500, 'FLO API 요청 실패'));
        });
        $this->mock(ImageService::class)->shouldNotReceive('uploadImage');
        $this->mockWebPush();

        try {
            $this->artisan('albums:update-new')->run();
            $this->fail('FLO API 실패 예외가 전달되어야 함');
        } catch (HttpException) {
        }

        $this->assertDatabaseHas('new_albums', ['flo_id' => 1001]);
        $this->assertDatabaseHas('new_album_artists', ['flo_id' => $artist->flo_id]);
        $this->assertSame(1, $user->notifications()->count());
    }

    private function createRecommendedArtist(User $user): Artist
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create();
        $song->artists()->attach($artist);
        Recommend::factory()->create(['song_id' => $song->id, 'user_id' => $user->id]);

        return $artist;
    }

    private function newAlbumData(int $albumFloId, Artist $artist): array
    {
        return [
            'flo_id' => $albumFloId,
            'title' => '새 앨범 ' . $albumFloId,
            'type' => '정규',
            'genre' => 'K-POP',
            'img_url' => 'https://example.com/album.jpg',
            'release_date' => '2026.10.11',
            'artist' => [['flo_id' => $artist->flo_id, 'name' => $artist->name, 'img_url' => '']],
        ];
    }

    private function mockFloApi(array $kpopAlbums, ?callable $extra = null): void
    {
        $kpop = ['albums_info' => [], 'artists_flo_id' => []];
        foreach ($kpopAlbums as $album) {
            $kpop['albums_info'][$album['flo_id']] = $album;
            foreach ($album['artist'] as $artist) {
                $kpop['artists_flo_id'][$artist['flo_id']] = $album['flo_id'];
            }
        }

        $this->mock(FloApiService::class, function ($mock) use ($kpop, $extra) {
            $mock->shouldReceive('getNewKpopAlbum')->andReturn($kpop);
            $mock->shouldReceive('getNewPopAlbum')->andReturn(['albums_info' => [], 'artists_flo_id' => []]);
            if ($extra) {
                $extra($mock);
            }
        });
    }

    private function mockWebPush(): void
    {
        $this->mock(WebPushService::class, function ($mock) {
            $mock->shouldReceive('queue');
            $mock->shouldReceive('flush');
        });
    }
}
