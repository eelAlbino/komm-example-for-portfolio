<?php
namespace App\Contracts\Docs\Swagger\Models;

/**
 * @OA\Schema(
 *     schema="CommentStatusFillable",
 *     type="object",
 *     title="Comment Status Fillable",
 *     description="Fillable properties for the CommentStatus model",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         example="In progress",
 *         description="Name of the comment status"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         example="in-progress",
 *         description="Slug or unique identifier for the status"
 *     ),
 *     @OA\Property(
 *         property="color",
 *         type="string",
 *         example="#7300ff",
 *         description="Color associated with the comment status, in hex format"
 *     )
 * ),
 *
 * @OA\Schema(
 *     schema="CommentStatus",
 *     type="object",
 *     title="Comment Status ",
 *     description="Basic properties of CommentStatus model",
 *     allOf={
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="id",
 *                 type="integer",
 *                 example=1,
 *                 description="Unique identifier of the comment status"
 *             ),
 *             @OA\Property(
 *                 property="created_at",
 *                 type="string",
 *                 format="date-time",
 *                 example="2024-01-01T00:00:00Z",
 *                 description="Timestamp when the status was created"
 *             ),
 *             @OA\Property(
 *                 property="updated_at",
 *                 type="string",
 *                 format="date-time",
 *                 example="2024-01-01T00:00:00Z",
 *                 description="Timestamp when the status was last updated"
 *             )
 *         ),
 *         @OA\Schema(ref="#/components/schemas/CommentStatusFillable")
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="CommentStatusExpanded",
 *     type="object",
 *     title="Comment Status Expanded",
 *     description="Expanded properties of CommentStatus model with relationships",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/CommentStatus")
 *     },
 *     @OA\Property(
 *         property="comments",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Comment"),
 *         description="Comments associated with this status"
 *     )
 * )
 */
interface CommentStatus
{
}