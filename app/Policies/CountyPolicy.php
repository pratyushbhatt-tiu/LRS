<?php

namespace App\Policies;

use App\Models\County;
use App\Models\User;

class CountyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin');
    }
    public function view(User $user, County $county): bool
    {
        return $user->hasRole('Admin');
    }
    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }
    public function update(User $user, County $county): bool
    {
        return $user->hasRole('Admin');
    }
    public function delete(User $user, County $county): bool
    {
        return $user->hasRole('Admin');
    }
    public function restore(User $user, County $county): bool
    {
        return $user->hasRole('Admin');
    }
    public function forceDelete(User $user, County $county): bool
    {
        return $user->hasRole('Admin');
    }
}
