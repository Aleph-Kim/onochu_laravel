<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Recommend;
use App\Models\RecommendPlay;
use App\Models\Song;
use App\Models\User;
use App\Services\FloApiService;
use App\Services\ImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\Traits\FakesFloApi;
use Tests\TestCase;

class RecommendsControllerTest extends TestCase
{
    use RefreshDatabase, FakesFloApi;

    private function loginSession(User $user, array $extra = []): array
    {
        return array_merge([
            'user' => ['id' => $user->id, 'nickname' => $user->nickname],
        ], $extra);
    }

    #[TestDox('로그인하지 않으면 로그인 페이지로 리다이렉트한다')]
    public function test_create_requires_login(): void
    {
        $response = $this->get('/recommends/create?id=123');

        $response->assertRedirect(route('login'));
    }

    #[TestDox('id가 없으면 400을 반환한다')]
    public function test_create_missing_id_returns_400(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession($this->loginSession($user))->get('/recommends/create');

        $response->assertStatus(400);
    }

    #[TestDox('유효한 id면 곡 ID를 담은 작성 폼을 반환한다')]
    public function test_create_valid_id_returns_view_with_song_id(): void
    {
        $user = User::factory()->create();
        $songInfo = $this->fakeTrackInfo(['flo_id' => 321]);

        $this->mock(FloApiService::class, function ($mock) use ($songInfo) {
            $mock->shouldReceive('getSongByFloId')->with(321)->andReturn($songInfo);
        });

        $response = $this->withSession($this->loginSession($user))->get('/recommends/create?id=321');

        $response->assertOk();
        $response->assertViewIs('recommends.index');
        $response->assertSee('name="id" value="321"', false);
    }

    #[TestDox('로그인하지 않으면 로그인 페이지로 리다이렉트한다')]
    public function test_store_requires_login(): void
    {
        $response = $this->post('/recommends', ['score' => 5]);

        $response->assertRedirect(route('login'));
    }

    #[TestDox('기존 아티스트/앨범이 있으면 재사용하여 추천을 생성한다')]
    public function test_store_creates_recommend_reusing_existing_artist_and_album(): void
    {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $album = Album::factory()->create();
        $songInfo = $this->fakeTrackInfo(['flo_id' => 321], ['flo_id' => $artist->flo_id], ['flo_id' => $album->flo_id]);

        $this->mock(FloApiService::class, function ($mock) use ($songInfo) {
            $mock->shouldReceive('getSongByFloId')->with(321)->andReturn($songInfo);
        });

        $response = $this->withSession($this->loginSession($user))
            ->post('/recommends', ['id' => 321, 'score' => 5, 'comment' => '최고예요']);

        $recommend = Recommend::first();
        $response->assertRedirect(route('recommends.show', $recommend));
        $this->assertDatabaseHas('recommends', [
            'user_id' => $user->id,
            'score'   => 5,
            'comment' => '최고예요',
        ]);
        $this->assertDatabaseHas('songs', ['flo_id' => $songInfo['song']['flo_id'], 'album_id' => $album->id]);
        $this->assertDatabaseCount('artists', 1);
        $this->assertDatabaseCount('albums', 1);
    }

    #[TestDox('별점을 0.5점 단위로 저장한다')]
    public function test_store_saves_half_point_score(): void
    {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $album = Album::factory()->create();
        $songInfo = $this->fakeTrackInfo(['flo_id' => 321], ['flo_id' => $artist->flo_id], ['flo_id' => $album->flo_id]);

        $this->mock(FloApiService::class, function ($mock) use ($songInfo) {
            $mock->shouldReceive('getSongByFloId')->with(321)->andReturn($songInfo);
        });

        $this->withSession($this->loginSession($user))
            ->post('/recommends', ['id' => 321, 'score' => 4.5]);

        $this->assertDatabaseHas('recommends', ['user_id' => $user->id, 'score' => 4.5]);
    }

    #[TestDox('별점이 0.5~5 범위의 0.5점 단위가 아니면 저장하지 않는다')]
    #[DataProvider('invalidScoreProvider')]
    public function test_store_rejects_invalid_score(mixed $score): void
    {
        $user = User::factory()->create();

        $response = $this->withSession($this->loginSession($user))
            ->post('/recommends', ['id' => 321, 'score' => $score]);

        $response->assertSessionHasErrors('score');
        $this->assertDatabaseCount('recommends', 0);
    }

    public static function invalidScoreProvider(): array
    {
        return [
            '0점' => [0],
            '0.5 단위가 아닌 값' => [4.3],
            '5점 초과' => [5.5],
        ];
    }

