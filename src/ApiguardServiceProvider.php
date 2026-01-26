<?php

namespace Nodesol\Apiguard;

// use App\Models\User;
// use App\Services\AuthApiService;
// use GraphQL\Error\UserError;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class ApiguardServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        \Log::info("GOT HERE");
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Log::info("GOT HERE");
        // Auth::viaRequest('api-token', function () {
            // throw new Exception("TEST");
            // $authApiService = new AuthApiService;
            // $response = $authApiService->getUser();

            // if (! $response || ! isset($response['data']['CurrentUser'])) {
            //     throw new UserError('Authentication failed: Invalid user data received from Auth API.');
            // }

            // $data = $response['data']['CurrentUser'];
            // $user = new User(...$data);
            // $user->fill($data);

            // return $user;
        // });
    }
}
