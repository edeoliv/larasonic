<?php

declare(strict_types=1);

namespace App\Actions\Jetstream;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Laravel\Jetstream\Events\TeamMemberRemoved;
use Illuminate\Auth\Access\AuthorizationException;
use Laravel\Jetstream\Contracts\RemovesTeamMembers;

final class RemoveTeamMember implements RemovesTeamMembers
{
    /**
     * Remove the team member from the given team.
     */
    public function remove(User $user, Team $team, User $teamMember): void
    {
        $this->authorize($user, $team, $teamMember);

        $this->ensureUserDoesNotOwnTeam($teamMember, $team);

        $team->removeUser($teamMember);

        TeamMemberRemoved::dispatch($team, $teamMember);
    }

    /**
     * Authorize that the user can remove the team member.
     */
    private function authorize(User $user, Team $team, User $teamMember): void
    {
        throw_if(! Gate::forUser($user)->check('removeTeamMember', $team) &&
            $user->id !== $teamMember->id, new AuthorizationException);
    }

    /**
     * Ensure that the currently authenticated user does not own the team.
     */
    private function ensureUserDoesNotOwnTeam(User $teamMember, Team $team): void
    {
        /** @var User $owner */
        $owner = $team->owner;
        throw_if($teamMember->id === $owner->id, ValidationException::withMessages([
            'team' => [__('You may not leave a team that you created.')],
        ])->errorBag('removeTeamMember'));
    }
}
