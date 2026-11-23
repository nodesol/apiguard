<?php

test('apiguard config is merged with defaults', function () {
    expect(config()->has('apiguard.guard_name'))->toBeTrue();
    expect(config()->has('apiguard.api_url'))->toBeTrue();
    expect(config()->has('apiguard.forwarded_headers'))->toBeTrue();
    expect(config()->has('apiguard.user_column'))->toBeTrue();
    expect(config()->has('apiguard.user_model'))->toBeTrue();
    expect(config()->has('apiguard.post_data'))->toBeTrue();
    expect(config()->has('apiguard.cache_store'))->toBeTrue();
});

test('the apiguard guard and provider are registered at boot', function () {
    expect(config('auth.guards.apiguard'))->toBe([
        'driver' => 'apiguard',
        'provider' => 'apiguard-users',
    ]);

    expect(config('auth.providers.apiguard-users'))->toBe([
        'driver' => 'eloquent',
        'model' => Nodesol\Apiguard\Tests\TestUser::class,
    ]);

    expect(auth()->guard('apiguard'))->toBeInstanceOf(Illuminate\Auth\RequestGuard::class);
});
