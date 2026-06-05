<div>
    @include('livewire.tables.partials.controls')

    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>{{ __('stockia.data_source.name') }}</th>
                    <th>{{ __('stockia.data_source.slug') }}</th>
                    <th>{{ __('stockia.data_source.url') }}</th>
                    <th>{{ __('stockia.data_source.active') }}</th>
                    <th class="text-end">{{ __('stockia.data_source.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->rows as $link)
                    <tr>
                        <td>{{ $link->name }}</td>
                        <td><code>{{ $link->slug }}</code></td>
                        <td><a href="{{ $link->url }}" target="_blank" rel="noopener">{{ Str::limit($link->url, 50) }}</a></td>
                        <td>{{ $link->is_active ? __('stockia.data_source.yes') : __('stockia.data_source.no') }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.data_source_links.edit', $link) }}" class="btn btn-sm btn-outline-primary">{{ __('stockia.data_source.edit') }}</a>
                            <form method="POST" action="{{ route('admin.data_source_links.destroy', $link) }}" class="d-inline" onsubmit="return confirm('{{ __('stockia.data_source.delete_confirm') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('stockia.data_source.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-muted text-center py-4">{{ __('stockia.data_source.no_links') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $this->rows->links() }}
    </div>
</div>
