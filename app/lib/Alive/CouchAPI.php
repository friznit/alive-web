<?php

namespace Alive;

use Tempo\TempoDebug;

// TODO: Move to someplace better
ini_set('max_execution_time', 400);

class CouchAPI
{

    ///*
    private $user = 'aliveadmin';
    private $pass = 'tupolov';
    private $url = 'http://db.alivemod.com:5984/';
    //*/

    /*
    private $user = 'arjay';
    private $pass = 'sfgdhl;asdr234';
    private $url = 'http://localhost:5984/';
    */

    public $reset = false;
    public $debug = false;
    public $cache = true;

    public $timeout = 60;

    /**
     * Create a new clan user
     *
     * @param string $name
     * @param string $password
     * @param string $group
     * @return array
     */
    public function createClanUser($name, $password, $group)
    {
        $path = '_users/org.couchdb.user:' . $name;

        $data = array(
            'name' => $name,
            'roles' => ['writer', 'reader'],
            'type' => 'user',
            'password' => $password,
            'ServerGroup' => $group,
        );

        $requestType = 'PUT';

        return $this->call($path, $data, $requestType);
    }

    /**
     * Delete a clan user
     *
     * @param string $name
     * @param $rev
     * @return array
     */
    public function deleteClanUser($name, $rev)
    {
        $path = '_users/org.couchdb.user:' . $name . '?rev=' . $rev;

        $requestType = 'DELETE';

        return $this->call($path, array(), $requestType);
    }

    /**
     * Get a clan user
     *
     * @param string $name
     * @param string $password
     * @return array
     */
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

    /**
     * Create a clan member
     *
     * @param $a3Id ?
     * @param string $username
     * @param string $group
     * @return array
     */
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

    /**
     * Delete a clan member
     *
     * @param $a3Id ?
     * @param $rev ?
     * @return array
     */
    public function deleteClanMember($a3Id, $rev)
    {
        $path = 'players/' . $a3Id . '?rev=' . $rev;

        $requestType = 'DELETE';

        return $this->call($path, array(), $requestType);
    }

    /**
     * Update a clan member
     *
     * @param $a3Id ?
     * @param string $username
     * @param string $group
     * @param $rev ?
     * @return array
     */
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

    /**
     * Get a clan member
     *
     * @param $a3Id ?
     * @return array
     */
    public function getClanMember($a3Id)
    {
        $path = 'players/' . $a3Id;

        $data = [];

        $requestType = 'GET';

        return $this->call($path, $data, $requestType);
    }

