<?php

use Alive\CouchAPI;
use Tempo\TempoDebug;

class AdminUserController extends BaseController {

    public function __construct()
    {
        //Check CSRF token on POST
        $this->beforeFilter('csrf', array('on' => 'post'));

        // Get the Throttle Provider
        $throttleProvider = Sentry::getThrottleProvider();

        // Enable the Throttling Feature
        $throttleProvider->enable();

        // Authenticated access only
        $this->beforeFilter('auth');

    }

    // Lists -----------------------------------------------------------------------------------------------------------

    public function getIndex()
    {
        $data = get_default_data();
        $auth = $data['auth'];

        if ($auth['isAdmin']) {

            $data['allUsers'] = Sentry::getUserProvider()->createModel()
                ->leftJoin('throttle', 'throttle.user_id', '=', 'users.id')
                ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
                ->paginate(10, array(
                    'users.id',
                    'users.email',
                    'users.activated',
                    'users.last_login',
                    'profiles.username',
                    'throttle.suspended',
                    'throttle.banned'
                ));

            $data['userStatus'] = array();

            foreach ($data['allUsers'] as $user) {
                if ($user->isActivated()) {
                    $data['userStatus'][$user->id] = "Active";
                } else {
                    $data['userStatus'][$user->id] = "Not Active";
                }

                if($user->suspended == '1') {
                    $data['userStatus'][$user->id] = "Suspended";
                }

                if($user->banned == '1') {
                    $data['userStatus'][$user->id] = "Banned";
                }
            }

            return View::make('admin/user.index')->with($data);
        }else{
            Alert::error('Sorry.')->flash();
            return Redirect::to('admin/user/show/'.$auth['userId']);
        }
    }

