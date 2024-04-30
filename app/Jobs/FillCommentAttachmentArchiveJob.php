<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use App\Models\CommentAttachmentArchive;
use App\Services\Comments\AttachmentArchiveService;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Orchid\Attachment\Models\Attachment;
use ZipArchive;
use Exception;

class FillCommentAttachmentArchiveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // ID of AttachmentArchiveService element
    protected int $archiveId;

    protected ?CommentAttachmentArchive $archive = null;

    protected ?Collection $attachments = null;

    // Archive filling time. If the time has been exceeded, created some new job to continue filling.
    protected int $timeoutSecs = 20;

    protected int $startTime = 0;

    // On before add file to archive check file size
    protected bool $checkEstimatedAddTime = false;

    /**
     * Create a new job instance.
     *
     * @param array $files
     */
    public function __construct(
        int $archiveId,
        ?int $timeoutSecs = null
    ) {
        $this->archiveId = $archiveId;
        if ($timeoutSecs > 0) {
            $this->timeoutSecs = $timeoutSecs;
        }
    }

    /**
     * Archive element.
     * @return CommentAttachmentArchive
     */
    private function archive(): CommentAttachmentArchive
    {
        if (!$this->archive) {
            $this->archive = CommentAttachmentArchive::findOrFail($this->archiveId);
        }
        return $this->archive;
    }

    /**
     * Return file path of archive element.
     *
     * @param CommentAttachmentArchive  $archiveElement
     * @return string|NULL
     */
    private function zipFilePath(CommentAttachmentArchive $archiveElement): ?string
    {
        if ($archiveAttachment = $archiveElement->archive_attachment) {
            return $this->attachmentPath($archiveAttachment);
        }
        return null;
    }

    /**
     * Get path by attachment
     * @param Attachment $attachment
     */
    private function attachmentPath(Attachment $attachment) {
        return Storage::disk($attachment->disk)->path($attachment->physicalPath());
    }

    /**
     * Recalc size of attachment file and save value.
     *
     * @param Attachment $attachment
     */
    private function recalcAttachmentSize(Attachment $attachment) {
        $attachment->fill([
            'size' => Storage::disk($attachment->disk)->size(
                $attachment->physicalPath()
            )
        ])->saveQuietly();
    }

    /**
     * Return ZipArchive object with zip file path.
     *
     * @param string $zipPath
     * @throws Exception
     * @return ZipArchive
     */
    private function openZip(string $zipPath): ZipArchive
    {
        $zip = new ZipArchive();
        if ($zipPath && $zip->open($zipPath, ZipArchive::CREATE) === true) {
            return $zip;
        }
        // Throw exception if zip archive creation failed
        throw new Exception('Cannot open zip file by path ' . $zipPath);
    }

    /**
     * Set attachment.
     *
     * @param Collection $attachments
     * @return self
     */
    public function setAttachments(Collection $attachments): self
    {
        $this->attachments = $attachments;
        return $this;
    }

    /**
     * Create temp elems in filling table on init filling archive.
     *
     * @param CommentAttachmentArchive $archive
     * @param Collection $attachments
     */
    private function createFillingItems(CommentAttachmentArchive $archive, Collection $attachments)
    {
        // Delete old elems if exists
        DB::table('comment_attachment_archives_filling')->where('archive_id', $archive->id)->delete();
        // Create elements for each attachment
        foreach ($attachments as $attachment) {
            DB::table('comment_attachment_archives_filling')->insert([
                'attachment_id' => $attachment->id, 'archive_id' => $archive->id
            ]);
        }
    }

    /**
     * Delete filling row.
     *
     * @param int $id
     */
    private function deleteFillingItem(int $id)
    {
        DB::table('comment_attachment_archives_filling')->where('id', $id)->delete();
    }

    /**
     * Get  Attachments for filling
     *
     * @param CommentAttachmentArchive $archiveElement
     * @param int $limit
     * @return Collection
     */
    private function getFillingAttachments(CommentAttachmentArchive $archiveElement, int $limit = 20): Collection
    {
        return Attachment::select('attachments.*', 'filling.id as filling_id')
            ->join('comment_attachment_archives_filling as filling', 'attachments.id', '=', 'filling.attachment_id')
            ->where([
                'filling.archive_id' => $archiveElement->id
            ])
            ->orderBy('attachments.id')
            ->limit($limit)
            ->get();
    }

    /**
     * Create empty zip archive.
     *
     * @param string $filename
     * @throws HttpException
     * @return ZipArchive
     */
    private function createTempZipArchive(string $filename): ZipArchive
    {
        $zip = new ZipArchive();
        if ($zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            return $zip;
        }
        // Throw exception if zip archive creation failed
        throw new HttpException(500, 'Cannot create zip file by path ' . $zipPath);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->startTime = time();
        $archiveElement = $this->archive();
        $tempZipFilePath = null;
        if ($zipPath = $this->zipFilePath($archiveElement)) {
            $zip = $this->openZip($zipPath);
        } else {
            // Temp name file
            $tempZipFilePath = tempnam(sys_get_temp_dir(), 'empty_archive');
            rename($tempZipFilePath, ($tempZipFilePath .= '.zip'));

            // Create zip file
            $zip = $this->createTempZipArchive($tempZipFilePath);
        }

        // If status is init, create filling items and set status filling
        if ($archiveElement->status == CommentAttachmentArchive::STATUS_INIT) {
            $attachments = $this->attachments;
            if (!$attachments) {
                $attachments = $archiveElement->commentAttachments();
            }
            $this->createFillingItems($archiveElement, $attachments);
            $archiveElement->fill([
                'status' => CommentAttachmentArchive::STATUS_FILLING
            ])->save();
        }

        if ($archiveElement->status == CommentAttachmentArchive::STATUS_FILLED) {
            throw new Exception('Archive ' . $archiveElement->id . ' already filled.');
        }
        $setFilled = false;
        do {
            //
            $fillingAttachments = $this->getFillingAttachments($archiveElement);
            if ($fillingAttachments->isEmpty()) {
                $setFilled = true;
                break;
            }
            foreach ($fillingAttachments as $item) {
                // Check estimated time for add file to archive
                if ($this->checkEstimatedAddTime) {
                    $estimatedTime = $this->estimatedFileAdditionTime(
                        $item->size
                    );

                    if ($estimatedTime && !$this->checkTimeout($estimatedTime)) {
                        break 2;
                    }
                }
                // Add each attachment file to the zip archive
                $sourcePath = Storage::disk($item->disk)->path($item->physicalPath());
                $filename = $item->getTitleAttribute();
                $zip->addFile($sourcePath, $filename);
                // Delete filling row for added file
                $this->deleteFillingItem($item->filling_id);
                // Check timeout
                if (!$this->checkTimeout()) {
                    break 2;
                }
            }
        } while ($this->checkTimeout());

        // Close and save archive file
        if ($zip->count()) {
            $zip->close();
        }
        if ($tempZipFilePath) {
            app(AttachmentArchiveService::class)->addZipAttachmentFile($archiveElement, $tempZipFilePath);
        }
        // Recalc archive size
        $this->recalcAttachmentSize($archiveElement->archive_attachment);

        // If archive has filled, change status
        if ($setFilled) {
            $archiveElement->fill([
                'status' => CommentAttachmentArchive::STATUS_FILLED
            ])->save();
        } else {
            // Create new job for continue
            $this->createContinuingJob();
        }
    }

    /**
     * Create new job for continue
     */
    private function createContinuingJob() {
        self::dispatch($this->archiveId, $this->timeoutSecs);
    }

    /**
     * Checks how much time has passed since started handle executing and whether the limit has been exceeded.
     *
     * @return bool
     */
    private function checkTimeout(int $additTime = 0): bool
    {
        return (time() + $additTime - $this->startTime) < $this->timeoutSecs;
    }

    /**
     * Estimated time of add file to archive, in secs. Min 1 sec
     *
     * @param int $fileSizeInBytes
     * @param number $diskWriteSpeed
     * @return int
     */
    private function estimatedFileAdditionTime(
        int $fileSizeInBytes,
        $diskWriteSpeed = 104857600 // 100 MB/s
    ): int
    {
        $result = round( $fileSizeInBytes / $diskWriteSpeed );
        return $result > 0 ? $result : 1;
    }

    public function setCheckEstimatedAddTime(bool $newValue) {
        $this->checkEstimatedAddTime = $newValue;
        return $this;
    }
}
