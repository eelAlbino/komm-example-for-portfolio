<?php
namespace App\Services\Comments;

use App\Models\Comment;
use App\Models\CommentAttachmentArchive;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Orchid\Attachment\File;
use Orchid\Attachment\Models\Attachment;
use ZipArchive;

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

        // Create a new attachment archive if not exists
        if (!$archive) {
            $archive = $this->create($this->comment, $attachment, $filterHash, $contentControlSum);
        }

        if ($archive) {
            $archive->showed_at = now();
            $archive->save();
            $archive->load('attachment');
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
     * @param Collection $attachment
     * @param string|null $filterHash
     * @param string $contentControlSum
     * @return CommentAttachmentArchive|NULL
     */
    private function create(
        Comment $comment,
        Collection $attachment,
        ?string $filterHash = null,
        string $contentControlSum
    ): ?CommentAttachmentArchive
    {
        return DB::transaction(function () use ($comment, $attachment, $filterHash, $contentControlSum) {
            // Create a new attachment archive record
            $archive = CommentAttachmentArchive::create([
                'comment_id' => $comment->id,
                'filter_hash' => $filterHash,
                'content_control_sum' => $contentControlSum
            ]);

            // Create archive file
            $archivePath = $this->createArchive($comment->id, $archive->id, $attachment);
            $attachmentFile = $this->createAttachmentFile($archivePath);

            // Attach the file to the archive
            $archive->attachment()->syncWithoutDetaching([
                $attachmentFile->id
            ]);

            return $archive;
        });
    }

    /**
     * Create zip archive containing the attachments
     *
     * @param int $commentId
     * @param int $archiveId
     * @param Collection $attachment
     * @throws HttpException
     * @return string|NULL
     */
    private function createArchive(int $commentId, int $archiveId, Collection $attachment): ?string
    {
        $zipFilename = 'comment_' . $commentId . '_' . $archiveId . '.zip';
        $zipPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $zipFilename;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($attachment as $item) {
                // Add each attachment file to the zip archive
                $sourcePath = Storage::disk($item->disk)->path($item->physicalPath());
                $filename = $item->getTitleAttribute();
                $zip->addFile($sourcePath, $filename);
            }
            $zip->close();
            return $zipPath;
        } else {
            // Throw exception if zip archive creation failed
            throw new HttpException(422, 'Cannot create zip file by path ' . $zipPath);
        }
    }

    /**
     * Create attachment file object by path
     *
     * @param string $archivePath
     * @return Attachment
     */
    private function createAttachmentFile(string $archivePath): Attachment
    {
        // Create uploaded file instance from archive path
        $uploadedFile = new UploadedFile(
            $archivePath,
            basename($archivePath),
            mime_content_type($archivePath),
            filesize($archivePath),
            false,
            true
        );

        // Load file into attachment
        return (new File($uploadedFile))->load();
    }
}
