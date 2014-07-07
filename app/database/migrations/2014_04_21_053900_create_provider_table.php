<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProviderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//Create Providers table
		Schema::create('providers', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id');
			// Provider Information
			$table->string('provider');
			$table->text('address');
			$table->string('city');
			$table->string('state');
			$table->string('zip');
			$table->string('website');
			$table->string('phone');
			$table->string('fax');
			$table->string('service_radius');
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
		//
		Schema::drop('providers');
	}

}
