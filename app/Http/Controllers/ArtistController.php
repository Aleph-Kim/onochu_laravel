<?php

namespace App\Http\Controllers;

use App\Services\FloApiService;
use Illuminate\Http\Request;

class ArtistController extends Controller
{
    public function __construct(private FloApiService $floApi) {}

    public function detail(Request $request)
    {
        $artistId = (int) $request->input('id');

        if (empty($artistId)) {
            abort(400);
        }

        $artistInfo = $this->floApi->getArtistByFloId($artistId);
        $albumsInfo = $this->floApi->getAlbumsByArtistFloId($artistId);

        return view('artist.detail', compact('artistInfo', 'albumsInfo'));
    }
}
