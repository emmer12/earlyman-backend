<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotedPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promoted_posts', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('property_id');
            $table->foreign('property_id')->references('id')->on('properties');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->string('plan');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('views')->unsigned()->default(0);

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
        Schema::dropIfExists('promoted_posts');
    }
}
