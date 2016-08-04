<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOnDashboardToWidgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('widgets', function (Blueprint $table) {
            $table->boolean('on_dashboard')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('widgets', function (Blueprint $table) {
            $table->dropColumn('on_dashboard');
        });
    }
}
