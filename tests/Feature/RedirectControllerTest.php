<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class RedirectControllerTest extends TestCase
{
    use RefreshDatabase;

    #[TestDox('flo id가 없으면 400을 반환한다')]
    public function test_flo_missing_id_returns_400(): void
    {
        $response = $this->get('/redirect/flo');

        $response->assertStatus(400);
    }

    #[TestDox('flo id가 있으면 FLO URL로 리다이렉트한다')]
    public function test_flo_redirects_to_flo_url(): void
    {
        $response = $this->get('/redirect/flo?id=123');

        $response->assertRedirect('https://www.music-flo.com/detail/track/123/details');
    }

    #[TestDox('검색어가 없으면 400을 반환한다')]
    public function test_youtube_missing_keyword_returns_400(): void
    {
        $response = $this->get('/redirect/youtube');

        $response->assertStatus(400);
    }

    #[TestDox('검색어가 있으면 유튜브 URL로 리다이렉트한다')]
    public function test_youtube_redirects_to_youtube_url(): void
    {
        $response = $this->get('/redirect/youtube?q=아이유');

        $response->assertRedirect('https://music.youtube.com/search?q=아이유');
    }
}
