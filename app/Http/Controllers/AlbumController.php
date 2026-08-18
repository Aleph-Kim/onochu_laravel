<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlbumDetailRequest;
use App\Models\Artist;
use App\Models\User;
use App\Services\FloApiService;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Cache;

class AlbumController extends Controller
{
    public function __construct(private FloApiService $floApi)
    {
    }

    public function detail(AlbumDetailRequest $request)
    {
        $albumId = $request->validated('id');
        $isNewAlbum = $request->validated('new_album');

        $album = $this->floApi->getAlbumByFloId($albumId);

        // 신규 앨범은 FLO API에 아티스트 이미지가 아직 없을 수 있어 DB에 저장된 이미지로 캐시 미리 채움
        if ($isNewAlbum && isset($album['songs_info'][0]['artists'][0]['flo_id'])) {
            $artistFloId = $album['songs_info'][0]['artists'][0]['flo_id'];
            $artist = Artist::where('flo_id', $artistFloId)->first();
            if ($artist) {
                Cache::put('flo:artist-img:' . $artistFloId, $artist->flo_img_url, 86400);
            }
        }

        // 신곡 알림을 통해 유입된 경우 해당 알림을 읽음 처리
        if ($isNewAlbum && session('user')) {
            DatabaseNotification::where('notifiable_type', User::class)
                ->where('notifiable_id', session('user.id'))
                ->where('data->new_album_flo_id', $albumId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return view('album.detail', [
            'albumInfo' => $album['album_info'],
            'songsInfo' => $album['songs_info'],
        ]);
    }
}
