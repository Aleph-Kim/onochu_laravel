<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ImageService
{
    private string $serverUrl = 'https://img.aleph.kr/files';

    public function uploadImage(string $imageUrl, string $imageType = 'album'): ?string
    {
        $maxFileSize = 10 * 1024 * 1024;

        $imageContent = @file_get_contents($imageUrl);
        if ($imageContent === false) {
            logger()->error("이미지 다운로드 실패: {$imageUrl}");
            return null;
        }

        if (strlen($imageContent) > $maxFileSize) {
            logger()->error('이미지 크기가 10MB를 초과했습니다: ' . strlen($imageContent) . ' bytes');
            return null;
        }

        $imgId = uniqid();
        $fileName = "{$imageType}-{$imgId}.jpg";

        $response = Http::withHeaders([
            'X-Username' => config('services.img_server.username'),
            'X-Secret'   => config('services.img_server.secret'),
        ])->attach('file', $imageContent, $fileName)->post($this->serverUrl);

        if ($response->successful()) {
            return $this->serverUrl . '/' . config('services.img_server.username') . "/{$fileName}";
        }

        logger()->error("이미지 업로드 실패: HTTP {$response->status()}");
        return null;
    }
}
