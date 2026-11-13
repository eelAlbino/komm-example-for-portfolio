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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('organisation_id')->constrained('organisations')->onDelete('cascade');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('prefix')->nullable();
            $table->string('client')->nullable();
            $table->string('url');
            $table->boolean('widget')->default(0);
            $table->boolean('widget_status')->default(0);
            $table->foreignId('archived_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamp('archived_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
};
