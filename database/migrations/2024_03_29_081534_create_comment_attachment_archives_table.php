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
        Schema::create('comment_attachment_archives', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('comment_id')->index();
            $table->foreign('comment_id')->references('id')->on('comments')->cascadeOnDelete();
            $table->string('filter_hash')->nullable();
            $table->timestamp('showed_at')->nullable();
            $table->string('content_control_sum');
            $table->unique(['comment_id', 'filter_hash'], 'unique_comment_attachment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comment_attachment_archives');
    }
};
