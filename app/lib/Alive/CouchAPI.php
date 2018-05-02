<?php

namespace Alive;

use Tempo\TempoDebug;

ini_set('max_execution_time', 600);

class CouchAPI {

    private $user = 'AliveAdmin';
    private $pass = 'tupolov';
    private $url = 'http://db.alivemod.com:5984/';
    
    public $reset = false;
    public $debug = false;
    public $cache = true;
    
    public $timeout = 60;

    public function createClanUser($name, $password, $group)
    {
        $path = '_users/org.couchdb.user:' . $name;

        $data = array(
            'name' => $name,
            'roles' => ['writer','reader'],
            'type' => 'user',
            'password' => $password,
			'ServerGroup' => $group,
        );

        $requestType = 'PUT';

        return $this->call($path, $data, $requestType);
    }

    public function deleteClanUser($name, $rev)
    {
        $path = '_users/org.couchdb.user:' . $name . '?rev=' . $rev;

        $requestType = 'DELETE';

        return $this->call($path, array(), $requestType);
    }

    public function getClanUser($name, $password)
    {
        $path = '_users/org.couchdb.user:' . $name;

        $data = array(
            'name' => $name,
            'password' => $password,
        );

        $requestType = 'GET';

        return $this->call($path, $data, $requestType);
    }

    public function createClanMember($a3Id, $username, $group)
    {
        $path = 'players/' . $a3Id;

        $data = array(
            'username' => $username,
            'ServerGroup' => $group,
            'A3PUID' => $a3Id,
        );

        $requestType = 'PUT';

        return $this->call($path, $data, $requestType);
    }

    public function deleteClanMember($a3Id, $rev)
    {
        $path = 'players/' . $a3Id . '?rev=' . $rev;

        $requestType = 'DELETE';

        return $this->call($path, array(), $requestType);
    }
	
    public function updateClanMember($a3Id, $username, $group, $rev)
    {
        $path = 'players/' . $a3Id . '?rev=' . $rev;
		
		$data = array(
            'username' => $username,
            'ServerGroup' => $group,
            'A3PUID' => $a3Id,
        );

        $requestType = 'PUT';

        return $this->call($path, $data, $requestType);
    }	

    public function getClanMember($a3Id)
    {
        $path = 'players/' . $a3Id;

        $data = [];

        $requestType = 'GET';

        return $this->call($path, $data, $requestType);
    }

    public function getAar($group, $map, $operation)
    {
        $group = rawurlencode($group);
        $map = rawurlencode($map);
        $operation = rawurlencode($operation);
        $path = 'sys_aar/_design/AAR/_view/titan_aar?key=['
            . '"' . $group . '",'
            . '"' . $map . '",'
            . '"' . $operation . '"]&limit=2000';
        return $this->call($path, [], 'GET', true);
    }

    public function getPlayersByOperation($group, $map, $operation)
    {
        $group = rawurlencode($group);
        $map = rawurlencode($map);
        $operation = rawurlencode($operation);
        $key1 = '["'.$map.'","'.$group.'","'.$operation.'"]';
        $key2 = '["'.$map.'","'.$group.'","'.$operation.'",{}]';
        $path = 'events/_design/operationPage/_view/players_list_names'
            . '?startkey='.$key1
            . '&endkey='.$key2
            . '&group=true';
        return $this->call($path, [], 'GET', true);
    }
    
    public function getOperationEvents($group, $map, $operation)
    {
        $group = rawurlencode($group);
        $map = rawurlencode($map);
        $operation = rawurlencode($operation);
        $path = 'events/_design/operationPage/_view/operation_events?key=['
            . '"' . $map . '",'
            . '"' . $group . '",'
            . '"' . $operation . '"]';
        return $this->call($path, [], 'GET', true);
    }
	
