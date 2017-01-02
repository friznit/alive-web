<?php

use Alive\CouchAPI;
use Alive\SigGenerate;
use Alive\AfterActionReplay;
use Tempo\TempoDebug;

class APIController extends BaseController
{

    private $couchAPI;
    private $data;
    private $aar;

    public function __construct()
    {
        $this->couchAPI = new CouchAPI();
        $this->aar = new AfterActionReplay();         
    }

    public function getAos()
    {
           $couchAPI = new Alive\CouchAPI();
              $cacheKey = 'aos';

           if (Cache::has($cacheKey)) {
               $aos = Cache::get($cacheKey);
           }else{
            $aos = AO::all();
                 foreach($aos as &$ao){
                    $ao->couchData = $couchAPI->getMapTotals($ao->configName);
                  }
            Cache::add($cacheKey, $aos, 60);
           }
         return $aos;
    }

    public function getDevs()
    {
        $couchAPI = new Alive\CouchAPI();

        $cacheKey = 'devs';

        if (Cache::has($cacheKey)) {
               $devs = Cache::get($cacheKey);
        }else{
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
               Cache::add($cacheKey, $devs, 60);
           }
     return $devs;
    }

    public function getClans()
    {
        $couchAPI = new Alive\CouchAPI();

        $cacheKey = 'clans';
      

        if (Cache::has($cacheKey)) {
            $clans = Cache::get($cacheKey);
        }else{
            $clans = Clan::where('parent', '!=', 'JTF')->orwhereNull('parent')->get();

                forEach($clans as &$clan) {

                    $clan->couchData = $couchAPI->getGroupTotalsByTag($clan->tag);

                    $clan->lastop = $couchAPI->getGroupLastOp($clan->tag);
                    $clanorbat = $clan->orbat();
                    $orbattype = $clanorbat['type'];
                    $orbatsize = $clanorbat['size'];

                    $icon = '';
                    $name = '';
                    $size = '';
                    $sizeicon = '';
                    $lat = '';
                    $lon = '';
                    $country = '';

                    if(count($orbattype) > 0){
                        $icon = $orbattype[0]->icon;
                        $name = $orbattype[0]->name;
                    }
                    if(count($orbatsize) > 0){
                        $size = $orbatsize[0]->name;
                        $sizeicon = $orbatsize[0]->icon;
                    }
                     if (is_null ($clan->country)) {
                        $country = "GB";
                     } else {
                        $country = $clan->country;
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

                    $clan->country = $country;
                    $clan->icon = $icon;
                    $clan->orbatname = $name;
                    $clan->size = $size;
                    $clan->sizeicon = $sizeicon;
                    $clan->lat = $lat;
                    $clan->lon = $lon;
                }
                 Cache::add($cacheKey, $clans, 60);
           }
     return $clans;
    }

    public function getAar()
    {
        // Hopefully crashes on huge AARs
        ini_set('memory_limit', '1024M');
        $group = Input::get('clan');
        $map = Input::get('map');
        $operation = Input::get('name');
        $rawEvents = $this->couchAPI->getOperationEvents($group, $map, $operation);      
        $events = $this->aar->convert($rawEvents);
        $rawAar = $this->couchAPI->getAar($group, $map, $operation);
        $aar = $this->aar->convert($rawAar);
        $final = array_merge($events, $aar);
        usort($final, function($a, $b) {
            $at = DateTime::createFromFormat('d/m/Y H:i:s', $a['realTime'])->getTimestamp();
            $bt = DateTime::createFromFormat('d/m/Y H:i:s', $b['realTime'])->getTimestamp();
            return ($at - $bt);
        });
        $count = 1;
        $last = -1;
        $high = -1;
        $prev = null;
        foreach ($final as &$a) {
            $a['old'] = $a['missionTime'];
            if ($last == -1) {
                $last = $a['missionTime'];
            }
            if ($a['missionTime'] - $last >= 5 || $a['missionTime'] - $last < 0) {
                $last = $a['missionTime'];
                $count++;
                $a['missionTime'] = $last;
            } else {
                $a['missionTime'] = $last;
            }
            if (intval($a['missionTime']) >= $high) {
                $high = intval($a['missionTime']);
            } else {
                $a['missionTime'] = $high + intval($a['missionTime']);
            }
            $prev = $a;
        }
        return $final;
    }

    public function getPlayersbyoperation()
    {
        $group = Input::get('clan');
        $map = Input::get('map');
        $operation = Input::get('name');
        $data = $this->couchAPI->getPlayersByOperation($group, $map, $operation);
        $result = [];
        foreach ($data['response']['rows'] as $row) {
            $result[] = [
                'id' => $row['key'][3],
                'name' => $row['key'][4]
            ];
        }
        return json_encode($result);
    }

    /**
     * TODO: Should this be public or private?
     *
     * @param $string
     * @return mixed
     */
    function stripNonNumeric($string)
    {
        return preg_replace("/[^0-9]/", "", $string);
    }

    /**
     * @return mixed
     */
    public function getSig()
    {
        $armaid = Input::get('id');

        $armaid = $this->stripNonNumeric($armaid);

        $public = public_path();
        $url = url();

        $sigPath = $public . '/sigs/' . $armaid . '.jpg';
        $sigURL = $url . '/sigs/' . $armaid . '.jpg';

        // the sig already exists
        if (file_exists($sigPath)) {

            // the sig is more than 1 day old
            // regenerate it

            //if (filemtime($sigPath) < time() - 86400) {

            if (filemtime($sigPath) < time() - 30) {

            } else {
                // the sig is less than 1 day old
                // output it

                header('Location: ' . $sigURL);
                exit;
            }
        }

        try {
            $profile = Profile::where('a3_id', '=', $armaid)->first();

            $data['player_id'] = $armaid;

            if (!is_null($profile)) {
                $user = $profile->user;
                $data['user'] = $user;
                if ($profile->clan_id > 0) {
                    $clan = $profile->clan;
                    $data['clan'] = $clan;
                    $data['clantar'] = $clan->avatar->url('thumb');
                } else {
                    $data['clantar'] = "/avatars/thumb/clan.png";
                }
                $data['country'] = $profile->country;
                $data['avatar'] = $profile->avatar->url('thumb');
                $data['username'] = $profile->username;
                $data['email'] = $profile->email;
                $data['a3_id'] = $profile->a3_id;
            }

            $couchAPI = new Alive\CouchAPI();
            $playerTotals = $couchAPI->getPlayerTotals($armaid);
            $playerDetails = $couchAPI->getPlayerDetails($armaid);
            $playerWeapon = $couchAPI->getPlayerWeapon($armaid, false);
            $playerVehicle = $couchAPI->getPlayerVehicle($armaid, false);
            $playerClass = $couchAPI->getPlayerClass($armaid, false);

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

    /**
     * @return string
     */
    public function getAuthorise()
    {
        if (Input::has('ip')) {
            $ip = Input::get('ip');
        } else {
            ;
            $request = Request::instance();
            //TempoDebug::dump($request);
            $ip = $request->getClientIp();
        }
        $group = Input::get('group');

        $result = "false";
        // Get server based on IP
        try {
            // Get the clan from the tag
            $clan = Clan::where('tag', '=', $group)->firstorFail()->toArray();

            try {
                $clan = Clan::where('tag', '=', $group)->get();

                $server = $clan[0]->servers()->where('ip', '=', $ip)->get()->toArray();

                if (count($server) > 0) {
                    $result = "true";
                } else {
                    $result = "false";
                }

            } catch (ModelNotFoundException $e) {
                $result = "false";
            }

        } catch (ModelNotFoundException $e) {
            $result = "false";
        }
        return json_encode($result);
    }

    /**
     * @return bool|string
     */
    public function getTotals()
    {
        return $this->couchAPI->getTotals();
    }

    /**
     * @return string
     */
    public function getOptotals()
    {
        $name = Input::get('name');
        $map = Input::get('map');
        $clan = Input::get('clan');
        return $this->couchAPI->getOpTotals($name, $map, $clan);
    }

    /**
     * @return bool|string
     */
    public function getMaptotals()
    {
        $name = Input::get('name');
        return $this->couchAPI->getMapTotals($name);
    }

    /**
     * @return bool|string
     */
    public function getActiveunitcount()
    {
        return $this->couchAPI->getActiveUnitCount();
    }

    /**
     * @return string
     */
    public function getOpactiveunitcount()
    {
        $name = Input::get('name');
        $map = Input::get('map');
        $clan = Input::get('clan');
        return $this->couchAPI->getOpActiveunitcount($name, $map, $clan);
    }

    /**
     * @return bool|string
     */
    public function getRecentoperations()
    {
        return $this->couchAPI->getRecentOperations();
    }

    /**
     * @return string
     */
    public function getLivefeed()
    {
        return $this->couchAPI->getLiveFeed();
    }

    /**
     * @return string
     */
    public function getOplivefeed()
    {
        $name = Input::get('name');
        $map = Input::get('map');
        $clan = Input::get('clan');
        return $this->couchAPI->getOpLiveFeed($map, $clan, $name);
    }

    /**
     * @return string
     */
    public function getOplivefeedpaged()
    {
        $name = Input::get('name');
        $map = Input::get('map');
        $clan = Input::get('clan');
        $limit = Input::get('limit');
        $skip = Input::get('skip');
        return $this->couchAPI->getOpLiveFeedPaged($map, $clan, $name, $limit, $skip);
    }

    /**
     * @return string
     */
    public function getOpliveaarfeedpaged()
    {
        $name = Input::get('name');
        $map = Input::get('map');
        $clan = Input::get('clan');
        $start = Input::get('start');
        $end = Input::get('end');
        return $this->couchAPI->getOpLiveAARFeedPaged($map, $clan, $name, $start, $end);
    }

    /**
     * @return string
     */
    public function getLossesblu()
    {
        return $this->couchAPI->getLossesBLU();
    }

    /**
     * @return string
     */
    public function getLossesopf()
    {
        return $this->couchAPI->getLossesOPF();
    }

    /**
     * @return string
     */
    public function getCasualties()
    {
        return $this->couchAPI->getCasualties();
    }

    /**
     * @return string
     */
    public function getOpcasualties()
    {
        $name = Input::get('name');
        $map = Input::get('map');
        $clan = Input::get('clan');
        return $this->couchAPI->getOpCasualties($name, $map, $clan);
    }

    /**
     * @return string
     */
    public function getOplossesblu()
    {
        $name = Input::get('name');
        $map = Input::get('map');
        $clan = Input::get('clan');
        return $this->couchAPI->getOpLossesBLU($name, $map, $clan);
    }

    /**
     * @return string
     */
    public function getOplossesopf()
    {
        $name = Input::get('name');
        $map = Input::get('map');
        $clan = Input::get('clan');
        return $this->couchAPI->getOpLossesOPF($name, $map, $clan);
    }

    /**
     * @return string
     */
    public function getOperationsbymap()
    {
        return $this->couchAPI->getOperationsByMap();
    }

    /**
     * @return string
     */
    public function getOperationsbyday()
    {
        return $this->couchAPI->getOperationsByDay();
    }

    /**
     * @return string
     */
    public function getPlayersbyday()
    {
        return $this->couchAPI->getPlayersByDay();
    }

    /**
     * @return string
     */
    public function getKillsbyday()
    {
        return $this->couchAPI->getKillsByDay();
    }

    /**
     * @return string
     */
    public function getDeathsbyday()
    {
        return $this->couchAPI->getDeathsByDay();
    }

    /**
     * @return bool|string
     */
    public function getT1operators()
    {
        return $this->couchAPI->getT1Operators();
    }

    /**
     * @return bool|string
     */
    public function getDevcredits()
    {
        $id = Input::get('id');
        return $this->couchAPI->getDevCredits($id);
    }

    /**
     * @return bool|string
     */
    public function getPlayerdetails()
    {
        $id = Input::get('id');
        return $this->couchAPI->getPlayerDetails($id);
    }

    /**
     * @return bool|string
     */
    public function getPlayerweapon()
    {
        $id = Input::get('id');
        return $this->couchAPI->getPlayerWeapon($id);
    }

    /**
     * @return bool|string
     */
    public function getPlayerweapons()
    {
        $id = Input::get('id');
        return $this->couchAPI->getPlayerWeapons($id);
    }

    /**
     * @return bool|string
     */
    public function getPlayervehicle()
    {
        $id = Input::get('id');
        return $this->couchAPI->getPlayerVehicle($id);
    }

    /**
     * @return bool|string
     */
    public function getPlayervehicles()
    {
        $id = Input::get('id');
        return $this->couchAPI->getPlayerVehicles($id);
    }

    /**
     * @return bool|string
     */
    public function getPlayerclasses()
    {
        $id = Input::get('id');
        return $this->couchAPI->getPlayerClasses($id);
    }

    /**
     * @return bool|string
     */
    public function getPlayeralias()
    {
        $id = Input::get('id');
        return $this->couchAPI->getPlayerAlias($id);
    }

    /**
     * @return bool|string
     */
    public function getPersonneltotals()
    {
        return $this->couchAPI->getPersonnelTotals();
    }

    /**
     * @return bool|string
     */
    public function getT1marksmen()
    {
        return $this->couchAPI->getT1Marksmen();
    }

    /**
     * @return bool|string
     */
    public function getVehiclecommanders()
    {
        return $this->couchAPI->getVehicleCommanders();
    }

    /**
     * @return bool|string
     */
    public function getPilots()
    {
        return $this->couchAPI->getPilots();
    }

    /**
     * @return bool|string
     */
    public function getMedics()
    {
        return $this->couchAPI->getMedics();
    }

    /**
     * @return bool|string
     */
    public function getScores()
    {
        return $this->couchAPI->getScores();
    }

    /**
     * @return bool|string
     */
    public function getAvescores()
    {
        return $this->couchAPI->getAveScores();
    }

    /**
     * @return bool|string
     */
    public function getRatings()
    {
        return $this->couchAPI->getRatings();
    }

    /**
     * @return bool|string
     */
    public function getGrouptotals()
    {
        return $this->couchAPI->getGroupTotals();
    }

    /**
     * @return bool|string
     */
    public function getGrouptotalsbytag()
    {
        $id = Input::get('id');
        return $this->couchAPI->getGroupTotalsByTag($id);
    }

    /**
     * @return bool|string
     */
    public function getGroupclasses()
    {
        $id = Input::get('id');
        return $this->couchAPI->getGroupClasses($id);
    }

    /**
     * @return bool|string
     */
    public function getOrbatrecentoperations()
    {
        $id = Input::get('id');
        return $this->couchAPI->getOrbatRecentOperations($id);
    }

    /**
     * @return bool|string
     */
    public function getOrbatt1()
    {
        $id = Input::get('id');
        return $this->couchAPI->getOrbatT1($id);
    }

    /**
     * @return bool|string
     */
    public function getOrbatmedics()
    {
        $id = Input::get('id');
        return $this->couchAPI->getOrbatMedics($id);
    }

    /**
     * @return bool|string
     */
    public function getOrbatpilots()
    {
        $id = Input::get('id');
        return $this->couchAPI->getOrbatPilots($id);
    }

    /**
     * @return bool|string
     */
    public function getOrbatkillsbyweapon()
    {
        $id = Input::get('id');
        return $this->couchAPI->getOrbatKillsByWeapon($id);
    }

    /**
     * @return bool|string
     */
    public function getOrbatweapons()
    {
        $id = Input::get('id');
        return $this->couchAPI->getOrbatWeapons($id);
    }

    /**
     * @return bool|string
     */
    public function getOrbatvehicles()
    {
        $id = Input::get('id');
        return $this->couchAPI->getOrbatVehicles($id);
    }

    /**
     * @return bool|string
     */
    public function getOrbatplayerkills()
    {
        $id = Input::get('id');
        return $this->couchAPI->getOrbatPlayerKills($id);
    }

    /**
     * @return bool|string
     */
    public function getOrbatmountedkills()
    {
        $id = Input::get('id');
        return $this->couchAPI->getOrbatMountedKills($id);
    }

    /**
     * @return bool|string
     */
    public function getOrbatclasses()
    {
        $id = Input::get('id');
        return $this->couchAPI->getOrbatClasses($id);
    }

    /**
     * @return bool|string
     */
    public function getOperations()
    {
        return $this->couchAPI->getOperations();
    }

    /**
     * @return bool|string
     */
    public function getOpsbreakdown()
    {
        return $this->couchAPI->getOpsBreakdown();
    }

    /**
     * @return string
     */
    public function getServerperf()
    {
        $id = Input::get('id');
        $servername = Input::get('servername');
        $type = Input::get('type');
        return $this->couchAPI->getServerPerf($id, $type, $servername);
    }

    /**
     * @return string
     */
    public function getServerperfall()
    {
        $id = Input::get('id');
        return $this->couchAPI->getServerPerfAll($id);
    }

    /**
     * @return string
     */
    public function getServerperfdate()
    {
        $date = Input::get('date');
        return $this->couchAPI->getServerPerfDate($date);
    }

    /**
     * @return string
     */
    public function getServerperfcheck()
    {
        return $this->couchAPI->getServerPerfCheck();
    }

    /**
     * @return string
     */
    public function getClanfeed()
    {
        $id = Input::get('id');
        return $this->couchAPI->getClanFeed($id);
    }

    /**
     * @return string
     */
    public function getPlayerfeed()
    {
        $id = Input::get('id');
        return $this->couchAPI->getPlayerFeed($id);
    }

    /**
     *
     */
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
