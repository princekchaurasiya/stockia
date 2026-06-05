<div>
    @if ($show)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.45);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $enrollmentId ? 'Edit enrollment' : 'Enroll student' }}</h5>
                        <button type="button" class="btn-close" wire:click="close"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <div class="mb-3">
                                <x-ui.form-label :help="\App\Support\FieldHelp::enrollmentBatch()">Batch</x-ui.form-label>
                                <select class="form-select @error('batch_id') is-invalid @enderror" wire:model="batch_id">
                                    <option value="">Select batch</option>
                                    @foreach ($batches as $batch)
                                        <option value="{{ $batch->id }}">
                                            {{ $batch->name }}{{ $batch->is_active ? '' : ' (inactive batch)' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('batch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <x-ui.form-label :help="\App\Support\FieldHelp::enrollmentStudent()">Student</x-ui.form-label>
                                <select class="form-select @error('user_id') is-invalid @enderror" wire:model="user_id" @disabled($enrollmentId)>
                                    <option value="">Select student</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}">{{ $student->name }} · {{ $student->email }}</option>
                                    @endforeach
                                </select>
                                @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @if ($enrollmentId)
                                    <p class="small text-muted mb-0 mt-1">Student cannot be changed when editing. Remove and re-enroll to move batches.</p>
                                @endif
                            </div>
                            <x-ui.form-check-field id="enrollment_modal_active" wire:model="is_active" :help="\App\Support\FieldHelp::active('enrollment')">
                                Active enrollment
                            </x-ui.form-check-field>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" wire:click="close">Cancel</button>
                            <button type="submit" class="btn btn-primary">{{ $enrollmentId ? 'Save changes' : 'Enroll student' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
