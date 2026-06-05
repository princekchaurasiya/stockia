<?php

namespace App\Services;

class IndexClassifier
{
    private static ?array $normalizedWhitelist = null;

    public static function isBroadMarket(?string $name): bool
    {
        if (! $name || trim((string) $name) === '') {
            return false;
        }

        $name = self::normalize($name);
        $whitelist = self::getNormalizedWhitelist();

        return in_array($name, $whitelist, true);
    }

    private static function normalize(string $name): string
    {
        return strtolower(trim(preg_replace('/\s+/', ' ', $name)));
    }

    private static function getNormalizedWhitelist(): array
    {
        if (self::$normalizedWhitelist === null) {
            $allowed = collect(config('indices.broad_market', []))
                ->map(fn ($item) => is_array($item) ? ($item['name'] ?? '') : $item)
                ->filter()
                ->all();
            self::$normalizedWhitelist = array_map([self::class, 'normalize'], $allowed);
        }

        return self::$normalizedWhitelist;
    }
}
