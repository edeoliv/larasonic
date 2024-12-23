<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

final class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasTeamPermission($user->currentTeam, 'read')
            && $user->tokenCan('read');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Allow if it's the same user and token has read ability
        if ($user->id === $model->id && $user->tokenCan('read')) {
            return true;
        }

        // Allow if user has read permission and belongs to same team
        return $user->belongsToTeam($model->currentTeam)
            && $user->hasTeamPermission($model->currentTeam, 'read')
            && $user->tokenCan('read');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ($user->hasTeamRole($user->currentTeam, 'admin')
            || $user->hasTeamPermission($user->currentTeam, 'create'))
            && $user->tokenCan('create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Allow if it's the same user and token has update ability
        if ($user->id === $model->id && $user->tokenCan('update')) {
            return true;
        }

        // Allow if user has write permission and belongs to same team
        return $user->belongsToTeam($model->currentTeam)
            && $user->hasTeamPermission($model->currentTeam, 'update')
            && $user->tokenCan('update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Prevent self-deletion
        if ($user->id === $model->id) {
            return false;
        }

        // Only admin can delete users
        return $user->belongsToTeam($model->currentTeam)
            && $user->hasTeamRole($user->currentTeam, 'admin')
            && $user->tokenCan('delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->belongsToTeam($model->currentTeam)
            && $user->hasTeamRole($user->currentTeam, 'admin')
            && $user->tokenCan('delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return $user->hasTeamRole($user->currentTeam, 'admin')
            && $user->tokenCan('delete');
    }
}
