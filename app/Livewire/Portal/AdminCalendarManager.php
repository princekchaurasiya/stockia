<?php

namespace App\Livewire\Portal;

use App\Models\CalendarEvent;
use Livewire\Component;
use Livewire\WithPagination;

class AdminCalendarManager extends Component
{
    use WithPagination;

    public ?int $eventId = null;
    public string $title = '';
    public ?string $event_date = null;
    public string $event_type = 'custom';
    public string $description = '';
    public bool $is_active = true;

    protected $rules = [
        'title' => ['required', 'string', 'max:255'],
        'event_date' => ['required', 'date'],
        'event_type' => ['required', 'in:expiry,rbi,results,holiday,custom'],
        'description' => ['nullable', 'string'],
        'is_active' => ['boolean'],
    ];

    public function edit(int $id): void
    {
        $event = CalendarEvent::findOrFail($id);
        $this->eventId = $event->id;
        $this->title = $event->title;
        $this->event_date = $event->event_date->format('Y-m-d');
        $this->event_type = $event->event_type;
        $this->description = (string) $event->description;
        $this->is_active = (bool) $event->is_active;
    }

    public function createNew(): void
    {
        $this->reset(['eventId', 'title', 'event_date', 'event_type', 'description', 'is_active']);
        $this->event_type = 'custom';
        $this->is_active = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->eventId) {
            CalendarEvent::whereKey($this->eventId)->update($data);
        } else {
            CalendarEvent::create($data);
        }

        $this->createNew();
        $this->resetPage();
        session()->flash('success', 'Calendar event saved.');
    }

    public function delete(int $id): void
    {
        CalendarEvent::whereKey($id)->delete();
        $this->resetPage();
        session()->flash('success', 'Event deleted.');
    }

    public function render()
    {
        return view('livewire.portal.admin-calendar-manager', [
            'events' => CalendarEvent::orderByDesc('event_date')->paginate(15),
        ]);
    }
}
