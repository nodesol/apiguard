<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Nodesol\Apiguard\UserService;

function setRequestHeaders(array $headers): void
{
    foreach ($headers as $name => $value) {
        request()->headers->set($name, $value);
    }
}

test('hashToken returns null for null and empty tokens', function () {
    $service = new UserService();

    expect($service->hashToken(null))->toBeNull();
    expect($service->hashToken(''))->toBeNull();
});

test('hashToken hashes a plain token', function () {
    $service = new UserService();

    expect($service->hashToken('plain-token'))
        ->toBe(hash('sha256', hash('sha256', 'plain-token')));
});

test('hashToken strips the id|token form before hashing', function () {
    $service = new UserService();

    expect($service->hashToken('42|real-token'))
        ->toBe(hash('sha256', hash('sha256', 'real-token')));
});

test('getUser returns cached tokenable data from the configured cache store without an HTTP call', function () {
    setRequestHeaders([
        'Authorization' => 'Bearer cached-token',
        'X-COMPANY-ID' => '7',
    ]);

    $key = 'auth_token_7_'.hash('sha256', hash('sha256', 'Bearer cached-token'));

    Cache::store('array')->put($key, ['tokenable' => ['id' => 7, 'name' => 'Cached User']], 60);

    Http::fake();

    $user = (new UserService)->getUser();

    expect($user)->toBe(['id' => 7, 'name' => 'Cached User']);

    Http::assertNothingSent();
});

test('getUser posts to the configured api_url with forwarded headers and walks user_column', function () {
    setRequestHeaders([
        'Authorization' => 'Bearer fresh-token',
        'X-COMPANY-ID' => '42',
    ]);

    config([
        'apiguard.api_url' => 'https://auth.example.com/me',
        'apiguard.user_column' => 'data.user',
    ]);

    Http::fake([
        'https://auth.example.com/me' => Http::response([
            'data' => [
                'user' => ['id' => 99, 'name' => 'API User', 'email' => 'api@example.com'],
            ],
        ], 200),
    ]);

    $user = (new UserService)->getUser();

    expect($user)->toBe(['id' => 99, 'name' => 'API User', 'email' => 'api@example.com']);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://auth.example.com/me'
            && $request->header('Authorization')[0] === 'Bearer fresh-token'
            && $request->header('X-COMPANY-ID')[0] === '42'
            && $request->hasHeader('Accept', 'application/json');
    });
});

test('getUser throws when the auth API response fails', function () {
    setRequestHeaders([
        'Authorization' => 'Bearer bad-token',
        'X-COMPANY-ID' => '42',
    ]);

    config([
        'apiguard.api_url' => 'https://auth.example.com/me',
        'apiguard.user_column' => 'data.user',
    ]);

    Http::fake([
        'https://auth.example.com/me' => Http::response(['error' => 'unauthenticated'], 401),
    ]);

    $thrown = null;

    try {
        (new UserService)->getUser();
    } catch (\Throwable $e) {
        $thrown = $e;
    }

    expect($thrown)->toBeInstanceOf(\Exception::class);
    expect($thrown->getMessage())->toBe('Auth API Request Failed');
});
