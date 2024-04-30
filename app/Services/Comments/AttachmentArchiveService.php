<?php
namespace App\Services\Comments;

use App\Models\Comment;
use App\Models\CommentAttachmentArchive;
use App\Jobs\FillCommentAttachmentArchiveJob;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Orchid\Attachment\File;
use Orchid\Attachment\Models\Attachment;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * The service is designed to create zip archives with all comment attachment files.
 */
class AttachmentArchiveService
{

    private Comment $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Retrieve or create attachment archive for a comment
     *
     * @param array $attachmentFilter
     * @throws HttpException
     * @return CommentAttachmentArchive|NULL
     */
    public function get(?array $attachmentFilter = null): ?CommentAttachmentArchive
    {
        // Get attachments for the comment
        $attachment = $this->comment->attachmentRecursive($attachmentFilter);

        // Throw exception if no attachments found
        if ($attachment->isEmpty()) {
            throw new HttpException(422, 'No files found');
        }

        $filterHash = $this->filterHash($attachmentFilter);
        $contentControlSum = $this->contentControlSum($attachment);

        // Check if an attachment archive already exists
        $archive = CommentAttachmentArchive::where([
            'comment_id' => $this->comment->id,
            'filter_hash' => $filterHash
        ])->first();

        // Delete archive if content doesn't match
        if ($archive && $archive->content_control_sum != $contentControlSum) {
            $archive->delete();
            $archive = null;
        }

        // Create a new attachment archive if not exists and start fill attachment to archive
        if (!$archive) {
            $archive = $this->create($this->comment, $filterHash, $contentControlSum);
            // Execute job
            (new FillCommentAttachmentArchiveJob($archive->id, 20))
                ->setCheckEstimatedAddTime(true)
                ->setAttachments($attachment)
                ->handle();
        }

        if ($archive) {
            $archive->load('attachment');
            $archive->refresh();
            return $archive;
        }

        return null;
    }

    /**
     * Generate filter hash.
     *
     * @param array|null $attachmentFilter
     * @return string|null
     */
    private function filterHash(?array $attachmentFilter = null): ?string
    {
        return $attachmentFilter ? sha1(serialize($attachmentFilter)) : null;
    }

    /**
     * Generate control sum for the attachments
     *
     * @param Collection $attachment
     * @return string
     */
    private function contentControlSum(Collection $attachment): string
    {
        return sha1(serialize($attachment->pluck('id')->toArray()));
    }

    /**
     * Create a new attachment archive
     *
     * @param Comment $comment
     * @param string|null $filterHash
     * @param string $contentControlSum
     * @return CommentAttachmentArchive|NULL
     */
    private function create(
        Comment $comment,
        ?string $filterHash = null,
        string $contentControlSum
    ): ?CommentAttachmentArchive
    {
        // Create a new attachment archive record
        $archive = CommentAttachmentArchive::create([
            'comment_id' => $comment->id,
            'filter_hash' => $filterHash,
            'content_control_sum' => $contentControlSum
        ]);
        return $archive;
    }

    /**
     * Add empty zip file as attachment to element.
     * 
     * @param CommentAttachmentArchive $archive
     * @param string $zipPath
     */
    public function addZipAttachmentFile(CommentAttachmentArchive $archive, string $zipPath) {
        DB::transaction(function () use ($archive, $zipPath) {
            $zipFilename = 'comment_' . $archive->comment_id . '_' . $archive->id . '.zip';
            //Create attachment file for archive element
            $attachmentFile = $this->createAttachmentFile($zipPath, $zipFilename);

            // Attach the file to the archive element
            $archive->attachment()->syncWithoutDetaching([
                $attachmentFile->id
            ]);
            $archive->saveQuietly();
        });
        $archive->refresh();
    }

    /**
     * Create attachment file object by path
     *
     * @param string $path
     * @param string $fileName
     * @return Attachment
     */
    private function createAttachmentFile($path, string $fileName): Attachment
    {
        // Create uploaded file instance from archive path
        $uploadedFile = new UploadedFile(
            $path,
            $fileName,
            'application/zip',
            filesize($path),
            false,
            true
        );

        // Create File with loading archive
        $attachment = (new File($uploadedFile))->load();

        return $attachment;
    }
}
