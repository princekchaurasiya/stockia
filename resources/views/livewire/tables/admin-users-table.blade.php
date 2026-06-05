<div>
    @include('livewire.tables.partials.controls')

    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Account</th>
                    <th class="text-end">{{ __('stockia.data_source.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->rows as $admin)
                    <tr>
                        <td>{{ $admin->name }}</td>
                        <td>{{ $admin->email }}</td>
                        <td>{{ $admin->account?->name ?? '—' }}</td>
                        <td class="text-end">
                            @if(auth()->user()->role === 'superadmin')
                                <form method="POST" action="{{ route('admin.impersonate.store', $admin) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('stockia.account.act_as_admin') }}</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-muted text-center py-4">{{ __('stockia.information_link.no_links') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $this->rows->links() }}
    </div>
</div>
