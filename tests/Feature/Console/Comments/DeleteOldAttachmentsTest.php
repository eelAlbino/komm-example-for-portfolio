<?php
namespace Tests\Feature\Console\Comments;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Orchid\Attachment\File;
use Tests\TestCase;
use App\Models\CommentAttachmentArchive;
use Carbon\Carbon;

class DeleteOldAttachmentsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed');
    }

    /** @test */
    public function it_deletes_old_attachments()
    {
        // Create old archive elem with zip archive
        $oldAttachment = CommentAttachmentArchive::factory()->create([
            'showed_at' => Carbon::now()->subDays(8) // 8 days ago
        ]);

        // Create old zip archive
        $oldAttachmentArchive = (
            new File(
                UploadedFile::fake()->create('old_archive.zip')
            )
        )->load();
        $oldAttachment->attachment()->syncWithoutDetaching([
            $oldAttachmentArchive->id
        ]);
        $oldZipPath = Storage::disk($oldAttachmentArchive->disk)->path($oldAttachmentArchive->physicalPath());

        // Create not old archive elem
        $recentAttachment = CommentAttachmentArchive::factory()->create([
            'showed_at' => Carbon::now()->subDays(1) // 1 day ago
        ]);

        // Call command
        $this->artisan('comments:delete-old-attachment-archives');

        // Check midding old elem
        $this->assertDatabaseMissing('comment_attachment_archives', [
            'id' => $oldAttachment->id
        ]);

        // Check zip file not exists
        $this->assertFileDoesNotExist($oldZipPath);

        // Check exist recent elem
        $this->assertDatabaseHas('comment_attachment_archives', [
            'id' => $recentAttachment->id
        ]);
    }
}
