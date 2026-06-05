<?php

namespace App\Livewire\Inputs;

use App\Models\Batch;
use Livewire\Attributes\Modelable;
use Livewire\Component;
use App\Livewire\Inputs\CreateBatchModal;
use App\Livewire\Learning\AdminLectureManager;

class BatchSelect extends Component
{
    #[Modelable]
    public $value = null;
    public string $name = 'batch_id';
    public bool $includeInactive = false;

    public function mount(?int $value = null, string $name = 'batch_id', bool $includeInactive = false): void
    {
        $this->value = $value;
        $this->name = $name;
        $this->includeInactive = $includeInactive;
    }

    public function updatedValue($value): void
    {
        if ($value === '__new') {
            $this->dispatch('openBatchCreateModal')->to(CreateBatchModal::class);
            $this->value = null;

            return;
        }

        $this->value = ($value === '' || $value === null) ? null : (int) $value;
    }

    public function onBatchCreated(int $id): void
    {
        $this->value = $id;
    }

    protected $listeners = [
        'batchCreated' => 'onBatchCreated',
        'batchListChanged' => '$refresh',
    ];

    public function getOptionsProperty()
    {
        return Batch::query()
            ->when(! $this->includeInactive, fn ($q) => $q->where('is_active', true))
            ->orderBy('created_at')
            ->get();
    }

    public function render()
    {
        return view('livewire.inputs.batch-select');
    }
}

