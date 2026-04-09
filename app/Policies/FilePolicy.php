<?php

namespace App\Policies;

use App\Models\File;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FilePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('files.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, File $file): bool
    {
        return $user->can('files.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('files.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, File $file): bool
    {
        // Once a file is in Recording or beyond, only Admins can edit
        $restrictedStatuses = [
            config('constants.file_statuses.RECORDING'),
            config('constants.file_statuses.RETURN'),
            config('constants.file_statuses.CLOSED'),
        ];

        if (in_array($file->current_status, $restrictedStatuses)) {
            return $user->hasRole('Admin');
        }

        return $user->can('files.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, File $file): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, File $file): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, File $file): bool
    {
        return $user->hasRole('Admin');
    }
}
