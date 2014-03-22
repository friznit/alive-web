<?php

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

                if(!is_null($profile->remote_id)){
                    $couchAPI = new Alive\CouchAPI();
                    $result = $couchAPI->deleteClanMember($profile->a3_id, $profile->remote_id);
                }

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

                echo '<h2>Connection test for group : ' . $clan->name . ' [' .  $clan->tag . ']</h2>';

                if(!is_null($clan->remote_id)){
                    echo '<p>Group remote_id is set rev id: ' . $clan->remote_id . '</p>';

                    echo '<h3>Get group couch profile..</h3>';
                    $couchAPI = new Alive\CouchAPI();
                    $result = $couchAPI->getClanUser($clan->key, $clan->password);

                    if(isset($result['response'])){
                        $response = $result['response'];
                        if(isset($response->error)){
                            echo '<p>Couch error response: ' . $response->error . '</p>';
                            echo '<p>Couch reason response: ' . $response->reason . '</p>';
                        }else{
                            echo '<p>Couch id: ' . $response->_id . '</p>';
                            echo '<p>Couch rev: ' . $response->_rev . '</p>';
                            echo '<p>Couch ServerGroup: ' . $response->ServerGroup . '</p>';
                        }
                    }
                }

                $couchAPI = new Alive\CouchAPI();
                $result = $couchAPI->getClanUser($clan->key, $clan->password);

                if(isset($result['response'])){
                    $response = $result['response'];
                    if(isset($response->error)){
                        echo '<p>Couch error response: ' . $response->error . '</p>';
                        echo '<p>Couch reason response: ' . $response->reason . '</p>';

                        echo '<h3>Attempt to create couch group user..</h3>';
                        $result = $couchAPI->createClanUser($clan->key, $clan->password, $clan->tag);

                        if(isset($result['response'])){
                            if(isset($result['response']->rev)){
                                $remoteId = $result['response']->rev;
                                $clan->remote_id = $remoteId;
                                $clan->save();

                                echo '<h3>Couch group user created!</h3>';
                                echo '<p>Couch id: ' . $response->id . '</p>';
                                echo '<p>Couch rev: ' . $response->rev . '</p>';
                            }else{
                                echo '<p>Couch error response: ' . $response->error . '</p>';
                                echo '<p>Couch reason response: ' . $response->reason . '</p>';
                            }
                        }
                    }
                }

                echo '<h2>Connection test for player : ' . $profile->username . '</h2>';

                if(!is_null($profile->remote_id)){
                    echo '<p>Profile remote_id is set rev id: ' . $profile->remote_id . '</p>';

                    echo '<h3>Get player couch profile..</h3>';

                    $couchAPI = new Alive\CouchAPI();
                    $result = $couchAPI->getClanMember($profile->a3_id);

                    if(isset($result['response'])){
                        $response = $result['response'];
                        if(isset($response->error)){
                            echo '<p>Couch error response: ' . $response->error . '</p>';
                            echo '<p>Couch reason response: ' . $response->reason . '</p>';
                        }else{
                            echo '<p>Couch _id: ' . $response->_id . '</p>';
                            echo '<p>Couch _rev: ' . $response->_rev . '</p>';
                            echo '<p>Couch username: ' . $response->username . '</p>';
                            echo '<p>Couch ServerGroup: ' . $response->ServerGroup . '</p>';
                            echo '<p>Couch A3PUID: ' . $response->A3PUID . '</p>';
                        }
                    }

                    exit;
                }

                if(is_null($profile->a3_id) || $profile->a3_id == ''){
                    echo '<p>Players profile A3ID is not set!</p>';
                    exit;
                }

                $couchAPI = new Alive\CouchAPI();
                $result = $couchAPI->getClanMember($profile->a3_id);

                if(isset($result['response'])){
                    $response = $result['response'];
                    echo '<h3>Get player couch profile..</h3>';

                    if(isset($response->error)){
                        echo '<p>Couch error response: ' . $response->error . '</p>';
                        echo '<p>Couch reason response: ' . $response->reason . '</p>';

                        echo '<h3>Attempt to create couch profile..</h3>';

                        $result = $couchAPI->createClanMember($profile->a3_id, $profile->username, $clan->tag);
                        if(isset($result['response'])){
                            $response = $result['response'];
                            if(isset($response->error)){
                                echo '<p>Couch error response: ' . $response->error . '</p>';
                                echo '<p>Couch reason response: ' . $response->reason . '</p>';
                            }else{
                                if(isset($response->rev)){
                                    $remoteId = $response->rev;
                                    $profile->remote_id = $remoteId;
                                    $profile->save();
                                    echo '<h3>Couch profile created!</h3>';
                                    echo '<p>Couch id: ' . $response->_id . '</p>';
                                    echo '<p>Couch rev: ' . $response->_rev . '</p>';
                                }
                            }
                        }
                    }else{
                        print_r($response);
                        /*
                        echo '<p>Couch _id: ' . $response->_id . '</p>';
                        echo '<p>Couch _rev: ' . $response->_rev . '</p>';
                        echo '<p>Couch username: ' . $response->username . '</p>';
                        echo '<p>Couch ServerGroup: ' . $response->ServerGroup . '</p>';
                        echo '<p>Couch A3PUID: ' . $response->A3PUID . '</p>';
                        */
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
}