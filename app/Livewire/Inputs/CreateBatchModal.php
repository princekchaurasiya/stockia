<?php

namespace App\Livewire\Inputs;

use App\Models\Batch;
use Livewire\Component;

class CreateBatchModal extends Component
{
    public string $name = '';
    public bool $is_active = true;

    protected $rules = [
        'name' => ['required', 'string', 'max:255'],
        'is_active' => ['boolean'],
    ];

    protected $listeners = [
        'openBatchCreateModal' => 'open',
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

        $batch = Batch::create($data);

        $this->dispatch('batchCreated', $batch->id)->to(BatchSelect::class);
        $this->dispatch('batchCreated', $batch->id)->to('learning.admin-lecture-manager');

        $this->show = false;
        $this->reset(['name', 'is_active']);
    }

    public function render()
    {
        return view('livewire.inputs.create-batch-modal');
    }
}

