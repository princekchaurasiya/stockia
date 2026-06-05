<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNoteImage extends Model
{
    protected $fillable = [
        'user_note_id',
        'file_path',
        'original_name',
        'sort_order',
    ];

    public function userNote(): BelongsTo
    {
        return $this->belongsTo(UserNote::class);
    }

    public function url(): string
    {
        return asset('storage/'.$this->file_path);
    }

    public function extension(): string
    {
        return strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
    }

    public function isPdf(): bool
    {
        return $this->extension() === 'pdf';
    }

    public function isImage(): bool
    {
        return in_array($this->extension(), ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
    }
}
