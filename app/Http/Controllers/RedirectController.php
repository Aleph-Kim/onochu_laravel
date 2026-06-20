<?php

namespace App\Http\Controllers;

use App\Services\PlatformService;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function __construct(private PlatformService $platform)
    {
    }

    public function flo(Request $request)
    {
        $floId = $request->input('id');

        if (empty($floId)) {
            abort(400);
        }

        return redirect()->away($this->platform->getFloUrl($floId));
    }

    public function youtube(Request $request)
    {
        $keyword = $request->input('q');

        if (empty($keyword)) {
            abort(400);
        }

        return redirect()->away($this->platform->getYoutubeUrl($keyword));
    }
}
