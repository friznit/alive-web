<?php

use Alive\CouchAPI;
use Alive\SigGenerate;
use Tempo\TempoDebug;

class APIController extends BaseController {

    private $couchAPI;

    public function __construct()
    {
        $this->couchAPI = new CouchAPI();
    }

    function stripNonNumeric( $string ) {
        return preg_replace( "/[^0-9]/", "", $string );
    }

	public function getSig()
	{
		$armaid = Input::get('id');

        $armaid = $this->stripNonNumeric($armaid);

        $public = public_path();
        $url = url();

        $sigPath = $public . '/sigs/'.$armaid.'.jpg';
        $sigURL = $url . '/sigs/'.$armaid.'.jpg';

        // the sig already exists
        if (file_exists($sigPath)) {

            // the sig is more than 1 day old
            // regenerate it

            //if (filemtime($sigPath) < time() - 86400) {

            if (filemtime($sigPath) < time() - 30) {

            }else{
                // the sig is less than 1 day old
                // output it

                header('Location: ' . $sigURL);
                exit;
            }
        }

        try {
            $profile = Profile::where('a3_id', '=', $armaid)->first();

            $data['player_id'] = $armaid;

            if(!is_null($profile)){
                $user = $profile->user;
                $data['user'] = $user;
                $clan = $profile->clan;
                $data['clan'] = $clan;
				$data['country'] = $profile->country;
                $data['avatar'] = $profile->avatar->url('thumb');
                $data['clantar'] = $clan->avatar->url('thumb');
                $data['a3_id'] = $profile->a3_id;
            }

            $couchAPI = new Alive\CouchAPI();
            $playerTotals = $couchAPI->getPlayerTotals($armaid);
            $playerDetails = $couchAPI->getPlayerDetails($armaid);
            $playerWeapon = $couchAPI->getPlayerWeapon($armaid,false);
            $playerVehicle = $couchAPI->getPlayerVehicle($armaid,false);
            $playerClass = $couchAPI->getPlayerClass($armaid,false);

            $playerdata = array(
                "Totals" => $playerTotals,
                "Details" => $playerDetails,
                "Weapon" => $playerWeapon,
                "Vehicle" => $playerVehicle,
                "Class" => $playerClass

            );
            $data['playerdata'] = $playerdata;

            $sigGenerator = new SigGenerate();
            $sigGenerator->create($data);

            if (file_exists($sigPath)) {
                header('Location: ' . $sigURL);
                exit;
            }

        } catch (ModelNotFoundException $e) {
            return Redirect::to('public/personnel.invalid');
        }
	}
	public function getAuthorise()
    {
		$ip = Input::get('ip');
		$group = Input::get('group');
		$result = "false";
		// Get server based on IP
		try {
			$servers = Server::where('ip','=',$ip)->get();
			// Get clan from server
			$clan = $servers[0]->clan;
			// Check clan tag = group 
			if ($clan->tag == $group) {
				$result = "true";
			} else {
				$result = "false";
			}
		} catch (ModelNotFoundException $e) {
            $result = "false";
        }
        return json_encode($result);
    }
    public function getTotals()
    {
        return $this->couchAPI->getTotals();
    }
	public function getOptotals()
    {
		$name = Input::get('name');
		$map = Input::get('map');
		$clan = Input::get('clan');
        return $this->couchAPI->getOpTotals($name,$map,$clan);
    }	
	public function getMaptotals()
    {
		$name = Input::get('name');
        return $this->couchAPI->getMapTotals($name);
    }
    public function getActiveunitcount()
    {
        return $this->couchAPI->getActiveUnitCount();
    }
	public function getOpactiveunitcount()
    {
		$name = Input::get('name');
		$map = Input::get('map');
		$clan = Input::get('clan');
        return $this->couchAPI->getOpActiveunitcount($name,$map,$clan);
    }	
    public function getRecentoperations()
    {
        return $this->couchAPI->getRecentOperations();
    }
    public function getLivefeed()
    {
        return $this->couchAPI->getLiveFeed();
    }
	public function getOplivefeed()
    {
		$name = Input::get('name');
		$map = Input::get('map');
		$clan = Input::get('clan');
        return $this->couchAPI->getOpLiveFeed($map,$clan,$name);
    }
    public function getOplivefeedpaged()
    {
        $name = Input::get('name');
        $map = Input::get('map');
        $clan = Input::get('clan');
        $limit = Input::get('limit');
        $skip = Input::get('skip');
        return $this->couchAPI->getOpLiveFeedPaged($map,$clan,$name,$limit,$skip);
    }
    public function getLossesblu()
    {
        return $this->couchAPI->getLossesBLU();
    }
    public function getLossesopf()
    {
        return $this->couchAPI->getLossesOPF();
    }
    public function getCasualties()
    {
        return $this->couchAPI->getCasualties();
    }
    public function getOpcasualties()
    {
		$name = Input::get('name');
		$map = Input::get('map');
		$clan = Input::get('clan');
        return $this->couchAPI->getOpCasualties($name,$map,$clan);
    }
	public function getOplossesblu()
    {
		$name = Input::get('name');
		$map = Input::get('map');
		$clan = Input::get('clan');
        return $this->couchAPI->getOpLossesBLU($name,$map,$clan);
    }
    public function getOplossesopf()
    {
		$name = Input::get('name');
		$map = Input::get('map');
		$clan = Input::get('clan');
        return $this->couchAPI->getOpLossesOPF($name,$map,$clan);
    }
    public function getOperationsbymap()
    {
        return $this->couchAPI->getOperationsByMap();
    }
    public function getOperationsbyday()
    {
        return $this->couchAPI->getOperationsByDay();
    }
    public function getPlayersbyday()
    {
        return $this->couchAPI->getPlayersByDay();
    }
    public function getKillsbyday()
    {
        return $this->couchAPI->getKillsByDay();
    }
    public function getDeathsbyday()
    {
        return $this->couchAPI->getDeathsByDay();
    }
    public function getT1operators()
    {
        return $this->couchAPI->getT1Operators();
    }
	public function getDevcredits()
    {
		$id = Input::get('id');
        return $this->couchAPI->getDevCredits($id);
    }
	public function getPlayerdetails()
    {
		$id = Input::get('id');
        return $this->couchAPI->getPlayerDetails($id);
    }
	public function getPlayerweapon()
    {
		$id = Input::get('id');
        return $this->couchAPI->getPlayerWeapon($id);
    }
	public function getPlayerweapons()
    {
		$id = Input::get('id');
        return $this->couchAPI->getPlayerWeapons($id);
    }
	public function getPlayervehicle()
    {
		$id = Input::get('id');
        return $this->couchAPI->getPlayerVehicle($id);
    }
	public function getPlayervehicles()
    {
		$id = Input::get('id');
        return $this->couchAPI->getPlayerVehicles($id);
    }
	public function getPlayerclasses()
    {
		$id = Input::get('id');
        return $this->couchAPI->getPlayerClasses($id);
    }
	public function getPlayeralias()
    {
		$id = Input::get('id');
        return $this->couchAPI->getPlayerAlias($id);
    }
	public function getPersonneltotals()
    {
        return $this->couchAPI->getPersonnelTotals();
    }
	public function getT1marksmen()
    {
        return $this->couchAPI->getT1Marksmen();
    }
	public function getVehiclecommanders()
    {
        return $this->couchAPI->getVehicleCommanders();
    }
	public function getPilots()
    {
        return $this->couchAPI->getPilots();
    }
	public function getMedics()
    {
        return $this->couchAPI->getMedics();
    }
	public function getScores()
    {
        return $this->couchAPI->getScores();
    }
	public function getAvescores()
    {
        return $this->couchAPI->getAveScores();
    }
	public function getRatings()
    {
        return $this->couchAPI->getRatings();
    }
	public function getGrouptotals()
    {
        return $this->couchAPI->getGroupTotals();
    }
	public function getGrouptotalsbytag()
    {
		$id = Input::get('id');
        return $this->couchAPI->getGroupTotalsByTag($id);
    }
	public function getGroupclasses()
    {
		$id = Input::get('id');
        return $this->couchAPI->getGroupClasses($id);
    }
	public function getOrbatrecentoperations()
    {
		$id = Input::get('id');
        return $this->couchAPI->getOrbatRecentOperations($id);
    }
	public function getOrbatt1()
    {
		$id = Input::get('id');
        return $this->couchAPI->getOrbatT1($id);
    }
	public function getOrbatmedics()
    {
		$id = Input::get('id');
        return $this->couchAPI->getOrbatMedics($id);
    }
	public function getOrbatpilots()
    {
		$id = Input::get('id');
        return $this->couchAPI->getOrbatPilots($id);
    }
	public function getOrbatkillsbyweapon()
    {
		$id = Input::get('id');
        return $this->couchAPI->getOrbatKillsByWeapon($id);
    }
	public function getOrbatweapons()
    {
		$id = Input::get('id');
        return $this->couchAPI->getOrbatWeapons($id);
    }
	public function getOrbatvehicles()
    {
		$id = Input::get('id');
        return $this->couchAPI->getOrbatVehicles($id);
    }
	public function getOrbatplayerkills()
    {
		$id = Input::get('id');
        return $this->couchAPI->getOrbatPlayerKills($id);
    }
	public function getOrbatmountedkills()
    {
		$id = Input::get('id');
        return $this->couchAPI->getOrbatMountedKills($id);
    }
	public function getOrbatclasses()
    {
		$id = Input::get('id');
        return $this->couchAPI->getOrbatClasses($id);
    }
	public function getOperations()
    {
        return $this->couchAPI->getOperations();
    }
	public function getOpsbreakdown()
    {
        return $this->couchAPI->getOpsBreakdown();
    }
	public function getServerperf()
    {
		$id = Input::get('id');
		$servername = Input::get('servername');
		$type = Input::get('type');
        return $this->couchAPI->getServerPerf($id,$type,$servername);
    }
	public function getClanfeed()
    {
		$id = Input::get('id');
        return $this->couchAPI->getClanFeed($id);
    }
	public function getPlayerfeed()
    {
		$id = Input::get('id');
        return $this->couchAPI->getPlayerFeed($id);
    }

