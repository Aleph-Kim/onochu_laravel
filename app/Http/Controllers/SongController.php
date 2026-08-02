<?php

namespace App\Http\Controllers;

use App\Http\Requests\SongDetailRequest;
use App\Services\FloApiService;

class SongController extends Controller
{
    public function __construct(private FloApiService $floApi)
    {
    }

    public function detail(SongDetailRequest $request)
    {
        $songInfo = $this->floApi->getSongByFloId($request->validated('id'));

        return view('song.detail', compact('songInfo'));
    }
}
