<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'password_hash',
        'phone',
        'role',
        'status',
        'avatar',
        'date_of_birth',
        'gender',
        'address',
        'organizer_request_status',
        'organizer_request_at',
        'google_id',      // Social login Google
        'facebook_id',    // Social login Facebook
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified' => 'boolean',
        'phone_verified' => 'boolean',
        'date_of_birth' => 'date',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function isOrganizer()
    {
        return $this->role === 'organizer';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Kiểm tra xem user có phải tài khoản social không
     */
    public function isSocialAccount()
    {
        return !empty($this->google_id) || !empty($this->facebook_id);
    }
    
    /**
     * Get name attribute (alias for full_name)
     */
    public function getNameAttribute()
    {
        return $this->attributes['full_name'] ?? null;
    }
}
