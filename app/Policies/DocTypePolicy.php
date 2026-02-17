<?php

namespace App\Policies;

use App\Models\DocType;
use App\Models\User;

class DocTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin');
    }
    public function view(User $user, DocType $docType): bool
    {
        return $user->hasRole('Admin');
    }
    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }
    public function update(User $user, DocType $docType): bool
    {
        return $user->hasRole('Admin');
    }
    public function delete(User $user, DocType $docType): bool
    {
        return $user->hasRole('Admin');
    }
    public function restore(User $user, DocType $docType): bool
    {
        return $user->hasRole('Admin');
    }
    public function forceDelete(User $user, DocType $docType): bool
    {
        return $user->hasRole('Admin');
    }
}
