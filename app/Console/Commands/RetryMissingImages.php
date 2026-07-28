<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\DelaysApiRequests;
use App\Models\Album;
use App\Models\Artist;
use App\Services\FloApiService;
use App\Services\ImageService;
use Illuminate\Console\Command;

class RetryMissingImages extends Command
{
    use DelaysApiRequests;

    protected $signature   = 'images:retry-missing';
    protected $description = 'img_url이 비어있는 아티스트/앨범을 찾아 이미지 업로드를 재시도';

    public function __construct(
        private FloApiService $floApi,
        private ImageService $imageService,
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('RetryMissingImages 시작');

        $this->retryArtists();
        $this->retryAlbums();

        $this->info('RetryMissingImages 완료');
    }

    private function retryArtists(): void
    {
        $artists = Artist::whereNull('img_url')->orWhere('img_url', '')->get();
        $this->info("이미지 누락 아티스트 수: {$artists->count()}");

        $successCount = 0;
        foreach ($artists as $artist) {
            $sourceUrl = $artist->flo_img_url;

            if (empty($sourceUrl)) {
                if (empty($artist->flo_id)) {
                    continue;
                }

                $this->delay();
                $floArtist = $this->floApi->getArtistByFloId($artist->flo_id);
                $sourceUrl = $floArtist['img_url'] ?? null;

                if (empty($sourceUrl)) {
                    continue;
                }

                $artist->flo_img_url = $sourceUrl;
            }

            $imgUrl = $this->imageService->uploadImage(
                $sourceUrl . '?/dims/resize/1000x1000/quality/90', 'artist'
            );

            if ($imgUrl) {
                $artist->img_url = $imgUrl;
                $artist->save();
                $successCount++;
            }
        }

        $this->info("아티스트 이미지 재업로드 성공 수: {$successCount}");
    }

    private function retryAlbums(): void
    {
        $albums = Album::whereNull('img_url')->orWhere('img_url', '')->get();
        $this->info("이미지 누락 앨범 수: {$albums->count()}");

        $successCount = 0;
        foreach ($albums as $album) {
            if (empty($album->flo_id)) {
                continue;
            }

            $this->delay();
            $floAlbum = $this->floApi->getAlbumByFloId($album->flo_id);
            $sourceUrl = $floAlbum['album_info']['img_url'] ?? null;

            if (empty($sourceUrl)) {
                continue;
            }

            $imgUrl = $this->imageService->uploadImage(
                $sourceUrl . '?/dims/resize/1000x1000/quality/90'
            );

            if ($imgUrl) {
                $album->img_url = $imgUrl;
                $album->save();
                $successCount++;
            }
        }

        $this->info("앨범 이미지 재업로드 성공 수: {$successCount}");
    }
}
