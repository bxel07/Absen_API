<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'fullname',
        'email',
        'password',
        'date_of_birth',
        'gender',
        'contact',
        'religion',
        'role_id',
        'image_profile',
        'google_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
        'password',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
       return [];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }

    public function attendance_request(): HasMany
    {
        return $this->hasMany(AttendanceRequest::class);
    }

    public function leave_request(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function shift_request(): HasMany {
        return $this->hasMany(ShiftRequest::class);
    }

    public function schedule_shift(): HasMany
    {
        return $this->hasMany(ScheduleShift::class, 'user_id');
    }

    public function approved_request(): HasMany {
        return $this->hasMany(ApprovedRequest::class);
    }

    public function employment(): HasMany
    {
        return $this->hasMany(Employment::class);
    }
}
