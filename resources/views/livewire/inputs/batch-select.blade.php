<div>
    <select name="{{ $name }}" class="form-select" wire:model="value">
        <option value="">Select batch</option>
        @foreach($this->options as $batch)
            <option value="{{ $batch->id }}">{{ $batch->name }}</option>
        @endforeach
        <option value="__new">+ Add batch</option>
    </select>
    @error($name)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
