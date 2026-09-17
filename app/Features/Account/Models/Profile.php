<?php

namespace App\Features\Account\Models;

use App\Features\Auth\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['username', 'full_name', 'bio', 'avatar'])]
class Profile extends Model
{    
    use SoftDeletes;

    /**
     * Method user
     *
     * @return BelongsTo Relation between profile and user models
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
