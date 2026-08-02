<?php

namespace App\Enums;

enum MusicApp: string
{
    case Youtube = 'youtube';
    case Flo = 'flo';
    case Spotify = 'spotify';
    case AppleMusic = 'apple_music';

    public function label(): string
    {
        return match ($this) {
            self::Youtube => 'YouTube Music',
            self::Flo => 'FLO',
            self::Spotify => 'Spotify',
            self::AppleMusic => 'Apple Music',
        };
    }

    public function logo(): string
    {
        return match ($this) {
            self::Youtube => 'image/music-apps/youtube-music.svg',
            self::Flo => 'image/music-apps/flo.svg',
            self::Spotify => 'image/music-apps/spotify.svg',
            self::AppleMusic => 'image/music-apps/apple-music.webp',
        };
    }
}
