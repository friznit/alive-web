<?php

class WarRoomController extends BaseController {

    public function __construct()
    {
        //Check CSRF token on POST
        $this->beforeFilter('csrf', array('on' => 'post'));

        // Authenticated access only
        $this->beforeFilter('auth');

    }

    // Home ------------------------------------------------------------------------------------------------------------

    public function getIndex()
    {
        $data = get_default_data();

        $data['allAOs'] = AO::all();

        $devs = Profile::where('remark', '=', 'Developer')->get();

        $profiles = array();

        forEach($devs as $profile) {
            $clan = $profile->clan;
            $profile->orbat = $clan->orbat();
            array_push($profiles, $profile);
        }

        $data['devs'] = $profiles;

        return View::make('warroom/home.index')->with($data);
    }

    // Personnel -------------------------------------------------------------------------------------------------------

    public function getPersonnel()
    {
        $data = get_default_data();
        return View::make('warroom/personnel.index')->with($data);
    }
	
	    // Show ------------------------------------------------------------------------------------------------------------

    public function getShowpersonnel($armaid)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        try {
            $profile = Profile::where('a3_id', '=', $armaid)->first();

			$data['profile'] = $profile;
			$data['player_id'] = $armaid;
			
			if(!is_null($profile)){
				$user = $profile->user;
				$data['user'] = $user;
				$clan = $profile->clan;
				$data['clan'] = $clan;
			}

			// Get data that won't be displayed as table data
            $couchAPI = new Alive\CouchAPI();
            $playerTotals = $couchAPI->getPlayerTotals($armaid);
			$playerDetails = $couchAPI->getPlayerDetails($armaid);
			$playerWeapon = $couchAPI->getPlayerWeapon($armaid,false);
			$playerVehicle = $couchAPI->getPlayerVehicle($armaid,false);
			$playerClass = $couchAPI->getPlayerClass($armaid,false);
			//$playerLoadout = $couchAPI->getPlayerLoadout($armaid, $clan->tag, $lastMissionName);

			$playerdata = array(
				"Totals" => $playerTotals,
				"Details" => $playerDetails,
				"Weapon" => $playerWeapon,
				"Vehicle" => $playerVehicle,
				"Class" => $playerClass
			//	"Loadout" => $playerLoadout
			);
			$data['playerdata'] = $playerdata;

			// Option to grab player data here, or do it in datatable within View
			
			// Get player totals data from couchdb to send to page
			
			// Get player superlatives
			
			// Get player alias, class, favourite weapons, vehicles,

            return View::make('warroom/personnel.show')->with($data);

        } catch (ModelNotFoundException $e) {
            return Redirect::to('warroom');
        }

    }

    // Operations ------------------------------------------------------------------------------------------------------

    public function getOperations()
    {
        $data = get_default_data();
        return View::make('warroom/operations.index')->with($data);
    }
	
	// Orbat -------------------------------------------------------------------------------------------------------

    public function getOrbat()
    {
        $data = get_default_data();
		
		// Create a list of server IP addresses and clan names to view in datatables
		$clans = Clan::all();
		$clansArr = array();
		foreach($clans as $clan) {

			$clanServer = array();
			$clanServer["tag"] = $clan->tag;
			$clanServer["Name"] = $clan->name;
			$clanServer["id"] = $clan->id;
			array_push($clansArr,$clanServer);
		}
		
		$data['clansArr'] = json_encode($clansArr); 

        return View::make('warroom/orbat.index')->with($data);
    }
	
	// Show ------------------------------------------------------------------------------------------------------------

    public function getShoworbat($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        try {
            $clan = Clan::findOrFail($id);
            $data['clan'] = $clan;
			
			// Get group stats from couch
			$couchAPI = new Alive\CouchAPI();
			$clanTotals = $couchAPI->getGroupTotalsByTag($clan->tag);	
			$clanLastOp = $couchAPI->getGroupLastOp($clan->tag);
			$data['clanTotals'] = $clanTotals;
			$data['clanLastOp']	= $clanLastOp;	
				
			$soldiers = array();
			$officers = array();
			
			$members = $clan->members->all();
			
			forEach($members as $member) {
					$user = Sentry::findUserById($member->user_id);
					$memberIsGrunt = $user->inGroup($auth['gruntGroup']);
					$memberIsOfficer = $user->inGroup($auth['officerGroup']);
					$memberIsLeader = $user->inGroup($auth['leaderGroup']);

					if($memberIsLeader) $leader = $member;
					
					if($memberIsOfficer) array_push($officers,$member);
					
					if($memberIsGrunt) array_push($soldiers,$member);
			}
				
			$data['leader'] = $leader;
			$data['officers'] = $officers;
			$data['soldiers'] = $soldiers;

			$data['clanOrbat'] = $clan->orbat();
			
            return View::make('warroom/orbat.show')->with($data);

        } catch (ModelNotFoundException $e) {
            return Redirect::to('warroom');
        }

    }
}