<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlbumDetailRequest;
use App\Models\Artist;
use App\Services\FloApiService;
use Illuminate\Support\Facades\Cache;

class AlbumController extends Controller
{
    public function __construct(private FloApiService $floApi) {}

    public function detail(AlbumDetailRequest $request)
    {
        $albumId = $request->validated('id');
        $isNewAlbum = $request->boolean('new_album');

        $album = $this->floApi->getAlbumByFloId($albumId);

        if ($isNewAlbum && isset($album['songs_info'][0]['artists'][0]['flo_id'])) {
            $artistFloId = $album['songs_info'][0]['artists'][0]['flo_id'];
            $artist = Artist::where('flo_id', $artistFloId)->first();
            if ($artist) {
                Cache::put('flo:artist-img:' . $artistFloId, $artist->flo_img_url, 86400);
            }
        }

        return view('album.detail', [
            'albumInfo' => $album['album_info'],
            'songsInfo' => $album['songs_info'],
        ]);
    }
}
