<?php

namespace App\Http\Controllers;

use App\Models\DataSourceLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataSourceLinkController extends Controller
{
    public function index(): View
    {
        return view('data_source_links.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:64|unique:data_source_links,slug',
            'url' => 'required|url',
        ], [], [
            'name' => __('stockia.data_source.name'),
            'slug' => __('stockia.data_source.slug'),
            'url' => __('stockia.data_source.url'),
        ]);

        DataSourceLink::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'url' => $validated['url'],
            'display_columns' => $validated['slug'] === config('stockia.nifty50.slug')
                ? config('stockia.nifty50.display_columns')
                : null,
            'is_active' => true,
        ]);

        Log::info('Data source link created', ['slug' => $validated['slug']]);

        return redirect()->route('admin.data_source_links.index')
            ->with('success', __('stockia.data_source.created'));
    }

    public function edit(DataSourceLink $data_source_link): View
    {
        return view('data_source_links.edit', ['link' => $data_source_link]);
    }

    public function update(Request $request, DataSourceLink $data_source_link): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:64|unique:data_source_links,slug,' . $data_source_link->id,
            'url' => 'required|url',
            'is_active' => 'boolean',
        ], [], [
            'name' => __('stockia.data_source.name'),
            'slug' => __('stockia.data_source.slug'),
            'url' => __('stockia.data_source.url'),
            'is_active' => __('stockia.data_source.active'),
        ]);

        $data_source_link->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'url' => $validated['url'],
            'is_active' => $request->boolean('is_active'),
        ]);

        Log::info('Data source link updated', ['slug' => $validated['slug']]);

        return redirect()->route('admin.data_source_links.index')
            ->with('success', __('stockia.data_source.updated'));
    }

    public function destroy(DataSourceLink $data_source_link): RedirectResponse
    {
        $slug = $data_source_link->slug;
        $data_source_link->delete();
        Log::info('Data source link deleted', ['slug' => $slug]);
        return redirect()->route('admin.data_source_links.index')
            ->with('success', __('stockia.data_source.deleted'));
    }

    /**
     * Open link in new tab (redirect to external URL).
     */
    public function open(DataSourceLink $dataSourceLink): RedirectResponse
    {
        if (! $dataSourceLink->is_active) {
            abort(404);
        }
        return redirect()->away($dataSourceLink->url);
    }

    /**
     * Proxy download: fetch file from URL and stream to user.
     */
    public function download(DataSourceLink $dataSourceLink): StreamedResponse|RedirectResponse
    {
        if (! $dataSourceLink->is_active) {
            abort(404);
        }

        $url = $dataSourceLink->url;
        $context = stream_context_create([
            'http' => [
                'header' => "User-Agent: Mozilla/5.0 (compatible; Stockia/1.0)\r\n",
            ],
        ]);

        $content = @file_get_contents($url, false, $context);
        if ($content === false) {
            Log::warning('Data source download failed', ['url' => $url, 'id' => $dataSourceLink->id]);
            return redirect()->back()->with('error', __('stockia.data_source.download_failed'));
        }

        $filename = $dataSourceLink->slug . '_' . now()->format('Y-m-d') . '.csv';
        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
