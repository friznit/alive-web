<?php

class SentryUserSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('users')->delete();
		
		Sentry::getUserProvider()->create(array(
				'email'    => 'arjaydev@gmail.com',
				'password' => 'cheese',
				'activated' => 1,
		));
		
		Sentry::getUserProvider()->create(array(
			'email' => 'tupolov73@gmail.com',
			'password' => 'cheese',
			'activated' => 1,
		));
		
		// Developer users for credits/easy login
		$data = array('tupolov73+sbw@gmail.com','tupolov73+mct@gmail.com','tupolov73+rsr@gmail.com','tupolov73+aja@gmail.com','tupolov73+ddh@gmail.com','tupolov73+ahf@gmail.com','tupolov73+djj@gmail.com','tupolov73+vrcr@gmail.com','tupolov73+kgg@gmail.com','tupolov73+whb@gmail.com');
		
		foreach ($data as $p) {
			Sentry::getUserProvider()->create(array(
				'email' => $p,
				'password' => 'cheese',
				'activated' => 1,
			));
		}
		
	}

}
