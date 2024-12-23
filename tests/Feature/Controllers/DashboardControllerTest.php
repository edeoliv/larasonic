<?php

declare(strict_types=1);

use App\Models\User;
use App\Http\Controllers\DashboardController;
use Inertia\Testing\AssertableInertia as Assert;

covers(DashboardController::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

test('guests cannot access dashboard', function (): void {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can access dashboard', function (): void {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard'));

    $response->assertInertia(
        fn (Assert $page): \Illuminate\Testing\Fluent\AssertableJson => $page
            ->component('Dashboard')
            ->has('auth.user')
            ->where('auth.user.id', $this->user->id)
    );
});

test('dashboard returns correct status code', function (): void {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard'));

    $response->assertStatus(200);
});

test('dashboard uses correct inertia component', function (): void {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard'));

    $response->assertInertia(
        fn (Assert $page): \Inertia\Testing\AssertableInertia => $page
            ->component('Dashboard')
    );
});

test('dashboard has required shared data', function (): void {
    $response = $this->actingAs($this->user)
        ->get(route('dashboard'));

    $response->assertInertia(
        fn (Assert $page): \Illuminate\Testing\Fluent\AssertableJson => $page
            ->has('auth')
            ->has('auth.user')
            ->has('auth.user.name')
            ->has('auth.user.email')
    );
});
