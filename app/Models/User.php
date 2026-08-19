<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\LogsActivity;
use Filament\Panel;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, LogsActivity, Notifiable;

    public function activites(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'activity_user');
    }

    // Helper mengecek apakah user adalah super admin atau bukan
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    // Helper mengecek apakah user ini mengelola kegiatan tertentu atau tidak
    public function managesActivity(int $activityId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        return $this->activites()->where('activities.id', $activityId)->exists();
    }

    public function canAccessPanel(Panel $panel): bool {
        // hanya super_admin dan activity_admin yang boleh masuk panel filament
        return in_array($this->role, ['super_admin', 'activity_admin']);
    }

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

    protected function activityLogName(): string
    {
        return 'Pengguna';
    }
}
