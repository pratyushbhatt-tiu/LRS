<?php

namespace App\Policies;

use App\Models\State;
use App\Models\User;

class StatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin');
    }
    public function view(User $user, State $state): bool
    {
        return $user->hasRole('Admin');
    }
    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }
    public function update(User $user, State $state): bool
    {
        return $user->hasRole('Admin');
    }
    public function delete(User $user, State $state): bool
    {
        return $user->hasRole('Admin');
    }
    public function restore(User $user, State $state): bool
    {
        return $user->hasRole('Admin');
    }
    public function forceDelete(User $user, State $state): bool
    {
        return $user->hasRole('Admin');
    }
}