	public function deleteOldEvents()
	{
		$path = 'events/_design/homePage/_view/old_events';
		
		$data = $this->call($path);
	
		if(isset($data['response']->rows[0])) {	

			$data = $data['response']->rows;

            $results = '';
					
            $i = 0;
			foreach ($data as $item){
                $i++;
                $eventpath = 'events/' . $item->id . '?rev=' . $item->value->_rev;

				$result = $this->call($eventpath, array(), 'DELETE');
                $result = $i . '. ' . $item->id . ' : ' . $result['response']->ok . PHP_EOL;
                $results = $results . $result;

			}

            return $results;
		}
		
	}

    public function getTotals()
    {
        $cacheKey = 'Totals';

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/homePage/_view/Totals';

        $data = $this->call($path);

        if(isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->value;

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getOptotals($name, $map, $clan)
    {
        $cacheKey = 'Totals' . $name . $map . $clan;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/homePage/_view/Totals?group_level=3';

        $data = $this->call($path);

        if(isset($data['response']->rows[0])) {

            $data = $data['response']->rows;

            foreach($data as $item){
                    if($item->key[0] == $map && $item->key[1] == $clan && $item->key[2] == $name){
                        $result = $item->value;
                    }
            }
			
            if($this->debug){
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getMapTotals($name)
    {

        $cacheKey = 'MapTotals' . $name;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $name = rawurlencode($name);

        $path = 'events/_design/operationsTable/_view/operationTotals?group_level=1&startkey=["' . $name . '"]&limit=1&stale=ok';

        $data = $this->call($path);

        if(isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->value;

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "day");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getActiveUnitCount()
    {

        $cacheKey = 'ActiveUnits';

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/homePage/_view/players_list?group_level=2';

        $data = $this->call($path);

        if(isset($data['response']->rows)) {

            $data = count($data['response']->rows);

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "day");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getOpActiveUnitCount($name, $map, $clan)
    {

        $cacheKey = 'OpActiveUnits' . $name . $map . $clan;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $map = rawurlencode($map);
        $name = rawurlencode($name);
        $clan = rawurlencode($clan);

        $path = 'events/_design/operationPage/_view/players_list?group_level=3';

        $data = $this->call($path);

        if(isset($data['response']->rows)) {

            $data = $data['response']->rows;

            foreach($data as $item){
                    if($item->key[0] == $map && $item->key[1] == $clan && $item->key[2] == $name){
                        $result = $item->value;
                    }
            }
			
            if($this->debug){
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "day");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getRecentOperations()
    {

        $cacheKey = 'RecentOperations';

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/homePage/_view/recent_operations?descending=true&limit=20';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, $this->_set_timeout("hour"));

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

	public function getLiveFeed()
    {

        $path = 'events/_design/homePage/_view/all_events?descending=true&limit=50';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getOpLiveFeed($map, $clan, $name)
    {
        $map = rawurlencode($map);
        $name = rawurlencode($name);
        $clan = rawurlencode($clan);
        $path = 'events/_design/operationPage/_list/sort_no_callback/operation_events?startkey=["' . $map . '","' . $clan . '","' . $name . '",{}]&endkey=["' . $map . '","' . $clan . '","' . $name . '"]&descending=true';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getOpLiveFeedPaged($map, $clan, $name, $limit, $skip)
    {
        $map = rawurlencode($map);
        $name = rawurlencode($name);
		$clan = rawurlencode($clan);

        $path = 'events/_design/operationPage/_list/sort_no_callback/operation_timeline_events?startkey=["' . $map . '","' . $clan . '","' . $name . '",{}]&endkey=["' . $map . '","' . $clan . '","' . $name . '"]&descending=true&limit=' . $limit . '&skip=' . $skip;

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getOpLiveAARFeedPaged($map, $clan, $name, $start, $end)
    {
        $map = rawurlencode($map);
        $name = rawurlencode($name);
		$clan = rawurlencode($clan);
				
        $path = 'sys_aar/_design/AAR/_list/sort_no_callback/operation_aar?startkey=["' . $map . '","' . $clan . '","' . $name . '","' . $start . '",{}]&endkey=["' . $map . '","' . $clan . '","' . $name . '","' . $end . '"]';
		
        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

        }else{
            $encoded = json_encode([$path]);
        }
        return $encoded;
    }

    public function getLossesBLU()
    {

        $cacheKey = 'LossesBLU';

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/homePage/_view/side_killed_count_by_class?group_level=2';

        $data = $this->call($path);

        if(isset($data['response']->rows)) {

            $data = $data['response']->rows;
            $result = array();

            foreach($data as $item){
                if($item->value > 0){
                    if($item->key[0] == 'WEST' && !is_null($item->key[1]) && $item->key[1] != 'any'){
                        array_push($result, [$item->key[1], $item->value]);
                    }
                }
            }

            if($this->debug){
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "day");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getLossesOPF()
    {

        $cacheKey = 'LossesOPF';

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/homePage/_view/side_killed_count_by_class?group_level=2';

        $data = $this->call($path);

        if(isset($data['response']->rows)) {

            $data = $data['response']->rows;
            $result = array();

            foreach($data as $item){
                if($item->value > 0){
                    if($item->key[0] == 'EAST' && !is_null($item->key[1]) && $item->key[1] != 'any'){
                        array_push($result, [$item->key[1], $item->value]);
                    }
                }
            }

            if($this->debug){
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "day");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getCasualties()
    {

        $cacheKey = 'Casualties';

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/homePage/_view/side_killed_count?group_level=1';

        $data = $this->call($path);

        if(isset($data['response']->rows)) {

            $data = $data['response']->rows;
            $result = array();

            foreach($data as $item){
                if($item->value > 0){
                    array_push($result, [$item->key, $item->value]);
                }
            }

            if($this->debug){
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "day");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
   public function getOpLossesBLU($name, $map, $clan)
    {

        $cacheKey = 'OpLossesBLU' . $name . $map . $clan;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/operationPage/_view/side_killed_count_by_class?group_level=5';

        $data = $this->call($path);

        if(isset($data['response']->rows)) {

            $data = $data['response']->rows;
            $result = array();

            foreach($data as $item){
                if($item->value > 0){
                    if($item->key[3] == 'WEST' && $item->key[0] == $map && $item->key[1] == $clan && $item->key[2] == $name){
                        array_push($result, [$item->key[4], $item->value]);
                    }
                }
            }

            if($this->debug){
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "day");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getOpLossesOPF($name, $map, $clan)
    {

        $cacheKey = 'OpLossesOPF' . $name . $map . $clan;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/operationPage/_view/side_killed_count_by_class?group_level=5';

        $data = $this->call($path);

        if(isset($data['response']->rows)) {

            $data = $data['response']->rows;
            $result = array();

            foreach($data as $item){
                if($item->value > 0){
                    if($item->key[3] == 'EAST' && $item->key[0] == $map && $item->key[1] == $clan && $item->key[2] == $name){
                        array_push($result, [$item->key[4], $item->value]);
                    }
                }
            }

            if($this->debug){
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "day");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getOpCasualties($name, $map, $clan)
    {

        $cacheKey = 'OpCasualties' . $name . $map . $clan;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/operationPage/_view/side_killed_count?group_level=4';

        $data = $this->call($path);

        if(isset($data['response']->rows)) {

            $data = $data['response']->rows;
            $result = array();

            foreach($data as $item){
                if($item->value > 0){
					if($item->key[0] == $map && $item->key[1] == $clan && $item->key[2] == $name){
                        array_push($result, [$item->key[3], $item->value]);
                    }
                }
            }

            if($this->debug){
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
    public function getOperationsByMap()
    {

        $cacheKey = 'OperationsByMap';

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/homePage/_view/operations_by_map?group_level=1';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response']->rows;
            $result = array();

            foreach($data as $item){
                if($item->value > 0){
                    if(!is_null($item->key)){
                       array_push($result, [$item->key, $item->value]);
                    }
                }
            }

            if($this->debug){
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getOperationsByDay()
    {

        $cacheKey = 'OperationsByDay';

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/operationsTable/_view/operations_by_day?group_level=1&stale=ok';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response']->rows;
            $result = array();

            foreach($data as $item){
                if($item->value > 0){
                    if(!is_null($item->key[0])){
                        array_push($result, [$item->key[0], $item->value]);
                    }
                }
            }

            if($this->debug){
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "day");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getPlayersByDay()
    {

        $cacheKey = 'PlayersByDay';

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/operationsTable/_view/players_by_day?group_level=1&stale=ok';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response']->rows;
            $result = array();

            foreach($data as $item){
                if($item->value > 0){
                    if(!is_null($item->key[0])){
                        array_push($result, [$item->key[0], $item->value]);
                    }
                }
            }

            if($this->debug){
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "day");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getKillsByDay()
    {

        $cacheKey = 'KillsByDay';

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/operationsTable/_view/kills_by_day?group_level=1&stale=ok';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response']->rows;
            $result = array();

            foreach($data as $item){
                if($item->value > 0){
                    if(!is_null($item->key[0])){
                        array_push($result, [$item->key[0], $item->value]);
                    }
                }
            }

            if($this->debug){
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "day");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getDeathsByDay()
    {

        $cacheKey = 'DeathsByDay';

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/operationsTable/_view/deaths_by_day?group_level=1&stale=ok';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response']->rows;
            $result = array();

            foreach($data as $item){
                if($item->value > 0){
                    if(!is_null($item->key[0])){
                        array_push($result, [$item->key[0], $item->value]);
                    }
                }
            }

            if($this->debug){
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "day");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getT1Operators()
    {

        $cacheKey = 'T1Operators';

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/homePage/_view/player_kills_count?group_level=2';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getDevCredits($id)
    {

        $cacheKey = 'Devcredits' . $id;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'credits/_design/warroom/_view/devcredits?key="' . $id . '"&stale=ok';

        $data = $this->call($path);

        if(isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->value;

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

	public function getPlayerTotals($id)
    {

        $cacheKey = 'PlayerTotals' . $id;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/playerPage/_view/playerTotals?group_level=1&startkey="' . $id . '"&endkey="' . $id . '"';

        $data = $this->call($path);

        if(isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->value;

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

	public function getPlayerDetails($id)
    {

        $cacheKey = 'PlayerDetails' . $id;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/playerPage/_list/sort_no_callback/player_finish?&startkey="' . $id . '"&endkey="' . $id . '"';

        $data = $this->call($path);

        if(isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->value;

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

	public function getPlayerWeapon($id)
    {

        $cacheKey = 'PlayerWeapon' . $id;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/playerPage/_list/sort_by_value/players_weapons?group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';

        $data = $this->call($path);

        if(isset($data['response']->rows[0])) {

            	$data = $data['response']->rows[0]->key;
			
            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getPlayerWeapons($id)
    {

        $cacheKey = 'PlayerWeapons' . $id;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/playerPage/_view/players_weapons?&group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            	$data = $data['response'];
			
            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getPlayerVehicle($id)
    {

        $cacheKey = 'PlayerVehicle' . $id;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/playerPage/_list/sort_by_value/players_vehxp?group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';

        $data = $this->call($path);

        if(isset($data['response']->rows[0])) {

            	$data = $data['response']->rows[0]->key;

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getPlayerVehicles($id)
    {

        $cacheKey = 'PlayerVehicles' . $id;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/playerPage/_view/players_vehxp?&group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            	$data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getPlayerClass($id)
    {

        $cacheKey = 'PlayerClass' . $id;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/playerPage/_list/sort_by_value/players_class?group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';

        $data = $this->call($path);

        if(isset($data['response']->rows[0])) {

            	$data = $data['response']->rows[0]->key;

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getPlayerClasses($id)
    {

        $cacheKey = 'PlayerClasses' . $id;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/playerPage/_view/players_class?&group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            	$data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getPlayerAlias($id)
    {

        $cacheKey = 'PlayerAlias' . $id;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/playerPage/_list/sort_by_value/players_alias?group_level=2&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getGroupTotals()
    {

        $cacheKey = 'GroupTotals';

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/groupTable/_view/groupTotals?group_level=1&stale=ok';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getGroupTotalsByTag($id)
    {

        $cacheKey = 'GroupTotals' . $id;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $id = rawurlencode($id);
		
        $path = 'events/_design/groupTable/_view/groupTotals?group_level=1&startkey=["' . $id . '"]&endkey=["' . $id . '",{}]&stale=ok';

        $data = $this->call($path);

        if(isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->value;

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getGroupClasses($id)
    {

        $cacheKey = 'GroupClasses' . $id;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $id = rawurlencode($id);
		
        $path = 'events/_design/groupPage/_view/group_classes?group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getGroupLastOp($id)
    {

        $cacheKey = 'GroupLastOp' . $id;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_list/sort_no_callback/group_finish?key=%22' . $id . '%22';

        $data = $this->call($path);

        if(isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->value;

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getPersonnelTotals()
    {

        $cacheKey = 'PersonnelTotals';

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $this->timeout = 360;

        $path = 'events/_design/playerTable/_view/playerTotals?group_level=2&stale=ok';

        $data = $this->call($path);    

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getT1Marksmen()
    {

        $cacheKey = 'T1marksmen';

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/playerTable/_view/kills_by_distance?group_level=4&stale=ok';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getVehicleCommanders()
    {

        $cacheKey = 'VehicleCommanders';

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/playerTable/_view/player_in_vehicle_kills_count?group_level=4&stale=ok';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getPilots()
    {

        $cacheKey = 'Pilots';

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/playerTable/_view/player_in_aircraft_kills_count?group_level=4&stale=ok';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getMedics()
    {

        $cacheKey = 'Medics';

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/playerTable/_view/player_heals_count?group_level=2&stale=ok';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getScores()
    {

        $cacheKey = 'Scores';

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/playerTable/_view/scoreTotal?group_level=2&stale=ok';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getRatings()
    {

        $cacheKey = 'Ratings';

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/playerTable/_view/AveRating?group_level=2&stale=ok';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

	public function getAvescores()
    {

        $cacheKey = 'Avescores';

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/playerTable/_view/AveScore?group_level=2&stale=ok';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

 public function getOrbatRecentOperations($id)
    {

        $cacheKey = 'OrbatRecentOperations' . $id;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_list/sort_no_callback/group_recent_ops?startkey=%22' . $id . '%22&endkey=%22' . $id . '%22&descending=true&limit=50';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
			
	public function getOrbatT1($id)
    {

        $cacheKey = 'OrbatT1'. $id;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_view/player_kills_count?group_level=3&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getOrbatPilots($id)
    {

        $cacheKey = 'OrbatPilots'. $id;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_view/player_in_aircraft_kills_count?group_level=5&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getOrbatMedics($id)
    {

        $cacheKey = 'OrbatMedics'. $id;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_view/player_heals_count?group_level=3&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getOrbatKillsByWeapon($id)
    {

        $cacheKey = 'OrbatKillsByWeapon' . $id;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_view/group_killsByWeapon?group_level=3&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getOrbatWeapons($id)
    {

        $cacheKey = 'OrbatWeapons' . $id;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_view/group_weapons?group_level=3&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getOrbatVehicles($id)
    {

        $cacheKey = 'OrbatVehicles' . $id;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_view/group_veh?group_level=3&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getOrbatPlayerKills($id)
    {

        $cacheKey = 'OrbatPlayerKills' . $id;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_view/player_kills_count_bygroup?&group_level=5&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getOrbatMountedKills($id)
    {

        $cacheKey = 'OrbatMountedKills' . $id;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_view/group_mwk?group_level=3&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getOrbatClasses($id)
    {

        $cacheKey = 'OrbatClasses' . $id;

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_view/group_classes?&group_level=3&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getOperations()
    {

        $cacheKey = 'Operations';

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/operationsTable/_view/operationKillsByClass?group_level=3&stale=ok';

        $data = $this->call($path);

        if(isset($data['response'])) {

            	$data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getOpsBreakdown()
    {

        $cacheKey = 'OpsBreakdown';

        if($cache = $this->getCache($cacheKey)){ return $cache;}

        $path = 'events/_design/operationsTable/_view/operationTotals?group_level=3&stale=ok';

        $data = $this->call($path);

        if(isset($data['response'])) {

            	$data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getServerPerf($id,$type,$servername)
    {

        $id = rawurlencode($id);
        $type = rawurlencode($type);
        $server = rawurlencode($servername);

        $path = 'sys_perf/_design/sys_perf/_view/'. $type .'?startkey=%22' . $id . '%22&endkey=%22' . $id . '%22';

        $data = $this->call($path);

        if(isset($data['response']->rows[0]->key)) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

		}else{
			if($this->debug){
                TempoDebug::dump($data);
			}
	   		 $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getServerPerfAll($id)
    {

        $id = rawurlencode($id);
		
        $path = 'sys_perf/_design/sys_perf/_view/all_perf_server?startkey=%22' . $id . '%22&endkey=%22' . $id . '%22';

        $data = $this->call($path);

        if(isset($data['response']->rows[0]->key)) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

		}else{
			 if($this->debug){
                TempoDebug::dump($data);	
			 }
	   		 $encoded = json_encode([]);
        }

        return $encoded;
    }	

	public function getServerPerfDate($date)
    {

        $date = rawurlencode($date);
		
        $path = 'sys_perf/_design/sys_perf/_view/all_perf?startkey=%22' . $date . '%22';

        $data = $this->call($path);

        if(isset($data['response']->rows[0]->key)) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

		}else{
			 if($this->debug){
                TempoDebug::dump($data);
			 }
	   		 $encoded = json_encode([]);
        }

        return $encoded;
    }	
	
	public function getServerPerfCheck()
    {

        $path = 'sys_perf/_design/sys_perf/_view/server_count_data?group=true';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

		}else{
                TempoDebug::dump($data);			
	   		 $encoded = json_encode([]);
        }

        return $encoded;
    }	
	
	public function getClanFeed($id)
    {

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_list/sort_no_callback/group_events?startkey=[%22' . $id . '%22,{}]&endkey=[%22' . $id . '%22]&descending=true&limit=50';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getPlayerFeed($id)
    {

        $path = 'events/_design/playerPage/_list/sort_no_callback/player_events?startkey=[%22' . $id . '%22,{}]&endkey=[%22' . $id . '%22]&descending=true&limit=50';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getCache($cacheKey)
    {
        if (\Cache::has($cacheKey) && !$this->reset && $this->cache) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }else{
            return false;
        }
    }

    public function setCache($cacheKey, $data, $timeout)
    {
        if ($this->cache) {
            \Cache::add($cacheKey, $data, $this->_set_timeout($timeout));
        }
    }

    public function call($path, $data=array(), $requestType='GET', $associative = false)
    {
        $payload = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
        curl_setopt($ch, CURLOPT_URL, $this->url . $path);
        curl_setopt($ch, CURLOPT_USERPWD, $this->user . ':' . $this->pass);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestType);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);        
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout); //timeout in seconds
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json',
            'Accept: application/json'
        ));
        if($this->debug){
            TempoDebug::message($this->url . $path);
            TempoDebug::dump($payload, 'Payload');
            $profiler = TempoDebug::startProfile();
        }
        $response = curl_exec($ch);
        $result = array();
        $result['info'] = curl_getinfo($ch);
        $result['error'] = curl_error($ch);
        $result['response'] = json_decode($response, $associative);

        if($this->debug){
            TempoDebug::stopProfile($profiler);
            TempoDebug::dump($result);
        }

        curl_close($ch);

        return $result;
    }

    protected function _set_timeout($length){

        $minutes = 60;

        switch($length){
            case 'minute':
                $minutes = 1;
                break;
            case 'ten-minutes':
                $minutes = 10 + rand(0,5);
                break;
            case 'hour':
                $minutes = 60 + rand(0,10);
                break;
            case 'three-hours':
                $minutes = 180 + rand(0,10);
                break;
            case 'six-hours':
                $minutes = 360 + rand(0,10);
                break;
            case 'twelve-hours':
                $minutes = 720 + rand(0,10);
                break;
            case 'day':
                $minutes = 1440 + rand(0,10);
                break;
        }

        if($this->debug){
            TempoDebug::dump($minutes , 'Cache Timeout value');
        }

        return $minutes;
    }
}