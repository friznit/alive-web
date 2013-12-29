<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrbatSizeTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orbatsizes', function(Blueprint $table) {
            $table->increments('id');
			$table->string('type', 100)->nullable();
            $table->string('name', 128)->nullable();
            $table->string('icon', 32)->nullable();
			$table->string('min', 32)->nullable();
			$table->string('max', 32)->nullable();
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
		Schema::drop('orbatsizes');
	}

}