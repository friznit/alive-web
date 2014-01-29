<?php

namespace Alive;

use Tempo\TempoDebug;

ini_set('max_execution_time', 60);

class CouchAPI {

    private $user = 'aliveadmin';
    private $pass = 'tupolov';
    private $url = 'https://alive.iriscouch.com/';
    private $reset = false;
    private $debug = false;

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

    public function getTotals()
    {

        $cacheKey = 'Totals';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/homePage/_view/Totals';

        $data = $this->call($path);

        if(isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->value;

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 1000);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	 public function getMapTotals($name)
    {

        $cacheKey = 'MapTotals' . $name;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/operationsTable/_view/operationTotals?group_level=1&startkey=["' . $name . '"]&limit=1';

        $data = $this->call($path);

        if(isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->value;

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 1000);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getActiveUnitCount()
    {

        $cacheKey = 'ActiveUnits';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/homePage/_view/players_list?group_level=2';

        $data = $this->call($path);

        if(isset($data['response']->rows)) {

            $data = count($data['response']->rows);

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 1000);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getRecentOperations()
    {

        $cacheKey = 'RecentOperations';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/homePage/_view/recent_operations?descending=true&limit=50';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

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

    public function getLossesBLU()
    {

        $cacheKey = 'LossesBLU';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

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

            \Cache::add($cacheKey, $encoded, 1000);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getLossesOPF()
    {

        $cacheKey = 'LossesOPF';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

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

            \Cache::add($cacheKey, $encoded, 1000);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getCasualties()
    {

        $cacheKey = 'Casualties';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

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

            \Cache::add($cacheKey, $encoded, 1000);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getOperationsByMap()
    {

        $cacheKey = 'OperationsByMap';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

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

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getOperationsByDay()
    {

        $cacheKey = 'OperationsByDay';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/operationsTable/_view/operations_by_day?group_level=1';

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

            \Cache::add($cacheKey, $encoded, 1000);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getPlayersByDay()
    {

        $cacheKey = 'PlayersByDay';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/operationsTable/_view/players_by_day?group_level=1';

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

            \Cache::add($cacheKey, $encoded, 1000);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getKillsByDay()
    {

        $cacheKey = 'KillsByDay';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/operationsTable/_view/kills_by_day?group_level=1';

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

            \Cache::add($cacheKey, $encoded, 1000);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getDeathsByDay()
    {

        $cacheKey = 'DeathsByDay';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/operationsTable/_view/deaths_by_day?group_level=1';

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

            \Cache::add($cacheKey, $encoded, 1000);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    public function getT1Operators()
    {

        $cacheKey = 'T1Operators';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/homePage/_view/player_kills_count?group_level=2';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getDevCredits($id)
    {

        $cacheKey = 'Devcredits' . $id;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'credits/_design/warroom/_view/devcredits?key="' . $id . '"';

        $data = $this->call($path);

        if(isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->value;

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

	public function getPlayerTotals($id)
    {

        $cacheKey = 'PlayerTotals' . $id;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/playerPage/_view/playerTotals?group_level=1&startkey="' . $id . '"&limit=1';

        $data = $this->call($path);

        if(isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->value;

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

	public function getPlayerDetails($id)
    {

        $cacheKey = 'PlayerDetails' . $id;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/playerPage/_list/sort_no_callback/player_finish?key=%22' . $id . '%22';

        $data = $this->call($path);

        if(isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->value;

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

	public function getPlayerWeapon($id)
    {

        $cacheKey = 'PlayerWeapon' . $id;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }


        	$path = 'events/_design/playerPage/_list/sort_by_value/players_weapons?group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';


        $data = $this->call($path);

        if(isset($data['response']->rows[0])) {

            	$data = $data['response']->rows[0]->key;
			
            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getPlayerWeapons($id)
    {

        $cacheKey = 'PlayerWeapons' . $id;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }


        	$path = 'events/_design/playerPage/_view/players_weapons?&group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';


        $data = $this->call($path);

        if(isset($data['response'])) {

            	$data = $data['response'];
			
            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getPlayerVehicle($id)
    {

        $cacheKey = 'PlayerVehicle' . $id;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        	$path = 'events/_design/playerPage/_list/sort_by_value/players_vehxp?group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';


        $data = $this->call($path);

        if(isset($data['response']->rows[0])) {

            	$data = $data['response']->rows[0]->key;

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getPlayerVehicles($id)
    {

        $cacheKey = 'PlayerVehicles' . $id;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        	$path = 'events/_design/playerPage/_view/players_vehxp?&group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';


        $data = $this->call($path);

        if(isset($data['response'])) {

            	$data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getPlayerClass($id)
    {

        $cacheKey = 'PlayerClass' . $id;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        	$path = 'events/_design/playerPage/_list/sort_by_value/players_class?group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';

        $data = $this->call($path);

        if(isset($data['response']->rows[0])) {

            	$data = $data['response']->rows[0]->key;

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getPlayerClasses($id)
    {

        $cacheKey = 'PlayerClasses' . $id;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        	$path = 'events/_design/playerPage/_view/players_class?&group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            	$data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getPlayerAlias($id)
    {

        $cacheKey = 'PlayerAlias' . $id;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/playerPage/_list/sort_by_value/players_alias?group_level=2&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getGroupTotals()
    {

        $cacheKey = 'GroupTotals';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/groupTable/_view/groupTotals?group_level=1';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getGroupTotalsByTag($id)
    {

        $cacheKey = 'GroupTotals' . $id;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }
		
        $path = 'events/_design/groupTable/_view/groupTotals?group_level=1&startkey=["' . $id . '"]&limit=1';

        $data = $this->call($path);

        if(isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->value;

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getGroupClasses($id)
    {

        $cacheKey = 'GroupClasses' . $id;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }
		
        $path = 'events/_design/groupPage/_view/group_classes?group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getGroupLastOp($id)
    {

        $cacheKey = 'GroupLastOp' . $id;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/groupPage/_list/sort_no_callback/group_finish?key=%22' . $id . '%22';

        $data = $this->call($path);

        if(isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->value;

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getPersonnelTotals()
    {

        $cacheKey = 'PersonnelTotals';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/playerTable/_view/playerTotals?group_level=2';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getT1Marksmen()
    {

        $cacheKey = 'T1marksmen';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/playerTable/_view/kills_by_distance?group_level=4';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getVehicleCommanders()
    {

        $cacheKey = 'VehicleCommanders';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/playerTable/_view/player_in_vehicle_kills_count?group_level=4';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getPilots()
    {

        $cacheKey = 'Pilots';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/playerTable/_view/player_in_aircraft_kills_count?group_level=4';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getMedics()
    {

        $cacheKey = 'Medics';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/playerTable/_view/player_heals_count?group_level=2';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getScores()
    {

        $cacheKey = 'Scores';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/playerTable/_view/scoreTotal?group_level=2';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getRatings()
    {

        $cacheKey = 'Ratings';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/playerTable/_view/AveRating?group_level=2';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

	public function getAvescores()
    {

        $cacheKey = 'Avescores';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/playerTable/_view/AveScore?group_level=2';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }

 public function getOrbatRecentOperations($id)
    {

        $cacheKey = 'OrbatRecentOperations' . $id;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/groupPage/_list/sort_no_callback/group_recent_ops?startkey=%22' . $id . '%22&endkey=%22' . $id . '%22&descending=true&limit=10';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
			
	public function getOrbatT1($id)
    {

        $cacheKey = 'OrbatT1'. $id;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/groupPage/_view/player_kills_count?group_level=3&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getOrbatPilots($id)
    {

        $cacheKey = 'OrbatPilots'. $id;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/groupPage/_view/player_in_aircraft_kills_count?group_level=5&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getOrbatMedics($id)
    {

        $cacheKey = 'OrbatMedics'. $id;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/groupPage/_view/player_heals_count?group_level=3&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getOrbatKillsByWeapon($id)
    {

        $cacheKey = 'OrbatKillsByWeapon' . $id;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/groupPage/_view/group_killsByWeapon?group_level=3&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getOrbatWeapons($id)
    {

        $cacheKey = 'OrbatWeapons' . $id;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/groupPage/_view/group_weapons?group_level=3&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getOrbatVehicles($id)
    {

        $cacheKey = 'OrbatVehicles' . $id;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/groupPage/_view/group_veh?group_level=3&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getOrbatPlayerKills($id)
    {

        $cacheKey = 'OrbatPlayerKills' . $id;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/groupPage/_view/player_kills_count_bygroup?&group_level=4&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getOrbatMountedKills($id)
    {

        $cacheKey = 'OrbatMountedKills' . $id;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/groupPage/_view/group_mwk?group_level=3&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getOrbatClasses($id)
    {

        $cacheKey = 'OrbatClasses' . $id;

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/groupPage/_view/group_classes?&group_level=3&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getOperations()
    {

        $cacheKey = 'Operations';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        	$path = 'events/_design/operationsTable/_view/operationKillsByClass?group_level=3';

        $data = $this->call($path);

        if(isset($data['response'])) {

            	$data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getOpsBreakdown()
    {

        $cacheKey = 'OpsBreakdown';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/operationsTable/_view/operationTotals?group_level=3';

        $data = $this->call($path);

        if(isset($data['response'])) {

            	$data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 60);

        }else{
            $encoded = json_encode([]);
        }

        return $encoded;
    }
	
	public function getServerPerf($id,$type,$servername)
    {

        $path = 'sys_perf/_design/sys_perf/_view/'. $type .'?startkey=%22' . $id . '%22&endkey=%22' . $id . '%22';

        $data = $this->call($path);

        if(isset($data['response']->rows[0]->key)) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

        }else{
		     $path = 'sys_perf/_design/sys_perf/_view/'. $type .'?startkey=%22' . $servername . '%22&endkey=%22' . $servername . '%22';

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
        }

        return $encoded;
    }
	
	public function getClanFeed($id)
    {

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
	
    public function call($path, $data=array(), $requestType='GET')
    {

        $payload = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . $path);
        curl_setopt($ch, CURLOPT_USERPWD, $this->user . ':' . $this->pass);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestType);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
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
        $result['response'] = json_decode($response);

        if($this->debug){
            TempoDebug::stopProfile($profiler);
            TempoDebug::dump($result);
        }

        curl_close($ch);

        return $result;
    }
}