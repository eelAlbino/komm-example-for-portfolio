<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $values = [
        'admin' => 300,
        'manager' => 200,
        'commenter' => 100
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_roles', function (Blueprint $table) {
            $table->integer('weight')->default(100);
        });
        // Set values
        foreach ($this->values as $slug => $value) {
            DB::table('project_roles')->where('slug', $slug)->update([
                'weight' => $value
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_roles', function (Blueprint $table) {
            $table->dropColumn('weight');
        });
    }
};
