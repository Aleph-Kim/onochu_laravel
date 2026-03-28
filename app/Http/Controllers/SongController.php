<?php

namespace App\Http\Controllers;

use App\Services\FloApiService;
use Illuminate\Http\Request;

class SongController extends Controller
{
    public function __construct(private FloApiService $floApi) {}

    public function detail(Request $request)
    {
        $songId = (int) $request->input('id');

        if (empty($songId)) {
            abort(400);
        }

        $songInfo = $this->floApi->getSongByFloId($songId);

        return view('song.detail', compact('songInfo'));
    }
}
