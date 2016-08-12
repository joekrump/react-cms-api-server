<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagePartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_parts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('page_id')->unsigned()->nullable();
            $table->foreign('page_id')->references('id')->on('pages');
            $table->string('title');
            $table->text('content');
            $table->integer('position');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('page_parts');
    }
}
