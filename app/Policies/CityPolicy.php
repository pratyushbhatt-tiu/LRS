<?php

namespace App\Policies;

use App\Models\City;
use App\Models\User;

class CityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin');
    }
    public function view(User $user, City $city): bool
    {
        return $user->hasRole('Admin');
    }
    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }
    public function update(User $user, City $city): bool
    {
        return $user->hasRole('Admin');
    }
    public function delete(User $user, City $city): bool
    {
        return $user->hasRole('Admin');
    }
    public function restore(User $user, City $city): bool
    {
        return $user->hasRole('Admin');
    }
    public function forceDelete(User $user, City $city): bool
    {
        return $user->hasRole('Admin');
    }
}
