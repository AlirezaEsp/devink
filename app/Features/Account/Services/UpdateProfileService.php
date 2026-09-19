<?php

namespace App\Features\Account\Services;

use App\Features\Account\Models\Profile;
use App\Features\Auth\Models\User;

class UpdateProfileService
{
    public function update(User $user, array $data): Profile
    {
        $profile = $user->profile;
        $profile->update($data);

        return $profile->refresh();
    }
}
