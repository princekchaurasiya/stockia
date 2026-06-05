<?php

namespace App\Services;

class IndexDataExtractor
{
    /**
     * Normalize keys to lowercase with underscores (handles INDEX, PREVIOUS CLOSE, GAIN/LOSS, etc.).
     */
    private function normalizeKeys(array $row): array
    {
        $out = [];
        foreach ($row as $k => $v) {
            $normalized = strtolower(str_replace([' ', '/'], ['_', '_'], (string) $k));
            $out[$normalized] = $v;
        }
        return $out;
    }

    /**
     * Sanitize numeric value from Excel (handles "25,807.20", " 33,166 ", etc.).
     */
    private function num(mixed $value): float
    {
        if ($value === null) {
            return 0.0;
        }
        $clean = str_replace(',', '', trim((string) $value));
        return is_numeric($clean) ? (float) $clean : 0.0;
    }

    /**
     * Normalize row data and compute return_pct.
     * Accepts both lowercase keys (index, previous_close, etc.) and display keys (INDEX, PREVIOUS CLOSE).
     *
     * @return array{name: string, prev_close: float, open: float, high: float, low: float, close: float, gain_loss: float, return_pct: float}|null
     */
    public function normalize(array $row): ?array
    {
        $row = $this->normalizeKeys($row);

        $index = $row['index'] ?? null;
        $prevClose = $row['previous_close'] ?? null;
        $closeRaw = $row['close'] ?? null;

        if ($index === null || $index === '' || $prevClose === null || $closeRaw === null || $closeRaw === '') {
            return null;
        }

        $prev = $this->num($prevClose);
        $closeVal = $this->num($closeRaw);

        if ($prev <= 0) {
            return null;
        }

        $return = log($closeVal / $prev) * 100;

        $open = $this->num($row['open'] ?? 0);
        $high = $this->num($row['high'] ?? 0);
        $low = $this->num($row['low'] ?? 0);
        $gainLoss = $this->num($row['gain_loss'] ?? 0);

        return [
            'name' => (string) $index,
            'prev_close' => $prev,
            'open' => $open,
            'high' => $high,
            'low' => $low,
            'close' => $closeVal,
            'gain_loss' => $gainLoss,
            'return_pct' => round($return, 4),
        ];
    }
}
