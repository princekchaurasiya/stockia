<?php

namespace App\Livewire\Admin\Learning;

use App\Models\Batch;
use App\Models\BatchEnrollment;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class EnrollmentFormModal extends Component
{
    public bool $show = false;

    public ?int $enrollmentId = null;

    public $batch_id = null;

    public $user_id = null;

    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'batch_id' => ['required', 'exists:batches,id'],
            'user_id' => ['required', 'exists:users,id'],
            'is_active' => ['boolean'],
        ];
    }

    #[On('openEnrollmentFormModal')]
    public function open(?int $id = null, ?int $batchId = null): void
    {
        $this->enrollmentId = $id;

        if ($id) {
            $enrollment = BatchEnrollment::findOrFail($id);
            $this->batch_id = $enrollment->batch_id;
            $this->user_id = $enrollment->user_id;
            $this->is_active = (bool) $enrollment->is_active;
        } else {
            $this->reset(['user_id']);
            $this->batch_id = $batchId;
            $this->is_active = true;
        }

        $this->show = true;
    }

    public function updatedBatchId($value): void
    {
        $this->batch_id = ($value === '' || $value === null) ? null : (int) $value;
    }

    public function updatedUserId($value): void
    {
        $this->user_id = ($value === '' || $value === null) ? null : (int) $value;
    }

    public function save(): void
    {
        $this->batch_id = ($this->batch_id === '' || $this->batch_id === null) ? null : (int) $this->batch_id;
        $this->user_id = ($this->user_id === '' || $this->user_id === null) ? null : (int) $this->user_id;

        $data = $this->validate();

        $student = User::whereKey($data['user_id'])->where('role', 'user')->first();
        if (! $student) {
            $this->addError('user_id', 'Only student accounts can be enrolled in a batch.');

            return;
        }

        if ($this->enrollmentId) {
            $enrollment = BatchEnrollment::findOrFail($this->enrollmentId);
            $enrollment->fill($data);
            $enrollment->save();
            $message = 'Enrollment updated.';
        } else {
            BatchEnrollment::updateOrCreate(
                [
                    'batch_id' => $data['batch_id'],
                    'user_id' => $data['user_id'],
                ],
                [
                    'is_active' => $data['is_active'],
                    'enrolled_at' => now(),
                ]
            );
            $message = 'Student enrolled in batch.';
        }

        $this->dispatch('enrollmentTableRefresh');
        $this->dispatch('batchTableRefresh');
        session()->flash('success', $message);
        $this->close();
    }

    public function close(): void
    {
        $this->show = false;
        $this->reset(['enrollmentId', 'batch_id', 'user_id']);
        $this->is_active = true;
    }

    public function render()
    {
        $batches = Batch::orderBy('name')->get();
        $students = User::query()
            ->where('role', 'user')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('livewire.admin.learning.enrollment-form-modal', compact('batches', 'students'));
    }
}
