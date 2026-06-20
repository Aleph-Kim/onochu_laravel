<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class FloApiService
{
    private const FLO_API_BASE = 'https://www.music-flo.com';
    private const SEARCH_PATH = '/api/search/v2/search';
    private const TRACK_PATH = '/api/meta/v1/track/';
    private const ARTIST_PATH = '/api/meta/v1/artist/';
    private const ALBUM_TRACK_PATH_SUFFIX = '/track';
    private const NEW_KPOP_ALBUM_PATH = '/api/meta/v1/album/KPOP/new';
    private const NEW_POP_ALBUM_PATH = '/api/meta/v1/album/POP/new';
    private const CACHE_TTL = 3600; // 1시간
    private const REQUEST_HEADERS = [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 '
            . '(KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
        'Accept'     => 'application/json, text/plain, */*',
        'Referer'    => self::FLO_API_BASE . '/',
    ];

    public function getSongsByKeyword(string $keyword): array
    {
        $params = [
            'keyword'    => $keyword,
            'searchType' => 'TRACK',
            'sortType'   => 'ACCURACY',
            'size'       => 30,
            'page'       => 1,
            'queryType'  => 'system',
        ];

        return $this->fetchAndExtract(self::SEARCH_PATH, $params, 'getSongs');
    }

    public function getSongByFloId(int $songId): array
    {
        return $this->fetchAndExtract(self::TRACK_PATH . $songId, null, 'getSong');
    }

    public function getArtistByFloId(int $artistId): array
    {
        return $this->fetchAndExtract(self::ARTIST_PATH . $artistId, null, 'getArtist');
    }

    public function getAlbumsByArtistFloId(int $artistId): array
    {
        $params = [
            'page'     => '1',
            'size'     => '100',
            'sortType' => 'RECENT',
            'roleType' => 'ALL',
        ];

        return $this->fetchAndExtract('/api/meta/v1/artist/' . $artistId . '/album', $params, 'getAlbums', $artistId);
    }

    public function getAlbumByFloId(int $albumId): array
    {
        return $this->fetchAndExtract('/api/meta/v1/album/' . $albumId . '/track', null, 'getAlbum');
    }

    public function getNewKpopAlbum(): array
    {
        $params = ['page' => '1', 'size' => '100'];
        return $this->fetchAndExtract(self::NEW_KPOP_ALBUM_PATH, $params, 'getNewAlbums');
    }

    public function getNewPopAlbum(): array
    {
        $params = ['page' => '1', 'size' => '100'];
        return $this->fetchAndExtract(self::NEW_POP_ALBUM_PATH, $params, 'getNewAlbums');
    }

    protected function fetchAndExtract(string $path, ?array $params, string $getMethod, mixed $otherParams = null): array
    {
        $fullPath = $path . ($params ? '?' . http_build_query($params) : '');
        $data = $this->fetchData($fullPath);
        return $this->$getMethod($data, $otherParams);
    }

    protected function fetchData(string $path): array
    {
        $cacheKey = 'flo:api-result:' . md5($path);
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $response = Http::withHeaders(self::REQUEST_HEADERS)
            ->timeout(10)
            ->get(self::FLO_API_BASE . $path);

        if ($response->failed()) {
            abort(500, 'FLO API 요청 실패');
        }

        $data = $response->json();
        Cache::put($cacheKey, $data, self::CACHE_TTL);

        return $data;
    }

    protected function getSong(array $data): array
    {
        if (!isset($data['data'])) {
            abort(400);
        }
        return $this->extractTrack($data['data']);
    }

    protected function getSongs(array $data): array
    {
        $songs = [];
        if (!isset($data['data']) || !is_array($data['data']['list'] ?? [])) {
            return $songs;
        }

        $track = $data['data']['list'][0] ?? null;
        if (!isset($track['list'])) {
            return $songs;
        }

        foreach ($track['list'] as $songData) {
            $songs[] = $this->extractTrack($songData);
        }

        return $songs;
    }

    protected function getAlbum(array $data): array
    {
        if (!isset($data['data'])) {
            abort(400);
        }

        $result = ['album_info' => [], 'songs_info' => []];

        if (!isset($data['data']['list'])) {
            return $result;
        }

        $albumData = $data['data']['list'][0]['album'];
        $result['album_info'] = $this->extractAlbum($albumData);

        foreach ($data['data']['list'] as $songData) {
            $result['songs_info'][] = $this->extractTrack($songData);
        }

        return $result;
    }

    protected function getAlbums(array $data, mixed $artistId): array
    {
        $albumsInfo = [];
        if (!isset($data['data']['list'])) {
            return $albumsInfo;
        }

        foreach ($data['data']['list'] as $album) {
            $albumInfo = $this->extractAlbum($album);
            $albumInfo['artists'] = $this->extractArtists($album['artistList']);

            if (count($albumInfo['artists']) > 1 || $artistId != $albumInfo['artists'][0]['flo_id']) {
                $albumInfo['type'] = '참여';
            }

            $albumsInfo[] = $albumInfo;
        }

        return $albumsInfo;
    }

    protected function getNewAlbums(array $data): array
    {
        $albumsInfo = [];
        $artistsFloId = [];

        if (!isset($data['data']['list'])) {
            return ['albums_info' => $albumsInfo, 'artists_flo_id' => $artistsFloId];
        }

        foreach ($data['data']['list'] as $album) {
            $albumsInfo[$album['id']] = $this->extractAlbum($album);
            $albumsInfo[$album['id']]['artist'] = $this->extractArtists($album['artistList']);

            foreach ($albumsInfo[$album['id']]['artist'] as $artist) {
                $artistsFloId[$artist['flo_id']] = $album['id'];
            }
        }

        return ['albums_info' => $albumsInfo, 'artists_flo_id' => $artistsFloId];
    }

    protected function getArtist(array $data): array
    {
        if (!isset($data['data'])) {
            abort(400);
        }
        return $this->extractArtist($data['data']);
    }

    protected function extractTrack(array $songData): array
    {
        $songInfo = [];
        $songInfo['song'] = $this->extractSong($songData);
        $songInfo['artists'] = $this->extractArtists($songData['artistList'] ?? []);
        $songInfo['album'] = $this->extractAlbum($songData['album'] ?? []);
        $songInfo['song']['url'] = app(PlatformService::class)->getPlatformUrl($songInfo['song'], $songInfo['artists']);

        return $songInfo;
    }

    protected function extractSong(array $songData): array
    {
        $writerRoles = $this->writerClassify($songData['trackArtistList'] ?? []);

        return [
            'flo_id'    => $songData['id'],
            'title'     => $songData['name'],
            'play_time' => $songData['playTime'],
            'genre'     => $songData['album']['genreStyle'] ?? null,
            'title_yn'  => $songData['titleYn'] ?? null,
            'lyrics'    => $songData['lyrics'] ?? null,
            'composer'  => implode(', ', $writerRoles['composers']),
            'lyricist'  => implode(', ', $writerRoles['lyricists']),
            'arranger'  => implode(', ', $writerRoles['arrangers']),
        ];
    }

    protected function extractAlbum(array $album): array
    {
        $releaseDate = null;
        if (!empty($album['releaseYmd'])) {
            $date = \DateTime::createFromFormat('Ymd', $album['releaseYmd']);
            $releaseDate = $date ? $date->format('Y.m.d') : null;
        }

        return [
            'flo_id'       => $album['id'] ?? null,
            'title'        => $album['title'] ?? null,
            'type'         => $album['albumTypeStr'] ?? null,
            'genre'        => $album['genreStyle'] ?? null,
            'img_url'      => strtok($album['imgList'][0]['url'] ?? '', '?'),
            'release_date' => $releaseDate,
        ];
    }

    protected function extractArtists(array $artistList): array
    {
        $artists = [];
        foreach ($artistList as $artist) {
            $artists[] = $this->extractArtist($artist);
        }
        return $artists;
    }

    protected function extractArtist(array $artist): array
    {
        if (empty($artist)) {
            return [];
        }

        $cacheKey = 'flo:artist-img:' . ($artist['id'] ?? '');
        $imgUrl = $artist['imgList'][0]['url'] ?? null;

        if ($imgUrl) {
            Cache::put($cacheKey, $imgUrl, 86400);
        } else {
            $imgUrl = Cache::get($cacheKey);
        }

        return [
            'flo_id'     => $artist['id'],
            'name'       => $artist['name'],
            'genre'      => $artist['artistStyle'] ?? null,
            'group_type' => $artist['artistGroupTypeStr'] ?? null,
            'img_url'    => strtok($imgUrl ?? '', '?'),
        ];
    }

    protected function writerClassify(array $array): array
    {
        $result = ['composers' => [], 'lyricists' => [], 'arrangers' => []];

        foreach ($array as $item) {
            match ($item['roleName'] ?? '') {
                '작곡' => $result['composers'][] = $item['name'],
                '작사' => $result['lyricists'][] = $item['name'],
                '편곡' => $result['arrangers'][] = $item['name'],
                default => null,
            };
        }

        return $result;
    }
}
