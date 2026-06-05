<?php

namespace App\Services;

use App\Imports\MarketActivityReportImport;
use App\Imports\StockSheetImport;
use App\Models\DataSourceLink;
use App\Models\SheetUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class SheetImportService
{
    public function import(
        UploadedFile $file,
        ?int $userId = null,
        ?int $dataSourceLinkId = null
    ): SheetUpload {
        $pathToRead = $file->getRealPath();
        if (! $pathToRead || ! is_readable($pathToRead)) {
            throw new \RuntimeException('Uploaded file is not readable.');
        }
        $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION) ?: 'csv';
        $path = 'sheets/' . Str::random(40) . '.' . strtolower($extension);

        try {
            return DB::transaction(function () use ($file, $path, $pathToRead, $userId, $dataSourceLinkId) {
                // Latest update overrides: when a data source is selected, replace previous upload for that source
                if ($dataSourceLinkId !== null) {
                    $query = SheetUpload::where('data_source_link_id', $dataSourceLinkId);
                    $query->where(function ($q) use ($userId) {
                        $userId !== null ? $q->where('user_id', $userId) : $q->whereNull('user_id');
                    });
                    $query->delete();
                }

                $dataSource = $dataSourceLinkId ? DataSourceLink::find($dataSourceLinkId) : null;
                $isMarketActivity = $dataSource && $dataSource->slug === 'market_activity';

                $import = $isMarketActivity
                    ? new MarketActivityReportImport($file->getClientOriginalName(), $path, $userId, $dataSourceLinkId)
                    : new StockSheetImport($file->getClientOriginalName(), $path, $userId, $dataSourceLinkId);

                Excel::import($import, $pathToRead);
                Storage::disk('local')->put($path, file_get_contents($pathToRead));
                $upload = SheetUpload::find($import->getCreatedUploadId());
                if (! $upload) {
                    throw new \RuntimeException('Sheet import did not create an upload record.');
                }
                Log::info('Sheet imported', [
                    'sheet_upload_id' => $upload->id,
                    'user_id' => $userId,
                    'data_source_link_id' => $dataSourceLinkId,
                ]);
                return $upload;
            });
        } catch (\Throwable $e) {
            Log::error('Sheet import failed', [
                'file' => $file->getClientOriginalName(),
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
