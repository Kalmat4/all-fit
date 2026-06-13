<?php

namespace App\Services;

use App\Models\User;

class SettingsService
{
    public function update(User $user, array $data): void
    {
        $user->update($data);
    }
}
