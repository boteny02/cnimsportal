<?php

namespace App\Concerns;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasRolesAndPermissions
{
    public function roles(): MorphToMany
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles');
    }

    public function permissions(): MorphToMany
    {
        return $this->morphToMany(Permission::class, 'model', 'model_has_permissions');
    }

    public function assignRole(Role|string ...$roles): self
    {
        foreach ($roles as $role) {
            if (is_string($role)) {
                $role = Role::firstOrCreate(
                    ['name' => $role],
                    ['display_name' => ucwords(str_replace('_', ' ', $role))]
                );
            }
            $this->roles()->syncWithoutDetaching([$role->id]);
        }

        $this->unsetRelation('roles');

        return $this;
    }

    public function removeRole(Role|string $role): self
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }

        if ($role) {
            $this->roles()->detach($role->id);
            $this->unsetRelation('roles');
        }

        return $this;
    }

    public function syncRoles(array $roles): self
    {
        $roleIds = [];
        foreach ($roles as $role) {
            if (is_string($role)) {
                $role = Role::firstOrCreate(
                    ['name' => $role],
                    ['display_name' => ucwords(str_replace('_', ' ', $role))]
                );
            }
            $roleIds[] = $role->id;
        }

        $this->roles()->sync($roleIds);
        $this->unsetRelation('roles');

        return $this;
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }

        return $this->roles->pluck('name')->intersect($roles)->isNotEmpty();
    }

    public function hasAnyRole(string ...$roles): bool
    {
        return $this->hasRole($roles);
    }

    public function hasPermissionTo(string $permission): bool
    {
        // Direct permission
        if ($this->permissions->contains('name', $permission)) {
            return true;
        }

        // Role permissions
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('name', $permission)) {
                return true;
            }
        }

        return false;
    }

    public function givePermissionTo(Permission|string ...$permissions): self
    {
        foreach ($permissions as $permission) {
            if (is_string($permission)) {
                $permission = Permission::firstOrCreate(
                    ['name' => $permission],
                    ['display_name' => ucwords(str_replace(['.', '_'], ' ', $permission))]
                );
            }
            $this->permissions()->syncWithoutDetaching([$permission->id]);
        }

        $this->unsetRelation('permissions');

        return $this;
    }
}
