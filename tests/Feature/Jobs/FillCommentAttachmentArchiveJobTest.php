<?php
namespace Tests\Feature\Jobs;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Comment;
use App\Models\CommentAttachmentArchive;
use App\Jobs\FillCommentAttachmentArchiveJob;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\UploadedFile;
use ZipArchive;

class FillCommentAttachmentArchiveJobTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed');
    }

    /**
     * Fill some archive in two stages.
     */
    public function testArchiveFillWithMultipleFiles()
    {
        // Create comment
        $comment = Comment::factory()->create();

        // Create attachments for comment
        $comment->uploadFile(
            UploadedFile::fake()->create(
                'small_document.pdf',
                1024 // 1kb
            )
        );
        $comment->uploadFile(
            UploadedFile::fake()->create(
                'big_document.pdf',
                1024*1024*10 // 10mb
            )
        );

        // Create archive element
        $archive = CommentAttachmentArchive::factory([
            'comment_id' => $comment->id
        ])->create();

        // Element haven't archive attachment
        $this->assertEquals(null, $archive->archive_attachment);

        // Set timeout 2 sec for fill only one file
        $timeoutSecs = 2;

        // Set queue.default config equal "redis". We use redis for queues
        Config::set('queue.default', 'redis');

        // Execute job
        (new FillCommentAttachmentArchiveJob(
                $archive->id,
                $timeoutSecs
            ))
            ->setCheckEstimatedAddTime(true)
            ->setAttachments($comment->attachment)
            ->handle();
        $archive->refresh();

        // Check that archive status changed to "filling"
        $this->assertEquals(CommentAttachmentArchive::STATUS_FILLING, $archive->status);

        // Execute second iteration through queue (redis)
        $this->artisan('queue:work', [
            '--once' => true
        ]);
        $archive->refresh();

        // Check that archive status changed to "filled"
        $this->assertEquals(CommentAttachmentArchive::STATUS_FILLED, $archive->status);

        // Check that archive attachment is exists
        $this->assertNotEmpty($archive->archive_attachment);

        $archiveAttachment = $archive->archive_attachment;
        // Zip archive path
        $zipPath = Storage::disk($archiveAttachment->disk)->path($archiveAttachment->physicalPath());

        // Check that archive file is exists
        $this->assertTrue(file_exists($zipPath));

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::RDONLY);

        // Check archive count
        $this->assertEquals(2, $zip->count());

        // Check archive content
        $this->assertEquals('small_document.pdf', $zip->statIndex(0)['name']);
        $this->assertEquals('big_document.pdf', $zip->statIndex(1)['name']);

        $zip->close();

        // Delete all created attachments
        $archiveAttachment->delete();
        foreach ($comment->attachmentRecursive() as $item) {
            unlink(Storage::disk($item->disk)->path($item->physicalPath()));
        }

    }
}