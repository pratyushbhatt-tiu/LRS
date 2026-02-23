<?php

namespace App\Policies;

use App\Models\FeeRule;
use App\Models\User;

class FeeRulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin');
    }
    public function view(User $user, FeeRule $feeRule): bool
    {
        return $user->hasRole('Admin');
    }
    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }
    public function update(User $user, FeeRule $feeRule): bool
    {
        return $user->hasRole('Admin');
    }
    public function delete(User $user, FeeRule $feeRule): bool
    {
        return $user->hasRole('Admin');
    }
    public function restore(User $user, FeeRule $feeRule): bool
    {
        return $user->hasRole('Admin');
    }
    public function forceDelete(User $user, FeeRule $feeRule): bool
    {
        return $user->hasRole('Admin');
    }
}
