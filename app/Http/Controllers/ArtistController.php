<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArtistDetailRequest;
use App\Services\FloApiService;

class ArtistController extends Controller
{
    public function __construct(private FloApiService $floApi) {}

    public function detail(ArtistDetailRequest $request)
    {
        $artistId = $request->validated('id');

        $artistInfo = $this->floApi->getArtistByFloId($artistId);
        $albumsInfo = $this->floApi->getAlbumsByArtistFloId($artistId);

        return view('artist.detail', compact('artistInfo', 'albumsInfo'));
    }
}
