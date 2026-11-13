<?php

namespace App\Traits;

use App\Models\ProjectUser;

trait HasProjectPermissions
{
    /**
     * Determine whether user exists in project
     * 
     * @param int   $projectId
     * 
     * @return bool
     */
    public function isInProject($projectId) {
        return ProjectUser::where('user_id', $this->id)->where('project_id', $projectId)->count() > 0;
    }

    /**
     * Determine whether user has project role in project
     * 
     * @param int   $projectId
     * @param array $roles
     * 
     * @return bool
     */
    public function hasProjectRole($projectId, $roles) {
        $arProjectUserRoles = ProjectUser::where('user_id', $this->id)->where('project_id', $projectId)->get();

        foreach($arProjectUserRoles as $item) {
            if (in_array($item->project_role->slug, $roles)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether user hase project permissions in project
     * 
     * @param int   $projectId
     * @param array $permissions
     * 
     * @return bool
     */
    public function hasProjectPermissions($projectId, $permissions) {
        $arProjectUserRoles = ProjectUser::where('user_id', $this->id)->where('project_id', $projectId)->get();
        foreach($arProjectUserRoles as $item) {
            $projectRolePermissions = $item->project_role->permissions ?? [];
            if (count(array_intersect($projectRolePermissions, $permissions)) > 0) {
                return true;
            }
        }

        return false;
    }
}