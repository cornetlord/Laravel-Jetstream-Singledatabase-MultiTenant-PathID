<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Log;

class PrepareAuthenticatedTenantSession
{
    public function __invoke($request, $next)
    {
        $user = $request->user();

        if ($user && $user->tenant_id) {
            $tenant = \App\Models\Tenant::find($user->tenant_id);

            if ($tenant) {
                Log::info('Initializing tenancy after authentication', ['tenant_id' => $tenant->id]);
                tenancy()->initialize($tenant);
            } else {
                Log::warning('Tenant not found for user', ['tenant_id' => $user->tenant_id]);
            }
        } else {
            Log::warning('User does not have a tenant ID after authentication');
        }

        return $next($request);
    }
}
