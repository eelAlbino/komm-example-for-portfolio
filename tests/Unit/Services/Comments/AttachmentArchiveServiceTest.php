<?php
namespace Tests\Unit\Services\Comments;

use App\Models\Comment;
use App\Models\CommentAttachmentArchive;
use App\Services\Comments\AttachmentArchiveService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Mockery;
use Orchid\Attachment\File;
use Orchid\Attachment\Models\Attachment;
use Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use ZipArchive;
use ReflectionObject;
use ReflectionMethod;

class AttachmentArchiveServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed');
    }

    /**
     * No attachments are found.
     */
    public function testNoAttachmentsFound()
    {
        // Create comment without attachment
        $comment = Comment::factory()->create();
        $service = new AttachmentArchiveService($comment);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('No files found');
        $service->get();
    }

    /**
     * Retrieving an existing attachment archive.
     */
    public function testGetExistingAttachmentArchive()
    {
        // Create a comment
        $comment = Comment::factory()->create();

        // Create a mock for attachment file
        $mockFile = Mockery::mock(File::class);
        $mockFile->id = 123;
        
        // Set collection with mock file to comment attachment
        $comment = Mockery::mock($comment)->makePartial();
        $comment->attachment = Collection::make([
            $mockFile
        ]);

        // Create service with comment
        $service = new AttachmentArchiveService($comment);
        $reflectionService = new ReflectionObject($service);

        // Get control sum value from private method contentControlSum
        $method = $reflectionService->getMethod('contentControlSum');
        $method->setAccessible(true);
        $contentControlSum = $method->invoke($service, $comment->attachment);

        // Create archive
        $archive = CommentAttachmentArchive::factory()->create([
            'comment_id' => $comment->id,
            'filter_hash' => null,
            'content_control_sum' => $contentControlSum
        ]);

        // Call the get method
        $retrievedArchive = $service->get();

        // Check if the retrieved archive matches the existing one
        $this->assertInstanceOf(CommentAttachmentArchive::class, $retrievedArchive);
        $this->assertEquals($archive->id, $retrievedArchive->id);
    }

    /**
     * Сreating archive file.
     */
    public function testCreateArchiveFile()
    {
        // Create a comment
        $comment = Comment::factory()->create();

        $archiveId = 1;
        $service = new AttachmentArchiveService($comment);

        $reflectionService = new ReflectionObject($service);

        // Reflection createAttachmentFile method
        $createAttachmentFile = $reflectionService->getMethod('createAttachmentFile');
        $createAttachmentFile->setAccessible(true);

        // Create attachment collection
        $attachments = Collection::make([
            $createAttachmentFile->invoke($service, $this->createTestFile('test1.txt', 'Content 1 (testCreateArchive)')),
            $createAttachmentFile->invoke($service, $this->createTestFile('test2.txt', 'Content 2 (testCreateArchive)')),
        ]);

        // Reflection createArchive method
        $createArchive = $reflectionService->getMethod('createArchive');
        $createArchive->setAccessible(true);

        // Call method
        $zipPath = $createArchive->invoke($service, $comment->id, $archiveId, new Collection($attachments));

        // Assert
        $this->assertNotNull($zipPath);
        $this->assertFileExists($zipPath);

        // Clean up. Delete attachments and zip
        foreach ($attachments as $file) {
            $this->deleteTestFile($file->original_name);
            $file->delete();
        }
        unlink($zipPath);
    }

    /**
     * Creating CommentAttachmentArchive
     */
    public function testCreateCommentAttachmentArchive()
    {
        // Create comment
        $comment = Comment::factory()->create();

        // Create service object with reflection
        $service = new AttachmentArchiveService($comment);
        $reflectionService = new ReflectionObject($service);

        // Reflection createAttachmentFile method
        $createAttachmentFile = $reflectionService->getMethod('createAttachmentFile');
        $createAttachmentFile->setAccessible(true);

        // Create attachment collection
        $attachment = Collection::make([
            $createAttachmentFile->invoke($service, $this->createTestFile('test1.txt', 'Content (testCreate)')),
        ]);

        // Reflection createArchive method
        $create = $reflectionService->getMethod('create');
        $create->setAccessible(true);

        // Call method
        $archive = $create->invoke($service, $comment, $attachment, null, 'test_content_control_sum');

        // Assert
        $this->assertInstanceOf(CommentAttachmentArchive::class, $archive);
        $this->assertDatabaseHas('comment_attachment_archives', [
            'id' => $archive->id
        ]);

        // Clean up. Delete attachments and archive
        foreach ($attachment as $file) {
            $this->deleteTestFile($file->original_name);
            $file->delete();
        }
        foreach ($archive->attachment as $file) {
            $file->delete();
        }
    }
    
    /**
     * Create a test file
     * @param string $filename
     * @param string $content
     * @return string|null
     */
    private function createTestFile(string $filename, string $content): ?string
    {
        $filePath = storage_path('app/' . $filename);
        file_put_contents($filePath, $content);
        return $filePath;
    }

    /**
     * Delete a test file
     * @param string $filename
     * @param string $content
     * @return null
     */
    private function deleteTestFile(string $filename): void
    {
        $filePath = storage_path('app/' . $filename);
        unlink($filePath);
    }

}
