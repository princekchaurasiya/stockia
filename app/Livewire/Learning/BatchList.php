<?php

namespace App\Livewire\Learning;

use App\Models\Batch;
use Livewire\Component;

class BatchList extends Component
{
    public ?int $selectedBatchId = null;

    public function mount(): void
    {
        $this->selectedBatchId = Batch::where('is_active', true)->orderBy('created_at')->value('id');
        if ($this->selectedBatchId) {
            $this->dispatch('batchSelected', $this->selectedBatchId);
        }
    }

    public function selectBatch(int $batchId): void
    {
        $this->selectedBatchId = $batchId;
        $this->dispatch('batchSelected', $batchId);
    }

    public function render()
    {
        $batches = Batch::where('is_active', true)
            ->orderBy('created_at')
            ->get();

        return view('livewire.learning.batch-list', compact('batches'));
    }
}

