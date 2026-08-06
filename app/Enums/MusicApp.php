<?php

namespace App\Enums;

enum MusicApp: string
{
    case AppleMusic = 'apple_music';
    case Flo = 'flo';
    case Melon = 'melon';
    case Spotify = 'spotify';
    case Youtube = 'youtube';

    public function label(): string
    {
        return match ($this) {
            self::AppleMusic => 'Apple Music',
            self::Flo => 'FLO',
            self::Melon => 'Melon',
            self::Spotify => 'Spotify',
            self::Youtube => 'YouTube Music',
        };
    }

    public function logo(): string
    {
        return match ($this) {
            self::AppleMusic => 'image/music-apps/apple-music.webp',
            self::Flo => 'image/music-apps/flo.svg',
            self::Melon => 'image/music-apps/melon.webp',
            self::Spotify => 'image/music-apps/spotify.svg',
            self::Youtube => 'image/music-apps/youtube-music.svg',
        };
    }
}
