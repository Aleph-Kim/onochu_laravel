<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Services\FloApiService;

class SearchController extends Controller
{
    public function __construct(private FloApiService $floApi)
    {
    }

    public function index(SearchRequest $request)
    {
        $keyword = $request->validated('q');

        $songs = $this->floApi->getSongsByKeyword($keyword);

        return view('search.index', ['songs' => $songs, 'keyword' => $keyword]);
    }
}
