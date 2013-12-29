<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrbatTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orbattypes', function(Blueprint $table) {
            $table->increments('id');
			$table->string('type', 100)->nullable();
            $table->string('name', 128)->nullable();
            $table->string('icon', 32)->nullable();
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
		Schema::drop('orbattypes');
	}

}