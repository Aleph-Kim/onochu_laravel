<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PlatformService
{
    private const APPLE_MUSIC_SEARCH_API = 'https://itunes.apple.com/search';
    private const APPLE_MUSIC_LOOKUP_API = 'https://itunes.apple.com/lookup';
    private const MELON_SEARCH_URL = 'https://www.melon.com/search/song/index.htm';
    private const GENIE_SEARCH_URL = 'https://www.genie.co.kr/search/searchMain';
    private const MUSIC_CACHE_TTL = 604800; // 7일

    public function getPlatformUrl(array $song, array $artists): array
    {
        $isMobile = $this->isMobile();
        $isAndroid = $this->isAndroid();
        $artistsName = implode(' ', array_column($artists, 'name'));
        $keyword = "{$artistsName} {$song['title']}";
        $query = urlencode($keyword);
        $pathKeyword = rawurlencode($keyword);
        $floId = $song['flo_id'];

        return [
            'youtube' => [
                'app' => !$isMobile ? null :
                    ($isAndroid ? "vnd.youtube.music:/search?q={$query}" : "youtubemusic:/search?q={$query}"),
                'web' => "https://music.youtube.com/search?q={$query}",
            ],
            'flo' => [
                'app' => $isMobile ? "flomusic://view/content?type=TRACK&id={$floId}" : null,
                'web' => "https://www.music-flo.com/detail/track/{$floId}/details",
            ],
            'spotify' => [
                'app' => $isMobile ? "spotify:search:{$pathKeyword}" : null,
                'web' => "https://open.spotify.com/search/{$pathKeyword}/tracks",
            ],
            'apple_music_keyword' => $keyword,
            'melon_keyword' => $keyword,
            'genie_keyword' => $keyword,
        ];
    }

    /**
     * iTunes Search·Lookup API로 실제 트랙/앨범 ID를 찾아 music:// 딥링크를 만든다.
     * Apple Music은 search 경로용 앱 스킴을 공식 지원하지 않아, 곡을 찾은 경우에만 album/song ID 기반 경로(공식 지원)로 앱이 열린다.
     * 곡을 못 찾으면 앱 검색 탭 진입 (검색어 전달은 앱에서 미지원), 앱이 열리지 않으면 웹 검색 페이지로 폴백
     */
    public function resolveAppleMusicUrl(string $keyword): array
    {
        $searchTerm = urlencode($keyword);
        $cacheKey = 'apple-music:track:' . md5($keyword);

        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $searchResult = [
            'web' => "https://music.apple.com/kr/search?term={$searchTerm}",
            'app' => 'music://search',
        ];

        try {
            // KR 스토어 검색 API의 결과 미반환(ID 조회는 정상)으로 인한 US 스토어 검색 후 KR 스토어 링크 조회
            $searchResponse = Http::timeout(5)->get(self::APPLE_MUSIC_SEARCH_API, [
                'term' => $keyword,
                'media' => 'music',
                'entity' => 'song',
                'country' => 'US',
                'limit' => 1,
            ]);

            $trackId = $searchResponse->json('results.0.trackId');
            if ($searchResponse->failed()) {
                logger()->warning('Apple Music 검색 실패', ['keyword' => $keyword, 'status' => $searchResponse->status()]);
            } elseif ($trackId) {
                $lookupResponse = Http::timeout(5)->get(self::APPLE_MUSIC_LOOKUP_API, [
                    'id' => $trackId,
                    'country' => 'KR',
                ]);

                $trackViewUrl = $lookupResponse->json('results.0.trackViewUrl');
                if ($lookupResponse->successful() && $trackViewUrl) {
                    $result = [
                        'web' => $trackViewUrl,
                        'app' => preg_replace('#^https://#', 'music://', $trackViewUrl),
                    ];

                    // 일시적 실패가 7일간 고정되지 않도록 곡을 찾은 결과만 캐시
                    Cache::put($cacheKey, $result, self::MUSIC_CACHE_TTL);

                    return $result;
                }

                if ($lookupResponse->failed()) {
                    logger()->warning('Apple Music 곡 조회 실패', ['keyword' => $keyword, 'track_id' => $trackId, 'status' => $lookupResponse->status()]);
                }
            }
        } catch (\Throwable $e) {
            // iTunes API 실패 시 검색 링크로 폴백
            logger()->warning('Apple Music 검색 실패', ['keyword' => $keyword, 'error' => $e->getMessage()]);
        }

        return $searchResult;
    }

    /**
     * 멜론 검색 페이지를 스크래핑해 실제 songId를 찾아 melonapp:// 딥링크를 만든다.
     * 멜론 앱 스킴은 검색이 아닌 songId 기반 재생만 지원하기 때문에 멜론 songId를 검색 결과에서 직접 파싱해야 한다.
     */
    public function resolveMelonUrl(string $keyword): array
    {
        $webFallback = "https://www.melon.com/search/total/index.htm?q=" . urlencode($keyword);
        $cacheKey = 'melon:track:' . md5($keyword);

        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $result = ['web' => $webFallback, 'app' => null];

        try {
            $response = Http::timeout(5)->get(self::MELON_SEARCH_URL, ['q' => $keyword]);

            if ($response->successful() && preg_match("/goSongDetail\('(\d+)'\)/", $response->body(), $matches)) {
                $songId = $matches[1];
                $result = [
                    'web' => "https://www.melon.com/song/detail.htm?songId={$songId}",
                    'app' => "melonapp://play?ctype=1&menuid=0&cid={$songId}",
                ];
            } elseif ($response->failed()) {
                logger()->warning('멜론 검색 실패', ['keyword' => $keyword, 'status' => $response->status()]);
            }
        } catch (\Throwable $e) {
            // 멜론 검색 실패 시 검색 링크로 폴백
            logger()->warning('멜론 검색 실패', ['keyword' => $keyword, 'error' => $e->getMessage()]);
        }

        Cache::put($cacheKey, $result, self::MUSIC_CACHE_TTL);

        return $result;
    }

    /**
     * 지니뮤직 검색 페이지를 스크래핑해 실제 songId를 찾아 상세페이지 URL을 만든다.
     * 지니 앱의 URI 스킴은 공식적으로 문서화되어 있지 않고 검증할 방법이 없어, app은 항상 null로 두고 웹으로만 연결한다.
     */
    public function resolveGenieUrl(string $keyword): array
    {
        $webFallback = "https://www.genie.co.kr/search/searchMain?query=" . urlencode($keyword);
        $cacheKey = 'genie:track:' . md5($keyword);

        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $result = ['web' => $webFallback, 'app' => null];

        try {
            $response = Http::timeout(5)->get(self::GENIE_SEARCH_URL, ['query' => $keyword]);

            if ($response->successful() && preg_match("/fnViewSongInfo\('(\d+)'\)/", $response->body(), $matches)) {
                $songId = $matches[1];
                $result = [
                    'web' => "https://www.genie.co.kr/detail/songInfo?xgnm={$songId}",
                    'app' => null,
                ];
            } elseif ($response->failed()) {
                logger()->warning('지니 검색 실패', ['keyword' => $keyword, 'status' => $response->status()]);
            }
        } catch (\Throwable $e) {
            // 지니 검색 실패 시 검색 링크로 폴백
            logger()->warning('지니 검색 실패', ['keyword' => $keyword, 'error' => $e->getMessage()]);
        }

        Cache::put($cacheKey, $result, self::MUSIC_CACHE_TTL);

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
        $keyword = urlencode($keyword);

        if (!$this->isMobile()) {
            return "https://music.youtube.com/search?q={$keyword}";
        }

        return $this->isAndroid()
            ? "vnd.youtube.music:/search?q={$keyword}"
            : "youtubemusic:/search?q={$keyword}";
    }

    public function isMobile(): bool
    {
        $userAgent = request()->userAgent() ?? '';
        return (bool)preg_match('/android|samsung|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i', $userAgent);
    }

    public function isAndroid(): bool
    {
        $userAgent = request()->userAgent() ?? '';
        return (bool)preg_match('/android|samsung/i', $userAgent);
    }
}
