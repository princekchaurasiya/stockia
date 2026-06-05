<?php

namespace App\Services;

use App\Models\ChartAsset;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChartAssetService
{
    public function store(UploadedFile $file, User $user, array $data): ChartAsset
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $fileName = (string) Str::uuid().'.'.$extension;
        $storedPath = null;

        DB::beginTransaction();

        try {
            $storedPath = $file->storeAs('chart-assets', $fileName, 'public');

            if (! $storedPath || ! Storage::disk('public')->exists($storedPath)) {
                throw new \RuntimeException('Failed to store chart asset.');
            }

            $asset = ChartAsset::create([
                'title' => $data['title'],
                'category' => $data['category'] ?? null,
                'file_path' => $storedPath,
                'file_type' => $extension,
                'report_date' => $data['report_date'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active' => $data['is_active'] ?? true,
                'uploaded_by' => $user->id,
            ]);

            DB::commit();

            return $asset;
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($storedPath && Storage::disk('public')->exists($storedPath)) {
                Storage::disk('public')->delete($storedPath);
            }

            Log::error('Chart asset upload failed', ['error' => $e->getMessage()]);

            throw $e;
        }
    }

    public function delete(ChartAsset $asset): void
    {
        DB::beginTransaction();

        try {
            $path = $asset->file_path;
            $asset->delete();
            DB::commit();

            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
