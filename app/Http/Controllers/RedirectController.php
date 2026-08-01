<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppleMusicUrlRequest;
use App\Http\Requests\RedirectFloRequest;
use App\Http\Requests\RedirectYoutubeRequest;
use App\Services\PlatformService;

class RedirectController extends Controller
{
    public function __construct(private PlatformService $platform)
    {
    }

    public function flo(RedirectFloRequest $request)
    {
        return redirect()->away($this->platform->getFloUrl($request->validated('id')));
    }

    public function youtube(RedirectYoutubeRequest $request)
    {
        return redirect()->away($this->platform->getYoutubeUrl($request->validated('q')));
    }

    public function appleMusicUrl(AppleMusicUrlRequest $request)
    {
        return response()->json($this->platform->resolveAppleMusicUrl($request->validated('q')));
    }
}
