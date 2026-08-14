<?php

namespace Tests\Feature;

use App\Enums\MusicApp;
use App\Models\Recommend;
use App\Models\Song;
use App\Models\User;
use App\Services\FloApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Traits\FakesFloApi;
use Tests\TestCase;

class MusicAppOpenControllerTest extends TestCase
{
    use RefreshDatabase, FakesFloApi;

    private function loginSession(User $user): array
    {
        return ['user' => ['id' => $user->id, 'nickname' => $user->nickname]];
    }

    #[TestDox('비로그인 상태면 로그인 페이지로 리다이렉트한다')]
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/music-app/open?id=123');

        $response->assertRedirect(route('login'));
    }

    #[TestDox('저장된 뮤직앱이 없으면 설정 화면으로 리다이렉트한다')]
    public function test_redirects_to_settings_when_no_preference_saved(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession($this->loginSession($user))->get('/music-app/open?id=123');

        $response->assertRedirect(route('mypage.music-app', ['redirect' => '/music-app/open?id=123']));
    }

    #[TestDox('저장된 앱이 youtube면 유튜브 뮤직 URL로 리다이렉트한다')]
    public function test_redirects_to_youtube_when_preferred(): void
    {
        $user = User::factory()->create(['preferred_music_app' => MusicApp::Youtube]);

        $this->mock(FloApiService::class, function ($mock) {
            $mock->shouldReceive('getSongByFloId')
                ->with(123)
                ->andReturn($this->fakeTrackInfo(['flo_id' => 123]));
        });

        $response = $this->withSession($this->loginSession($user))->get('/music-app/open?id=123');

        $response->assertRedirect('https://music.youtube.com/search?q=test');
    }

    #[TestDox('저장된 앱이 apple_music이면 앱-웹 폴백 트랜지션 화면을 보여준다')]
    public function test_shows_transition_page_when_apple_music_preferred(): void
    {
        $user = User::factory()->create(['preferred_music_app' => MusicApp::AppleMusic]);

        Http::fake([
            'itunes.apple.com/*' => Http::response([
                'results' => [
                    ['trackViewUrl' => 'https://music.apple.com/kr/album/test/1?i=2'],
                ],
            ]),
        ]);

        $this->mock(FloApiService::class, function ($mock) {
            $mock->shouldReceive('getSongByFloId')
                ->with(123)
                ->andReturn($this->fakeTrackInfo(['flo_id' => 123]));
        });

        $response = $this->withSession($this->loginSession($user))->get('/music-app/open?id=123');

        $response->assertOk();
        $response->assertViewIs('music-app.redirect');
    }

    #[TestDox('모바일에서 저장된 앱이 flo면 앱 스킴으로 이동하는 트랜지션 화면을 보여준다')]
    public function test_shows_transition_page_when_flo_preferred_on_mobile(): void
    {
        $user = User::factory()->create(['preferred_music_app' => MusicApp::Flo]);

        $this->mock(FloApiService::class, function ($mock) {
            $mock->shouldReceive('getSongByFloId')
                ->with(123)
                ->andReturn($this->fakeTrackInfo(['flo_id' => 123, 'url' => [
                    'youtube' => ['app' => null, 'web' => 'https://music.youtube.com/search?q=test'],
                    'flo' => ['app' => 'flomusic://view/content?type=TRACK&id=123', 'web' => 'https://www.music-flo.com/detail/track/123/details'],
                    'spotify' => ['app' => null, 'web' => 'https://open.spotify.com/search/test/tracks'],
                    'apple_music_keyword' => 'test',
                ]]));
        });

        $response = $this->withSession($this->loginSession($user))
            ->withHeader('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)')
            ->get('/music-app/open?id=123');

        $response->assertOk();
        $response->assertViewIs('music-app.redirect');
        $response->assertViewHas('appUrl', 'flomusic://view/content?type=TRACK&id=123');
    }

    #[TestDox('모바일에서 저장된 앱이 melon이면 melonapp 스킴으로 이동하는 트랜지션 화면을 보여준다')]
    public function test_shows_transition_page_when_melon_preferred_on_mobile(): void
    {
        $user = User::factory()->create(['preferred_music_app' => MusicApp::Melon]);

        Http::fake([
            'www.melon.com/search/song/*' => Http::response("melon.link.goSongDetail('30314784')"),
        ]);

        $this->mock(FloApiService::class, function ($mock) {
            $mock->shouldReceive('getSongByFloId')
                ->with(123)
                ->andReturn($this->fakeTrackInfo(['flo_id' => 123]));
        });

        $response = $this->withSession($this->loginSession($user))
            ->withHeader('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)')
            ->get('/music-app/open?id=123');

        $response->assertOk();
        $response->assertViewIs('music-app.redirect');
        $response->assertViewHas('appUrl', 'melonapp://play?ctype=1&menuid=0&cid=30314784');
    }

    #[TestDox('저장된 앱이 genie면 지니 상세페이지 웹 URL로 리다이렉트한다')]
    public function test_redirects_to_genie_web_url_when_preferred(): void
    {
        $user = User::factory()->create(['preferred_music_app' => MusicApp::Genie]);

        Http::fake([
            'www.genie.co.kr/search/searchMain*' => Http::response("fnViewSongInfo('30314784')"),
        ]);

        $this->mock(FloApiService::class, function ($mock) {
            $mock->shouldReceive('getSongByFloId')
                ->with(123)
                ->andReturn($this->fakeTrackInfo(['flo_id' => 123]));
        });

        $response = $this->withSession($this->loginSession($user))->get('/music-app/open?id=123');

        $response->assertRedirect('https://www.genie.co.kr/detail/songInfo?xgnm=30314784');
    }

    #[TestDox('다른 회원의 추천곡을 재생하면 재생 기록이 생성된다')]
    public function test_creates_recommend_play_when_playing_others_recommend(): void
    {
        $listener = User::factory()->create(['preferred_music_app' => MusicApp::Youtube]);
        $recommender = User::factory()->create();
        $song = Song::factory()->create(['flo_id' => 123]);
        $recommend = Recommend::factory()->create(['song_id' => $song->id, 'user_id' => $recommender->id]);

        $this->mock(FloApiService::class, function ($mock) {
            $mock->shouldReceive('getSongByFloId')
                ->with(123)
                ->andReturn($this->fakeTrackInfo(['flo_id' => 123]));
        });

        $this->withSession($this->loginSession($listener))
            ->get("/music-app/open?id=123&recommend={$recommend->id}");

        $this->assertDatabaseHas('recommend_plays', [
            'user_id' => $listener->id,
            'recommend_id' => $recommend->id,
            'notified_at' => null,
        ]);
    }

    #[TestDox('본인이 작성한 추천을 재생하면 재생 기록이 생성되지 않는다')]
    public function test_does_not_create_recommend_play_for_own_recommend(): void
    {
        $user = User::factory()->create(['preferred_music_app' => MusicApp::Youtube]);
        $song = Song::factory()->create(['flo_id' => 123]);
        $recommend = Recommend::factory()->create(['song_id' => $song->id, 'user_id' => $user->id]);

        $this->mock(FloApiService::class, function ($mock) {
            $mock->shouldReceive('getSongByFloId')
                ->with(123)
                ->andReturn($this->fakeTrackInfo(['flo_id' => 123]));
        });

        $this->withSession($this->loginSession($user))
            ->get("/music-app/open?id=123&recommend={$recommend->id}");

        $this->assertDatabaseCount('recommend_plays', 0);
    }

    #[TestDox('recommend 파라미터가 없으면 재생 기록이 생성되지 않는다')]
    public function test_does_not_create_recommend_play_without_recommend_param(): void
    {
        $user = User::factory()->create(['preferred_music_app' => MusicApp::Youtube]);

        $this->mock(FloApiService::class, function ($mock) {
            $mock->shouldReceive('getSongByFloId')
                ->with(123)
                ->andReturn($this->fakeTrackInfo(['flo_id' => 123]));
        });

        $this->withSession($this->loginSession($user))->get('/music-app/open?id=123');

        $this->assertDatabaseCount('recommend_plays', 0);
    }
}
