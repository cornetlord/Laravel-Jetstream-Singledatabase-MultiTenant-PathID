<?php

namespace App\Providers;

use App\Actions\Fortify\PrepareAuthenticatedTenantSession;
use App\Actions\Jetstream\DeleteUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePermissions();

        Jetstream::deleteUsersUsing(DeleteUser::class);

        Fortify::authenticateThrough(function () {
            return [
                EnsureLoginIsNotThrottled::class,
                RedirectIfTwoFactorAuthenticatable::class,
                AttemptToAuthenticate::class,
                PrepareAuthenticatedTenantSession::class, // Custom tenancy initialization
            ];
        });

        // Ensure tenancy is initialized for other Jetstream features
        Fortify::loginView(function () {
            if (auth()->check() && auth()->user()->tenant_id) {
                $tenant = \App\Models\Tenant::find(auth()->user()->tenant_id);

                if ($tenant) {
                    Log::info('Initializing tenancy in login view', ['tenant_id' => $tenant->id]);
                    tenancy()->initialize($tenant);
                }
            }

            return view('auth.login');
        });

        Fortify::registerView(function () {
            return view('auth.register');
        });
    }

    /**
     * Configure the permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::permissions([
            'create',
            'read',
            'update',
            'delete',
        ]);
    }
}