    public function postSearch()
    {
        $data = get_default_data();
        $auth = $data['auth'];

        $input = array(
            'query' => Input::get('query'),
            'type' => Input::get('type')
        );

        $rules = array (
            'query' => 'required',
            'type' => 'required|alpha',
        );

        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            return Redirect::to('admin/user/')->withErrors($v)->withInput()->with($data);
        } else {

            $query = $input['query'];
            $type = $input['type'];

            if ($auth['isAdmin']) {

                $users =  Sentry::getUserProvider()->getEmptyUser()
                    ->join('throttle', 'throttle.user_id', '=', 'users.id')
                    ->join('profiles', 'profiles.user_id', '=', 'users.id');

                switch($type){
                    case 'id':
                        $users = $users->where('users.id', $query);
                        break;
                    case 'email':
                        $users = $users->where('email', 'LIKE', '%'.$query.'%');
                        break;
                    case 'userName':
                        $users = $users->where('profiles.username', 'LIKE', '%'.$query.'%');
                        break;
                }

                $users = $users->paginate(10);

                $data['userStatus'] = array();
                foreach ($users as $user) {
                    if ($user->isActivated()) {
                        $data['userStatus'][$user->id] = "Active";
                    } else {
                        $data['userStatus'][$user->id] = "Not Active";
                    }

                    if($user->suspended == '1') {
                        $data['userStatus'][$user->id] = "Suspended";
                    }

                    if($user->banned == '1') {
                        $data['userStatus'][$user->id] = "Banned";
                    }
                }

                $data['links'] = $users->links();
                $data['allUsers'] = $users;
                $data['query'] = $query;

                return View::make('admin/user.search')->with($data);
            }else{
                Alert::error('Sorry.')->flash();
                return Redirect::to('admin/user/show/'.$auth['userId']);
            }
        }
    }

    // Show ------------------------------------------------------------------------------------------------------------

    public function getShow($id)
    {

        try {

            $data = get_default_data();
            $auth = $data['auth'];

            if ($auth['isAdmin'] || $auth['userId'] == $id) {

                $user = Sentry::getUserProvider()->findById($id);

                $applicationCount = $user->applications->count();
                if($applicationCount > 0){
                    $data['applications'] = $user->applications->all();
                }

                $data['user'] = $user;
                $data['profile'] = $data['user']->profile;
                $data['clan'] = $data['profile']->clan;
                $data['myGroups'] = $data['user']->getGroups();
                return View::make('admin/user.show')->with($data);

            } else {
                Alert::error('Sorry.')->flash();
                return Redirect::to('admin/user/show/'.$auth['userId']);
            }

        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Alert::error('There was a problem accessing that account.')->flash();
            return Redirect::to('admin/user/show/'.$auth['userId']);
        }
    }

    // Edit ------------------------------------------------------------------------------------------------------------

    public function getEdit($id)
    {
        try {

            $data = get_default_data();
            $auth = $data['auth'];

            if ($auth['isAdmin']) {
                $data['ageGroup'] = get_age_group_data();
                $data['countries'] = DB::table('countries')->lists('name','iso_3166_2');
                $data['user'] = Sentry::getUserProvider()->findById($id);
                $data['userGroups'] = $data['user']->getGroups();
                $data['profile'] = $data['user']->profile;
                $data['allGroups'] = Sentry::getGroupProvider()->findAll();
                return View::make('admin/user.edit')->with($data);

            } elseif ($auth['userId'] == $id) {
                $data['ageGroup'] = get_age_group_data();
                $data['countries'] = DB::table('countries')->lists('name','iso_3166_2');
                $data['user'] = Sentry::getUserProvider()->findById($id);
                $data['userGroups'] = $data['user']->getGroups();
                $data['profile'] = $data['user']->profile;
                return View::make('admin/user.edit')->with($data);

            } else {
                Alert::error('Sorry.')->flash();
                return Redirect::to('admin/user/show/'.$auth['userId']);
            }

        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Alert::error('There was a problem accessing this account.')->flash();
            return Redirect::to('admin/user/show/'.$auth['userId']);
        }
    }

    public function postEdit($id) {

        $data = get_default_data();
        $auth = $data['auth'];

        $input = array(
            'username' => Input::get('username'),
            'a3ID' => Input::get('a3ID'),
            'country' => Input::get('country'),
            'ageGroup' => Input::get('ageGroup'),
            'twitchStream' => Input::get('twitchStream'),
            'remark' => Input::get('remark'),
            /*
            'a2ID' => Input::get('a2ID'),
            'preferredClass' => Input::get('preferredClass'),
            'primaryProfile' => Input::get('primaryProfile'),
            'secondaryProfile' => Input::get('secondaryProfile'),
            'alias' => Input::get('alias'),
            'armaFace' => Input::get('armaFace'),
            'armaVoice' => Input::get('armaVoice'),
            'armaPitch' => Input::get('armaPitch'),
            */
        );

        $rules = array (
            'username' => 'required',
            'a3ID' => 'required',
        );

        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            return Redirect::to('admin/user/edit/' . $id)->withErrors($v)->withInput()->with($data);
        } else {

            if ($auth['isAdmin'] || $auth['userId'] == $id) {

                try {

                    $user = Sentry::getUserProvider()->findById($id);
                    $profile = $user->profile;

                    $clan_id = $profile->clan_id;
                    $clan = Clan::find($clan_id);

                    if ($user->save()) {

                        if($input['country'] != ''){
                            $countries = DB::table('countries')->lists('name','iso_3166_2');
                            $countryName = $countries[$input['country']];
                            $profile->country = $input['country'];
                            $profile->country_name = $countryName;
                        }

                        $cloudCreate = false;
                        if(is_null($profile->a3_id)){
                            $cloudCreate = true;
                        }

                        $profile->username = $input['username'];
                        $profile->a3_id = $input['a3ID'];
                        $profile->age_group = $input['ageGroup'];
                        $profile->twitch_stream = $input['twitchStream'];
                        $profile->remark = $input['remark'];

                        /*
                        $profile->alias = $input['alias'];
                        $profile->a2_id = $input['a2ID'];
                        $profile->primary_profile = $input['primaryProfile'];
                        $profile->secondary_profile = $input['secondaryProfile'];
                        $profile->arma_face = $input['armaFace'];
                        $profile->arma_voice = $input['armaVoice'];
                        $profile->arma_pitch = $input['armaPitch'];
                        */

                        if ($profile->save()) {

                            if($cloudCreate){
                                $couchAPI = new Alive\CouchAPI();
                                $result = $couchAPI->createClanMember($profile->a3_id, $profile->username, $clan->tag);

                                if(isset($result['response'])){
                                    if(isset($result['response']->rev)){
                                        $remoteId = $result['response']->rev;
                                        $profile->remote_id = $remoteId;
                                        $profile->save();

                                        Alert::success('Member connected to the cloud data store.')->flash();
                                        return Redirect::to('admin/user/show/'.$auth['userId']);
                                    }else{
                                        Alert::error('There was an error connecting to the cloud data store, please try again later.')->flash();
                                        return Redirect::to('admin/user/show/'.$auth['userId']);
                                    }
                                }else{
                                    Alert::error('There was an error connecting to the cloud data store, please try again later.')->flash();
                                    return Redirect::to('admin/user/show/'.$auth['userId']);
                                }
                            }

                            Alert::success('Profile updated.')->flash();
                            return Redirect::to('admin/user/show/'. $id);
                        }
                    } else {
                        Alert::error('Profile could not be updated.')->flash();
                        return Redirect::to('admin/user/edit/' . $id);
                    }

                } catch (Cartalyst\Sentry\Users\UserExistsException $e) {
                    Alert::error('User already exists.')->flash();
                    return Redirect::to('admin/user/edit/' . $id);
                } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
                    Alert::error('User was not found.')->flash();
                    return Redirect::to('admin/user/edit/' . $id);
                }

            } else {
                Alert::error('Sorry.')->flash();
                return Redirect::to('admin/user/show/'.$auth['userId']);
            }

        }
    }
	
    // Avatar ----------------------------------------------------------------------------------------------------------

    public function postChangeavatar($id) {

        $data = get_default_data();
        $auth = $data['auth'];

        $input = array(
            'avatar' => Input::file('avatar'),
        );

        $rules = array (
            'avatar' => 'mimes:jpeg,bmp,png',
        );

        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            return Redirect::to('admin/user/edit/' . $id)->withErrors($v)->withInput()->with($data);
        } else {

            if ($auth['isAdmin'] || $auth['userId'] == $id) {

                try {

                    $user = Sentry::getUserProvider()->findById($id);
                    $profile = $user->profile;

                    $profile->avatar->clear();
                    $profile->avatar = $input['avatar'];

                    if ($profile->save()) {
                        Alert::success('Profile updated.')->flash();
                        return Redirect::to('admin/user/show/'. $id);
                    } else {
                        Alert::error('Profile could not be updated.')->flash();
                        return Redirect::to('admin/user/edit/' . $id);
                    }

                } catch (Cartalyst\Sentry\Users\UserExistsException $e) {
                    Alert::error('User already exists.')->flash();
                    return Redirect::to('admin/user/edit/' . $id);
                } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
                    Alert::error('User was not found.')->flash();
                    return Redirect::to('admin/user/edit/' . $id);
                }

            } else {
                Alert::error('Sorry.')->flash();
                return Redirect::to('admin/user/show/'.$auth['userId']);
            }
        }
    }

    // Password --------------------------------------------------------------------------------------------------------

    public function postChangepassword($id) {

        $data = get_default_data();
        $auth = $data['auth'];

        $input = array(
            'oldPassword' => Input::get('oldPassword'),
            'newPassword' => Input::get('newPassword'),
            'newPassword_confirmation' => Input::get('newPassword_confirmation')
        );

        $rules = array (
            'oldPassword' => 'required|min:6',
            'newPassword' => 'required|min:6|confirmed',
            'newPassword_confirmation' => 'required'
        );

        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            return Redirect::to('admin/user/edit/' . $id)->withErrors($v)->withInput()->with($data);
        } else {

            if ($auth['isAdmin'] || $auth['userId'] == $id) {

                try {

                    $user = Sentry::getUserProvider()->findById($id);
                    if ($user->checkHash($input['oldPassword'], $user->getPassword())) {
                        $user->password = $input['newPassword'];

                        if ($user->save()) {
                            Alert::success('Your password has been changed.')->flash();
                            return Redirect::to('admin/user/show/'. $id);
                        } else {
                            Alert::error('Your password could not be changed.')->flash();
                            return Redirect::to('admin/user/edit/' . $id);
                        }
                    } else {
                        Alert::error('You did not provide the correct password.')->flash();
                        return Redirect::to('admin/user/edit/' . $id);
                    }

                } catch (Cartalyst\Sentry\Users\LoginRequiredException $e) {
                    Alert::error('Login field required.')->flash();
                    return Redirect::to('admin/user/edit/' . $id);
                } catch (Cartalyst\Sentry\Users\UserExistsException $e) {
                    Alert::error('User already exists.')->flash();
                    return Redirect::to('admin/user/edit/' . $id);
                } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
                    Alert::error('User was not found.')->flash();
                    return Redirect::to('admin/user/edit/' . $id);
                }

            } else {
                Alert::error('Sorry.')->flash();
                return Redirect::to('admin/user/show/'.$auth['userId']);
            }
        }
    }

    public function getClearReset($userId = null)
    {
        try {
            $user = Sentry::getUserProvider()->findById($userId);

            $user->clearResetPassword();

            echo "clear.";
        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            echo 'User does not exist';
        }
    }

    // Email -----------------------------------------------------------------------------------------------------------

    public function postChangeemail($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        $input = array(
            'oldEmail' => Input::get('oldEmail'),
            'newEmail' => Input::get('newEmail'),
            'newEmail_confirmation' => Input::get('newEmail_confirmation')
        );

        $rules = array (
            'oldEmail' => 'required|email',
            'newEmail' => 'required|email|confirmed',
            'newEmail_confirmation' => 'required'
        );

        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            return Redirect::to('admin/user/edit/' . $id)->withErrors($v)->withInput()->with($data);
        } else {

            if ($auth['isAdmin'] || $auth['userId'] == $id) {

                try {

                    $user = Sentry::getUserProvider()->findById($id);

                    $user->email = $input['newEmail'];

                    if ($user->save()) {
                        Alert::success('Your email has been changed.')->flash();
                        return Redirect::to('admin/user/show/'. $id);
                    } else {
                        Alert::error('Your email could not be changed.')->flash();
                        return Redirect::to('admin/user/edit/' . $id);
                    }
                } catch (Cartalyst\Sentry\Users\LoginRequiredException $e) {
                    Alert::error('Login field required.')->flash();
                    return Redirect::to('admin/user/edit/' . $id);
                } catch (Cartalyst\Sentry\Users\UserExistsException $e) {
                    Alert::error('User already exists.')->flash();
                    return Redirect::to('admin/user/edit/' . $id);
                } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
                    Alert::error('User was not found.')->flash();
                    return Redirect::to('admin/user/edit/' . $id);
                }

            } else {
                Alert::error('Sorry.')->flash();
                return Redirect::to('admin/user/show/'.$auth['userId']);
            }
        }
    }

    // User Groups -----------------------------------------------------------------------------------------------------

    public function postUpdatememberships($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        try {

            if ($auth['isAdmin'] || $auth['userId'] == $id) {

                $user = Sentry::getUserProvider()->findById($id);
                $allGroups = Sentry::getGroupProvider()->findAll();
                $permissions = Input::get('permissions');

                $statusMessage = '';
                foreach ($allGroups as $group) {
                    if (isset($permissions[$group->id])) {
                        if ($user->addGroup($group)) {
                            $statusMessage .= "Added to " . $group->name . "<br />";
                        } else {
                            $statusMessage .= "Could not be added to " . $group->name . "<br />";
                        }
                    } else {
                        if ($user->removeGroup($group)) {
                            $statusMessage .= "Removed from " . $group->name . "<br />";
                        } else {
                            $statusMessage .= "Could not be removed from " . $group->name . "<br />";
                        }
                    }
                }

                Alert::success($statusMessage)->flash();
                return Redirect::to('admin/user/show/'. $id);

            } else {
                Alert::error('Sorry.')->flash();
                return Redirect::to('admin/user/show/'.$auth['userId']);
            }

        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Alert::error('User was not found.')->flash();
            return Redirect::to('admin/user/edit/' . $id);
        } catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e) {
            Alert::error('Trying to access unidentified Groups.')->flash();
            return Redirect::to('admin/user/edit/' . $id);
        }
    }

    // Suspend ---------------------------------------------------------------------------------------------------------

    public function getSuspend($id)
    {
        $data = get_default_data();
        $auth = $data['auth'];

        if ($auth['isAdmin']) {

            try {
                $data['user'] = Sentry::getUserProvider()->findById($id);
                return View::make('admin/user.suspend')->with($data);

            } catch (Cartalyst\Sentry\UserNotFoundException $e) {
                Alert::error('There was a problem accessing that user\s account.')->flash();
                return Redirect::to('admin/user');
            }

        } else {
            Alert::error('Sorry.')->flash();
            return Redirect::to('admin/user/show/'.$auth['userId']);
        }
    }

    public function postSuspend($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        $input = array(
            'suspendTime' => Input::get('suspendTime')
        );

        $rules = array (
            'suspendTime' => 'required|numeric'
        );

        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            return Redirect::to('admin/user/suspend/' . $id)->withErrors($v)->withInput()->with($data);
        } else {

            if ($auth['isAdmin']) {

                try {
                    $throttle = Sentry::getThrottleProvider()->findByUserId($id);
                    $throttle->setSuspensionTime($input['suspendTime']);
                    $throttle->suspend();

                    Alert::success("User has been suspended for " . $input['suspendTime'] . " minutes.")->flash();
                    return Redirect::to('admin/user/show/'. $id);

                } catch (Cartalyst\Sentry\UserNotFoundException $e) {
                    Alert::error('There was a problem accessing that user\s account.')->flash();
                    return Redirect::to('admin/user');
                }

            } else {
                Alert::error('Sorry.')->flash();
                return Redirect::to('admin/user/show/'.$auth['userId']);
            }
        }
    }

    // Delete ----------------------------------------------------------------------------------------------------------

    public function postDelete($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        try {

            if ($auth['isAdmin'] || $auth['userId'] == $id) {

                $user = Sentry::getUserProvider()->findById($id);

                $profile = $user->profile;

                $applications = $user->applications->all();

                foreach($applications as $application){
                    $application->delete();
                }

                /*
                if(!is_null($profile->remote_id)){
                    $couchAPI = new Alive\CouchAPI();
                    $result = $couchAPI->deleteClanMember($profile->a3_id, $profile->remote_id);
                }
                */

                $profile->delete();

                if ($user->delete()) {

                    if($auth['userId'] == $id){
                        return Redirect::to('/');
                    }else{
                        Alert::success('User deleted.')->flash();
                        return Redirect::to('admin/user');
                    }

                } else {
                    Alert::error('There was a problem deleting that user.')->flash();
                    return Redirect::to('admin/user');
                }

            } else {
                Alert::error('Sorry.')->flash();
                return Redirect::to('admin/user/show/'.$auth['userId']);
            }

        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Alert::error('User was not found.')->flash();
            return Redirect::to('admin/user/edit/' . $id);
        } catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e) {
            Alert::error('Trying to access unidentified Groups.')->flash();
            return Redirect::to('admin/user/edit/' . $id);
        }
    }

    // Cloud connect ---------------------------------------------------------------------------------------------------

    public function getConnect($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        try {

            if ($auth['isAdmin'] || $auth['userId'] == $id) {

                $user = Sentry::getUserProvider()->findById($id);
                $profile = $user->profile;

                $clan_id = $profile->clan_id;

                $clan = Clan::find($clan_id);

                if(!is_null($profile->remote_id)){
                    Alert::error('Already connected to cloud data store.')->flash();
                    return Redirect::to('admin/user/show/'.$auth['userId']);
                }

                if(is_null($profile->a3_id)){
                    Alert::error('You need to add your Arma 3 player id to your profile to connect to the cloud.')->flash();
                    return Redirect::to('admin/user/show/'.$auth['userId']);
                }

                $couchAPI = new Alive\CouchAPI();
                $result = $couchAPI->createClanMember($profile->a3_id, $profile->username, $clan->tag);

                if(isset($result['response'])){
                    if(isset($result['response']->rev)){
                        $remoteId = $result['response']->rev;
                        $profile->remote_id = $remoteId;
                        $profile->save();

                        Alert::success('Member connected to the cloud data store.')->flash();
                        return Redirect::to('admin/user/show/'.$auth['userId']);
                    }else{
                        Alert::error('There was an error connecting to the cloud data store, please try again later.')->flash();
                        return Redirect::to('admin/user/show/'.$auth['userId']);
                    }
                }else{
                    Alert::error('There was an error connecting to the cloud data store, please try again later.')->flash();
                    return Redirect::to('admin/user/show/'.$auth['userId']);
                }

            } else {
                Alert::error('Sorry.')->flash();
                return Redirect::to('admin/user/show/'.$auth['userId']);
            }

        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Alert::error('User was not found.')->flash();
            return Redirect::to('admin/user/edit/' . $id);
        } catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e) {
            Alert::error('Trying to access unidentified Groups.')->flash();
            return Redirect::to('admin/user/edit/' . $id);
        }
    }

    public function getConnectdebug($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        try {

            if ($auth['isAdmin']) {

                $user = Sentry::getUserProvider()->findById($id);
                $profile = $user->profile;
                $clan_id = $profile->clan_id;
                $clan = Clan::find($clan_id);

                TempoDebug::dump($user->toArray());
                TempoDebug::dump($profile->toArray());
                TempoDebug::dump($clan->toArray());

                TempoDebug::message('Connection test for group : ' . $clan->name . ' [' .  $clan->tag . ']');

                if(!is_null($clan->remote_id)){
                    TempoDebug::message('Group remote_id is set rev id: ' . $clan->remote_id);
                    TempoDebug::message('Get group couch profile..');

                    $couchAPI = new Alive\CouchAPI();
                    $result = $couchAPI->getClanUser($clan->key, $clan->password);

                    if(isset($result['response'])){
                        $response = $result['response'];
                        if(isset($response->error)){
                            TempoDebug::dump($response);
                        }else{
                            TempoDebug::dump($response);
                        }
                    }
                }

                $couchAPI = new Alive\CouchAPI();
                $result = $couchAPI->getClanUser($clan->key, $clan->password);

                if(isset($result['response'])){
                    $response = $result['response'];
                    if(isset($response->error)){

                        TempoDebug::message('Attempt to create couch group user..');
                        TempoDebug::dump($response);

                        $result = $couchAPI->createClanUser($clan->key, $clan->password, $clan->tag);

                        if(isset($result['response'])){
                            if(isset($result['response']->rev)){
                                $remoteId = $result['response']->rev;
                                $clan->remote_id = $remoteId;
                                $clan->save();

                                TempoDebug::message('Couch group user created!');
                            }
                        }
                    }
                }

                TempoDebug::message('Connection test for player : ' . $profile->username);

                if(!is_null($profile->remote_id)){

                    TempoDebug::message('Profile remote_id is set rev id: ' . $profile->remote_id);
                    TempoDebug::message('Get player couch profile..');

                    $couchAPI = new Alive\CouchAPI();
                    $result = $couchAPI->getClanMember($profile->a3_id);

                    if(isset($result['response'])){
                        $response = $result['response'];
                        TempoDebug::dump($response);
                    }

                    exit;
                }

                if(is_null($profile->a3_id) || $profile->a3_id == ''){
                    TempoDebug::message('Players profile A3ID is not set!');
                    exit;
                }

                $couchAPI = new Alive\CouchAPI();
                $result = $couchAPI->getClanMember($profile->a3_id);

                if(isset($result['response'])){
                    $response = $result['response'];

                    TempoDebug::message('Get player couch profile..');

                    if(isset($response->error)){

                        TempoDebug::dump($response);
                        TempoDebug::message('Attempt to create couch profile..');

                        $result = $couchAPI->createClanMember($profile->a3_id, $profile->username, $clan->tag);
                        if(isset($result['response'])){
                            $response = $result['response'];
                            if(isset($response->error)){
                            }else{
                                if(isset($response->rev)){
                                    $remoteId = $response->rev;
                                    $profile->remote_id = $remoteId;
                                    $profile->save();
                                    TempoDebug::message('Couch profile created!');
                                }
                            }
                        }
                    }else{

                        TempoDebug::dump($response);

                        $remoteId = $response->_rev;
                        $profile->remote_id = $remoteId;
                        $profile->save();
                        TempoDebug::message('Rev id saved!');
                    }
                }

            } else {
                Alert::error('Sorry.')->flash();
                return Redirect::to('admin/user/show/'.$auth['userId']);
            }

        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Alert::error('User was not found.')->flash();
            return Redirect::to('admin/user/edit/' . $id);
        } catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e) {
            Alert::error('Trying to access unidentified Groups.')->flash();
            return Redirect::to('admin/user/edit/' . $id);
        }
    }
	
	// Email

	 public function getEmail()
    {

        $data = get_default_data();
        $auth = $data['auth'];

        return View::make('admin/user/email')->with($data);

    }
	

	public function postEmail()
    {
 		$data = get_default_data();
        $auth = $data['auth'];

        /*
        $input = array(
            'msg' => Input::get('msg')
        );

        $rules = array (
            'msg' => 'required'
        );

        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            return Redirect::to('admin/user/email/'.$id)->withErrors($v)->withInput()->with($data);
        } else {
        */

            $currentUser = $auth['user'];
            $profile = $auth['profile'];

            if (!$auth['isAdmin']) {
                Alert::error('You don\'t have access to do that.')->flash();
                return Redirect::to('admin/user/index');
            }

            $date = new \DateTime;
            $nowTime = time();

            $content = View::make('emails/bulk/manw')->render();

            $result = DB::select('select * from bulk_email where id =?', array(1));

            echo '<a href="' . url('admin/user/index') . '">Go Back</a><br/><br/>';

            if(count($result) == 0){
                DB::table('bulk_email')->insert(array('count' => 0,'created_at' => $date,'updated_at' => $date));
                $count = 0;
            }else{
                $count = (int) $result[0]->count;
                $updated_at = strtotime($result[0]->updated_at);
                $timeDiff = $nowTime-$updated_at;

                /*
                if($timeDiff < 3600) {
                    $window = floor((3600 - $timeDiff) / 100);
                    echo 'Last called less than one hour ago, next window opens in ' . $window . ' minutes.';
                    exit;
                }
                */

            }

            $users = DB::table('users')
                ->skip($count)
                ->take(300)
                ->get();

            if(count($users)){

                echo count($users) . " users found<br/><br/>";

                $toSend = 0;

                foreach($users as $user){

                    $user = (array) $user;

                    $user_id = $user['id'];
                    $email = $user['email'];
                    $isSystemAddress = strstr($email,'alivemod.com');

                    if($isSystemAddress == false){

                        echo 'sending to user id: ' . $user_id . ' ' . $email . '<br>';

                        $toSend++;

                        $to_array = [
                            [
                                //'email' => $email,
                                'email' => 'unsub-test@testing.mandrillapp.com',
                                //'email' => 'tupolov73@gmail.com',
                                'name' => 'ALiVE user',
                                'type' => 'to'
                            ]
                        ];

                        try {

                            $mandrill = new Mandrill('9vhpxeW9tOS4PEtcPhtmyA');
                            //$mandrill = new Mandrill('AkPMcwIKlP87oLU2aJaPsg'); // test key
                            $message = array(
                                'html' => $content,
                                'text' => '',
                                'subject' => 'ALiVE Mod Update: 0.8 release and please support us in the MANW!',
                                'from_email' => 'noreply@alivemod.com',
                                'from_name' => 'Arma 3 ALIVE Mod Team',
                                'to' => $to_array,
                                'headers' => array('Reply-To' => 'noreply@alivemod.com'),
                                'important' => false,
                                'track_opens' => null,
                                'track_clicks' => null,
                                'auto_text' => null,
                                'auto_html' => null,
                                'inline_css' => null,
                                'url_strip_qs' => null,
                                'preserve_recipients' => null,
                                'view_content_link' => null,
                                'tracking_domain' => null,
                                'signing_domain' => null,
                                'return_path_domain' => null
                            );
                            $async = false;

                            $result = $mandrill->messages->send($message, $async);

                            if($result[0]['status'] == 'sent') {
                                echo 'sent to: ' . $email . '<br>';

                            }else{
                                var_dump($result);
                            }

                        } catch(Mandrill_Error $e) {
                            echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
                            throw $e;
                        }

                        exit;

                    }
                }

                $count += 300;

                DB::table('bulk_email')
                    ->where('id', 1)
                    ->update(array('count' => $count,'created_at' => $date,'updated_at' => $date));
            }else{
                echo "all emails sent!<br/>";

                /*
                DB::table('bulk_email')
                    ->where('id', 1)
                    ->update(array('count' => 0,'created_at' => $date,'updated_at' => $date));
                */
            }

            /*
			Alert::success('You have successfully sent an email to all users.')->flash();
			return Redirect::to('admin/user/index');
            */

        //}
    }	

}

