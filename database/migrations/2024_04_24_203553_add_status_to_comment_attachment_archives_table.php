<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\CommentAttachmentArchive;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comment_attachment_archives', function (Blueprint $table) {
            $table->string('status')->default(CommentAttachmentArchive::STATUS_INIT)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comment_attachment_archives', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
