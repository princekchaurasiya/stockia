<?php

namespace App\Livewire\Inputs;

use App\Models\Module;
use Livewire\Component;

class CreateModuleModal extends Component
{
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

    protected $listeners = [
        'openModuleCreateModal' => 'open',
    ];

    public bool $show = false;

    public function open(): void
    {
        $this->resetValidation();
        $this->show = true;
    }

    public function save(): void
    {
        $data = $this->validate();
        $data['timeframe'] = $data['timeframe'] !== '' ? $data['timeframe'] : null;

        $module = Module::create($data);

        $this->dispatch('moduleCreated', $module->id)->to(ModuleSelect::class);
        $this->dispatch('moduleCreated', $module->id)->to('learning.admin-lecture-manager');

        $this->show = false;
        $this->reset(['name', 'timeframe', 'description', 'sort_order', 'is_active']);
    }

    public function render()
    {
        return view('livewire.inputs.create-module-modal');
    }
}

