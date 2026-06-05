<div>
    @include('livewire.tables.partials.controls')

    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>{{ __('stockia.information_link.title') }}</th>
                    <th>URL</th>
                    <th>{{ __('stockia.information_link.sort_order') }}</th>
                    <th>{{ __('stockia.information_link.active') }}</th>
                    @if(auth()->user()->role === 'superadmin')
                        <th>Scope</th>
                    @endif
                    <th class="text-end">{{ __('stockia.data_source.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->rows as $link)
                    <tr>
                        <td>{{ $link->title }}</td>
                        <td><a href="{{ $link->url }}" target="_blank" rel="noopener">{{ Str::limit($link->url, 40) }}</a></td>
                        <td>{{ $link->sort_order }}</td>
                        <td>{{ $link->is_active ? __('stockia.data_source.yes') : __('stockia.data_source.no') }}</td>
                        @if(auth()->user()->role === 'superadmin')
                            <td>{{ $link->account_id ? ($link->account?->name ?? '—') : __('stockia.information_link.global') }}</td>
                        @endif
                        <td class="text-end">
                            <a href="{{ route('admin.information_links.edit', $link) }}" class="btn btn-sm btn-outline-primary">{{ __('stockia.data_source.edit') }}</a>
                            <form method="POST" action="{{ route('admin.information_links.destroy', $link) }}" class="d-inline" onsubmit="return confirm('{{ __('stockia.information_link.delete_confirm') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('stockia.data_source.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->role === 'superadmin' ? 6 : 5 }}" class="text-muted text-center py-4">{{ __('stockia.information_link.no_links') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $this->rows->links() }}
    </div>
</div>
