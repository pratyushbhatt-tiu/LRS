<?php

namespace App\Policies;

use App\Models\RecordingPurpose;
use App\Models\User;

class RecordingPurposePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin');
    }
    public function view(User $user, RecordingPurpose $recordingPurpose): bool
    {
        return $user->hasRole('Admin');
    }
    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }
    public function update(User $user, RecordingPurpose $recordingPurpose): bool
    {
        return $user->hasRole('Admin');
    }
    public function delete(User $user, RecordingPurpose $recordingPurpose): bool
    {
        return $user->hasRole('Admin');
    }
    public function restore(User $user, RecordingPurpose $recordingPurpose): bool
    {
        return $user->hasRole('Admin');
    }
    public function forceDelete(User $user, RecordingPurpose $recordingPurpose): bool
    {
        return $user->hasRole('Admin');
    }
}
