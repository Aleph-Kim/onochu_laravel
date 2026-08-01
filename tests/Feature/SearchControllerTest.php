<?php

namespace Tests\Feature;

use App\Services\FloApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Traits\FakesFloApi;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    use RefreshDatabase, FakesFloApi;

    #[TestDox('검색어가 없으면 400을 반환한다')]
    public function test_missing_keyword_returns_400(): void
    {
        $response = $this->get('/search');

        $response->assertStatus(400);
    }

    #[TestDox('검색어가 있으면 검색 결과를 반환한다')]
    public function test_valid_keyword_returns_search_results(): void
    {
        $this->mock(FloApiService::class, function ($mock) {
            $mock->shouldReceive('getSongsByKeyword')
                ->with('아이유')
                ->andReturn([$this->fakeTrackInfo()]);
        });

        $response = $this->get('/search?q=아이유');

        $response->assertOk();
        $response->assertViewIs('search.index');
        $response->assertViewHas('keyword', '아이유');
    }
}
