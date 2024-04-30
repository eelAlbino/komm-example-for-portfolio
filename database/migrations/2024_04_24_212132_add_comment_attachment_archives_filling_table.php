<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment_attachment_archives_filling', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('archive_id');
            $table->foreign('archive_id')->references('id')->on('comment_attachment_archives')->onDelete('cascade');
            $table->unsignedInteger('attachment_id');
            $table->foreign('attachment_id')->references('id')->on('attachments')->onDelete('cascade');
            $table->unique(['archive_id', 'attachment_id'], 'archive_id_attachment_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comment_attachment_archives_filling');
    }
};
