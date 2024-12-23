<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Models\Team;
use App\Models\User;
use Stripe\Customer;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

final class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => Arr::get($input, 'password') ? $this->passwordRules() : 'sometimes',
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return DB::transaction(fn () => tap(User::query()->create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Arr::get($input, 'password') ? Hash::make($input['password']) : Str::random(12),
        ]), function (User $user): void {
            $this->createTeam($user);
            $this->createCustomer($user);
        }));
    }

    /**
     * Create a personal team for the user.
     */
    private function createTeam(User $user): void
    {
        $user->ownedTeams()->save(Team::query()->forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0]."'s Team",
            'personal_team' => true,
        ]));
    }

    /**
     * Create a billing customer for the user.
     */
    private function createCustomer(User $user): void
    {
        if (! Config::get('cashier.billing_enabled')) {
            return;
        }

        /** @var Customer $stripeCustomer */
        $stripeCustomer = $user->createOrGetStripeCustomer();

        $user->update([
            'stripe_id' => $stripeCustomer->id,
        ]);
    }
}
