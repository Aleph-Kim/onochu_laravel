<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PlatformService
{
    private const APPLE_MUSIC_SEARCH_API = 'https://itunes.apple.com/search';
    private const APPLE_MUSIC_CACHE_TTL = 604800; // 7일

    public function getPlatformUrl(array $song, array $artists): array
    {
        $isMobile = $this->isMobile();
        $isAndroid = $this->isAndroid();
        $artistsName = implode(' ', array_column($artists, 'name'));
        $keyword = "{$artistsName} {$song['title']}";
        $floId = $song['flo_id'];

        if ($isMobile) {
            $youtube = $isAndroid
                ? "vnd.youtube.music:/search?q={$keyword}"
                : "youtubemusic:/search?q={$keyword}";
            $flo     = "flomusic://view/content?type=TRACK&id={$floId}";
            $spotify = "spotify:search:{$keyword}";
        } else {
            $youtube = "https://music.youtube.com/search?q={$keyword}";
            $flo     = "https://www.music-flo.com/detail/track/{$floId}/details";
            $spotify = "https://open.spotify.com/search/{$keyword}/tracks";
        }
        $appleMusic = $this->resolveAppleMusicUrl($keyword);

        return [
            'youtube'         => $youtube,
            'flo'             => $flo,
            'spotify'         => $spotify,
            'apple_music'     => $appleMusic['web'],
            'apple_music_app' => $appleMusic['app'],
        ];
    }

    /**
     * iTunes Search API로 실제 트랙/앨범 ID를 찾아 music:// 딥링크를 만든다.
     * Apple Music은 search 경로용 앱 스킴을 지원하지 않아, 검색 결과로 얻은
     * album/song ID 기반 경로(공식 지원)로만 앱이 열린다.
     */
    private function resolveAppleMusicUrl(string $keyword): array
    {
        $webFallback = "https://music.apple.com/kr/search?term=" . urlencode($keyword);
        $cacheKey = 'apple-music:track:' . md5($keyword);

        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $result = ['web' => $webFallback, 'app' => null];

        try {
            $response = Http::timeout(5)->get(self::APPLE_MUSIC_SEARCH_API, [
                'term'    => $keyword,
                'media'   => 'music',
                'entity'  => 'song',
                'country' => 'KR',
                'limit'   => 1,
            ]);

            $track = $response->json('results.0');
            if ($response->successful() && isset($track['trackViewUrl'])) {
                $result = [
                    'web' => $track['trackViewUrl'],
                    'app' => preg_replace('#^https://#', 'music://', $track['trackViewUrl']),
                ];
            }
        } catch (\Throwable $e) {
            // iTunes API 실패 시 검색 링크로 폴백
        }

        Cache::put($cacheKey, $result, self::APPLE_MUSIC_CACHE_TTL);

        return $result;
    }

    public function getFloUrl(string $floId): string
    {
        return $this->isMobile()
            ? "flomusic://view/content?type=TRACK&id={$floId}"
            : "https://www.music-flo.com/detail/track/{$floId}/details";
    }

    public function getYoutubeUrl(string $keyword): string
    {
        if (! $this->isMobile()) {
            return "https://music.youtube.com/search?q={$keyword}";
        }

        return $this->isAndroid()
            ? "vnd.youtube.music:/search?q={$keyword}"
            : "youtubemusic:/search?q={$keyword}";
    }

    public function isMobile(): bool
    {
        $userAgent = request()->userAgent() ?? '';
        return (bool) preg_match('/android|samsung|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i', $userAgent);
    }

    public function isAndroid(): bool
    {
        $userAgent = request()->userAgent() ?? '';
        return (bool) preg_match('/android|samsung/i', $userAgent);
    }
}
