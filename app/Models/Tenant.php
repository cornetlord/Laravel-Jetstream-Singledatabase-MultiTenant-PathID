<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Tenant extends BaseTenant
{
    protected $fillable = ['id', 'name'];

    public static function getCustomColumns(): array
    {
        return ['id', 'name']; // Include 'name' as a custom column
    }
    /**
     * Get the users associated with the tenant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'tenant_id', 'id');
    }
}