    public function getTestcalls()
    {

        $this->couchAPI->debug = true;
        $this->couchAPI->reset = false;

        /*

        $name = 'SOMETHING';
        $id = 'SOMETHING';

        $profiler = TempoDebug::startProfile();
        $result = $this->getMaptotals($name;
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getDevcredits($id);
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getPlayerdetails($id);
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getPlayerweapon($id);
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getPlayerweapons($id);
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getPlayerVehicle($id);
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getPlayerVehicles($id);
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getPlayerClasses($id);
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getPlayerAlias($id);
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getGrouptotalsbytag($id);
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getGroupclasses($id);
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getOrbatrecentoperations($id);
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getOrbatt1($id);
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getOrbatmedics($id);
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getOrbatpilots($id);
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getOrbatkillsbyweapon($id);
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getOrbatweapons($id);
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getOrbatvehicles($id);
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getOrbatplayerkills($id);
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getOrbatmountedkills($id);
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getServerperf($id);
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getClanfeed($id);
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getPlayerfeed($id);
        TempoDebug::stopProfile($profiler);

        */

        /*
        $profiler = TempoDebug::startProfile();
        $result = $this->getLivefeed();
        TempoDebug::stopProfile($profiler);
        */



        $profiler = TempoDebug::startProfile();
        $result = $this->getTotals();
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getActiveunitcount();
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getRecentoperations();
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getLossesblu();
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getLossesopf();
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getCasualties();
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getOperationsbymap();
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getOperationsbyday();
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getPlayersByDay();
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getKillsbyday();
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getDeathsbyday();
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getT1operators();
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getPersonnelTotals();
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getT1Marksmen();
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getVehicleCommanders();
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getPilots();
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getMedics();
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getScores();
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getAvescores();
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getRatings();
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getGrouptotals();
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getOrbatclasses();
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getOperations();
        TempoDebug::stopProfile($profiler);

        $profiler = TempoDebug::startProfile();
        $result = $this->getOpsbreakdown();
        TempoDebug::stopProfile($profiler);

    }
}
