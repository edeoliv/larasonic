<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use App\Policies\UserPolicy;

beforeEach(function (): void {
    $this->policy = new UserPolicy();

    // Create admin user with team
    $this->admin = User::factory()->withPersonalTeam()->create();
    $this->admin->currentTeam->users()->attach(
        $this->admin, ['role' => 'admin']
    );

    // Create regular user with write permissions
    $this->writer = User::factory()->create();
    $this->admin->currentTeam->users()->attach(
        $this->writer, ['role' => 'editor']
    );
    $this->writer->switchTeam($this->admin->currentTeam);

    // Create user in different team
    $this->otherUser = User::factory()->withPersonalTeam()->create();
});

test('admin can view any users', function (): void {
    $this->admin->withAccessToken(
        $this->admin->createToken('test-token', ['read'])->accessToken
    );
    expect($this->policy->viewAny($this->admin))->toBeTrue();
});

test('admin can view specific user', function (): void {
    $this->admin->withAccessToken(
        $this->admin->createToken('test-token', ['read'])->accessToken
    );
    expect($this->policy->view($this->admin, $this->writer))->toBeTrue();
});

test('users can view their own profile', function (): void {
    $this->writer->withAccessToken(
        $this->writer->createToken('test-token', ['read'])->accessToken
    );
    expect($this->policy->view($this->writer, $this->writer))->toBeTrue();
});

test('users cannot view users from different teams', function (): void {
    $this->writer->withAccessToken(
        $this->writer->createToken('test-token', ['read'])->accessToken
    );
    expect($this->policy->view($this->writer, $this->otherUser))->toBeFalse();
});

test('admin can create users', function (): void {
    $this->admin->withAccessToken(
        $this->admin->createToken('test-token', ['create'])->accessToken
    );
    expect($this->policy->create($this->admin))->toBeTrue();
});

test('writers can create users', function (): void {
    $this->writer->withAccessToken(
        $this->writer->createToken('test-token', ['create'])->accessToken
    );
    expect($this->policy->create($this->writer))->toBeTrue();
});

test('users can update their own profile', function (): void {
    $this->writer->withAccessToken(
        $this->writer->createToken('test-token', ['update'])->accessToken
    );
    expect($this->policy->update($this->writer, $this->writer))->toBeTrue();
});

test('admin can update other users', function (): void {
    $this->admin->withAccessToken(
        $this->admin->createToken('test-token', ['update'])->accessToken
    );
    expect($this->policy->update($this->admin, $this->writer))->toBeTrue();
});

test('users cannot delete themselves', function (): void {
    $this->writer->withAccessToken(
        $this->writer->createToken('test-token', ['delete'])->accessToken
    );
    expect($this->policy->delete($this->writer, $this->writer))->toBeFalse();
});

test('only admin can delete users', function (): void {
    $this->admin->withAccessToken(
        $this->admin->createToken('test-token', ['delete'])->accessToken
    );
    expect($this->policy->delete($this->admin, $this->writer))->toBeTrue();

    $this->writer->withAccessToken(
        $this->writer->createToken('test-token', ['delete'])->accessToken
    );
    expect($this->policy->delete($this->writer, $this->admin))->toBeFalse();
});

test('only admin can restore users', function (): void {
    $this->admin->withAccessToken(
        $this->admin->createToken('test-token', ['delete'])->accessToken
    );
    expect($this->policy->restore($this->admin, $this->writer))->toBeTrue();

    $this->writer->withAccessToken(
        $this->writer->createToken('test-token', ['delete'])->accessToken
    );
    expect($this->policy->restore($this->writer, $this->admin))->toBeFalse();
});

test('only admin can force delete users', function (): void {
    $this->admin->withAccessToken(
        $this->admin->createToken('test-token', ['delete'])->accessToken
    );
    expect($this->policy->forceDelete($this->admin))->toBeTrue();

    $this->writer->withAccessToken(
        $this->writer->createToken('test-token', ['delete'])->accessToken
    );
    expect($this->policy->forceDelete($this->writer))->toBeFalse();
});
