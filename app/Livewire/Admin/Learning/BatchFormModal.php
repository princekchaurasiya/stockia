<?php

namespace App\Livewire\Admin\Learning;

use App\Models\Batch;
use Livewire\Attributes\On;
use Livewire\Component;

class BatchFormModal extends Component
{
    public bool $show = false;

    public ?int $batchId = null;

    public string $name = '';

    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }

    #[On('openBatchFormModal')]
    public function open(?int $id = null): void
    {
        $this->batchId = $id;

        if ($id) {
            $batch = Batch::findOrFail($id);
            $this->name = $batch->name;
            $this->is_active = (bool) $batch->is_active;
        } else {
            $this->reset(['name']);
            $this->is_active = true;
        }

        $this->show = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->batchId) {
            Batch::whereKey($this->batchId)->update($data);
            $message = 'Batch updated.';
        } else {
            Batch::create($data);
            $message = 'Batch created.';
        }

        $this->dispatch('batchTableRefresh');
        session()->flash('success', $message);
        $this->close();
    }

    public function close(): void
    {
        $this->show = false;
        $this->reset(['batchId', 'name']);
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.admin.learning.batch-form-modal');
    }
}
