<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CentralUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'central_users';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nama_user',
        'password_hash',
        'role',
        'jabatan',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Get the name attribute (alias for nama_user).
     */
    public function getNameAttribute()
    {
        return $this->nama_user;
    }

    protected function casts(): array
    {
        return [
            // No automated hashing cast
        ];
    }

    /**
     * Check if user has any of the given roles.
     *
     * @param array|string $roles
     * @return bool
     */
    public function hasRole(array|string $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];
        return in_array($this->role, $roles);
    }

    /**
     * Check if user is superuser.
     */
    public function isSuperuser(): bool
    {
        // For central users, they might all be superusers, but we check anyway.
        return $this->role === 'superuser';
    }

    /**
     * Check if user is admin. // Only for compatibility with RoleMiddleware if needed
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
