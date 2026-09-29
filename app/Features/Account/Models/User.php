<?php

namespace App\Features\Account\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\Features\Account\Models\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Features\Account\Models\Profile;
use App\Features\Account\Notifications\PasswordResetNotification;

#[Fillable(['email', 'password'])]
#[Hidden(['password'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

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

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new PasswordResetNotification($token));
    }

    /**
     * Method profile
     *
     * @return HasOne Relation between user and profile models
     */
    public function profile(): HasOne {
        return $this->hasOne(Profile::class);
    }
    
    /**
     * Method booted
     * 
     * Sync User and Profile model delete and restore
     *
     * @return void
     */
    protected static function booted (): void {
        static::deleting(function (User $user) : void {
            if ($user->isForceDeleting()) {
                $user->profile()->withTrashed()->forceDelete();
            } else {
                $user->profile()->delete();
            }
        });

        static::restoring(function (User $user) : void {
            $user->profile()->withTrashed()->restore();
        });
    }
}
