<?php

use Alive\CouchAPI;

class APIController extends BaseController {

    private $couchAPI;

    public function __construct()
    {
        $this->couchAPI = new CouchAPI();
    }

    public function getTotals()
    {
        return $this->couchAPI->getTotals();
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

    public function getRecentoperations()
    {
        return $this->couchAPI->getRecentOperations();
    }

    public function getLivefeed()
    {
        return $this->couchAPI->getLiveFeed();
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
		$type = Input::get('type');
        return $this->couchAPI->getServerPerf($id,$type);
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
}
