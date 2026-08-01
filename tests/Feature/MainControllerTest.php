<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Recommend;
use App\Models\Song;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class MainControllerTest extends TestCase
{
    use RefreshDatabase;

    #[TestDox('비로그인 사용자는 인기 아티스트와 추천곡을 볼 수 있다')]
    public function test_guest_sees_popular_artists_and_recommends(): void
    {
        $artist = Artist::factory()->create();
        $song = Song::factory()->create();
        $song->artists()->attach($artist);
        Recommend::factory()->create(['song_id' => $song->id]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertViewIs('main.index');
        $response->assertViewHas('recommends');
        $response->assertViewHas('artists');
    }

    #[TestDox('로그인한 사용자는 자신이 추천한 아티스트를 볼 수 있다')]
    public function test_logged_in_user_sees_their_recommended_artists(): void
    {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $song = Song::factory()->create();
        $song->artists()->attach($artist);
        Recommend::factory()->create(['song_id' => $song->id, 'user_id' => $user->id]);

        $response = $this->withSession(['user' => ['id' => $user->id, 'nickname' => $user->nickname]])
            ->get('/');

        $response->assertOk();
        $response->assertViewHas('artists', function ($artists) use ($artist) {
            return $artists->contains('flo_id', $artist->flo_id);
        });
    }
}
