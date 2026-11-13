<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create a new field with a new data type
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('new_status_id')->after('status_id')->nullable();
        });

        // Transfer old values from the old field to the new one
        DB::statement('UPDATE comments SET new_status_id = status_id');

        // Remove the old field and rename the new one
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('status_id');
            $table->renameColumn('new_status_id', 'status_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Create a new field with old data type
        Schema::table('comments', function (Blueprint $table) {
            $table->string('new_status_id')->constrained('comment_statuses')->after('status_id')->nullable();
        });

        // Transfer old values from the old field to the new one
        DB::statement('UPDATE comments SET new_status_id = status_id');

        // Remove the old field and rename the new one
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('status_id');
            $table->renameColumn('new_status_id', 'status_id');
        });
    }
};
