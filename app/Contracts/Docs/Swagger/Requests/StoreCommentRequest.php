<?php
namespace App\Contracts\Docs\Swagger\Requests;

/**
 * @OA\Schema(
 *     schema="StoreCommentRequest",
 *     type="object",
 *     title="Store Comment Request",
 *     description="Request schema for storing a new comment",
 *     required={"project_id", "text"},
 *     @OA\Property(
 *         property="",
 *         type="integer",
 *         example=1,
 *         description="ID of the associated project"
 *     ),
 *     
 *     @OA\Property(
 *         property="text",
 *         type="string",
 *         maxLength=2000,
 *         example="This is a comment text",
 *         description="Text content of the comment"
 *     ),
 *     
 *     @OA\Property(
 *         property="parent_id",
 *         type="integer",
 *         example=5,
 *         description="ID of the parent comment, if replying to another comment"
 *     ),
 *     
 *     @OA\Property(
 *         property="status_id",
 *         type="integer",
 *         example=2,
 *         description="Status ID for the comment"
 *     ),
 *     
 *     @OA\Property(
 *         property="url",
 *         type="string",
 *         maxLength=2000,
 *         example="https://example.com/page",
 *         description="URL associated with the comment"
 *     ),
 *     
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         maxLength=2000,
 *         example="Comment Title",
 *         description="Optional title for the comment"
 *     ),
 *     
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         enum={"text", "media"},
 *         example="text",
 *         description="Type of the comment, either text or media"
 *     ),
 *     
 *     @OA\Property(
 *         property="selector_start",
 *         type="string",
 *         example="start_selector_value",
 *         description="Start selector for referencing the specific content area"
 *     ),
 *     
 *     @OA\Property(
 *         property="selector_end",
 *         type="string",
 *         example="end_selector_value",
 *         description="End selector for referencing the specific content area"
 *     ),
 *     
 *     @OA\Property(
 *         property="fragment",
 *         type="string",
 *         example="fragment_value",
 *         description="Content fragment for selection"
 *     ),
 *     
 *     @OA\Property(
 *         property="index_start",
 *         type="integer",
 *         example=0,
 *         description="Start index for the selected content area"
 *     ),
 *     
 *     @OA\Property(
 *         property="index_end",
 *         type="integer",
 *         example=100,
 *         description="End index for the selected content area"
 *     ),
 *     
 *     @OA\Property(
 *         property="screen",
 *         type="string",
 *         maxLength=255,
 *         example="1920x1080",
 *         description="Screen resolution at the time of commenting"
 *     ),
 *     
 *     @OA\Property(
 *         property="viewport",
 *         type="string",
 *         maxLength=255,
 *         example="1280x720",
 *         description="Viewport size at the time of commenting"
 *     )
 * )
 */
interface StoreCommentRequest
{
}