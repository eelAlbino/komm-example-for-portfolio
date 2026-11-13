<?php
namespace App\Contracts\Docs\Swagger\Models;

/**
 * @OA\Schema(
 *     schema="AttachmentBase",
 *     type="object",
 *     title="Attachment Base",
 *     description="Base properties for the Attachment model",
 *     @OA\Property(
 *         property="extension",
 *         type="string",
 *         example="jpg",
 *         description="File extension"
 *     ),
 *     @OA\Property(
 *         property="size",
 *         type="integer",
 *         example=102400,
 *         description="Size of the file in bytes"
 *     ),
 * ),
 *
 * @OA\Schema(
 *     schema="AttachmentFillable",
 *     type="object",
 *     title="Attachment Fillable",
 *     description="Fillable properties for the Attachment model",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/AttachmentBase"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 example="file.jpg",
 *                 description="Original name of the uploaded file"
 *             ),
 *             @OA\Property(
 *                 property="path",
 *                 type="string",
 *                 example="uploads/attachments/2024/01/file.jpg",
 *                 description="Path to the file on the server"
 *             ),
 *             @OA\Property(
 *                 property="mime",
 *                 type="string",
 *                 example="image/jpeg",
 *                 description="MIME type of the file"
 *             ),
 *             @OA\Property(
 *                 property="alt",
 *                 type="string",
 *                 example="Alternative text for the image",
 *                 description="Alternative text for the attachment, used for images"
 *             ),
 *             @OA\Property(
 *                 property="description",
 *                 type="string",
 *                 example="Sample file description",
 *                 description="Description of the attachment"
 *             ),
 *             @OA\Property(
 *                 property="user_id",
 *                 type="integer",
 *                 example=1,
 *                 description="ID of the user who uploaded the file"
 *             )
 *         )
 *     }
 * ),
 *
 * @OA\Schema(
 *     schema="AttachmentArchive",
 *     type="object",
 *     title="Attachment Archive",
 *     description="Properties of attachment archive",
 *     allOf={
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="original_name",
 *                 type="string",
 *                 example="example.jpg",
 *                 description="Original file name"
 *             ),
 *             @OA\Property(
 *                 property="url",
 *                 type="string",
 *                 example="https://local.test/example.jpg",
 *                 description="Link to file"
 *             )
 *         ),
 *         @OA\Schema(ref="#/components/schemas/AttachmentBase")
 *     }
 * ),
 *
 * @OA\Schema(
 *     schema="AttachmentPreview",
 *     type="object",
 *     title="Attachment Preview",
 *     description="Preview properties of the Attachment model",
 *     allOf={
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="id",
 *                 type="integer",
 *                 example=1,
 *                 description="Unique identifier of the attachment"
 *             ),
 *             @OA\Property(
 *                 property="original_name",
 *                 type="string",
 *                 example="example.jpg",
 *                 description="Original file name"
 *             ),
 *             @OA\Property(
 *                 property="url",
 *                 type="string",
 *                 example="https://local.test/example.jpg",
 *                 description="Link to file"
 *             ),
 *             @OA\Property(
 *                 property="cover",
 *                 type="string",
 *                 example="https://local.test/example-cover.jpg",
 *                 description="Link to cover, if file is image"
 *             ),
 *             @OA\Property(
 *                 property="is_media",
 *                 type="boolean",
 *                 example="true",
 *                 description="File is media: image or video"
 *             )
 *         ),
 *         @OA\Schema(ref="#/components/schemas/AttachmentBase")
 *     }
 * ),
 *
 * @OA\Schema(
 *     schema="Attachment",
 *     type="object",
 *     title="Attachment ",
 *     description="Basic properties of the Attachment model",
 *     allOf={
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="id",
 *                 type="integer",
 *                 example=1,
 *                 description="Unique identifier of the attachment"
 *             ),
 *             @OA\Property(
 *                 property="created_at",
 *                 type="string",
 *                 format="date-time",
 *                 example="2024-01-01T00:00:00Z",
 *                 description="Timestamp when the attachment was created"
 *             ),
 *             @OA\Property(
 *                 property="updated_at",
 *                 type="string",
 *                 format="date-time",
 *                 example="2024-01-01T00:00:00Z",
 *                 description="Timestamp when the attachment was last updated"
 *             )
 *         ),
 *         @OA\Schema(ref="#/components/schemas/AttachmentFillable")
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="AttachmentExpanded",
 *     type="object",
 *     title="Attachment Expanded",
 *     description="Expanded properties of the Attachment model",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/Attachment")
 *     },
 *     @OA\Property(
 *         property="url",
 *         type="string",
 *         format="url",
 *         example="https://example.com/uploads/attachments/2024/01/file.jpg",
 *         description="Full URL to access the attachment"
 *     ),
 *     @OA\Property(
 *         property="thumbnail",
 *         type="string",
 *         format="url",
 *         example="https://example.com/uploads/attachments/2024/01/thumbnail_file.jpg",
 *         description="URL for the thumbnail version of the file, if available"
 *     )
 * )
 */
interface Attachment
{
}