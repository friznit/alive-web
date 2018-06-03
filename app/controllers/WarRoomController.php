<?php

use Alive\CouchAPI;
use Tempo\TempoDebug;

class WarRoomController extends BaseController {

    public function __construct()
    {
        // Check CSRF token on POST
        $this->beforeFilter('csrf', array('on' => 'post'));

        // Authenticated access only
        $this->beforeFilter('auth');

    }

    // Home ------------------------------------------------------------------------------------------------------------

   public function getIndex()
   {
        ini_set('memory_limit', '1024M');
        $data = get_default_data();
        $couchAPI = new Alive\CouchAPI();

        // AO data
        $aos = AO::all();
        $aoData = $couchAPI->getAllMapTotals();

        foreach($aos as &$ao){
            $ao->couchData = (!empty($aoData[strtolower($ao->configName)]))
                           ? $aoData[strtolower($ao->configName)]
                           : [];
            $ao->thumbAO = $ao->image->url('thumbAO');
        }

        $data['allAOs'] = $aos->toJson();
        unset($oas);
        unset($aoData);

        $orbatTypes = [];
        foreach (OrbatType::all() as $orbatType) {
            $orbatTypes[$orbatType->type] = $orbatType;
        }

        $orbatSizes = [];
        foreach (OrbatSize::all() as $orbatSize) {
            $orbatSizes[$orbatSize->type] = $orbatSize;
        }

        // DEV data
        $devs = Profile::where('remark', '=', 'Developer')->with('clan')->get();
        $devData = $couchAPI->getAllDevCredits();

        forEach($devs as &$dev) {
            $dev->couchData = (!empty($devData[$dev->a3_id]))
                            ? $devData[$dev->a3_id]
                            : [];

            $orbatType = $orbatTypes[$dev->clan->type];
            $orbatTize = $orbatSizes[$dev->clan->size];

            $dev->icon = $orbatType->icon;
            $dev->orbatname = $orbatType->name;
            $dev->size = $orbatSize->name;
            $dev->sizeicon = $orbatSize->icon;
        }

        $data['devs'] = $devs->toJson();
        unset($devs);
        unset($devData);

        // CLAN data
        $clans = Clan::where('parent', '!=', 'JTF')->orwhereNull('parent')->get();
        $clanData = $couchAPI->getGroupTotals();
        $clanData = json_decode($clanData);
        $newData = [];

        // hacky but whatever
        foreach ($clanData->rows as $row) {
            $newData[$row->key[0]] = (array)$row->value;
        }

        $clanData = $newData;
        unset($newData);
        $clanOps = $couchAPI->getAllLastOps();

        forEach($clans as $key => &$clan) {
            // remove clans without any group and/or OP couch data
            if (empty($clanData[$clan->tag]) || empty($clanOps[$clan->tag])) {
                $clans->forget($key);
                continue;
            }

            // remove inactive clans from the map overview thingy
            $opDate = new DateTime($clanOps[$clan->tag][0][1]);
            $now = new DateTime();
            $diff = $opDate->diff($now);

            if ($diff->m + ($diff->y * 12) >= 6) {
                $clans->forget($key);
                continue;
            }

            $clan->couchData = $clanData[$clan->tag];
            $clan->lastop = [
                'date' => $clanOps[$clan->tag][0][1],
                'Operation' => $clanOps[$clan->tag][0][2]
            ];

            $orbatType = $orbatTypes[$clan->type];
            $orbatTize = $orbatSizes[$clan->size];

            $clan->icon = $orbatType->icon;
            $clan->orbatname = $orbatType->name;
            $clan->size = $orbatSize->name;
            $clan->sizeicon = $orbatSize->icon;

            if (is_null ($clan->lat)) {
                $lat = rand(3000,4500);
            } else {
                $lat = $clan->lat;
            }

            if (is_null ($clan->lon)) {
                $lon = rand(1800,6400);
            } else {
                $lon = $clan->lon;
            }

            $clan->lat = $lat;
            $clan->lon = $lon;

            $clan->thumbAvatar = $clan->avatar->url('thumb');
        }

        $data['clans'] = $clans->values()->toJson();
        unset($clans);
        unset($clanData);
        unset($clanOps);

        $content = View::make('warroom/home.index')->with($data);
        $header = View::make('warroom/layouts/home_header');
        $nav = View::make('warroom/layouts/nav')->with(get_default_data());
        $footer = View::make('warroom/layouts/home_footer')->with(get_default_data());

        return $header . $nav . $content . $footer;
    }

    /**
     * Get a personnel list
     *
     * @return mixed
     */
    public function getPersonnel()
    {
        $data = get_default_data();
        return View::make('warroom/personnel.index')->with($data);
    }

    /**
     * Show personnel by ArmAID
     *
     * @param int $armaid The ArmA id to request
     * @return mixed
     */
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

    /**
     * Get the War Room operations list
     *
     * @return mixed
     */
    public function getOperations()
    {
        $data = get_default_data();
        return View::make('warroom/operations.index')->with($data);
    }

    /**
     * Show an operation by GET query strings
     *
     * @return mixed
     */
	public function getShowoperation()
    {
		$name = Input::get('name');
		$map = Input::get('map');
		$clan = Input::get('clan');
		
        $data = get_default_data();
		
		// Get Map and Clan
		$data['name'] = urldecode($name);
        $data['ao'] = AO::where('configName', '=', urldecode($map))->first();
		$data['clan'] = Clan::where('tag', '=', urldecode($clan))->first();

        return View::make('warroom/operations.show')->with($data);
    }

    /**
     * Get a list of ORBATs
     *
     * @return mixed
     */
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

    /**
     * Show a specific ORBAT by ID
     *
     * @param int $id The ID of the ORBAT to show
     * @return mixed
     */
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
					$tuser = Sentry::findUserById($member->user_id);
					$memberIsOfficer = $tuser->inGroup($auth['officerGroup']);
					$memberIsLeader = $tuser->inGroup($auth['leaderGroup']);

					if($memberIsLeader) $leader = $member;
					
					if($memberIsOfficer) array_push($officers,$member);

			}
				
			$data['leader'] = $leader;
			$data['officers'] = $officers;
			$data['soldiers'] = $members;

			$data['clanOrbat'] = $clan->orbat();
			
            return View::make('warroom/orbat.show')->with($data);

        } catch (ModelNotFoundException $e) {
            return Redirect::to('warroom');
        }

    }

}
