<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyDefaultValuesForPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pages', function($table) {
            $table->boolean('draft')->default(true)->change();
            $table->boolean('in_menu')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pages', function($table) {
            $table->boolean('draft')->change();
            $table->boolean('in_menu')->change();
        });
    }
}
