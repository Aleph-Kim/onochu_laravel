<?php

namespace App\WebPush;

class NewAlbumPayload
{
    /**
     * @param array $albums 각 원소: ['title','flo_id','img_url','artist'=>[['name','flo_id'],...]]
     */
    public static function build(array $albums): array
    {
        $count = count($albums);

        $labels = array_map(
            fn($album) => "{$album['artist'][0]['name']} - {$album['title']}",
            $albums,
        );

        if ($count <= 3) {
            $body = implode("\n", $labels);
        } else {
            $body = implode("\n", array_slice($labels, 0, 2)) . "\n외 " . ($count - 2) . '개';
        }

        $title = $count === 1
            ? '추천 아티스트의 새 앨범'
            : "추천 아티스트의 새 앨범 {$count}개";

        $url = $count === 1
            ? route('album.detail', ['id' => $albums[0]['flo_id'], 'new_album' => 1])
            : route('main');

        return [
            'title' => $title,
            'body' => $body,
            'icon' => $albums[0]['img_url'],
            'data' => ['url' => $url],
        ];
    }
}
