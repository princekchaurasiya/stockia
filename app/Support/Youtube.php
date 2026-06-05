<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Youtube
{
    public static function extractId(?string $url): ?string
    {
        if ($url === null || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        $patterns = [
            '/(?:youtube\.com\/(?:watch\?(?:.*&)?v=|embed\/|v\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/',
            '/^([A-Za-z0-9_-]{11})$/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    public static function thumbnailUrl(?string $url, string $quality = 'hqdefault'): ?string
    {
        $id = self::extractId($url);

        if ($id === null) {
            return null;
        }

        $allowed = ['default', 'mqdefault', 'hqdefault', 'sddefault', 'maxresdefault'];
        $quality = in_array($quality, $allowed, true) ? $quality : 'hqdefault';

        return "https://img.youtube.com/vi/{$id}/{$quality}.jpg";
    }

    public static function embedUrl(?string $url): ?string
    {
        $id = self::extractId($url);

        return $id ? "https://www.youtube.com/embed/{$id}" : null;
    }

    public static function watchUrl(?string $url): ?string
    {
        $id = self::extractId($url);

        return $id ? "https://www.youtube.com/watch?v={$id}" : null;
    }

    public static function fetchTitle(?string $url): ?string
    {
        $id = self::extractId($url);

        if ($id === null) {
            return null;
        }

        return Cache::remember("youtube_title:{$id}", now()->addWeek(), function () use ($url, $id) {
            try {
                $response = Http::timeout(5)->get('https://www.youtube.com/oembed', [
                    'url' => self::watchUrl($url) ?? "https://www.youtube.com/watch?v={$id}",
                    'format' => 'json',
                ]);

                if ($response->successful()) {
                    $title = $response->json('title');

                    return is_string($title) && $title !== '' ? $title : null;
                }
            } catch (\Throwable) {
                // YouTube oEmbed unavailable — fall back to no title.
            }

            return null;
        });
    }
}
