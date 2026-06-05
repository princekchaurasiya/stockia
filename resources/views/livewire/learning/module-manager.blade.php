<div class="card mb-3">
    <div class="card-body">
        <h2 class="h6 mb-3">Modules</h2>

        @if (session('success_module'))
            <div class="alert alert-success">{{ session('success_module') }}</div>
        @endif

        <form wire:submit.prevent="save" class="row g-2 mb-3">
            <div class="col-md-3">
                <x-ui.form-label :help="\App\Support\FieldHelp::moduleName()">Name</x-ui.form-label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="e.g. lecture 5">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <x-ui.form-label-trading-style />
                <input type="text" class="form-control @error('timeframe') is-invalid @enderror" wire:model="timeframe" placeholder="e.g. Intraday — 1 min, 5 min">
                @error('timeframe')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2">
                <x-ui.form-label-sort-order context="modules" />
                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" wire:model="sort_order" min="0">
                @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <x-ui.form-check-field id="module_manager_active" wire:model="is_active" class="me-3" :help="\App\Support\FieldHelp::active('module')">
                    Active
                </x-ui.form-check-field>
                <button type="submit" class="btn btn-primary me-2">{{ $moduleId ? 'Update' : 'Add' }} module</button>
                <button type="button" class="btn btn-outline-secondary" wire:click="createNew">Clear</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>
                            <span>Trading style</span>
                            <x-ui.module-trading-style-help />
                        </th>
                        <th>
                            Sort
                            <x-ui.module-sort-order-help />
                        </th>
                        <th>Active</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($modules as $module)
                        <tr>
                            <td>{{ $module->name }}</td>
                            <td>{{ $module->timeframe ?: '—' }}</td>
                            <td>{{ $module->sort_order }}</td>
                            <td>
                                <span class="badge {{ $module->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $module->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="edit({{ $module->id }})">Edit</button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="delete({{ $module->id }})" onclick="return confirm('Delete this module and its lectures/videos?');">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted text-center py-3">No modules yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-2">
            {{ $modules->links() }}
        </div>
    </div>
</div>
