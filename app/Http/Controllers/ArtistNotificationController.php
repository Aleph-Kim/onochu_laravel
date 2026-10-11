<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\ArtistNotificationMute;
use App\Models\User;

class ArtistNotificationController extends Controller
{
    public function index()
    {
        $user = User::findOrFail(session('user.id'));
        $artistList = $user->likeArtists(null);
        $mutedArtistIds = $user->mutedArtists()->pluck('artists.id')->all();

        return view('mypage.notifications', compact('artistList', 'mutedArtistIds'));
    }

    public function toggle(Artist $artist)
    {
        $userId = session('user.id');

        $mute = ArtistNotificationMute::where('user_id', $userId)->where('artist_id', $artist->id)->first();

        if ($mute) {
            $mute->delete();
            $muted = false;
        } else {
            ArtistNotificationMute::create(['user_id' => $userId, 'artist_id' => $artist->id]);
            $muted = true;
        }

        return $this->successResponse(
            $muted ? '이 아티스트의 알림을 껐습니다.' : '이 아티스트의 알림을 켰습니다.',
            ['muted' => $muted],
        );
    }
}
