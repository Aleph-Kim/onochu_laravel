<?php

namespace App\Services;

class PlatformService
{
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

        return [
            'youtube' => $youtube,
            'flo'     => $flo,
            'spotify' => $spotify,
        ];
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
