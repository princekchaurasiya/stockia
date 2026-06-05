<?php

namespace App\Livewire\Learning;

use App\Models\Batch;
use Livewire\Component;
use Livewire\WithPagination;

class BatchManager extends Component
{
    use WithPagination;

    public ?int $batchId = null;
    public string $name = '';
    public bool $is_active = true;

    protected $rules = [
        'name' => ['required', 'string', 'max:255'],
        'is_active' => ['boolean'],
    ];

    public function edit(int $id): void
    {
        $batch = Batch::findOrFail($id);
        $this->batchId = $batch->id;
        $this->name = $batch->name;
        $this->is_active = (bool) $batch->is_active;
    }

    public function createNew(): void
    {
        $this->reset(['batchId', 'name', 'is_active']);
        $this->is_active = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->batchId) {
            Batch::whereKey($this->batchId)->update($data);
        } else {
            $batch = Batch::create($data);
            $this->dispatch('batchCreated', $batch->id);
        }

        $this->resetPage();
        session()->flash('success_batch', 'Batch saved.');
    }

    public function delete(int $id): void
    {
        $batch = Batch::findOrFail($id);
        $batch->delete();
        $this->resetPage();
        session()->flash('success_batch', 'Batch deleted.');
        $this->dispatch('batchListChanged');
    }

    public function render()
    {
        $batches = Batch::orderBy('created_at')->paginate(10);

        return view('livewire.learning.batch-manager', compact('batches'));
    }
}

