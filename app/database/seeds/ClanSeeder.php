<?php

class ClanSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('clans')->delete();
		
		$data = array();
		// Developer groups for credits
		$data['JTFHQ'] = array('tupolov73+sbw@gmail.com','Joint Task Force Headquarters','JTF','HQ','Brigade');
		$data['NSG'] = array('tupolov73+mct@gmail.com','Naval Support Group','JTF','Maritime','Squadron');
		$data['BSG'] = array('tupolov73+rsr@gmail.com','Brigade Support Group','JTF','Support','Battalion');
		$data['JHTF'] = array('tupolov73+kgg@gmail.com','Joint Helicopter Task Force','JTF','Helicopter','Squadron');
		$data['BRF'] = array('tupolov73+ddh@gmail.com','Brigade Reconnaisance Force','JTF','Recon','Battalion');
		$data['1IBG'] = array('tupolov73+aja@gmail.com','1st Infantry Battle Group','JTF','Infantry','Battalion');
		$data['2IBG'] = array('tupolov73+djj@gmail.com','2nd Infantry Battle Group','JTF','MotorizedInfantry','Battalion');
		$data['3ABG'] = array('tupolov73+ahf@gmail.com','3rd Armoured Battle Group','JTF','Armored','Battalion');
		$data['4IBG'] = array('tupolov73+vrcr@gmail.com','4th Infantry Battle Group','JTF','MechanizedInfantry','Battalion');
		$data['JTFAG'] = array('tupolov73+whb@gmail.com','Joint Task Force Air Group','JTF','CombatAviation','Squadron');
		
		foreach ($data as $d => $v) {
			$clan = array();
			$currentUser = Sentry::getUserProvider()->findByLogin($v[0]);
			$clan['name'] = $v[1];
			$clan['tag'] = $d;
			$clan['parent'] = $v[2];
			$clan['type'] = $v[3];
			$clan['size'] = $v[4];
			Clan::create($clan);
			
			$newclan = Clan::where('tag', '=', $d)->firstOrFail();
				
			$profile = $currentUser->profile;
			$profile->clan_id = $newclan->id;
			$currentUser->addGroup(Sentry::getGroupProvider()->findByName('leader'));
			$profile->save();
		}
	}

}
