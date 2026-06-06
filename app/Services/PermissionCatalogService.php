<?php

namespace App\Services;

use Illuminate\Support\Collection;

class PermissionCatalogService
{
    /**
     * @return array<int, array{key: string, label: string, icon: string, permissions: Collection}>
     */
    public function getGrouped(Collection $permissions): array
    {
        $groupsConfig = config('permissions.groups', []);
        $labels = config('permissions.labels', []);
        $assigned = [];
        $groups = [];

        foreach ($groupsConfig as $key => $group) {
            $items = $permissions->filter(
                fn ($permission) => in_array($permission->name, $group['permissions'], true)
            );

            if ($items->isEmpty()) {
                continue;
            }

            $groups[] = [
                'key' => $key,
                'label' => $group['label'],
                'icon' => $group['icon'] ?? 'fas fa-key',
                'permissions' => $items->map(fn ($permission) => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'label' => $labels[$permission->name] ?? $permission->name,
                ])->values(),
            ];

            foreach ($items as $permission) {
                $assigned[] = $permission->name;
            }
        }

        $uncategorized = $permissions->filter(
            fn ($permission) => ! in_array($permission->name, $assigned, true)
        );

        if ($uncategorized->isNotEmpty()) {
            $groups[] = [
                'key' => 'other',
                'label' => 'صلاحيات أخرى',
                'icon' => 'fas fa-ellipsis-h',
                'permissions' => $uncategorized->map(fn ($permission) => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'label' => $labels[$permission->name] ?? $permission->name,
                ])->values(),
            ];
        }

        return $groups;
    }

    public function getTotalCount(Collection $permissions): int
    {
        return $permissions->count();
    }
}
