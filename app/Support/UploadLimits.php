<?php

namespace App\Support;

class UploadLimits
{
    public const NOTE_IMAGE_MAX_KB = 5120;

    public static function parseIniSizeToKilobytes(?string $value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        $value = trim($value);

        if (is_numeric($value)) {
            return (int) max(1, ceil(((int) $value) / 1024));
        }

        $unit = strtolower(substr($value, -1));
        $number = (float) substr($value, 0, -1);

        return (int) match ($unit) {
            'g' => $number * 1024 * 1024,
            'm' => $number * 1024,
            'k' => $number,
            default => max(1, (int) ceil(((float) $value) / 1024)),
        };
    }

    public static function phpUploadMaxKilobytes(): int
    {
        return self::parseIniSizeToKilobytes(ini_get('upload_max_filesize'));
    }

    public static function phpPostMaxKilobytes(): int
    {
        return self::parseIniSizeToKilobytes(ini_get('post_max_size'));
    }

    public static function maxFileKilobytes(): int
    {
        $phpLimit = min(
            self::phpUploadMaxKilobytes() ?: self::NOTE_IMAGE_MAX_KB,
            self::phpPostMaxKilobytes() ?: self::NOTE_IMAGE_MAX_KB,
        );

        return max(1, min(self::NOTE_IMAGE_MAX_KB, $phpLimit));
    }

    public static function maxFileMegabytesLabel(): string
    {
        $kb = self::maxFileKilobytes();

        if ($kb >= 1024 && $kb % 1024 === 0) {
            return (string) ($kb / 1024).' MB';
        }

        if ($kb >= 1024) {
            return number_format($kb / 1024, 1).' MB';
        }

        return $kb.' KB';
    }

    public static function isPhpLimitBelowAppMax(): bool
    {
        return self::maxFileKilobytes() < self::NOTE_IMAGE_MAX_KB;
    }
}
