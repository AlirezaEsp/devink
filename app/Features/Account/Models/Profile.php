<?php

namespace App\Features\Account\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Features\Account\Models\User;
use Database\Factories\Features\Account\Models\ProfileFactory;

#[Fillable(['username', 'full_name', 'bio', 'avatar'])]
class Profile extends Model
{    
    /** @use HasFactory<ProfileFactory> */
    use SoftDeletes, HasFactory;

    /**
     * Method user
     *
     * @return BelongsTo Relation between profile and user models
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