    #[TestDox('트랙 응답에 아티스트 이미지가 없으면 아티스트 API 이미지로 업로드한다')]
    public function test_store_falls_back_to_artist_api_when_track_has_no_artist_image(): void
    {
        $user = User::factory()->create();
        $album = Album::factory()->create();
        $songInfo = $this->fakeTrackInfo(['flo_id' => 321], ['flo_id' => 49, 'img_url' => false], ['flo_id' => $album->flo_id]);
        $floImgUrl = 'https://cdn.music-flo.com/image/v2/artist/017/000/00/04/400000017_6555662a_o.jpg';

        $this->mock(FloApiService::class, function ($mock) use ($songInfo, $floImgUrl) {
            $mock->shouldReceive('getSongByFloId')->with(321)->andReturn($songInfo);
            $mock->shouldReceive('getArtistByFloId')->with(49)->andReturn(['img_url' => $floImgUrl]);
        });
        $this->mock(ImageService::class, function ($mock) use ($floImgUrl) {
            $mock->shouldReceive('uploadImage')
                ->with($floImgUrl . '?/dims/resize/1000x1000/quality/90', 'artist')
                ->once()
                ->andReturn('https://img.aleph.kr/files/test/artist-1.jpg');
        });

        $this->withSession($this->loginSession($user))
            ->post('/recommends', ['id' => 321, 'score' => 5]);

        $this->assertDatabaseHas('artists', [
            'flo_id'      => 49,
            'img_url'     => 'https://img.aleph.kr/files/test/artist-1.jpg',
            'flo_img_url' => $floImgUrl,
        ]);
    }

    #[TestDox('아티스트 API 폴백이 실패해도 이미지 없이 추천을 저장한다')]
    public function test_store_saves_recommend_without_image_when_artist_api_fails(): void
    {
        $user = User::factory()->create();
        $album = Album::factory()->create();
        $songInfo = $this->fakeTrackInfo(['flo_id' => 321], ['flo_id' => 49, 'img_url' => false], ['flo_id' => $album->flo_id]);

        $this->mock(FloApiService::class, function ($mock) use ($songInfo) {
            $mock->shouldReceive('getSongByFloId')->with(321)->andReturn($songInfo);
            $mock->shouldReceive('getArtistByFloId')->with(49)->andThrow(new HttpException(500));
        });
        $this->mock(ImageService::class, function ($mock) {
            $mock->shouldNotReceive('uploadImage');
        });

        $response = $this->withSession($this->loginSession($user))
            ->post('/recommends', ['id' => 321, 'score' => 5]);

        $response->assertRedirect(route('recommends.show', Recommend::first()));
        $this->assertDatabaseHas('artists', ['flo_id' => 49, 'img_url' => null]);
    }

    #[TestDox('곡 ID가 없으면 추천을 저장하지 않는다')]
    public function test_store_without_song_id_fails_validation(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession($this->loginSession($user))
            ->post('/recommends', ['score' => 5]);

        $response->assertSessionHasErrors('id');
        $this->assertDatabaseCount('recommends', 0);
    }

    #[TestDox('추천 상세 정보를 반환한다')]
    public function test_show_returns_recommend_detail(): void
    {
        $recommend = $this->createFullRecommend();

        $response = $this->get(route('recommends.show', $recommend));

        $response->assertOk();
        $response->assertViewIs('recommends.detail');
        $response->assertViewHas('recommend', fn ($viewRecommend) => $viewRecommend->is($recommend));
    }

    #[TestDox('존재하지 않는 추천이면 404를 반환한다')]
    public function test_show_nonexistent_recommend_returns_404(): void
    {
        $response = $this->get('/recommends/99999');

        $response->assertNotFound();
    }

    #[TestDox('로그인하지 않으면 401을 반환한다')]
    public function test_destroy_requires_login(): void
    {
        $recommend = $this->createFullRecommend();

        $response = $this->deleteJson("/api/recommends/{$recommend->id}");

        $response->assertStatus(401);
        $response->assertJson(['success' => false]);
    }

    #[TestDox('본인의 추천이 아니면 403을 반환한다')]
    public function test_destroy_by_non_owner_returns_403(): void
    {
        $recommend = $this->createFullRecommend();
        $otherUser = User::factory()->create();

        $response = $this->withSession($this->loginSession($otherUser))
            ->deleteJson("/api/recommends/{$recommend->id}");

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
        $this->assertModelExists($recommend);
    }

    #[TestDox('본인의 추천이면 삭제하고 프로필 앨범을 초기화한다')]
    public function test_destroy_by_owner_deletes_and_resets_profile_album(): void
    {
        $recommend = $this->createFullRecommend();
        $owner = $recommend->user;
        $owner->update(['profile_album_id' => $recommend->song->album_id]);

        $response = $this->withSession($this->loginSession($owner))
            ->deleteJson("/api/recommends/{$recommend->id}");

        $response->assertOk();
        $response->assertJson(['success' => true, 'data' => ['profile_reset' => true]]);
        $this->assertModelMissing($recommend);
        $this->assertDatabaseHas('users', ['id' => $owner->id, 'profile_album_id' => null]);
    }

    #[TestDox('다른 유저가 재생한 추천도 재생 기록과 함께 삭제한다')]
    public function test_destroy_deletes_recommend_with_plays(): void
    {
        $recommend = $this->createFullRecommend();
        $play = RecommendPlay::factory()->create(['recommend_id' => $recommend->id]);

        $response = $this->withSession($this->loginSession($recommend->user))
            ->deleteJson("/api/recommends/{$recommend->id}");

        $response->assertOk();
        $this->assertModelMissing($recommend);
        $this->assertModelMissing($play);
    }

    private function createFullRecommend(): Recommend
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create();
        $song->artists()->attach($artist);

        return Recommend::factory()->create(['song_id' => $song->id]);
    }
}
