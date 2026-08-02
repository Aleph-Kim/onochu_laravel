<?php

namespace App\Http\Controllers;

use App\Http\Requests\SongDetailRequest;
use App\Models\User;
use App\Services\FloApiService;

class SongController extends Controller
{
    public function __construct(private FloApiService $floApi)
    {
    }

    public function detail(SongDetailRequest $request)
    {
        $songInfo = $this->floApi->getSongByFloId($request->validated('id'));

        $preferredMusicApp = session('user.id') ? User::find(session('user.id'))?->preferred_music_app : null;

        return view('song.detail', compact('songInfo', 'preferredMusicApp'));
    }
}
