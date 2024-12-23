<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use App\Actions\Jetstream\AddTeamMember;
use Laravel\Jetstream\Events\TeamMemberAdded;
use Illuminate\Validation\ValidationException;
use Laravel\Jetstream\Events\AddingTeamMember;
use Illuminate\Auth\Access\AuthorizationException;

beforeEach(function (): void {
    $this->owner = User::factory()->create();
    $this->team = Team::factory()->create(['user_id' => $this->owner->id]);
    $this->newMember = User::factory()->create();
    $this->action = new AddTeamMember();
    $this->jetstreamInstance = Mockery::mock(Jetstream::class);
});

describe('add team member action', function (): void {
    test('can add team member', function (): void {
        Event::fake([AddingTeamMember::class, TeamMemberAdded::class]);

        $this->jetstreamInstance->shouldReceive('hasRoles')->andReturn(false);

        Gate::shouldReceive('forUser')
            ->with($this->owner)
            ->andReturn(Gate::partialMock());

        Gate::shouldReceive('authorize')
            ->with('addTeamMember', $this->team)
            ->andReturn(true);

        $this->action->add(
            $this->owner,
            $this->team,
            $this->newMember->email,
            'admin'
        );

        $this->team->refresh();

        expect($this->team->users)->toHaveCount(1)
            ->and($this->team->hasUserWithEmail($this->newMember->email))->toBeTrue();

        Event::assertDispatched(AddingTeamMember::class);
        Event::assertDispatched(TeamMemberAdded::class);
    });

    test('validates team member email exists', function (): void {
        Gate::shouldReceive('forUser')
            ->with($this->owner)
            ->andReturn(Gate::partialMock());

        Gate::shouldReceive('authorize')
            ->with('addTeamMember', $this->team)
            ->andReturn(true);

        expect(fn () => $this->action->add(
            $this->owner,
            $this->team,
            'nonexistent@example.com',
            'admin'
        ))->toThrow(ValidationException::class);
    });

    test('prevents adding existing team member', function (): void {
        $this->team->users()->attach($this->newMember, ['role' => 'admin']);

        Gate::shouldReceive('forUser')
            ->with($this->owner)
            ->andReturn(Gate::partialMock());

        Gate::shouldReceive('authorize')
            ->with('addTeamMember', $this->team)
            ->andReturn(true);

        expect(fn () => $this->action->add(
            $this->owner,
            $this->team,
            $this->newMember->email,
            'admin'
        ))->toThrow(ValidationException::class);
    });

    test('requires role when jetstream has roles', function (): void {
        $this->jetstreamInstance->shouldReceive('hasRoles')->andReturn(true);

        Gate::shouldReceive('forUser')
            ->with($this->owner)
            ->andReturn(Gate::partialMock());

        Gate::shouldReceive('authorize')
            ->with('addTeamMember', $this->team)
            ->andReturn(true);

        expect(fn () => $this->action->add(
            $this->owner,
            $this->team,
            $this->newMember->email,
            null
        ))->toThrow(ValidationException::class);
    });

    test('validates role when provided', function (): void {
        $this->jetstreamInstance->shouldReceive('hasRoles')->andReturn(true);

        Gate::shouldReceive('forUser')
            ->with($this->owner)
            ->andReturn(Gate::partialMock());

        Gate::shouldReceive('authorize')
            ->with('addTeamMember', $this->team)
            ->andReturn(true);

        expect(fn () => $this->action->add(
            $this->owner,
            $this->team,
            $this->newMember->email,
            'invalid-role'
        ))->toThrow(ValidationException::class);
    });

    test('unauthorized user cannot add team member', function (): void {
        Gate::shouldReceive('forUser')
            ->with($this->owner)
            ->andReturn(Gate::partialMock());

        Gate::shouldReceive('authorize')
            ->with('addTeamMember', $this->team)
            ->andThrow(new AuthorizationException);

        expect(fn () => $this->action->add(
            $this->owner,
            $this->team,
            $this->newMember->email,
            'admin'
        ))->toThrow(AuthorizationException::class);
    });

    test('dispatches events in correct order', function (): void {
        $events = [];
        Event::fake([AddingTeamMember::class, TeamMemberAdded::class]);

        Gate::shouldReceive('forUser')
            ->with($this->owner)
            ->andReturn(Gate::partialMock());

        Gate::shouldReceive('authorize')
            ->with('addTeamMember', $this->team)
            ->andReturn(true);

        $this->action->add(
            $this->owner,
            $this->team,
            $this->newMember->email,
            'admin'
        );

        Event::assertDispatched(AddingTeamMember::class, fn ($event): bool => $event->team->is($this->team) &&
               $event->user->is($this->newMember));

        Event::assertDispatched(TeamMemberAdded::class, fn ($event): bool => $event->team->is($this->team) &&
               $event->user->is($this->newMember));
    });
});
