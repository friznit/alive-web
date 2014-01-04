<?php

namespace Alive;

use Tempo\TempoDebug;

ini_set('max_execution_time', 60);

class CouchAPI {

    private $user = 'ARJay';
    private $pass = 'letmein';
    private $url = 'https://msostore.iriscouch.com/';
    private $reset = false;
    private $debug = false;

    public function createClanUser($name, $password)
    {
        $path = '_users/org.couchdb.user:' . $name;

        $data = array(
            'name' => $name,
            'roles' => ['writer','reader'],
            'type' => 'user',
            'password' => $password,
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

        $path = 'events/_design/events/_view/Totals';

        $data = $this->call($path);

        if(isset($data['response'])) {

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

        if(isset($data['response'])) {

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

        $path = 'events/_design/events/_view/players_list?group_level=2';

        $data = $this->call($path);

        if(isset($data['response'])) {

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

        $path = 'events/_design/events/_view/recent_operations?descending=true&limit=50';

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

        $cacheKey = 'LiveFeed';

        if (\Cache::has($cacheKey) && !$this->reset) {
            $data = \Cache::get($cacheKey);

            if($this->debug){
                TempoDebug::dump($data , $cacheKey . ' From Cache');
            }

            return $data;
        }

        $path = 'events/_design/events/_view/all_events?descending=true&limit=12';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response'];

            if($this->debug){
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, 10);

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

        $path = 'events/_design/kill/_view/side_killed_count_by_class?group_level=2';

        $data = $this->call($path);

        if(isset($data['response'])) {

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

        $path = 'events/_design/kill/_view/side_killed_count_by_class?group_level=2';

        $data = $this->call($path);

        if(isset($data['response'])) {

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

        $path = 'events/_design/kill/_view/side_killed_count?group_level=1';

        $data = $this->call($path);

        if(isset($data['response'])) {

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

        $path = 'events/_design/events/_view/operations_by_map?group_level=1';

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

        $path = 'events/_design/events/_view/operations_by_day?group_level=1';

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

        $path = 'events/_design/events/_view/players_by_day?group_level=1';

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

        $path = 'events/_design/events/_view/kills_by_day?group_level=1';

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

        $path = 'events/_design/events/_view/deaths_by_day?group_level=1';

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

        $path = 'events/_design/kill/_view/player_kills_count?group_level=2';

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

        if(isset($data['response'])) {

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

        if(isset($data['response'])) {

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

        $path = 'events/_design/events/_list/sort/player_finish?key="' . $id . '"&limit=1';

        $data = $this->call($path);

        if(isset($data['response'])) {

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

        $path = 'events/_design/events/_list/sort_by_value/players_weapons?group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

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

        $path = 'events/_design/events/_list/sort_by_value/players_vehxp?group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

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

        $path = 'events/_design/events/_list/sort_by_value/players_class?group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

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

        $path = 'events/_design/events/_list/sort_by_value/players_alias?group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';

        $data = $this->call($path);

        if(isset($data['response'])) {

            $data = $data['response']->rows;

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