<div>
    <select name="{{ $name }}" class="form-select" wire:model="value">
        <option value="">Select module</option>
        @foreach($this->options as $module)
            <option value="{{ $module->id }}">{{ $module->name }}</option>
        @endforeach
        <option value="__new">+ Add module</option>
    </select>
    @error($name)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
