<?php

namespace App\Livewire\Learning;

use App\Models\Module;
use Livewire\Component;
use Livewire\WithPagination;

class ModuleManager extends Component
{
    use WithPagination;

    public ?int $moduleId = null;
    public string $name = '';
    public string $timeframe = '';
    public string $description = '';
    public int $sort_order = 0;
    public bool $is_active = true;

    protected $rules = [
        'name' => ['required', 'string', 'max:255'],
        'timeframe' => ['nullable', 'string', 'max:255'],
        'description' => ['nullable', 'string'],
        'sort_order' => ['nullable', 'integer', 'min:0'],
        'is_active' => ['boolean'],
    ];

    public function edit(int $id): void
    {
        $module = Module::findOrFail($id);
        $this->moduleId = $module->id;
        $this->name = $module->name;
        $this->timeframe = (string) ($module->timeframe ?? '');
        $this->description = (string) $module->description;
        $this->sort_order = $module->sort_order;
        $this->is_active = (bool) $module->is_active;
    }

    public function createNew(): void
    {
        $this->reset(['moduleId', 'name', 'timeframe', 'description', 'sort_order', 'is_active']);
        $this->is_active = true;
    }

    public function save(): void
    {
        $data = $this->validate();
        $data['timeframe'] = $data['timeframe'] !== '' ? $data['timeframe'] : null;

        if ($this->moduleId) {
            Module::whereKey($this->moduleId)->update($data);
        } else {
            $module = Module::create($data);
            $this->dispatch('moduleCreated', $module->id);
        }

        $this->resetPage();
        session()->flash('success_module', 'Module saved.');
    }

    public function delete(int $id): void
    {
        $module = Module::findOrFail($id);
        $module->delete();
        $this->resetPage();
        session()->flash('success_module', 'Module deleted.');
        $this->dispatch('moduleListChanged');
    }

    public function render()
    {
        $modules = Module::orderBy('sort_order')->paginate(10);

        return view('livewire.learning.module-manager', compact('modules'));
    }
}

