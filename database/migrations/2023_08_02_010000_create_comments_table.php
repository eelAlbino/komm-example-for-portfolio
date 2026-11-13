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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');
            $table->string('status_id')->constrained('comment_statuses');
            $table->string('number', 255)->nullable();
            $table->text('text');
            $table->json('mentioned_users')->nullable();
            $table->string('url')->nullable();
            $table->string('title')->nullable();
            $table->string('type')->nullable();
            $table->string('selector_start')->nullable();
            $table->string('selector_end')->nullable();
            $table->longText('fragment')->nullable();
            $table->string('index_start')->nullable();
            $table->string('index_end')->nullable();
            $table->string('screen')->nullable();
            $table->string('viewport')->nullable();
            $table->string('ua')->nullable();
            $table->string('os')->nullable();
            $table->string('browser')->nullable();
            $table->string('version')->nullable();
            $table->string('device')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
};
