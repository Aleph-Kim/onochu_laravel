<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Recommend;
use App\Models\Song;
use App\Models\User;
use App\Services\FloApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestDox;
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

    #[TestDox('유효한 id면 뷰를 반환하고 세션에 곡 정보를 저장한다')]
    public function test_create_valid_id_returns_view_and_stores_song_info_in_session(): void
    {
        $user = User::factory()->create();
        $songInfo = $this->fakeTrackInfo(['flo_id' => 321]);

        $this->mock(FloApiService::class, function ($mock) use ($songInfo) {
            $mock->shouldReceive('getSongByFloId')->with(321)->andReturn($songInfo);
        });

        $response = $this->withSession($this->loginSession($user))->get('/recommends/create?id=321');

        $response->assertOk();
        $response->assertViewIs('recommends.index');
        $this->assertSame(321, session('song_info.song.flo_id'));
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
        $songInfo = $this->fakeTrackInfo([], ['flo_id' => $artist->flo_id], ['flo_id' => $album->flo_id]);

        $response = $this->withSession($this->loginSession($user, ['song_info' => $songInfo]))
            ->post('/recommends', ['score' => 5, 'comment' => '최고예요']);

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

    #[TestDox('본인의 추천이 아니면 400을 반환한다')]
    public function test_destroy_by_non_owner_returns_400(): void
    {
        $recommend = $this->createFullRecommend();
        $otherUser = User::factory()->create();

        $response = $this->withSession($this->loginSession($otherUser))
            ->deleteJson("/api/recommends/{$recommend->id}");

        $response->assertStatus(400);
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

    private function createFullRecommend(): Recommend
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create();
        $song->artists()->attach($artist);

        return Recommend::factory()->create(['song_id' => $song->id]);
    }
}
