<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProjectRole;

class ProjectRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Administrator
        ProjectRole::firstOrCreate([
            'slug' => 'admin'
        ],[
            'name' => __('seeders.project_role.admin'),
            'weight' => 300,
            'permissions' => $this->getAllowPermissions([
                'project.add',
                'project.edit',
                'comment.add',
                'comment.edit',
                'comment.status',
                'comment.file',
                'comment.private',
                'task.add',
                'task.edit',
                'project_user.add',
                'project_user.edit',
                'integration.add',
                'integration.edit',
                'subscription.add',
                'subscription.edit',
                'invite.add',
                'invite.edit',
            ])
        ]);
        // Manager
        ProjectRole::firstOrCreate([
            'slug' => 'manager',
        ], [
            'name' => __('seeders.project_role.manager'),
            'weight' => 200,
            'permissions' => $this->getAllowPermissions([
                'project.add',
                'project.edit',
                'comment.add',
                'comment.edit',
                'comment.status',
                'comment.file',
                'comment.private',
                'task.add',
                'task.edit',
                'project_user.add',
                'project_user.edit',
                'invite.add',
                'invite.edit',
            ])
        ]);
        // Commenter
        ProjectRole::firstOrCreate([
            'slug' => 'commenter',
        ], [
            'name' => __('seeders.project_role.commenter'),
            'weight' => 100,
            'permissions' => $this->getAllowPermissions([
                'comment.add',
                'comment.edit',
                'comment.file',
                'project_user.add',
                'invite.add',
                'invite.edit',
            ])
        ]);
    }

    /**
     * Allow permissions from config.
     * 
     * @param array $permissionCodes
     * @return array
     */
    private function getAllowPermissions(array $permissionCodes): array
    {
        $result = [];
        $permissions = config('app.project_permissions', []);
        foreach ($permissionCodes as $code) {
            if (isset($permissions[ $code ])) {
                $result[]= $code;
            }
        }
        return $result;
    }
}
