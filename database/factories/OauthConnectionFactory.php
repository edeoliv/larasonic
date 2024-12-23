<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\OauthConnection;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OauthConnection>
 */
final class OauthConnectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<model-property<OauthConnection>, mixed>
     */
    public function definition(): array
    {
        /** @var array<int, array{slug: string, active: bool, icon: string}> $availableProviders */
        $availableProviders = Config::array('oauth.providers');
        $provider = $availableProviders[0] ?? [
            'slug' => 'github',
            'active' => true,
            'icon' => 'mdi:github',
        ];

        return [
            'user_id' => User::factory(),
            'provider' => $provider['slug'],
            'provider_id' => (string) fake()->randomNumber(8),
            'data' => $this->generateProviderData(),
            'token' => fake()->uuid(),
            'refresh_token' => fake()->uuid(),
            'expires_at' => fake()->dateTimeBetween('+1 day', '+1 week'),
        ];
    }

    /**
     * Indicate that the connection is for GitHub.
     */
    public function withProvider(string $provider): self
    {
        return $this->state(fn (array $attributes): array => [
            'provider' => $provider,
            'data' => $this->generateProviderData(),
        ]);
    }

    /**
     * Indicate that the connection has an expired token.
     */
    public function expired(): self
    {
        return $this->state(fn (array $attributes): array => [
            'token' => null,
            'refresh_token' => null,
        ]);
    }

    /**
     * Generate the data for the provider.
     *
     * @return array<string, mixed>
     */
    private function generateProviderData(): array
    {
        return [
            'login' => fake()->userName(),
            'id' => fake()->randomNumber(8),
            'node_id' => 'MDQ6VXNlcj'.fake()->regexify('[A-Za-z0-9]{8}'),
            'avatar_url' => fake()->imageUrl(),
            'gravatar_id' => '',
            'url' => 'https://api.github.com/users/'.fake()->userName(),
            'html_url' => 'https://github.com/'.fake()->userName(),
            'name' => fake()->name(),
            'company' => '@'.fake()->company(),
            'blog' => fake()->url(),
            'location' => fake()->city().', '.fake()->country(),
            'email' => fake()->safeEmail(),
            'hireable' => fake()->boolean(),
            'bio' => fake()->sentence(),
            'twitter_username' => fake()->userName(),
            'public_repos' => fake()->numberBetween(0, 100),
            'public_gists' => fake()->numberBetween(0, 50),
            'followers' => fake()->numberBetween(0, 1000),
            'following' => fake()->numberBetween(0, 1000),
            'created_at' => fake()->dateTimeThisDecade()->format('Y-m-d\TH:i:s\Z'),
            'updated_at' => fake()->dateTimeThisYear()->format('Y-m-d\TH:i:s\Z'),
        ];
    }
}
