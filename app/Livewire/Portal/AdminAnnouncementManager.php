<?php

namespace App\Livewire\Portal;

use App\Models\Announcement;
use Livewire\Component;
use Livewire\WithPagination;

class AdminAnnouncementManager extends Component
{
    use WithPagination;

    public ?int $announcementId = null;
    public string $title = '';
    public string $body = '';
    public string $type = 'general';
    public bool $is_pinned = false;
    public ?string $published_at = null;
    public bool $is_active = true;

    protected $rules = [
        'title' => ['required', 'string', 'max:255'],
        'body' => ['required', 'string'],
        'type' => ['required', 'in:class_update,market_alert,material,general'],
        'is_pinned' => ['boolean'],
        'published_at' => ['nullable', 'date'],
        'is_active' => ['boolean'],
    ];

    public function edit(int $id): void
    {
        $a = Announcement::findOrFail($id);
        $this->announcementId = $a->id;
        $this->title = $a->title;
        $this->body = $a->body;
        $this->type = $a->type;
        $this->is_pinned = (bool) $a->is_pinned;
        $this->published_at = $a->published_at?->format('Y-m-d\TH:i');
        $this->is_active = (bool) $a->is_active;
    }

    public function createNew(): void
    {
        $this->reset(['announcementId', 'title', 'body', 'type', 'is_pinned', 'published_at', 'is_active']);
        $this->type = 'general';
        $this->is_active = true;
    }

    public function save(): void
    {
        $data = $this->validate();
        $data['created_by'] = auth()->id();
        $data['published_at'] = $data['published_at'] ?? now();

        if ($this->announcementId) {
            Announcement::whereKey($this->announcementId)->update(collect($data)->except('created_by')->toArray());
        } else {
            Announcement::create($data);
        }

        $this->createNew();
        $this->resetPage();
        session()->flash('success', 'Announcement saved.');
    }

    public function delete(int $id): void
    {
        Announcement::whereKey($id)->delete();
        $this->resetPage();
        session()->flash('success', 'Announcement deleted.');
    }

    public function render()
    {
        return view('livewire.portal.admin-announcement-manager', [
            'announcements' => Announcement::orderByDesc('published_at')->paginate(10),
        ]);
    }
}
