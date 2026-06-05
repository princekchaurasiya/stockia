<?php

namespace App\Livewire\Inputs;

use App\Models\Module;
use Livewire\Attributes\Modelable;
use Livewire\Component;
use App\Livewire\Inputs\CreateModuleModal;
use App\Livewire\Learning\AdminLectureManager;

class ModuleSelect extends Component
{
    #[Modelable]
    public $value = null;
    public string $name = 'module_id';
    public bool $includeInactive = false;

    public function mount(?int $value = null, string $name = 'module_id', bool $includeInactive = false): void
    {
        $this->value = $value;
        $this->name = $name;
        $this->includeInactive = $includeInactive;
    }

    public function updatedValue($value): void
    {
        if ($value === '__new') {
            $this->dispatch('openModuleCreateModal')->to(CreateModuleModal::class);
            $this->value = null;

            return;
        }

        $this->value = ($value === '' || $value === null) ? null : (int) $value;
    }

    public function onModuleCreated(int $id): void
    {
        $this->value = $id;
    }

    protected $listeners = [
        'moduleCreated' => 'onModuleCreated',
        'moduleListChanged' => '$refresh',
    ];

    public function getOptionsProperty()
    {
        return Module::query()
            ->when(! $this->includeInactive, fn ($q) => $q->where('is_active', true))
            ->orderBy('sort_order')
            ->get();
    }

    public function render()
    {
        return view('livewire.inputs.module-select');
    }
}

