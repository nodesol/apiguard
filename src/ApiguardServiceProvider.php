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
        $this->mergeConfigFrom(
            __DIR__.'/../config/apiguard.php', 'apiguard'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->bootConfig();
        $this->createDriver();
        $this->registerRuntimeGuard();
    }

    protected function createDriver(): void
    {
        Auth::viaRequest(config('apiguard.guard_name'), function () {
            // throw new Exception("TEST");
            try {
                $service = new UserService();
                $data = $service->getUser();
                $model = config("apiguard.user_model");
                $user = new $model();
                $user->forceFill($data);
                $user->exists = true;
                return $user;
            } catch (\Exception $e) {
                return null;
            }
        });
    }

    protected function registerRuntimeGuard(): void
    {
        config([
            'auth.guards.apiguard' => [
                'driver' => 'apiguard',
                'provider' => 'apiguard-users',
            ],
        ]);

        config([
            'auth.providers.apiguard-users' => [
                'driver' => 'eloquent',
                'model'  => config("apiguard.user_model"),
            ],
        ]);
    }

    protected function bootConfig() {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/apiguard.php' => config_path('apiguard.php'),
            ], 'apiguard-config');
        }
    }
}
