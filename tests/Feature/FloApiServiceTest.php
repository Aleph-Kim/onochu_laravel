<?php

namespace Tests\Feature;

use App\Services\FloApiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class FloApiServiceTest extends TestCase
{
    #[TestDox('FLO API 요청이 실패하면 경로와 상태 코드를 에러 로그로 남긴다')]
    public function test_failed_request_is_logged_with_path_and_status(): void
    {
        Http::fake(['www.music-flo.com/*' => Http::response([], 503)]);
        Log::spy();

        try {
            app(FloApiService::class)->getSongByFloId(123);
            $this->fail('FLO API 실패 시 예외가 발생해야 함');
        } catch (HttpException) {
        }

        Log::shouldHaveReceived('error')->once()->with('FLO API 요청 실패', [
            'path' => '/api/meta/v1/track/123',
            'status' => 503,
        ]);
    }
}
