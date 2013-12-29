<?php

class ProfileSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('profiles')->delete();

		$adminUser = Sentry::getUserProvider()->findByLogin('arjaydev@gmail.com');		
		$profile = array();
		$profile['user_id'] = $adminUser->getId();
		$profile['username'] = 'ARJay';
		$profile['a3_id'] = '76561198021311392';
		$profile['primary_profile'] = 'ARJay';
		$profile['twitch_stream'] = 'http://www.twitch.tv/arjaydev';
		Profile::create($profile);
			
		$adminUser = Sentry::getUserProvider()->findByLogin('tupolov73@gmail.com');
        $profile = array();
        $profile['user_id'] = $adminUser->getId();
        $profile['username'] = 'Tupolov';
        $profile['a3_id'] = '76561197982137286';
        $profile['primary_profile'] = 'Matt';
        Profile::create($profile);
		
		// Developer profiles for credits
		$data = array();
		$data['1'] = array('tupolov73+sbw@gmail.com','S.B.Wolffy','AU','Australia');
		$data['2'] = array('tupolov73+mct@gmail.com','M.C.Tupolov','GB','United Kingdom');
		$data['3'] = array('tupolov73+rsr@gmail.com','R.S.Rye','GB','United Kingdom');
		$data['4'] = array('tupolov73+kgg@gmail.com','K.G.Gunny','GB','United Kingdom');
		$data['5'] = array('tupolov73+aja@gmail.com','A.J.Arjay','AU','Australia');
		$data['6'] = array('tupolov73+djj@gmail.com','D.J.Jman','GB','United Kingdom');
		$data['7'] = array('tupolov73+ahf@gmail.com','A.H.Friznit','GB','United Kingdom');
		$data['8'] = array('tupolov73+vrcr@gmail.com','V.R.C.Raptor','US','United States');
		$data['9'] = array('tupolov73+whb@gmail.com','W.H.Bob','GB','United Kingdom');
		$data['10'] = array('tupolov73+ddh@gmail.com','D.D.Highhead','AT','Austria');
				
		foreach ($data as $p => $v) {
			$adminUser = Sentry::getUserProvider()->findByLogin($v[0]);
			$profile = array();
			$profile['user_id'] = $adminUser->getId();
			$profile['username'] = $v[1];
			$profile['a3_id'] = $p;
			$profile['primary_profile'] = $v[1];
			$profile['remark'] = 'Developer';
			$profile['country'] = $v[2];
			$profile['country_name'] = $v[3];
			Profile::create($profile);
		}

	}

}
