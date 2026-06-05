<?php

namespace Tests\Unit;

use App\Support\Youtube;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\TestCase;

class YoutubeTest extends TestCase
{
    public function test_extract_id_from_watch_url(): void
    {
        $this->assertSame(
            'dQw4w9WgXcQ',
            Youtube::extractId('https://www.youtube.com/watch?v=dQw4w9WgXcQ')
        );
    }

    public function test_extract_id_from_short_url(): void
    {
        $this->assertSame(
            'dQw4w9WgXcQ',
            Youtube::extractId('https://youtu.be/dQw4w9WgXcQ')
        );
    }

    public function test_extract_id_from_embed_url(): void
    {
        $this->assertSame(
            'dQw4w9WgXcQ',
            Youtube::extractId('https://www.youtube.com/embed/dQw4w9WgXcQ')
        );
    }

    public function test_extract_id_from_shorts_url(): void
    {
        $this->assertSame(
            'dQw4w9WgXcQ',
            Youtube::extractId('https://www.youtube.com/shorts/dQw4w9WgXcQ')
        );
    }

    public function test_builds_thumbnail_and_embed_urls(): void
    {
        $url = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';

        $this->assertSame(
            'https://img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg',
            Youtube::thumbnailUrl($url)
        );
        $this->assertSame(
            'https://www.youtube.com/embed/dQw4w9WgXcQ',
            Youtube::embedUrl($url)
        );
        $this->assertSame(
            'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            Youtube::watchUrl($url)
        );
    }

    public function test_returns_null_for_invalid_url(): void
    {
        $this->assertNull(Youtube::extractId('https://example.com/video'));
        $this->assertNull(Youtube::thumbnailUrl('not-a-url'));
    }

    public function test_fetch_title_from_oembed(): void
    {
        Cache::flush();

        Http::fake([
            'www.youtube.com/oembed*' => Http::response([
                'title' => 'Sample YouTube Video',
            ], 200),
        ]);

        $this->assertSame(
            'Sample YouTube Video',
            Youtube::fetchTitle('https://www.youtube.com/watch?v=dQw4w9WgXcQ')
        );
    }
}
