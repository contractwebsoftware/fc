<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('customers', function(Blueprint $table) {
			$table->increments('id');
			// Customer Information
			$table->string('phone');
			$table->string('relationship');
			$table->string('street_address');
			$table->string('apartment');
			$table->string('remains_plan');
			$table->string('cremate_address');
			// Deceased Information
			$table->string('deceased_name');
			$table->string('cremation_reason');
			$table->string('cremation_reason');	
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
		Schema::drop('customers');
	}

}
