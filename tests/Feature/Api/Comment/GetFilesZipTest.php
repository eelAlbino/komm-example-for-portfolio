<?php
namespace Tests\Feature\Api\Comment;

use App\Models\Comment;
use App\Models\CommentAttachmentArchive;
use App\Models\User;
use App\Models\Project;
use APP\Models\ProjectUser;
use App\Services\Comments\AttachmentArchiveService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Mockery;
use Orchid\Attachment\File;

class GetFilesZipTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed');
    }

    /**
     * Test the getFilesZip method with authentication.
     *
     * @return void
     */
    public function testGetFilesZipAuthenticated()
    {
        // Create User, ProjectUser and Project for test
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $projectUser = ProjectUser::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project->id
        ]);

        // Create Comment.
        $comment = Comment::factory()->create([
            'created_by' => $user->id,
            'project_id' => $project->id
        ]);

        // Create comment attachment
        $comment->uploadFile(
            UploadedFile::fake()->create(
                'document.pdf',
                1024 // 1kb
            )
        );

//         // Create CommentAttachmentArchive
//         $archive = CommentAttachmentArchive::factory()->create([
//             'comment_id' => $comment->id
//         ]);

//         // Create a mock File
//         $mockFileData = [
//             'original_name' => "comment_{$comment->id}_{$archive->id}.zip",
//             'url' => 'https://example.com/archive.zip',
//             'extension' => 'zip',
//             'size' => 1024
//         ];
//         $mockFile = Mockery::mock(File::class);
//         $mockFile->shouldReceive('only')->andReturn($mockFileData);

//         // Add mock on attachment for archive element
//         $archive = Mockery::mock($archive)->makePartial();
//         $archive->attachment = collect([
//             $mockFile
//         ]);

//         // Replace the service implementation with a mock.
//         $mockService = Mockery::mock(AttachmentArchiveService::class);
//         $mockService->shouldReceive('get')->andReturn($archive);

//         // Bind the mock instance into the service container.
//         $this->app->bind(AttachmentArchiveService::class, function ($app) use ($mockService) {
//             return $mockService;
//         });

        // Auth user
        Auth::guard('api')->login($user);

        // Send a request to the getFilesZip method with authentication.
        $response = $this->getJson('/api/v1/comment/' . $comment->id . '/file/zip');

        $archiveElement = CommentAttachmentArchive::where([
            'comment_id' => $comment->id
        ])->first();

        // Ensure that the response contains a successful status and the expected data.
        $response->assertStatus(Response::HTTP_OK)->assertJson([
            'status' => 'success',
            'filling_status' => CommentAttachmentArchive::STATUS_FILLED,
            'archive' => $archiveElement->archive_attachment->only(['original_name', 'url', 'extension', 'size'])
        ]);

        // Clean up. Delete attachments and zip
        $archiveElement->delete();
        foreach ($comment->attachmentRecursive() as $item) {
            unlink(Storage::disk($item->disk)->path($item->physicalPath()));
        }
    }

    /**
     * Test the getFilesZip method without authentication.
     *
     * @return void
     */
    public function testGetFilesZipUnauthenticated()
    {
        // Create Comment.
        $comment = Comment::factory()->create();

        // Send a request to the getFilesZip method without authentication.
        $response = $this->getJson('/api/v1/comment/' . $comment->id . '/file/zip');

        // Ensure that the response status is 401
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        // Create and auth by user who is not the owner of the comment
        $user = User::factory()->create();
        Auth::guard('api')->login($user);

        // Send a request to the getFilesZip method
        $response = $this->getJson('/api/v1/comment/' . $comment->id . '/file/zip');

        // Ensure that the response status is 403
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
