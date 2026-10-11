<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\Artist;
use App\Services\FloApiService;
use App\Services\ImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class RetryMissingImagesTest extends TestCase
{
    use RefreshDatabase;

    #[TestDox('이미지가 비어 있는 아티스트와 앨범의 이미지를 다시 업로드한다')]
    public function test_retries_missing_artist_and_album_images(): void
    {
        $artist = Artist::factory()->create(['img_url' => null, 'flo_img_url' => 'https://flo.example/artist.jpg']);
        $album = Album::factory()->create(['img_url' => null, 'flo_id' => 2001]);

        $this->mock(FloApiService::class, function ($mock) {
            $mock->shouldReceive('getAlbumByFloId')->with(2001)
                ->andReturn(['album_info' => ['img_url' => 'https://flo.example/album.jpg'], 'songs_info' => []]);
        });
        $this->mock(ImageService::class, function ($mock) {
            $mock->shouldReceive('uploadImage')
                ->with('https://flo.example/artist.jpg?/dims/resize/1000x1000/quality/90', 'artist')
                ->andReturn('https://img.example/artist.jpg');
            $mock->shouldReceive('uploadImage')
                ->with('https://flo.example/album.jpg?/dims/resize/1000x1000/quality/90')
                ->andReturn('https://img.example/album.jpg');
        });

        $this->artisan('images:retry-missing')->assertSuccessful();

        $this->assertSame('https://img.example/artist.jpg', $artist->fresh()->img_url);
        $this->assertSame('https://img.example/album.jpg', $album->fresh()->img_url);
    }

    #[TestDox('업로드에 실패하면 이미지를 비워 둔 채 다음 재시도를 기다린다')]
    public function test_upload_failure_keeps_image_empty(): void
    {
        $artist = Artist::factory()->create(['img_url' => null, 'flo_img_url' => 'https://flo.example/artist.jpg']);

        $this->mock(FloApiService::class);
        $this->mock(ImageService::class)->shouldReceive('uploadImage')->andReturn(null);

        $this->artisan('images:retry-missing')->assertSuccessful();

        $this->assertNull($artist->fresh()->img_url);
    }
}
