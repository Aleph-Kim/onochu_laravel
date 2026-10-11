<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\ArtistNotificationMute;
use App\Models\NewAlbum;
use App\Models\PushSubscription;
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

    #[TestDox('추천 아티스트의 새 앨범을 저장하고 인앱 알림과 웹 푸시를 보낸다')]
    public function test_saves_new_album_and_notifies_recommender(): void
    {
        $user = User::factory()->create();
        PushSubscription::factory()->create(['user_id' => $user->id]);
        $artist = $this->createRecommendedArtist($user);

        $this->mockFloApi([$this->newAlbumData(1001, $artist)], function ($mock) use ($artist) {
            $mock->shouldReceive('getArtistByFloId')->andReturn(['img_url' => $artist->flo_img_url]);
        });
        $this->mock(ImageService::class)->shouldNotReceive('uploadImage');
        $this->mock(WebPushService::class, function ($mock) {
            $mock->shouldReceive('queue')->once();
            $mock->shouldReceive('flush')->once();
        });

        $this->artisan('albums:update-new')->assertSuccessful();

        $this->assertDatabaseHas('new_albums', ['flo_id' => 1001]);
        $this->assertSame(1, $user->notifications()->count());
        $this->assertSame(1001, $user->notifications()->first()->data['new_album_flo_id']);
    }

    #[TestDox('아티스트 알림을 끈 유저에게는 알림을 보내지 않는다')]
    public function test_muted_user_is_not_notified(): void
    {
        $user = User::factory()->create();
        PushSubscription::factory()->create(['user_id' => $user->id]);
        $artist = $this->createRecommendedArtist($user);
        ArtistNotificationMute::create(['user_id' => $user->id, 'artist_id' => $artist->id]);

        $this->mockFloApi([$this->newAlbumData(1001, $artist)], function ($mock) use ($artist) {
            $mock->shouldReceive('getArtistByFloId')->andReturn(['img_url' => $artist->flo_img_url]);
        });
        $this->mock(WebPushService::class, function ($mock) {
            $mock->shouldNotReceive('queue');
            $mock->shouldReceive('flush');
        });

        $this->artisan('albums:update-new')->assertSuccessful();

        $this->assertDatabaseHas('new_albums', ['flo_id' => 1001]);
        $this->assertSame(0, $user->notifications()->count());
    }

    #[TestDox('이미 저장된 새 앨범은 다시 저장하거나 알림을 보내지 않는다')]
    public function test_already_saved_album_is_skipped(): void
    {
        $user = User::factory()->create();
        $artist = $this->createRecommendedArtist($user);
        NewAlbum::create(['album_title' => '기존 앨범', 'album_img_url' => null, 'flo_id' => 1001]);

        $this->mockFloApi([$this->newAlbumData(1001, $artist)], function ($mock) {
            $mock->shouldNotReceive('getArtistByFloId');
        });
        $this->mockWebPush();

        $this->artisan('albums:update-new')->assertSuccessful();

        $this->assertDatabaseCount('new_albums', 1);
        $this->assertSame(0, $user->notifications()->count());
    }

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
