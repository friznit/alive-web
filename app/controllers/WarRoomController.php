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
        $data = get_default_data();
        $couchAPI = new Alive\CouchAPI();

        $cacheKey = 'home';

        if (Cache::has($cacheKey)) {
            $content = Cache::get($cacheKey);
        }else{

            // AO data

            $aos = AO::all();
            foreach($aos as &$ao){
                $ao->couchData = $couchAPI->getMapTotals($ao->configName);
            }

            $data['allAOs'] = $aos;

            // DEV data

            $devs = Profile::where('remark', '=', 'Developer')->get();

            forEach($devs as &$dev) {

                $dev->couchData = $couchAPI->getDevCredits($dev->a3_id);

                $clan = $dev->clan;
                $dev->orbat = $clan->orbat();

                $orbattype = $dev->orbat['type'];
                $orbatsize = $dev->orbat['size'];

                $icon = '';
                $name = '';
                $size = '';
                $sizeicon = '';

                if(count($orbattype) > 0){
                    $icon = $orbattype[0]->icon;
                    $name = $orbattype[0]->name;
                }
                if(count($orbatsize) > 0){
                    $size = $orbatsize[0]->name;
                    $sizeicon = $orbatsize[0]->icon;
                }

                $dev->icon = $icon;
                $dev->name = $name;
                $dev->size = $size;
                $dev->sizeicon = $sizeicon;

            }

            $data['devs'] = $devs;

            // CLAN data

            $clans = Clan::where('parent', '!=', 'JTF')->orwhereNull('parent')->get();

            forEach($clans as &$clan) {

                $clan->couchData = $couchAPI->getGroupTotalsByTag($clan->tag);

                $clanorbat = $clan->orbat();
                $orbattype = $clanorbat['type'];
                $orbatsize = $clanorbat['size'];

                $icon = '';
                $name = '';
                $size = '';
                $sizeicon = '';
                $lat = '';
                $lon = '';

                if(count($orbattype) > 0){
                    $icon = $orbattype[0]->icon;
                    $name = $orbattype[0]->name;
                }
                if(count($orbatsize) > 0){
                    $size = $orbatsize[0]->name;
                    $sizeicon = $orbatsize[0]->icon;
                }

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

                $clan->icon = $icon;
                $clan->orbatname = $name;
                $clan->size = $size;
                $clan->sizeicon = $sizeicon;
                $clan->lat = $lat;
                $clan->lon = $lon;
            }

            $data['clans'] = $clans;

            $content = View::make('warroom/home.index')->with($data)->render();
            Cache::add($cacheKey, $content, 60);
        }


        $header = View::make('warroom/layouts/home_header')->render();
        $nav = View::make('warroom/layouts/nav')->with($data)->render();
        $footer = View::make('warroom/layouts/home_footer')->with($data)->render();

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