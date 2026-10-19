<?php

namespace App\Models;

use App\Enums\UserRole;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    /** @deprecated Use studentProfile() */
    public function student(): HasOne
    {
        return $this->studentProfile();
    }

    public function accompanyingLecturer(): HasOne
    {
        return $this->hasOne(AccompanyingLecturer::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->whereNull('read_at')->count();
    }

    public function hasRole(UserRole $role): bool
    {
        return $this->role === $role;
    }

    public function isStudent(): bool
    {
        return $this->hasRole(UserRole::Student);
    }

    public function isCoordinator(): bool
    {
        return $this->hasRole(UserRole::Coordinator);
    }

    public function isLecturer(): bool
    {
        return $this->hasRole(UserRole::Lecturer);
    }

    public function isItcAdmin(): bool
    {
        return $this->hasRole(UserRole::ItcAdmin);
    }

    public function isCommittee(): bool
    {
        return $this->hasRole(UserRole::Committee);
    }

    public function isStudentAffairs(): bool
    {
        return $this->hasRole(UserRole::StudentAffairs);
    }

    public function isManagement(): bool
    {
        return $this->isCommittee() || $this->isStudentAffairs() || $this->isCoordinator();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
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
            'role' => UserRole::class,
        ];
    }
}
