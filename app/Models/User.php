<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Enums\UserType;
use App\Enums\UserStatus;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'status',
        'login_id',
        'password',
        'email',
        'name',
        'phone',
        'company',
        'birth',
        'is_marketing_email_allowed',
        'confirmed_at',
        'expired_at',
    ];

    protected $casts = [
        'type' => UserType::class,
        'status' => UserStatus::class,
    ];

    public function file(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }

}
