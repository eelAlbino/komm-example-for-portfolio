<?php

namespace App\Console\Commands\Comments;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\CommentAttachmentArchive;
use Carbon\Carbon;

class DeleteOldAttachmentArchives extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comments:delete-old-attachment-archives';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old attachments from CommentAttachmentArchive - elements that showed_at earlier than a week ago';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Min showed_at value is week ago
        $minShowedAt = Carbon::now()->subWeek();

        // Delete old elements by filter
        $oldElems = CommentAttachmentArchive::where('showed_at', '<', $minShowedAt)->get();
        $oldElems->each(function($item) {
            DB::transaction(function() use ($item) {
                $item->attachment()->each(function($attachment) {
                    $attachment->delete();
                });
                $item->delete();
            });
        });

        $this->info('Old attachments deleted successfully.');
        return Command::SUCCESS;
    }
}
