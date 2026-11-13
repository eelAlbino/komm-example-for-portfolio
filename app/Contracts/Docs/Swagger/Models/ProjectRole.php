<?php
namespace App\Contracts\Docs\Swagger\Models;

/**
 * @OA\Schema(
 *     schema="ProjectRoleFillable",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the project role",
 *         example="Admin"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="Slug of the project role",
 *         example="admin"
 *     ),
 *     @OA\Property(
 *         property="permissions",
 *         type="array",
 *         @OA\Items(type="string"),
 *         description="Permissions assigned to the role. Possible values include:
 *             - project.add: Create projects
 *             - project.edit: Edit/delete projects
 *             - comment.add: Create comments
 *             - comment.edit: Edit/delete comments
 *             - comment.status: Change comment status
 *             - comment.file: Attach files to comments
 *             - task.add: Create tasks
 *             - task.edit: Edit/delete tasks
 *             - project_user.add: Add users to project
 *             - project_user.edit: Edit/remove users from project
 *             - integration.add: Create integrations
 *             - integration.edit: Edit/delete integrations
 *             - subscription.add: Purchase subscriptions
 *             - subscription.edit: Edit/remove subscriptions
 *             - invite.add: Create invites
 *             - invite.edit: Edit/delete invites",
 *         example={"project.add", "project.edit", "comment.add"}
 *     ),
 *     @OA\Property(
 *         property="weight",
 *         type="integer",
 *         description="Priority weight of the role",
 *         example=10
 *     )
 * ),
 *
 * @OA\Schema(
 *     schema="ProjectRole",
 *     allOf={
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="id",
 *                 type="integer",
 *                 description="ID of the project role",
 *                 example=1
 *             ),
 *             @OA\Property(
 *                 property="created_at",
 *                 type="string",
 *                 format="date-time",
 *                 description="Timestamp when the role was created",
 *                 example="2023-01-15T08:23:45Z"
 *             ),
 *             @OA\Property(
 *                 property="updated_at",
 *                 type="string",
 *                 format="date-time",
 *                 description="Timestamp when the role was last updated",
 *                 example="2023-10-12T11:15:22Z"
 *             )
 *         ),
 *         @OA\Schema(ref="#/components/schemas/ProjectRoleFillable")
 *     }
 * ),
 *
 * @OA\Schema(
 *     schema="ProjectRoleExpanded",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/ProjectRole"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="projectUsers",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/ProjectUser"),
 *                 description="List of users associated with this role"
 *             )
 *         )
 *     }
 * )
 */
interface ProjectRole
{
}
