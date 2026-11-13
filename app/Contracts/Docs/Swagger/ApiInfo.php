<?php
namespace App\Contracts\Docs\Swagger;

/**
 * @OA\Info(
 *     title="API v1",
 *     version="0.0.1",
 *     description="API documentation for application." 
 * ),
 *
 * @OA\SecurityScheme(
 *     securityScheme="BearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Use `Authorization: Bearer {token}` in the header"
 * ),
 *
 * @OA\Schema(
 *     schema="UnauthorizedResponse",
 *     @OA\Property(property="message", type="string", example="Unauthorized")
 * )
 */
interface ApiInfo
{
}
