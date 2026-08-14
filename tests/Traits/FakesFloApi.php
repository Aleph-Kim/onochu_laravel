<?php

namespace Tests\Traits;

trait FakesFloApi
{
    protected function fakeArtistInfo(array $overrides = []): array
    {
        return array_merge([
            'flo_id'     => fake()->unique()->numberBetween(1, 999999),
            'name'       => fake()->name(),
            'genre'      => 'K-POP',
            'group_type' => '솔로',
            'img_url'    => fake()->imageUrl(),
        ], $overrides);
    }

    protected function fakeAlbumInfo(array $overrides = []): array
    {
        return array_merge([
            'flo_id'       => fake()->unique()->numberBetween(1, 999999),
            'title'        => fake()->words(3, true),
            'type'         => '정규',
            'genre'        => 'K-POP',
            'img_url'      => fake()->imageUrl(),
            'release_date' => '2024.01.01',
        ], $overrides);
    }

    protected function fakeSongInfo(array $overrides = []): array
    {
        return array_merge([
            'flo_id'    => fake()->unique()->numberBetween(1, 999999),
            'title'     => fake()->sentence(3),
            'play_time' => '03:30',
            'genre'     => 'K-POP',
            'title_yn'  => 'Y',
            'lyrics'    => fake()->paragraph(),
            'composer'  => fake()->name(),
            'lyricist'  => fake()->name(),
            'arranger'  => fake()->name(),
            'url'       => [
                'youtube'             => ['app' => null, 'web' => 'https://music.youtube.com/search?q=test'],
                'flo'                 => ['app' => null, 'web' => 'https://www.music-flo.com/detail/track/1/details'],
                'spotify'             => ['app' => null, 'web' => 'https://open.spotify.com/search/test/tracks'],
                'apple_music_keyword' => 'test',
                'melon_keyword'       => 'test',
                'genie_keyword'       => 'test',
            ],
        ], $overrides);
    }

    protected function fakeTrackInfo(array $song = [], array $artists = [], array $album = []): array
    {
        return [
            'song'    => $this->fakeSongInfo($song),
            'artists' => [$this->fakeArtistInfo($artists)],
            'album'   => $this->fakeAlbumInfo($album),
        ];
    }
}
