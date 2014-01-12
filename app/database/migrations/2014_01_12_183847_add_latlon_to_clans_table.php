<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLatlonToClansTable extends Migration {

	/**
	 * Make changes to the table.
	 *
	 * @return void
	 */
	public function up()
	{	
		Schema::table('clans', function(Blueprint $table) {		
			
			$table->integer("lon")->nullable();
			$table->integer("lat")->nullable();

		});

	}

	/**
	 * Revert the changes to the table.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('aos', function(Blueprint $table) {

			$table->dropColumn("lat");
			$table->dropColumn("lon");

		});
	}

}