<?php

class AOSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('aos')->delete();
        $ao = array();
        $ao['name'] = 'Altis';
        $ao['size'] = 30720;
        $ao['configName'] = 'Altis';
        $ao['imageMapX'] = 4674;
		$ao['imageMapY'] = 3845;
		$ao['latitude'] = -35.152;
		$ao['longitude'] = 16.661;
        AO::create($ao);
		
		$ao = array();
        $ao['name'] = 'Stratis';
        $ao['size'] = 8192;
        $ao['configName'] = 'Stratis';
        $ao['imageMapX'] = 4680;
		$ao['imageMapY'] = 4000;
		$ao['latitude'] = -35.097;
		$ao['longitude'] = 16.482;
        AO::create($ao);
		
		$ao = array();
        $ao['name'] = 'Chernarus';
        $ao['size'] = 15000;
        $ao['configName'] = 'Chernarus';
        $ao['imageMapX'] = 4788;
		$ao['imageMapY'] = 3655;
		$ao['latitude'] = -45;
		$ao['longitude'] = 30;
        AO::create($ao);
		
		$ao = array();
        $ao['name'] = 'Takistan';
        $ao['size'] = 8192;
        $ao['configName'] = 'Takistan';
        $ao['imageMapX'] = 5613;
		$ao['imageMapY'] = 4004;
		$ao['latitude'] = -34;
		$ao['longitude'] = 66;
        AO::create($ao);
		
		$ao = array();
        $ao['name'] = 'Utes';
        $ao['size'] = 8192;
        $ao['configName'] = 'utes';
        $ao['imageMapX'] = 4942;
		$ao['imageMapY'] = 3709;
		$ao['latitude'] = -45;
		$ao['longitude'] = 30;
        AO::create($ao);
		
		$ao = array();
        $ao['name'] = 'Shapur';
        $ao['size'] = 4096;
        $ao['configName'] = 'Shapur_BAF';
        $ao['imageMapX'] = 5378;
		$ao['imageMapY'] = 3892;
		$ao['latitude'] = -33;
		$ao['longitude'] = 64;
        AO::create($ao);
		
		$ao = array();
        $ao['name'] = 'Zargabad';
        $ao['size'] = 4096;
        $ao['configName'] = 'Zargabad';
        $ao['imageMapX'] = 5489;
		$ao['imageMapY'] = 4028;
		$ao['latitude'] = -33;
		$ao['longitude'] = 64;
        AO::create($ao);
		
		$ao = array();
        $ao['name'] = 'Proving Grounds';
        $ao['size'] = 4096;
        $ao['configName'] = 'ProvingGrounds_PMC';
        $ao['imageMapX'] = 5495;
		$ao['imageMapY'] = 4124;
		$ao['latitude'] = -33;
		$ao['longitude'] = 64;
        AO::create($ao);

	}

}