    /**
     * Get totals (?)
     *
     * @return bool|string
     */
    public function getTotals()
    {
        $cacheKey = 'Totals';

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/homePage/_view/Totals';

        $data = $this->call($path);

        if (isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->value;

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get operation totals
     *
     * @param string $name ?
     * @param string $map Map identifier (?)
     * @param string $clan ?
     * @return bool|string
     */
    public function getOptotals($name, $map, $clan)
    {
        $cacheKey = 'Totals' . $name . $map . $clan;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/homePage/_view/Totals?group_level=3';

        $data = $this->call($path);

        if (isset($data['response']->rows[0])) {

            $data = $data['response']->rows;

            foreach ($data as $item) {
                if ($item->key[0] == $map && $item->key[1] == $clan && $item->key[2] == $name) {
                    $result = $item->value;
                }
            }

            if ($this->debug) {
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get totals for a map
     *
     * @param string $name Name of the map
     * @return bool|string
     */
    public function getMapTotals($name)
    {

        $cacheKey = 'MapTotals' . $name;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $name = rawurlencode($name);

        $path = 'events/_design/operationsTable/_view/operationTotals?group_level=1&startkey=["' . $name . '"]&limit=1';

        $data = $this->call($path);

        if (isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->value;

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "day");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get active unit count
     *
     * @return bool|string
     */
    public function getActiveUnitCount()
    {

        $cacheKey = 'ActiveUnits';

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/homePage/_view/players_list?group_level=2';

        $data = $this->call($path);

        if (isset($data['response']->rows)) {

            $data = count($data['response']->rows);

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "day");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get operation active unit count
     *
     * @param string $name
     * @param string $map
     * @param string $clan
     * @return bool|string
     */
    public function getOpActiveUnitCount($name, $map, $clan)
    {

        $cacheKey = 'OpActiveUnits' . $name . $map . $clan;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $map = rawurlencode($map);
        $name = rawurlencode($name);
        $clan = rawurlencode($clan);

        $path = 'events/_design/operationPage/_view/players_list?group_level=3';

        $data = $this->call($path);

        if (isset($data['response']->rows)) {

            $data = $data['response']->rows;

            foreach ($data as $item) {
                if ($item->key[0] == $map && $item->key[1] == $clan && $item->key[2] == $name) {
                    $result = $item->value;
                }
            }

            if ($this->debug) {
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "day");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get recent operations
     *
     * @return bool|string
     */
    public function getRecentOperations()
    {

        $cacheKey = 'RecentOperations';

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/homePage/_view/recent_operations?descending=true&limit=20';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            \Cache::add($cacheKey, $encoded, $this->_set_timeout("hour"));

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get the 'live' feed
     *
     * @return string
     */
    public function getLiveFeed()
    {

        $path = 'events/_design/homePage/_view/all_events?descending=true&limit=50';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get the live feed for an operation
     *
     * @param string $map Map the operation is on
     * @param string $clan The clan the operation is for
     * @param string $name ?
     * @return string
     */
    public function getOpLiveFeed($map, $clan, $name)
    {
        $map = rawurlencode($map);
        $name = rawurlencode($name);
        $clan = rawurlencode($clan);
        $path = 'events/_design/operationPage/_list/sort_no_callback/operation_events?startkey=["' . $map . '","' . $clan . '","' . $name . '",{}]&endkey=["' . $map . '","' . $clan . '","' . $name . '"]&descending=true';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get operation live feed by page
     *
     * @param $map
     * @param $clan
     * @param $name
     * @param $limit
     * @param $skip
     * @return string
     */
    public function getOpLiveFeedPaged($map, $clan, $name, $limit, $skip)
    {
        $map = rawurlencode($map);
        $name = rawurlencode($name);
        $clan = rawurlencode($clan);

        $path = 'events/_design/operationPage/_list/sort_no_callback/operation_timeline_events?startkey=["' . $map . '","' . $clan . '","' . $name . '",{}]&endkey=["' . $map . '","' . $clan . '","' . $name . '"]&descending=true&limit=' . $limit . '&skip=' . $skip;

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get operation live AAR feed by page
     *
     * @param $map
     * @param $clan
     * @param $name
     * @param $start
     * @param $end
     * @return string
     */
    public function getOpLiveAARFeedPaged($map, $clan, $name, $start, $end)
    {
        $map = rawurlencode($map);
        $name = rawurlencode($name);
        $clan = rawurlencode($clan);

        $path = 'sys_aar/_design/AAR/_list/sort_no_callback/operation_aar?startkey=["' . $map . '","' . $clan . '","' . $name . '","' . $start . '",{}]&endkey=["' . $map . '","' . $clan . '","' . $name . '","' . $end . '"]';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

        } else {
            $encoded = json_encode([$path]);
        }
        return $encoded;
    }

    /**
     * Get total of BLUFOR losses
     *
     * @return bool|string
     */
    public function getLossesBLU()
    {

        $cacheKey = 'LossesBLU';

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/homePage/_view/side_killed_count_by_class?group_level=2';

        $data = $this->call($path);

        if (isset($data['response']->rows)) {

            $data = $data['response']->rows;
            $result = array();

            foreach ($data as $item) {
                if ($item->value > 0) {
                    if ($item->key[0] == 'WEST' && !is_null($item->key[1]) && $item->key[1] != 'any') {
                        array_push($result, [$item->key[1], $item->value]);
                    }
                }
            }

            if ($this->debug) {
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "day");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get total of OPFOR losses
     *
     * @return bool|string
     */
    public function getLossesOPF()
    {

        $cacheKey = 'LossesOPF';

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/homePage/_view/side_killed_count_by_class?group_level=2';

        $data = $this->call($path);

        if (isset($data['response']->rows)) {

            $data = $data['response']->rows;
            $result = array();

            foreach ($data as $item) {
                if ($item->value > 0) {
                    if ($item->key[0] == 'EAST' && !is_null($item->key[1]) && $item->key[1] != 'any') {
                        array_push($result, [$item->key[1], $item->value]);
                    }
                }
            }

            if ($this->debug) {
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "day");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get total casualties
     *
     * @return bool|string
     */
    public function getCasualties()
    {

        $cacheKey = 'Casualties';

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/homePage/_view/side_killed_count?group_level=1';

        $data = $this->call($path);

        if (isset($data['response']->rows)) {

            $data = $data['response']->rows;
            $result = array();

            foreach ($data as $item) {
                if ($item->value > 0) {
                    array_push($result, [$item->key, $item->value]);
                }
            }

            if ($this->debug) {
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "day");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get total OPFOR losses by BLUFOR
     *
     * @param $name
     * @param $map
     * @param $clan
     * @return bool|string
     */
    public function getOpLossesBLU($name, $map, $clan)
    {

        $cacheKey = 'OpLossesBLU' . $name . $map . $clan;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/operationPage/_view/side_killed_count_by_class?group_level=5';

        $data = $this->call($path);

        if (isset($data['response']->rows)) {

            $data = $data['response']->rows;
            $result = array();

            foreach ($data as $item) {
                if ($item->value > 0) {
                    if ($item->key[3] == 'WEST' && $item->key[0] == $map && $item->key[1] == $clan && $item->key[2] == $name) {
                        array_push($result, [$item->key[4], $item->value]);
                    }
                }
            }

            if ($this->debug) {
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "day");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get total OPFOR losses by BLUFOR
     *
     * @param $name
     * @param $map
     * @param $clan
     * @return bool|string
     */
    public function getOpLossesOPF($name, $map, $clan)
    {

        $cacheKey = 'OpLossesOPF' . $name . $map . $clan;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/operationPage/_view/side_killed_count_by_class?group_level=5';

        $data = $this->call($path);

        if (isset($data['response']->rows)) {

            $data = $data['response']->rows;
            $result = array();

            foreach ($data as $item) {
                if ($item->value > 0) {
                    if ($item->key[3] == 'EAST' && $item->key[0] == $map && $item->key[1] == $clan && $item->key[2] == $name) {
                        array_push($result, [$item->key[4], $item->value]);
                    }
                }
            }

            if ($this->debug) {
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "day");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get total operation casualties
     *
     * @param $name
     * @param $map
     * @param $clan
     * @return bool|string
     */
    public function getOpCasualties($name, $map, $clan)
    {

        $cacheKey = 'OpCasualties' . $name . $map . $clan;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/operationPage/_view/side_killed_count?group_level=4';

        $data = $this->call($path);

        if (isset($data['response']->rows)) {

            $data = $data['response']->rows;
            $result = array();

            foreach ($data as $item) {
                if ($item->value > 0) {
                    if ($item->key[0] == $map && $item->key[1] == $clan && $item->key[2] == $name) {
                        array_push($result, [$item->key[3], $item->value]);
                    }
                }
            }

            if ($this->debug) {
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get operations by map
     *
     * @return bool|string
     */
    public function getOperationsByMap()
    {

        $cacheKey = 'OperationsByMap';

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/homePage/_view/operations_by_map?group_level=1';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response']->rows;
            $result = array();

            foreach ($data as $item) {
                if ($item->value > 0) {
                    if (!is_null($item->key)) {
                        array_push($result, [$item->key, $item->value]);
                    }
                }
            }

            if ($this->debug) {
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get operations by day
     *
     * @return bool|string
     */
    public function getOperationsByDay()
    {

        $cacheKey = 'OperationsByDay';

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/operationsTable/_view/operations_by_day?group_level=1';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response']->rows;
            $result = array();

            foreach ($data as $item) {
                if ($item->value > 0) {
                    if (!is_null($item->key[0])) {
                        array_push($result, [$item->key[0], $item->value]);
                    }
                }
            }

            if ($this->debug) {
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "day");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get players by day
     *
     * @return bool|string
     */
    public function getPlayersByDay()
    {

        $cacheKey = 'PlayersByDay';

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/operationsTable/_view/players_by_day?group_level=1';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response']->rows;
            $result = array();

            foreach ($data as $item) {
                if ($item->value > 0) {
                    if (!is_null($item->key[0])) {
                        array_push($result, [$item->key[0], $item->value]);
                    }
                }
            }

            if ($this->debug) {
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "day");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get kills by day
     *
     * @return bool|string
     */
    public function getKillsByDay()
    {

        $cacheKey = 'KillsByDay';

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/operationsTable/_view/kills_by_day?group_level=1';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response']->rows;
            $result = array();

            foreach ($data as $item) {
                if ($item->value > 0) {
                    if (!is_null($item->key[0])) {
                        array_push($result, [$item->key[0], $item->value]);
                    }
                }
            }

            if ($this->debug) {
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "day");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get deaths by day
     *
     * @return bool|string
     */
    public function getDeathsByDay()
    {

        $cacheKey = 'DeathsByDay';

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/operationsTable/_view/deaths_by_day?group_level=1';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response']->rows;
            $result = array();

            foreach ($data as $item) {
                if ($item->value > 0) {
                    if (!is_null($item->key[0])) {
                        array_push($result, [$item->key[0], $item->value]);
                    }
                }
            }

            if ($this->debug) {
                TempoDebug::dump($result);
            }

            $encoded = json_encode($result);

            $this->setCache($cacheKey, $encoded, "day");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get T1 operators
     *
     * @return bool|string
     */
    public function getT1Operators()
    {

        $cacheKey = 'T1Operators';

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/homePage/_view/player_kills_count?group_level=2';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get developer credits
     *
     * @param $id ?
     * @return bool|string
     */
    public function getDevCredits($id)
    {

        $cacheKey = 'Devcredits' . $id;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'credits/_design/warroom/_view/devcredits?key="' . $id . '"';

        $data = $this->call($path);

        if (isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->value;

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get total players
     *
     * @param $id ?
     * @return bool|string
     */
    public function getPlayerTotals($id)
    {

        $cacheKey = 'PlayerTotals' . $id;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/playerPage/_view/playerTotals?group_level=1&startkey="' . $id . '"&endkey="' . $id . '"';

        $data = $this->call($path);

        if (isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->value;

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get player details
     *
     * @param $id ?
     * @return bool|string
     */
    public function getPlayerDetails($id)
    {

        $cacheKey = 'PlayerDetails' . $id;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/playerPage/_list/sort_no_callback/player_finish?&startkey="' . $id . '"&endkey="' . $id . '"';

        $data = $this->call($path);

        if (isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->value;

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get player weapon
     *
     * @param $id ?
     * @return bool|string
     */
    public function getPlayerWeapon($id)
    {

        $cacheKey = 'PlayerWeapon' . $id;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/playerPage/_list/sort_by_value/players_weapons?group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';

        $data = $this->call($path);

        if (isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->key;

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get player weapons
     *
     * @param $id ?
     * @return bool|string
     */
    public function getPlayerWeapons($id)
    {

        $cacheKey = 'PlayerWeapons' . $id;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/playerPage/_view/players_weapons?&group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get player vehicle
     *
     * @param $id ?
     * @return bool|string
     */
    public function getPlayerVehicle($id)
    {

        $cacheKey = 'PlayerVehicle' . $id;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/playerPage/_list/sort_by_value/players_vehxp?group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';

        $data = $this->call($path);

        if (isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->key;

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get player vehicles
     *
     * @param $id ?
     * @return bool|string
     */
    public function getPlayerVehicles($id)
    {

        $cacheKey = 'PlayerVehicles' . $id;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/playerPage/_view/players_vehxp?&group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get player class
     *
     * @param $id ?
     * @return bool|string
     */
    public function getPlayerClass($id)
    {

        $cacheKey = 'PlayerClass' . $id;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/playerPage/_list/sort_by_value/players_class?group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';

        $data = $this->call($path);

        if (isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->key;

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get player classes
     *
     * @param $id ?
     * @return bool|string
     */
    public function getPlayerClasses($id)
    {

        $cacheKey = 'PlayerClasses' . $id;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/playerPage/_view/players_class?&group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get player alias
     *
     * @param $id ?
     * @return bool|string
     */
    public function getPlayerAlias($id)
    {

        $cacheKey = 'PlayerAlias' . $id;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/playerPage/_list/sort_by_value/players_alias?group_level=2&startkey=["' . $id . '"]&endkey=["' . $id . '",%20{}]';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get group totals
     *
     * @return bool|string
     */
    public function getGroupTotals()
    {

        $cacheKey = 'GroupTotals';

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/groupTable/_view/groupTotals?group_level=1&stale=ok';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get group totals by tag
     *
     * @param $id ?
     * @return bool|string
     */
    public function getGroupTotalsByTag($id)
    {

        $cacheKey = 'GroupTotals' . $id;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $id = rawurlencode($id);

        $path = 'events/_design/groupTable/_view/groupTotals?group_level=1&startkey=["' . $id . '"]&endkey=["' . $id . '",{}]';

        $data = $this->call($path);

        if (isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->value;

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get group classes
     *
     * @param $id ?
     * @return bool|string
     */
    public function getGroupClasses($id)
    {

        $cacheKey = 'GroupClasses' . $id;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_view/group_classes?group_level=3&startkey=["' . $id . '"]&endkey=["' . $id . '",{}]';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get group last op?
     *
     * @param $id ?
     * @return bool|string
     */
    public function getGroupLastOp($id)
    {

        $cacheKey = 'GroupLastOp' . $id;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_list/sort_no_callback/group_finish?key=%22' . $id . '%22';

        $data = $this->call($path);

        if (isset($data['response']->rows[0])) {

            $data = $data['response']->rows[0]->value;

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get total personnel
     *
     * @return bool|string
     */
    public function getPersonnelTotals()
    {

        $cacheKey = 'PersonnelTotals';

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/playerTable/_view/playerTotals?group_level=2&stale=ok';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get total T1marksmen
     *
     * @return bool|string
     */
    public function getT1Marksmen()
    {

        $cacheKey = 'T1marksmen';

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/playerTable/_view/kills_by_distance?group_level=4&stale=ok';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get total vehicle commanders
     *
     * @return bool|string
     */
    public function getVehicleCommanders()
    {

        $cacheKey = 'VehicleCommanders';

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/playerTable/_view/player_in_vehicle_kills_count?group_level=4&stale=ok';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get total pilots
     *
     * @return bool|string
     */
    public function getPilots()
    {

        $cacheKey = 'Pilots';

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/playerTable/_view/player_in_aircraft_kills_count?group_level=4';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get total medics
     *
     * @return bool|string
     */
    public function getMedics()
    {

        $cacheKey = 'Medics';

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/playerTable/_view/player_heals_count?group_level=2';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get scores
     *
     * @return bool|string
     */
    public function getScores()
    {

        $cacheKey = 'Scores';

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/playerTable/_view/scoreTotal?group_level=2';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get ratings
     *
     * @return bool|string
     */
    public function getRatings()
    {

        $cacheKey = 'Ratings';

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/playerTable/_view/AveRating?group_level=2';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get average scores
     *
     * @return bool|string
     */
    public function getAvescores()
    {

        $cacheKey = 'Avescores';

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/playerTable/_view/AveScore?group_level=2';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get recent orbat operations
     *
     * @param $id
     * @return bool|string
     */
    public function getOrbatRecentOperations($id)
    {

        $cacheKey = 'OrbatRecentOperations' . $id;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_list/sort_no_callback/group_recent_ops?startkey=%22' . $id . '%22&endkey=%22' . $id . '%22&descending=true&limit=50';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get ORBAT T1?
     *
     * @param $id
     * @return bool|string
     */
    public function getOrbatT1($id)
    {

        $cacheKey = 'OrbatT1' . $id;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_view/player_kills_count?group_level=3&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get orbat pilots
     *
     * @param $id
     * @return bool|string
     */
    public function getOrbatPilots($id)
    {

        $cacheKey = 'OrbatPilots' . $id;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_view/player_in_aircraft_kills_count?group_level=5&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get ORBAT medics
     *
     * @param $id ?
     * @return bool|string
     */
    public function getOrbatMedics($id)
    {

        $cacheKey = 'OrbatMedics' . $id;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_view/player_heals_count?group_level=3&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get ORBAT kills by weapon
     *
     * @param $id ?
     * @return bool|string
     */
    public function getOrbatKillsByWeapon($id)
    {

        $cacheKey = 'OrbatKillsByWeapon' . $id;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_view/group_killsByWeapon?group_level=3&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get ORBAT weapons
     *
     * @param $id ?
     * @return bool|string
     */
    public function getOrbatWeapons($id)
    {

        $cacheKey = 'OrbatWeapons' . $id;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_view/group_weapons?group_level=3&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get ORBAT vehicles
     *
     * @param $id
     * @return bool|string
     */
    public function getOrbatVehicles($id)
    {

        $cacheKey = 'OrbatVehicles' . $id;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_view/group_veh?group_level=3&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get ORBAT player kills
     *
     * @param $id ?
     * @return bool|string
     */
    public function getOrbatPlayerKills($id)
    {

        $cacheKey = 'OrbatPlayerKills' . $id;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_view/player_kills_count_bygroup?&group_level=5&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get ORBAT mounted kills
     *
     * @param $id ?
     * @return bool|string
     */
    public function getOrbatMountedKills($id)
    {

        $cacheKey = 'OrbatMountedKills' . $id;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_view/group_mwk?group_level=3&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get ORBAT classes
     *
     * @param $id
     * @return bool|string
     */
    public function getOrbatClasses($id)
    {

        $cacheKey = 'OrbatClasses' . $id;

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_view/group_classes?&group_level=3&startkey=[%22' . $id . '%22]&endkey=[%22' . $id . '%22,{}]';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get all operations
     *
     * @return bool|string
     */
    public function getOperations()
    {

        $cacheKey = 'Operations';

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/operationsTable/_view/operationKillsByClass?group_level=3';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get operations breakdown
     *
     * @return bool|string
     */
    public function getOpsBreakdown()
    {

        $cacheKey = 'OpsBreakdown';

        if ($cache = $this->getCache($cacheKey)) {
            return $cache;
        }

        $path = 'events/_design/operationsTable/_view/operationTotals?group_level=3';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

            $this->setCache($cacheKey, $encoded, "hour");

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get server performance
     *
     * @param $id
     * @param $type
     * @param $servername
     * @return string
     */
    public function getServerPerf($id, $type, $servername)
    {

        $id = rawurlencode($id);
        $type = rawurlencode($type);
        $server = rawurlencode($servername);

        $path = 'sys_perf/_design/sys_perf/_view/' . $type . '?startkey=%22' . $id . '%22&endkey=%22' . $id . '%22';

        $data = $this->call($path);

        if (isset($data['response']->rows[0]->key)) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

        } else {
            if ($this->debug) {
                TempoDebug::dump($data);
            }
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get All (?) Server Performance
     *
     * @param $id
     * @return string
     */
    public function getServerPerfAll($id)
    {

        $id = rawurlencode($id);

        $path = 'sys_perf/_design/sys_perf/_view/all_perf_server?startkey=%22' . $id . '%22&endkey=%22' . $id . '%22';

        $data = $this->call($path);

        if (isset($data['response']->rows[0]->key)) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

        } else {
            if ($this->debug) {
                TempoDebug::dump($data);
            }
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get Server Performance by date
     *
     * @param $date The date to check against
     * @return string
     */
    public function getServerPerfDate($date)
    {

        $date = rawurlencode($date);

        $path = 'sys_perf/_design/sys_perf/_view/all_perf?startkey=%22' . $date . '%22';

        $data = $this->call($path);

        if (isset($data['response']->rows[0]->key)) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

        } else {
            if ($this->debug) {
                TempoDebug::dump($data);
            }
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Run a server performance check
     *
     * @return string
     */
    public function getServerPerfCheck()
    {

        $path = 'sys_perf/_design/sys_perf/_view/server_count_data?group=true';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

        } else {
            TempoDebug::dump($data);
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get a feed of a specific clan
     *
     * @param $id
     * @return string
     */
    public function getClanFeed($id)
    {

        $id = rawurlencode($id);

        $path = 'events/_design/groupPage/_list/sort_no_callback/group_events?startkey=[%22' . $id . '%22,{}]&endkey=[%22' . $id . '%22]&descending=true&limit=50';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get a feed of a specific player
     *
     * @param $id
     * @return string
     */
    public function getPlayerFeed($id)
    {

        $path = 'events/_design/playerPage/_list/sort_no_callback/player_events?startkey=[%22' . $id . '%22,{}]&endkey=[%22' . $id . '%22]&descending=true&limit=50';

        $data = $this->call($path);

        if (isset($data['response'])) {

            $data = $data['response'];

            if ($this->debug) {
                TempoDebug::dump($data);
            }

            $encoded = json_encode($data);

        } else {
            $encoded = json_encode([]);
        }

        return $encoded;
    }

    /**
     * Get a cache item by key
     *
     * @param string $cacheKey The key of the cache item to request
     * @return bool
     */
    public function getCache($cacheKey)
    {
        if (\Cache::has($cacheKey) && !$this->reset && $this->cache) {
            $data = \Cache::get($cacheKey);

            if ($this->debug) {
                TempoDebug::dump($data, $cacheKey . ' From Cache');
            }

            return $data;
        } else {
            return false;
        }
    }

    /**
     * Set a cache item by key
     *
     * @param string $cacheKey The key to set cache against
     * @param string|int|array $data The data to store in cache
     * @param $timeout
     */
    public function setCache($cacheKey, $data, $timeout)
    {
        if ($this->cache) {
            \Cache::add($cacheKey, $data, $this->_set_timeout($timeout));
        }
    }

    /**
     * Make a call to the CouchAPI db
     *
     * TODO: Move this into parent class? Extendability?
     *
     * @param $path
     * @param array $data
     * @param string $requestType
     * @return array
     */
    public function call($path, $data = array(), $requestType = 'GET')
    {

        $payload = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_URL, $this->url . $path);
        curl_setopt($ch, CURLOPT_USERPWD, $this->user . ':' . $this->pass);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestType);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout); //timeout in seconds
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json',
            'Accept: application/json'
        ));

        if ($this->debug) {
            TempoDebug::message($this->url . $path);
            TempoDebug::dump($payload, 'Payload');
            $profiler = TempoDebug::startProfile();
        }

        $response = curl_exec($ch);

        $result = array();
        $result['info'] = curl_getinfo($ch);
        $result['error'] = curl_error($ch);
        $result['response'] = json_decode($response);

        if ($this->debug) {
            TempoDebug::stopProfile($profiler);
            TempoDebug::dump($result);
        }

        curl_close($ch);

        return $result;
    }

    /**
     * Return a timeout for how long the cache should last
     *
     * @param $length
     * @return int
     */
    protected function _set_timeout($length)
    {

        $minutes = 60;

        switch ($length) {
            case 'minute':
                $minutes = 1;
                break;
            case 'ten-minutes':
                $minutes = 10 + rand(0, 5);
                break;
            case 'hour':
                $minutes = 60 + rand(0, 10);
                break;
            case 'three-hours':
                $minutes = 180 + rand(0, 10);
                break;
            case 'six-hours':
                $minutes = 360 + rand(0, 10);
                break;
            case 'twelve-hours':
                $minutes = 720 + rand(0, 10);
                break;
            case 'day':
                $minutes = 1440 + rand(0, 10);
                break;
        }

        if ($this->debug) {
            TempoDebug::dump($minutes, 'Cache Timeout value');
        }

        return $minutes;
    }
}