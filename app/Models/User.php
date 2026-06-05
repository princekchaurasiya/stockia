<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'account_id',
        'avatar_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /** Account this user owns (admin only). */
    public function ownedAccount(): HasMany
    {
        return $this->hasMany(Account::class, 'owner_id');
    }

    /** For admin: the single account they own. */
    public function ownedAccountSingle(): ?Account
    {
        return $this->ownedAccount()->first();
    }

    public function sheetUploads(): HasMany
    {
        return $this->hasMany(SheetUpload::class);
    }

    public function batchEnrollments(): HasMany
    {
        return $this->hasMany(BatchEnrollment::class);
    }

    public function enrolledBatches(): BelongsToMany
    {
        return $this->belongsToMany(Batch::class, 'batch_enrollments')
            ->withPivot(['is_active', 'enrolled_at'])
            ->withTimestamps();
    }

    public function isStudent(): bool
    {
        return ($this->role ?? '') === 'user';
    }
}
