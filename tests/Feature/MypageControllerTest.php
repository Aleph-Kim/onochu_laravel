<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Recommend;
use App\Models\Song;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class MypageControllerTest extends TestCase
{
    use RefreshDatabase;

    private function loginSession(User $user): array
    {
        return ['user' => ['id' => $user->id, 'nickname' => $user->nickname]];
    }

    private function createFullRecommend(?User $user = null): Recommend
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create();
        $song->artists()->attach($artist);

        return Recommend::factory()->create([
            'song_id' => $song->id,
            'user_id' => ($user ?? User::factory()->create())->id,
        ]);
    }

    #[TestDox('로그인하지 않으면 로그인 페이지로 리다이렉트한다')]
    public function test_index_requires_login(): void
    {
        $response = $this->get('/mypage');

        $response->assertRedirect(route('login'));
    }

    #[TestDox('내 마이페이지를 보여준다')]
    public function test_index_shows_own_page(): void
    {
        $user = User::factory()->create();
        $this->createFullRecommend($user);

        $response = $this->withSession($this->loginSession($user))->get('/mypage');

        $response->assertOk();
        $response->assertViewIs('mypage.show');
        $response->assertViewHas('isOwner', true);
        $response->assertViewHas('user', fn ($viewUser) => $viewUser->is($user));
    }

    #[TestDox('다른 유저의 마이페이지를 보여준다')]
    public function test_user_shows_other_users_page(): void
    {
        $viewer = User::factory()->create();
        $owner = User::factory()->create();
        $this->createFullRecommend($owner);

        $response = $this->withSession($this->loginSession($viewer))->get(route('mypage.user', $owner));

        $response->assertOk();
        $response->assertViewIs('mypage.show');
        $response->assertViewHas('isOwner', false);
    }

    #[TestDox('본인의 id로 접근하면 내 마이페이지로 리다이렉트한다')]
    public function test_user_redirects_to_index_when_viewing_self(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession($this->loginSession($user))->get(route('mypage.user', $user));

        $response->assertRedirect(route('mypage.index'));
    }

    #[TestDox('존재하지 않는 유저면 404를 반환한다')]
    public function test_user_nonexistent_returns_404(): void
    {
        $response = $this->get('/mypage/99999');

        $response->assertNotFound();
    }

    #[TestDox('로그인하지 않으면 401을 반환한다')]
    public function test_set_profile_album_requires_login(): void
    {
        $recommend = $this->createFullRecommend();

        $response = $this->postJson("/api/mypage/profile-album/{$recommend->id}");

        $response->assertStatus(401);
        $response->assertJson(['success' => false]);
    }

    #[TestDox('본인의 추천이 아니면 400을 반환한다')]
    public function test_set_profile_album_by_non_owner_returns_400(): void
    {
        $recommend = $this->createFullRecommend(User::factory()->create());
        $otherUser = User::factory()->create();

        $response = $this->withSession($this->loginSession($otherUser))
            ->postJson("/api/mypage/profile-album/{$recommend->id}");

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    #[TestDox('본인의 추천이면 프로필 앨범을 변경한다')]
    public function test_set_profile_album_by_owner_updates_profile_album(): void
    {
        $owner = User::factory()->create();
        $recommend = $this->createFullRecommend($owner);

        $response = $this->withSession($this->loginSession($owner))
            ->postJson("/api/mypage/profile-album/{$recommend->id}");

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('users', [
            'id'               => $owner->id,
            'profile_album_id' => $recommend->song->album_id,
        ]);
    }
}
