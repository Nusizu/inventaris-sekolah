<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Punya akses ke area pengelola (Admin atau Petugas). */
    public function isAdmin(): bool
    {
        return $this->can('admin.access');
    }

    /** Punya akses ke area peminjam. */
    public function isUser(): bool
    {
        return $this->can('user.access');
    }

    /**
     * Samakan kolom `role` (admin atau user) dengan izin dari peran Spatie.
     */
    public function syncRoleColumn(): void
    {
        $this->unsetRelation('roles');
        $this->unsetRelation('permissions');

        $area = $this->can('admin.access') ? 'admin' : 'user';

        if ($this->role !== $area) {
            $this->role = $area;
            $this->save();
        }
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}