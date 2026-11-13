<?php
namespace App\Contracts\Docs\Swagger\Models;

/**
 * @OA\Schema(
 *     schema="CommentAttachmentArchiveStatus",
 *     type="string",
 *     enum={"init", "filling", "filled"},
 *     example="filled",
 *     description="Archive status"
 * ),
 *  
 * @OA\Schema(
 *     schema="CommentAttachmentArchiveFillable",
 *     type="object",
 *     title="CommentAttachmentArchive Fillable",
 *     description="Fillable properties for the CommentAttachmentArchive model",
 *     @OA\Property(
 *         property="comment_id",
 *         type="integer",
 *         example=1,
 *         description="ID of the associated comment"
 *     ),
 *     @OA\Property(
 *         property="filter_hash",
 *         type="string",
 *         example="f62f64bc394c8542dbc3aeeb565b6a723e083594",
 *         description="Hash sum from filter for getting attachment items"
 *     ),
 *     @OA\Property(
 *         property="content_control_sum",
 *         type="string",
 *         example="52de83fbca13087b61f9c067dd8c7bf965956156",
 *         description="control sum for the attachments in archive"
 *     ),
 *     @OA\Property(
 *         property="showed_at",
 *         type="string",
 *         format="date-time",
 *         example="2024-01-01T00:00:00Z",
 *         description="Time when the archive get the status 'filled'"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         ref="#/components/schemas/CommentAttachmentArchiveStatus"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="CommentAttachmentArchive",
 *     type="object",
 *     title="CommentAttachmentArchive",
 *     description="Basic properties of the CommentAttachmentArchive model",
 *     allOf={
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="id",
 *                 type="integer",
 *                 example=1,
 *                 description="Unique identifier of the element"
 *             ),
 *             @OA\Property(
 *                 property="created_at",
 *                 type="string",
 *                 format="date-time",
 *                 example="2024-01-01T00:00:00Z",
 *                 description="Timestamp when the element was created"
 *             ),
 *             @OA\Property(
 *                 property="updated_at",
 *                 type="string",
 *                 format="date-time",
 *                 example="2024-01-01T00:00:00Z",
 *                 description="Timestamp when the element was last updated"
 *             )
 *         ),
 *         @OA\Schema(ref="#/components/schemas/CommentAttachmentArchiveFillable")
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="CommentAttachmentArchiveExpanded",
 *     type="object",
 *     title="CommentAttachmentArchive Expanded",
 *     description="Expanded properties of the CommentAttachmentArchive model",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/CommentAttachmentArchive")
 *     },
 *     @OA\Property(
 *         property="archive_attachment",
 *         ref="#/components/schemas/Attachment",
 *         description="Archive attachment file"
 *     ),
 *     @OA\Property(
 *         property="comment",
 *         ref="#/components/schemas/Comment",
 *         description="Associated comment"
 *     )
 * )
 */
interface CommentAttachmentArchive
{
}