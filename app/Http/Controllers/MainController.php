<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\NewAlbum;
use App\Models\Recommend;

class MainController extends Controller
{
    public function index()
    {
        abort(500);
        $recommends = Recommend::latestPerSong(10);

        $userId = session('user.id');
        $artists = $userId ? Artist::byUser($userId) : Artist::popularByRecommends(20);

        $newAlbums = $this->getNewAlbumsForUser($artists);

        return view('main.index', compact('recommends', 'artists', 'newAlbums'));
    }

    private function getNewAlbumsForUser(array $artists): array
    {
        $newAlbumData = NewAlbum::with('artists')
            ->where('created_at', '>=', now()->subDays(7))
            ->orderByDesc('created_at')
            ->get();

        $newAlbums = [];
        $artistFloIds = [];
        foreach ($newAlbumData as $album) {
            $albumFloId = $album->flo_id;
            $newAlbums[$albumFloId] = [
                'id'      => $album->id,
                'title'   => $album->album_title,
                'img_url' => $album->album_img_url,
                'flo_id'  => $albumFloId,
                'artists' => [],
            ];
            foreach ($album->artists as $artist) {
                $newAlbums[$albumFloId]['artists'][] = [
                    'name'   => $artist->artist_name,
                    'flo_id' => $artist->flo_id,
                ];
                $artistFloIds[$artist->flo_id] = $albumFloId;
            }
        }

        $userArtistIds = array_column($artists, 'flo_id');
        $matchingIds = array_intersect(array_keys($artistFloIds), $userArtistIds);

        $result = [];
        foreach ($matchingIds as $artistFloId) {
            $albumFloId = $artistFloIds[$artistFloId];
            if (!isset($result[$albumFloId])) {
                $result[$albumFloId] = $newAlbums[$albumFloId];
            }
        }

        return array_values($result);
    }
}
