<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
        'gender',
        'date_birth',
        'username',
        'department_name',
        'employee_id',
        'date_commenced',
        'email',
        'role_id',
        'password'

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

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
    protected static function booted()
    {
        static::creating(function ($user) {
            if (is_null($user->role_id)) {
                $roleUser = Role::where('name', 'User')->first();
                if ($roleUser) {
                    $user->role_id = $roleUser->id;
                }
            }
        });
    }
    public function scopeSearch($query, $term)
    {
        return $query->where('name',  'like', '%' . $term . '%');
    }
    public function moderatorAssignments()
    {
        return $this->hasMany(ModeratorAssignment::class);
    }
    public function assignedTo(Hazard $report): bool
    {
        $hasDirect = $this->moderatorAssignments()->where(function ($q) use ($report) {
            $q->where('department_id', $report->department_id)
                ->orWhere('contractor_id', $report->contractor_id)
                ->orWhere('company_id', $report->company_id);
        })->exists();

        if ($hasDirect) return true;

        $companyAssignments = $this->moderatorAssignments()->whereNotNull('company_id')->get();

        foreach ($companyAssignments as $assignment) {
            if (
                optional($report->department)->company_id === $assignment->company_id ||
                optional($report->contractor)->company_id === $assignment->company_id
            ) {
                return true;
            }
        }

        return false;
    }
    public function departments()
    {
        return $this->belongsToMany(Department::class);
    }

    public function contractors()
    {
        return $this->belongsToMany(Contractor::class);
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }
}
