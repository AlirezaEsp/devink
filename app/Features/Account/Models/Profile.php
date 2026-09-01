<?php

namespace App\Features\Accounts\Models;

use App\Features\Auth\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['username', 'full_name', 'bio', 'avatar'])]
class Profile extends Model
{    
    /**
     * Method user
     *
     * @return BelongsTo Relation between profile and user models
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
