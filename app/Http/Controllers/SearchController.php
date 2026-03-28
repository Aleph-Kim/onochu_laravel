<?php

namespace App\Http\Controllers;

use App\Services\FloApiService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(private FloApiService $floApi) {}

    public function index(Request $request)
    {
        $keyword = $request->input('q', '');

        if (empty($keyword)) {
            abort(400);
        }

        $songs = $this->floApi->getSongsByKeyword($keyword);

        return view('search.index', compact('songs', 'keyword'));
    }
}
