<?php

namespace App\Services;

use App\Models\ResearchUpload;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResearchUploadService
{
    public function store(UploadedFile $file, User $user, array $data): ResearchUpload
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $fileName = (string) Str::uuid().'.'.$extension;
        $storedPath = null;

        DB::beginTransaction();

        try {
            $storedPath = $file->storeAs('research-uploads', $fileName, 'public');

            if (! $storedPath || ! Storage::disk('public')->exists($storedPath)) {
                throw new \RuntimeException('Failed to store research file.');
            }

            $upload = ResearchUpload::create([
                'user_id' => $user->id,
                'category' => $data['category'],
                'title' => $data['title'],
                'report_date' => $data['report_date'] ?? null,
                'file_path' => $storedPath,
                'file_type' => $extension,
                'original_name' => $file->getClientOriginalName(),
                'status' => 'pending',
            ]);

            DB::commit();

            return $upload;
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($storedPath && Storage::disk('public')->exists($storedPath)) {
                Storage::disk('public')->delete($storedPath);
            }

            Log::error('Research upload failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);

            throw $e;
        }
    }

    public function approve(ResearchUpload $upload, User $reviewer): void
    {
        $upload->update([
            'status' => 'approved',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);
    }

    public function reject(ResearchUpload $upload, User $reviewer, ?string $reason = null): void
    {
        $upload->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function delete(ResearchUpload $upload): void
    {
        DB::beginTransaction();

        try {
            $path = $upload->file_path;
            $upload->delete();
            DB::commit();

            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Research delete failed', ['id' => $upload->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }
}
