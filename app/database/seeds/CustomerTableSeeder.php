<?php
class CustomerTableSeeder {
	public function run()
	{
		DB::table('client')->delete();
		Customer::create(
			[
				'deceased_name'=>'Phyllis Rufus',
				'phone'=>'714 750 5336',
				
			]
		);
	}
}