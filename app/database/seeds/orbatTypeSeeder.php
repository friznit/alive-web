<?php

class orbatTypeSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('orbatTypes')->delete();
		
		$data = array();
		// Orbat group types
		
		$data['Unknown'] = array('unknown','Unknown');
		$data['Infantry'] = array('inf','Infantry');
		$data['MotorizedInfantry'] = array('motor_inf','Motorized Infantry');
		$data['MechanizedInfantry'] = array('mech_inf','Mechanized Infantry');
		$data['Armored'] = array('armor','Armored');
		$data['Recon'] = array('recon','Reconnaisance');
		$data['Cavalry'] = array('recon','Cavalry');
		$data['Airborne'] = array('inf','Airborne Infantry');
		$data['Helicopter'] = array('air','Helicopter');
		$data['CombatAviation'] = array('air','Combat Aviation');
		$data['AttackRecon'] = array('air','Ground Attack/Reconnaisance Aircraft');
		$data['GeneralSupport'] = array('air','General Support Aircraft (Helicopter)');
		$data['Assault'] = array('air','Assault Aircraft (Helicopter)');
		$data['AviationSupport'] = array('air','Aviation Support');
		$data['Fighter'] = array('plane','Fighter Aircraft');
		$data['UAV'] = array('uav','Unmanned Aerial Vehicle (UAV)');
		$data['Medical'] = array('med','Medical');
		$data['Artillery'] = array('art','Artillery');
		$data['Mortar'] = array('mortar','Mortar');
		$data['HQ'] = array('hq','HQ');
		$data['Support'] = array('support','Support');
		$data['Maintenance'] = array('maint','Maintenance');
		$data['Service'] = array('service','Service');
		$data['Maritime'] = array('naval','Naval');
		
		foreach ($data as $d => $v) {
			DB::table('orbatTypes')->insert(array(
				'name' => $v[1],
				'icon' => $v[0],
				'type' => $d
			));
		}
	}

}
