<?php

namespace App\Livewire\Admin\Learning;

use App\Models\Module;
use Livewire\Attributes\On;
use Livewire\Component;

class ModuleFormModal extends Component
{
    public bool $show = false;

    public ?int $moduleId = null;

    public string $name = '';

    public string $timeframe = '';

    public string $description = '';

    public int $sort_order = 0;

    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'timeframe' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }

    #[On('openModuleFormModal')]
    public function open(?int $id = null): void
    {
        $this->moduleId = $id;

        if ($id) {
            $module = Module::findOrFail($id);
            $this->name = $module->name;
            $this->timeframe = (string) ($module->timeframe ?? '');
            $this->description = (string) $module->description;
            $this->sort_order = $module->sort_order;
            $this->is_active = (bool) $module->is_active;
        } else {
            $this->reset(['name', 'timeframe', 'description', 'sort_order']);
            $this->is_active = true;
        }

        $this->show = true;
    }

    public function save(): void
    {
        $data = $this->validate();
        $data['timeframe'] = $data['timeframe'] !== '' ? $data['timeframe'] : null;

        if ($this->moduleId) {
            Module::whereKey($this->moduleId)->update($data);
            $message = 'Module updated.';
        } else {
            Module::create($data);
            $message = 'Module created.';
        }

        $this->dispatch('moduleTableRefresh');
        session()->flash('success', $message);
        $this->close();
    }

    public function close(): void
    {
        $this->show = false;
        $this->reset(['moduleId', 'name', 'timeframe', 'description', 'sort_order']);
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.admin.learning.module-form-modal');
    }
}
