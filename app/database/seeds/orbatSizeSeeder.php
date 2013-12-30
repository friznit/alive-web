<?php

class orbatSizeSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('orbatsizes')->delete();
		
		$data = array();
		// Orbat group sizes
		$data['FireTeam'] = array('Fireteam','group_0','2','6');
		$data['Squad'] = array('Squad','group_1','6','13');
		$data['Flight'] = array('Flight','group_1','3','12');
		$data['Section'] = array('Section','group_2','8','13');
		$data['Platoon'] = array('Platoon','group_3','26','64');
		$data['Troop'] = array('Troop','group_4','26','128');
		$data['Company'] = array('Company','group_4','80','225');
		$data['Squadron'] = array('Squadron','group_5','60','300');
		$data['Battalion'] = array('Battalion','group_5','300','1200');
		$data['Regiment'] = array('Regiment','group_6','300','3000');
		$data['Brigade'] = array('Brigade','group_7','3000','5000');
		$data['BCT'] = array('Brigade Combat Team','group_7','3000','5000');
		$data['IBCT'] = array('Infantry Brigade Combat Team','group_7','3000','5000');
		$data['SBCT'] = array('Stryker Brigade Combat Team','group_7','3000','5000');
		$data['HBCT'] = array('Armoured Brigade Combat Team','group_7','3000','5000');
		$data['Division'] = array('Division','group_8','10000','15000');
		$data['Corps'] = array('Corps','group_9','20000','45000');
		$data['Army'] = array('Army','group_10','80000','200000');
		$data['ArmyGroup'] = array('Army Group','group_11','400000','1000000');
		
		foreach ($data as $d => $v) {
			DB::table('orbatsizes')->insert(array(
				'name' => $v[0],
				'icon' => $v[1],
				'type' => $d,
				'min' => $v[2],
				'max' => $v[3]
			));
			
		}
	}

}
