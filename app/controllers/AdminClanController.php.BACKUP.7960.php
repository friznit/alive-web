<?php

use Tempo\TempoDebug;

class AdminClanController extends BaseController
{

    public function __construct()
    {
        // Check CSRF token on POST
        $this->beforeFilter('csrf', array('on' => 'post'));

        // Authenticated access only
        $this->beforeFilter('auth');

    }

    /**
     * Get a list of clans
     *
     * @return mixed
     */
    public function getIndex()
    {

        $data = get_default_data();
        $auth = $data['auth'];

        if ($auth['isAdmin']) {
            $data['allClans'] = Clan::paginate(10);
            return View::make('admin/clan.index')->with($data);
        } else {
            Alert::error('Sorry.')->flash();
            return Redirect::to('admin/user/show/' . $auth['userId']);
        }
    }

    /**
     * Search by POST
     *
     * @return mixed
     */
    public function postSearch()
    {

        $data = get_default_data();
        $auth = $data['auth'];

        $input = array(
            'query' => Input::get('query'),
            'type' => Input::get('type')
        );

        $rules = array(
            'query' => 'required',
            'type' => 'required|alpha',
        );

        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            return Redirect::to('admin/clan/')->withErrors($v)->withInput()->with($data);
        } else {

            if ($auth['isAdmin']) {

                $query = $input['query'];
                $type = $input['type'];

                switch ($type) {
                    case 'name':
                        $clans = Clan::where('clans.name', 'LIKE', '%' . $query . '%');
                        break;
					case 'tag':
                        $clans = Clan::where('clans.tag', 'LIKE', '%'.$query.'%');
                        break;	
                }

                $clans = $clans->paginate(10);

                $data['links'] = $clans->links();
                $data['allClans'] = $clans;
                $data['query'] = $query;

                return View::make('admin/clan.search')->with($data);

            } else {
                Alert::error('Sorry.')->flash();
                return Redirect::to('admin/user/show/' . $auth['userId']);
            }
        }
    }

    /**
     * Form to create a clan
     *
     * @return mixed
     */
    public function getCreate()
    {

        $data = get_default_data();
        $auth = $data['auth'];

        $data['user'] = $auth['user'];
        $data['myGroups'] = $data['user']->getGroups();
        return View::make('admin/clan.create')->with($data);

    }

    /**
     * Create a clan by POST
     *
     * @return mixed
     */
    public function postCreate()
    {

        $data = get_default_data();
        $auth = $data['auth'];

        $input = array(
            'newGroup' => Input::get('newGroup'),
            'tag' => Input::get('tag'),
        );

        $rules = array(
            'newGroup' => 'required|alpha_num',
            'tag' => 'required',
        );

        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            return Redirect::to('admin/clan/create/')->withErrors($v)->withInput()->with($data);
        } else {

            try {
                $currentUser = $auth['user'];
                $profile = $auth['profile'];

                if ($auth['isGrunt'] || $auth['isOfficer'] || $auth['isLeader']) {
                    Alert::error('You already belong to an active group, you will need to leave this group to create a new one.')->flash();
                    return Redirect::to('admin/clan/show/' . $profile->clan_id);
                }

                if (!is_null($profile->clan_id)) {
                    $clan = new Clan;

                    $tag = str_replace("[", "", $input['tag']);
                    $tag = str_replace("]", "", $tag);

                    $clan->name = $input['newGroup'];
                    $clan->tag = $tag;
                    $clan->key = $this->_generatePassword(32);
                    $clan->password = $this->_generatePassword(32);
                    $clan->type = "Infantry";
                    $clan->size = "Squad";
                    $clan->lat = rand(3300, 4200);
                    $clan->lon = rand(2000, 6000);

                    $clanExists = Clan::where('name', $clan->name)->count();
                    $tagExists = Clan::where('tag', $clan->tag)->count();

                    if (!$clanExists) {
                        if (!$tagExists) {
                            if ($clan->save()) {

                                $profile = $currentUser->profile;

                                $profile->clan_id = $clan->id;

                                if ($profile->save()) {

                                    if ($auth['isUser']) {
                                        $currentUser->removeGroup($auth['userGroup']);
                                        $currentUser->addGroup($auth['leaderGroup']);
                                    }

                                    if ($auth['isAdmin']) {
                                        $currentUser->addGroup($auth['leaderGroup']);
                                    }

                                    $couchAPI = new Alive\CouchAPI();
                                    $result = $couchAPI->createClanUser($clan->key, $clan->password, $clan->tag);

                                    if (isset($result['response'])) {
                                        if (isset($result['response']->rev)) {
                                            $remoteId = $result['response']->rev;
                                            $clan->remote_id = $remoteId;
                                            $clan->save();
                                        }
                                    }

                                    if (is_null($profile->remote_id)) {
                                        $couchAPI = new Alive\CouchAPI();
                                        $result = $couchAPI->createClanMember($profile->a3_id, $profile->username,
                                            $clan->tag);

                                        if (isset($result['response'])) {
                                            if (isset($result['response']->rev)) {
                                                $remoteId = $result['response']->rev;
                                                $profile->remote_id = $remoteId;
                                                $profile->save();
                                            }
                                        }
                                    }

                                    Alert::success('Group created. You are now the leader of this group')->flash();
                                    return Redirect::to('admin/clan/show/' . $clan->id);
                                }
                            }
                        } else {
                            Alert::error('A group with this tag already exists.')->flash();
                            return Redirect::to('admin/clan/create/');
                        }
                    } else {
                        Alert::error('A group with this name already exists.')->flash();
                        return Redirect::to('admin/clan/create/');
                    }
                } else {
                    Alert::success('You already belong to an active group, you will need to leave this group to create a new one.')->flash();
                    return Redirect::to('admin/clan/show/' . $profile->clan_id);
                }
            } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
                Alert::error('There was a problem accessing this account.')->flash();
                return Redirect::to('user/login');
            }
        }
    }

    /**
     * Show a clan by ID
     *
     * @param $id
     * @return mixed
     */
    public function getShow($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        try {
            $clan = Clan::findOrFail($id);
            $data['clan'] = $clan;

            $applicationCount = $clan->applications->count();
            if ($applicationCount > 0) {
                $data['applications'] = $clan->applications->all();
            }

            $serverCount = $clan->servers->count();
            if ($serverCount > 0) {
                $data['servers'] = $clan->servers->all();
            }

            $members = $clan->members();

            $data['members'] = $members->paginate(10);
            return View::make('admin/clan.show')->with($data);

        } catch (ModelNotFoundException $e) {
            return Redirect::to('warroom');
        }

    }

    /**
     * Form to edit a clan
     *
     * @param int $id The ID of the clan to edit
     * @return mixed
     */
    public function getEdit($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        try {

            $currentUser = $auth['user'];
            $profile = $auth['profile'];

            $clan = Clan::find($id);
            $orbat = $clan->orbat();

            $clans = Clan::all();
            $data['clans'] = $clans;

            $data['countries'] = DB::table('countries')->lists('name', 'iso_3166_2');
            $data['groupTypes'] = DB::table('orbattypes')->lists('name', 'type');
            if ($auth['isAdmin']) {
                $data['groupSizes'] = DB::table('orbatsizes')->get();
            } else {
                $data['groupSizes'] = DB::table('orbatsizes')->take(9)->get();
            }

            $data['orbat'] = $orbat;

            if ($auth['isAdmin']) {
                $data['clan'] = $clan;
                return View::make('admin/clan.edit')->with($data);
            } elseif ($auth['isLeader']) {
                if ($profile->clan_id == $clan->id) {
                    $data['clan'] = $clan;
                    return View::make('admin/clan.edit')->with($data);
                } else {
                    Alert::error('You don\'t have access to that group.')->flash();
                    return Redirect::to('admin/clan/show/' . $id);
                }
            } else {
                Alert::error('You don\'t have access to that group.')->flash();
                return Redirect::to('admin/clan/show/' . $id);
            }

        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Alert::error('There was a problem accessing this account.')->flash();
            return Redirect::to('admin');
        }
    }

    /**
     * Edit a form by POST
     *
     * @param int $id The ID of the clan to edit
     * @return mixed
     */
    public function postEdit($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        $input = array(
            'name' => Input::get('name'),
            'title' => Input::get('title'),
            'tag' => Input::get('tag'),
            'website' => Input::get('website'),
            'twitchStream' => Input::get('twitchStream'),
            'teamspeak' => Input::get('teamspeak'),
            'country' => Input::get('country'),
            'type' => Input::get('type'),
            'size' => Input::get('size'),
            'description' => Input::get('description'),
            'allowApplicants' => Input::get('allowApplicants', 0),
            'applicationText' => Input::get('applicationText', 0),
            'lat' => Input::get('lat'),
            'lon' => Input::get('lon'),
        );

<<<<<<< HEAD
        $rules = array (
            'name' => 'required|alpha_num',
=======
        $rules = array(
            'name' => 'required',
>>>>>>> 2111035cc6ba25dad0aecd5a5a3387edee5e136d
            'website' => 'url',
            'twitchStream' => 'url',
        );

        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            return Redirect::to('admin/clan/edit/' . $id)->withErrors($v)->withInput()->with($data);
        } else {

            try {
                $currentUser = $auth['user'];
                $profile = $auth['profile'];

                $clan = Clan::find($id);

                if (!$auth['isAdmin'] && !$auth['isLeader']) {
                    Alert::error('You don\'t have access to that group.')->flash();
                    return Redirect::to('admin/clan/show/' . $id);
                }

                if (!$auth['isAdmin'] && $auth['isLeader'] && $profile->clan_id != $clan->id) {
                    Alert::error('You don\'t have access to that group.')->flash();
                    return Redirect::to('admin/clan/show/' . $id);
                }

                $allowApplicants = false;
                if (isset($input['allowApplicants'])) {
                    if ($input['allowApplicants'] === 'on') {
                        $allowApplicants = true;
                    }
                }

                if ($input['country'] != '') {
                    $countries = DB::table('countries')->lists('name', 'iso_3166_2');
                    $countryName = $countries[$input['country']];
                    $clan->country = $input['country'];
                    $clan->country_name = $countryName;
                }

                $clan->name = $input['name'];
                $clan->title = $input['title'];
                $clan->tag = $input['tag'];
                $clan->website = $input['website'];
                $clan->twitch_stream = $input['twitchStream'];
                $clan->teamspeak = $input['teamspeak'];
                $clan->description = $input['description'];
                $clan->allow_applicants = $allowApplicants;
                $clan->application_text = $input['applicationText'];

                $clan->type = $input['type'];
                $clan->size = $input['size'];

                $clan->lat = $input['lat'];
                $clan->lon = $input['lon'];

                if ($clan->save()) {
                    Alert::success('Group updated.')->flash();
                    return Redirect::to('admin/clan/show/' . $id);
                } else {
                    Alert::error('Group could not be updated.')->flash();
                    return Redirect::to('admin/clan/edit/' . $id);
                }

            } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
                Alert::error('User was not found.')->flash();
                return Redirect::to('admin/user/edit/' . $id);
            }
        }
    }

    /**
     * Change an avatar by ID
     *
     * @param int $id The ID of the clan to change the avatar for
     * @return mixed
     */
    public function postChangeavatar($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        $input = array(
            'avatar' => Input::file('avatar'),
        );

        $rules = array(
            'avatar' => 'mimes:jpeg,bmp,png',
        );

        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            return Redirect::to('admin/clan/edit/' . $id)->withErrors($v)->withInput()->with($data);
        } else {

            try {

                $currentUser = $auth['user'];
                $profile = $auth['profile'];

                $clan = Clan::find($id);

                if (!$auth['isAdmin'] && !$auth['isLeader']) {
                    Alert::error('You don\'t have access to that group.')->flash();
                    return Redirect::to('admin/clan/show/' . $id);
                }

                if (!$auth['isAdmin'] && $auth['isLeader'] && $profile->clan_id != $clan->id) {
                    Alert::error('You don\'t have access to that group.')->flash();
                    return Redirect::to('admin/clan/show/' . $id);
                }

                $clan->avatar->clear();
                $clan->avatar = $input['avatar'];

                if ($clan->save()) {
                    Alert::success('Group updated.')->flash();
                    return Redirect::to('admin/clan/show/' . $id);
                } else {
                    Alert::error('Group could not be updated.')->flash();
                    return Redirect::to('admin/clan/edit/' . $id);
                }

            } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
                Alert::error('User was not found.')->flash();
                return Redirect::to('user/login' . $id);
            }
        }
    }

    /**
     * Delete a clan by POST
     *
     * @param int $id The ID of the clan to delete
     * @return mixed
     */
    public function postDelete($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        try {

            $currentUser = $auth['user'];
            $profile = $auth['profile'];

            $clan = Clan::find($id);

            if (!$auth['isAdmin'] && !$auth['isLeader']) {
                Alert::error('You don\'t have access to that group.')->flash();
                return Redirect::to('admin/clan/show/' . $id);
            }

            if (!$auth['isAdmin'] && $auth['isLeader'] && $profile->clan_id != $clan->id) {
                Alert::error('You don\'t have access to that group.')->flash();
                return Redirect::to('admin/clan/show/' . $id);
            }

            $userGroup = Sentry::findGroupByName('Users');
            $gruntGroup = Sentry::findGroupByName('Grunt');
            $officerGroup = Sentry::findGroupByName('Officer');
            $leaderGroup = Sentry::findGroupByName('Leader');
            $adminGroup = Sentry::findGroupByName('Admins');

            $members = $clan->members->all();

            foreach ($members as $member) {

                $user = Sentry::findUserById($member->user_id);
                $profile = $user->profile;

                if ($user->inGroup($auth['gruntGroup'])) {
                    $user->removeGroup($auth['gruntGroup']);
                }
                if ($user->inGroup($auth['officerGroup'])) {
                    $user->removeGroup($auth['officerGroup']);
                }
                if ($user->inGroup($auth['leaderGroup'])) {
                    $user->removeGroup($auth['leaderGroup']);
                }

                if (!$user->inGroup($auth['adminGroup'])) {
                    $user->addGroup($auth['userGroup']);
                }

                $profile->clan_id = 0;
                $profile->save();
            }

            $servers = $clan->servers->all();

            foreach ($servers as $server) {
                $server->delete();
            }

            $applications = $clan->applications->all();

            foreach ($applications as $application) {
                $application->delete();
            }

            if (!is_null($clan->remote_id)) {
                $couchAPI = new Alive\CouchAPI();
                $result = $couchAPI->deleteClanUser($clan->key, $clan->remote_id);
            }

            $clan->delete();

            return Redirect::to('admin/user/show/' . $currentUser->getId());

        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Alert::error('User was not found.')->flash();
            return Redirect::to('user/login' . $id);
        }
    }

    /**
     * Remove a member from a clan
     *
     * @param int $id The ID of the user to remove
     * @return mixed
     */
    public function postRemovemember($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        try {

            $currentUser = $auth['user'];
            $profile = $auth['profile'];

            $user = Sentry::findUserById($id);
            $memberProfile = $user->profile;

            $clan_id = $memberProfile->clan_id;

            $clan = Clan::find($clan_id);

            if (!$auth['isAdmin'] && !$auth['isLeader'] && !$auth['isOfficer']) {
                Alert::error('You don\'t have access to that group.')->flash();
                return Redirect::to('admin/clan/show/' . $id);
            }

            if (!$auth['isAdmin'] && ($auth['isLeader'] || $auth['isOfficer']) && $profile->clan_id != $clan->id) {
                Alert::error('You don\'t have access to that group.')->flash();
                return Redirect::to('admin/clan/show/' . $id);
            }

            if ($user->inGroup($auth['gruntGroup'])) {
                $user->removeGroup($auth['gruntGroup']);
            }
            if ($user->inGroup($auth['officerGroup'])) {
                $user->removeGroup($auth['officerGroup']);
            }
            if ($user->inGroup($auth['leaderGroup'])) {
                $user->removeGroup($auth['leaderGroup']);
            }

            if (!$user->inGroup($auth['adminGroup'])) {
                $user->addGroup($auth['userGroup']);
            }

            // Remove player ServerGroup from CouchDB
            if (!is_null($memberProfile->remote_id)) {
                $group = "";
                $couchAPI = new Alive\CouchAPI();
                $result = $couchAPI->updateClanMember($memberProfile->a3_id, $memberProfile->username, $group,
                    $memberProfile->remote_id);

                if (isset($result['response'])) {
                    if (isset($result['response']->rev)) {
                        $remoteId = $result['response']->rev;
                        $memberProfile->remote_id = $remoteId;
                    }
                }
            }

            $memberProfile->clan_id = 0;
            $memberProfile->save();

            Alert::success('Member removed.')->flash();
            return Redirect::to('admin/clan/show/' . $clan_id);

        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Alert::error('User was not found.')->flash();
            return Redirect::to('user/login' . $id);
        }
    }

    /**
     * Remove the logged in user from a clan
     *
     * @param int $id The ID of the clan to leave
     * @return mixed
     */
    public function postLeave($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        try {

            $currentUser = $auth['user'];
            $profile = $auth['profile'];

            $clan_id = $profile->clan_id;

            $clan = Clan::find($id);

            if (!$auth['isGrunt'] && !$auth['isOfficer'] && !$auth['isOfficer']) {
                Alert::error('Sorry.')->flash();
                return Redirect::to('admin/clan/show/' . $id);
            }

            if (!$auth['isAdmin'] && ($auth['isGrunt'] || $auth['isOfficer']) && $id != $clan_id) {
                Alert::error('Sorry.')->flash();
                return Redirect::to('admin/clan/show/' . $id);
            }

            if ($currentUser->inGroup($auth['gruntGroup'])) {
                $currentUser->removeGroup($auth['gruntGroup']);
            }
            if ($currentUser->inGroup($auth['officerGroup'])) {
                $currentUser->removeGroup($auth['officerGroup']);
            }
            if ($currentUser->inGroup($auth['leaderGroup'])) {
                $currentUser->removeGroup($auth['leaderGroup']);
            }

            if (!$currentUser->inGroup($auth['adminGroup'])) {
                $currentUser->addGroup($auth['userGroup']);
            }

            // Remove player ServerGroup from CouchDB
            if (!is_null($profile->remote_id)) {
                $group = "";
                $couchAPI = new Alive\CouchAPI();
                $result = $couchAPI->updateClanMember($profile->a3_id, $profile->username, $group, $profile->remote_id);

                if (isset($result['response'])) {
                    if (isset($result['response']->rev)) {
                        $remoteId = $result['response']->rev;
                        $profile->remote_id = $remoteId;
                    }
                }
            }

            $profile->clan_id = 0;
            $profile->save();

            Alert::success('You have left the group.')->flash();
            return Redirect::to('admin/user/show/' . $auth['userId']);

        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Alert::error('User was not found.')->flash();
            return Redirect::to('user/login' . $id);
        }
    }

    /**
     * Form to edit a clan member
     *
     * @param int $id The ID of the user to edit
     * @return mixed
     */
    public function getMemberedit($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        try {
            $currentUser = $auth['user'];
            $profile = $auth['profile'];

            $user = Sentry::findUserById($id);
            $memberProfile = $user->profile;

            $clan_id = $memberProfile->clan_id;

            $clan = Clan::find($clan_id);

            if (!$auth['isAdmin'] && !$auth['isLeader'] && !$auth['isOfficer']) {
                Alert::error('You don\'t have access to that group.')->flash();
                return Redirect::to('admin/clan/show/' . $id);
            }

            if (!$auth['isAdmin'] && ($auth['isLeader'] || $auth['isOfficer']) && $profile->clan_id != $clan->id) {
                Alert::error('You don\'t have access to that group.')->flash();
                return Redirect::to('admin/clan/show/' . $id);
            }

            $data['member'] = $memberProfile;
            $data['user'] = $user;

            return View::make('admin/clan.member_edit')->with($data);

        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Alert::error('User was not found.')->flash();
            return Redirect::to('user/login' . $id);
        }
    }

    /**
     * Update a member by POST
     *
     * @param int $id The ID of the user you are editing
     * @return mixed
     */
    public function postMemberedit($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        $input = array(
            /*
            'a2ID' => Input::get('a2ID'),
            'a3ID' => Input::get('a3ID'),
            */
            'remark' => Input::get('remark'),
        );

        $rules = array(//'a3ID' => 'required',
        );

        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            return Redirect::to('admin/clan/memberedit/' . $id)->withErrors($v)->withInput()->with($data);
        } else {

            try {

                $currentUser = $auth['user'];
                $profile = $auth['profile'];

                $user = Sentry::findUserById($id);
                $memberProfile = $user->profile;

                $clan_id = $memberProfile->clan_id;

                $clan = Clan::find($clan_id);

                if (!$auth['isAdmin'] && !$auth['isLeader'] && !$auth['isOfficer']) {
                    Alert::error('You don\'t have access to that group.')->flash();
                    return Redirect::to('admin/clan/show/' . $id);
                }

                if (!$auth['isAdmin'] && ($auth['isLeader'] || $auth['isOfficer']) && $profile->clan_id != $clan->id) {
                    Alert::error('You don\'t have access to that group.')->flash();
                    return Redirect::to('admin/clan/show/' . $id);
                }

                /*
                $memberProfile->a2_id = $input['a2ID'];
                $memberProfile->a3_id = $input['a3ID'];
                */
                $memberProfile->remark = $input['remark'];
                $memberProfile->save();

                Alert::success('Member updated.')->flash();
                return Redirect::to('admin/clan/show/' . $clan_id);

            } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
                Alert::error('User was not found.')->flash();
                return Redirect::to('user/login' . $id);
            }
        }
    }

    /**
     * Add a new member to a clan
     *
     * @param int $id The ID of the user to add
     * @return mixed
     */
    public function postMemberadd($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        $input = array(
            'email' => Input::get('email'),
            'username' => Input::get('username'),
            'password' => Input::get('password'),
            'password_confirmation' => Input::get('password_confirmation'),
            'remark' => Input::get('remark'),
        );

        $rules = array(
            'email' => 'required|min:4|max:32|email',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required'
        );

        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            return Redirect::to('admin/clan/edit/' . $id)->withErrors($v)->withInput()->with($data);
        } else {

            try {

                $currentUser = $auth['user'];
                $profile = $auth['profile'];

                $clan = Clan::find($id);

                if (!$auth['isAdmin'] && !$auth['isLeader']) {
                    Alert::error('You don\'t have access to that group.')->flash();
                    return Redirect::to('admin/clan/show/' . $id);
                }

                if (!$auth['isAdmin'] && $auth['isLeader'] && $profile->clan_id != $clan->id) {
                    Alert::error('You don\'t have access to that group.')->flash();
                    return Redirect::to('admin/clan/show/' . $id);
                }

                $existingMember = User::where('email', $input['email'])->get();

                if (count($existingMember) > 0) {
                    Alert::error('That member already exists')->flash();
                    return Redirect::to('admin/clan/edit/' . $id)->withInput();
                }

                $user = Sentry::register(array(
                    'email' => $input['email'],
                    'password' => $input['password'],
                ), true);

                $data['activationCode'] = $user->GetActivationCode();
                $data['email'] = $input['email'];
                $data['userId'] = $user->getId();

                $gruntGroup = Sentry::findGroupByName('Grunt');

                $user->addGroup($gruntGroup);

                $profile = new Profile;
                $profile->user_id = $data['userId'];
                $profile->username = $input['username'];
                $profile->alias = $input['username'];
                $profile->remark = $input['remark'];
                $profile->clan_id = $id;

                $profile->save();

                // should there be a connect user to couch call here?

                if (isset($result['response'])) {
                    if (isset($result['response']->rev)) {
                        $remoteId = $result['response']->rev;
                        $clan->remote_id = $remoteId;
                        $clan->save();
                    }
                }

                Alert::success('Member added.')->flash();
                return Redirect::to('admin/clan/show/' . $id);

            } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
                Alert::error('User was not found.')->flash();
                return Redirect::to('user/login' . $id);
            }
        }
    }

    /**
     * Form to step down the leader of a clan
     *
     * @param int $id The ID of the clan
     * @return mixed
     */
    public function getLeaderstepdown($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        try {

            $clan = Clan::findOrFail($id);
            $data['clan'] = $clan;
            $members = $clan->members;
            $data['members'] = $members->all();
            return View::make('admin/clan.leader_stepdown')->with($data);

        } catch (ModelNotFoundException $e) {
            return Redirect::to('warroom');
        }
    }

    /**
     * Step down a leader of a clan by POST
     *
     * @param int $id The ID of the clan
     * @return mixed
     */
    public function postLeaderstepdown($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        $input = array(
            'replacement' => Input::get('replacement'),
        );

        $rules = array(
            'replacement' => 'required',
        );

        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            return Redirect::to('admin/clan/leaderstepdown/' . $id)->withErrors($v)->withInput()->with($data);
        } else {

            try {

                $currentUser = $auth['user'];
                $profile = $auth['profile'];

                $user = Sentry::findUserById($input['replacement']);
                $memberProfile = $user->profile;

                $clan = Clan::find($id);

                if (!$auth['isAdmin'] && !$auth['isLeader']) {
                    Alert::error('You don\'t have access to that group.')->flash();
                    return Redirect::to('admin/clan/show/' . $id);
                }

                if (!$auth['isAdmin'] && $auth['isLeader'] && $profile->clan_id != $clan->id) {
                    Alert::error('You don\'t have access to that group.')->flash();
                    return Redirect::to('admin/clan/show/' . $id);
                }

                if ($auth['isLeader']) {
                    $currentUser->removeGroup($auth['leaderGroup']);
                    $currentUser->addGroup($auth['officerGroup']);
                }


                if ($user->inGroup($auth['gruntGroup'])) {
                    $user->removeGroup($auth['gruntGroup']);
                }
                if ($user->inGroup($auth['officerGroup'])) {
                    $user->removeGroup($auth['officerGroup']);
                }
                if ($user->inGroup($auth['leaderGroup'])) {
                    $user->removeGroup($auth['leaderGroup']);
                }

                if (!$user->inGroup($auth['adminGroup'])) {
                    $user->addGroup($auth['leaderGroup']);
                }

                Alert::success('You have stepped down as leader. You are now an officer')->flash();
                return Redirect::to('admin/clan/show/' . $id);

            } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
                Alert::error('User was not found.')->flash();
                return Redirect::to('user/login' . $id);
            }
        }
    }

    /**
     * Step down the officer by POST
     *
     * @param int $id The ID of the clan
     * @return mixed
     */
    public function postOfficerstepdown($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        try {

            $currentUser = $auth['user'];
            $profile = $auth['profile'];

            $clan = Clan::find($id);

            if (!$auth['isAdmin'] && !$auth['isLeader']) {
                Alert::error('You don\'t have access to that group.')->flash();
                return Redirect::to('admin/clan/show/' . $id);
            }

            if (!$auth['isAdmin'] && $auth['isLeader'] && $profile->clan_id != $clan->id) {
                Alert::error('You don\'t have access to that group.')->flash();
                return Redirect::to('admin/clan/show/' . $id);
            }

            if ($currentUser->inGroup($auth['officerGroup'])) {
                $currentUser->removeGroup($auth['officerGroup']);
                $currentUser->addGroup($auth['gruntGroup']);
            }

            Alert::success('You have stepped down as officer. You are now a member')->flash();
            return Redirect::to('admin/clan/show/' . $id);

        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Alert::error('User was not found.')->flash();
            return Redirect::to('user/login' . $id);
        }
    }

    /**
     * Promote a user by ID
     *
     * @param int $id The ID of the user to promote
     * @return mixed
     */
    public function postPromote($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        try {

            $currentUser = $auth['user'];
            $profile = $auth['profile'];

            $user = Sentry::findUserById($id);
            $memberProfile = $user->profile;

            $clan_id = $memberProfile->clan_id;

            $clan = Clan::find($clan_id);

            if (!$auth['isAdmin'] && !$auth['isLeader'] && !$auth['isOfficer']) {
                Alert::error('You don\'t have access to that group.')->flash();
                return Redirect::to('admin/clan/show/' . $id);
            }

            if (!$auth['isAdmin'] && ($auth['isLeader'] || $auth['isOfficer']) && $profile->clan_id != $clan->id) {
                Alert::error('You don\'t have access to that group.')->flash();
                return Redirect::to('admin/clan/show/' . $id);
            }

            if ($user->inGroup($auth['gruntGroup'])) {
                $user->removeGroup($auth['gruntGroup']);
            }
            if ($user->inGroup($auth['officerGroup'])) {
                $user->removeGroup($auth['officerGroup']);
            }
            if ($user->inGroup($auth['leaderGroup'])) {
                $user->removeGroup($auth['leaderGroup']);
            }

            if (!$user->inGroup($auth['adminGroup'])) {
                $user->addGroup($auth['officerGroup']);
            }

            Alert::success('Member promoted.')->flash();
            return Redirect::to('admin/clan/show/' . $clan_id);

        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Alert::error('User was not found.')->flash();
            return Redirect::to('user/login' . $id);
        }

    }

    /**
     * Demote a user
     *
     * @param int $id The ID of the user to demote
     * @return mixed
     */
    public function postDemote($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        try {

            $currentUser = $auth['user'];
            $profile = $auth['profile'];

            $user = Sentry::findUserById($id);
            $memberProfile = $user->profile;

            $clan_id = $memberProfile->clan_id;

            $clan = Clan::find($clan_id);

            if (!$auth['isAdmin'] && !$auth['isLeader']) {
                Alert::error('You don\'t have access to that group.')->flash();
                return Redirect::to('admin/clan/show/' . $id);
            }

            if (!$auth['isAdmin'] && $auth['isLeader'] && $profile->clan_id != $clan->id) {
                Alert::error('You don\'t have access to that group.')->flash();
                return Redirect::to('admin/clan/show/' . $id);
            }

            if ($user->inGroup($auth['gruntGroup'])) {
                $user->removeGroup($auth['gruntGroup']);
            }
            if ($user->inGroup($auth['officerGroup'])) {
                $user->removeGroup($auth['officerGroup']);
            }
            if ($user->inGroup($auth['leaderGroup'])) {
                $user->removeGroup($auth['leaderGroup']);
            }

            if (!$user->inGroup($auth['adminGroup'])) {
                $user->addGroup($auth['gruntGroup']);
            }

            Alert::success('Member demoted.')->flash();
            return Redirect::to('admin/clan/show/' . $clan_id);

        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Alert::error('User was not found.')->flash();
            return Redirect::to('user/login' . $id);
        }
    }

    /**
     * Import a squad through XML
     *
     * @param int $id The ID of the clan to import to
     * @return mixed
     */
    public function postImportsquad($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        $input = array(
            'squadURL' => Input::get('squadURL'),
        );

        $rules = array(
            'squadURL' => 'required|url',
        );

        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            return Redirect::to('admin/clan/edit/' . $id)->withErrors($v)->withInput()->with($data);
        } else {

            try {
                $currentUser = $auth['user'];
                $profile = $auth['profile'];

                $clan = Clan::find($id);

                if (!$auth['isAdmin'] && !$auth['isLeader']) {
                    Alert::error('You don\'t have access to that group.')->flash();
                    return Redirect::to('admin/clan/show/' . $id);
                }

                if (!$auth['isAdmin'] && $auth['isLeader'] && $profile->clan_id != $clan->id) {
                    Alert::error('You don\'t have access to that group.')->flash();
                    return Redirect::to('admin/clan/show/' . $id);
                }

                $url = $input['squadURL'];
                $curl = new anlutro\cURL\cURL;
                $xml = $curl->get($url);
                $xmldata = json_decode(json_encode(simplexml_load_string($xml)), true);

                if (isset($xmldata['name']) && !is_array($xmldata['name'])) {
                    $clan->name = $xmldata['name'];
                }

                if (isset($xmldata['title']) && !is_array($xmldata['title'])) {
                    $clan->title = $xmldata['title'];
                }

                if (isset($xmldata['web']) && !is_array($xmldata['web'])) {
                    $clan->website = $xmldata['web'];
                }

                $consolidatedMembers = array();

                foreach ($xmldata['member'] as $member) {

                    if (isset($member['@attributes']['nick'])) {

                        $newMember = new stdClass();

                        $newMember->created = false;
                        $newMember->reason = '';

                        $newMember->username = $member['@attributes']['nick'];

                        if (isset($member['@attributes']['id'])) {
                            $id = $member['@attributes']['id'];
                            if (strlen($id) < 17) {
                                $newMember->a2_id = $id;
                            } else {
                                $newMember->a3_id = $id;
                            }
                        }

                        if (isset($member['name'])) {
                            $newMember->name = $member['name'];
                        }

                        if (isset($member['email'])) {
                            $newMember->email = $member['email'];
                        }

                        if (isset($member['remark'])) {
                            $newMember->remark = $member['remark'];
                        }

                        if (!isset($consolidatedMembers[$newMember->username])) {
                            $consolidatedMembers[$newMember->username] = $newMember;
                        } else {
                            $consolidatedMembers[$newMember->username] = (object)array_merge((array)$consolidatedMembers[$newMember->username],
                                (array)$newMember);
                        }
                    }
                }

                foreach ($consolidatedMembers as $member) {
                    if (!isset($member->name)) {
                        $member->name = '';
                    }
                    if (!isset($member->email) || (is_array($member->email))) {
                        $member->email = $clan->tag . $member->username;
                        $member->email = preg_replace("/[^A-Za-z0-9 ]/", '', $member->email);
                        $member->email = str_replace(' ', '', $member->email);
                        $member->email = $member->email . '@alivemod.com';
                        $member->temporaryEmail = true;
                    } else {
                        $member->temporaryEmail = false;
                    }
                    if (!isset($member->remark)) {
                        $member->remark = '';
                    }
                    if (!isset($member->a2_id)) {
                        $member->a2_id = '';
                    }
                    if (!isset($member->a3_id)) {
                        $member->a3_id = '';
                    }
                }

                foreach ($consolidatedMembers as $member) {

                    $existingMember = User::where('email', $member->email)->get();

                    $existingID = 0;
                    if ($member->a3_id != '') {
                        $existingID = Profile::where('a3_id', $member->a3_id)->get();
                    }

                    if (count($existingMember) == 0 && count($existingID) == 0) {

                        $member->password = $this->_generatePassword();

                        try {
                            $user = Sentry::register(array(
                                'email' => $member->email,
                                'password' => $member->password,
                            ), true);

                            $data['activationCode'] = $user->GetActivationCode();
                            $data['email'] = $member->email;
                            $data['userId'] = $user->getId();

                            $member->user_id = $data['userId'];

                            $user->addGroup($auth['gruntGroup']);

                            $profile = new Profile;
                            $profile->user_id = $data['userId'];
                            $profile->username = $member->username;
                            $profile->alias = $member->username;

                            if (!is_array($member->remark)) {
                                $profile->remark = $member->remark;
                            }

                            $profile->a2_id = $member->a2_id;
                            $profile->a3_id = $member->a3_id;
                            $profile->clan_id = $clan->id;


                            $profile->save();

                            $couchAPI = new Alive\CouchAPI();
                            $result = $couchAPI->createClanMember($member->a3_id, $member->username, $clan->tag);

                            if (isset($result['response'])) {
                                if (isset($result['response']->rev)) {
                                    $remoteId = $result['response']->rev;
                                    $profile->remote_id = $remoteId;
                                    $profile->save();
                                }
                            }

                            $member->created = true;
                            $member->reason = '';

                        } catch (Cartalyst\Sentry\Users\UserExistsException $e) {
                            echo 'User with this login already exists.';
                        }
                    } else {
                        $member->reason = 'User already exists';
                    }
                }

                $clan->save();

                Sentry::login($currentUser, false);

                $data['to'] = $currentUser->email;
                $data['clan'] = $clan;
                $data['results'] = $consolidatedMembers;

                Mail::send('emails.clan.import', $data, function ($m) use ($data) {
                    $m->to($data['to'])->subject('ALiVE War Room - Squad import results');
                });

                return View::make('admin/clan.imported')->with($data);


            } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
                Alert::error('User was not found.')->flash();
                return Redirect::to('admin/user/edit/' . $id);
            }
        }
    }

    /**
     * Export a squad to XML
     *
     * @param int $id The ID of the squad to export
     * @return mixed
     */
    public function postExportsquad($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        try {
            $currentUser = $auth['user'];
            $profile = $auth['profile'];

            $clan = Clan::find($id);

            if (!$auth['isAdmin'] && !$auth['isLeader']) {
                Alert::error('You don\'t have access to that group.')->flash();
                return Redirect::to('admin/clan/show/' . $id);
            }

            if (!$auth['isAdmin'] && $auth['isLeader'] && $profile->clan_id != $clan->id) {
                Alert::error('You don\'t have access to that group.')->flash();
                return Redirect::to('admin/clan/show/' . $id);
            }

            $data['clan'] = $clan;
            $data['results'] = $clan->members->all();

            $content = View::make('admin/clan.exported')->with($data);

            $headers = array(
                'Content-Type' => 'application/x-tt',
                'Content-Disposition' => 'inline;filename=squad.xml',
            );
            return Response::make($content, 200, $headers);


        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Alert::error('User was not found.')->flash();
            return Redirect::to('admin/user/edit/' . $id);
        }

    }

    /**
     * Connect to the cloud
     *
     * @param int $id The ID of the clan to connect with
     * @return mixed
     */
    public function getConnect($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        try {

            $currentUser = $auth['user'];
            $profile = $auth['profile'];

            $clan = Clan::find($id);

            if (!$auth['isAdmin'] && !$auth['isLeader']) {
                Alert::error('You don\'t have access to that group.')->flash();
                return Redirect::to('admin/clan/show/' . $id);
            }

            if (!$auth['isAdmin'] && $auth['isLeader'] && $profile->clan_id != $clan->id) {
                Alert::error('You don\'t have access to that group.')->flash();
                return Redirect::to('admin/clan/show/' . $id);
            }

            if (!is_null($clan->remote_id)) {
                Alert::error('Already connected to cloud data store.')->flash();
                return Redirect::to('admin/clan/show/' . $id);
            }

            $couchAPI = new Alive\CouchAPI();
            $result = $couchAPI->createClanUser($clan->key, $clan->password, $clan->tag);

            if (isset($result['response'])) {
                if (isset($result['response']->rev)) {
                    $remoteId = $result['response']->rev;
                    $clan->remote_id = $remoteId;
                    $clan->save();

                    Alert::success('You have connected to the cloud data store.')->flash();
                    return Redirect::to('admin/clan/show/' . $id);
                } else {
                    Alert::error('There was an error connecting to the cloud data store, please try again later.')->flash();
                    return Redirect::to('admin/clan/show/' . $id);
                }
            } else {
                Alert::error('There was an error connecting to the cloud data store, please try again later.')->flash();
                return Redirect::to('admin/clan/show/' . $id);
            }

        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Alert::error('User was not found.')->flash();
            return Redirect::to('user/login' . $id);
        }
    }

    /**
     * ?
     *
     * TODO: What does this do
     *
     * @param int $id ?
     * @return mixed
     */
    public function getConnectmember($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        try {

            $currentUser = $auth['user'];
            $profile = $auth['profile'];

            $user = Sentry::findUserById($id);
            $memberProfile = $user->profile;

            $clan_id = $memberProfile->clan_id;

            $clan = Clan::find($clan_id);

            if (!$auth['isAdmin'] && !$auth['isLeader']) {
                Alert::error('You don\'t have access to that group.')->flash();
                return Redirect::to('admin/clan/show/' . $clan_id);
            }

            if (!$auth['isAdmin'] && $auth['isLeader'] && $profile->clan_id != $clan->id) {
                Alert::error('You don\'t have access to that group.')->flash();
                return Redirect::to('admin/clan/show/' . $clan_id);
            }

            if (!is_null($memberProfile->remote_id)) {
                Alert::error('Already connected to cloud data store.')->flash();
                return Redirect::to('admin/clan/show/' . $clan_id);
            }

            if (is_null($memberProfile->a3_id) || ($memberProfile->a3_id === '')) {
                Alert::error('Member needs to add their Arma 3 player id to their profile to connect to the cloud.')->flash();
                return Redirect::to('admin/clan/show/' . $clan_id);
            }

            $couchAPI = new Alive\CouchAPI();
            $result = $couchAPI->getClanMember($memberProfile->a3_id);

            if (isset($result['response'])) {
                $response = $result['response'];

                if (isset($response->error)) {

                    $result = $couchAPI->createClanMember($memberProfile->a3_id, $memberProfile->username, $clan->tag);

                    if (isset($result['response'])) {
                        if (isset($result['response']->rev)) {
                            $remoteId = $result['response']->rev;
                            $memberProfile->remote_id = $remoteId;
                            $memberProfile->save();

                            Alert::success('Member connected to the cloud data store.')->flash();
                            return Redirect::to('admin/clan/show/' . $clan_id);
                        } else {
                            Alert::error('There was an error connecting to the cloud data store, please try again later.')->flash();
                            return Redirect::to('admin/clan/show/' . $clan_id);
                        }
                    } else {
                        Alert::error('There was an error connecting to the cloud data store, please try again later.')->flash();
                        return Redirect::to('admin/clan/show/' . $clan_id);
                    }

                } else {

                    $remoteId = $response->_rev;
                    $memberProfile->remote_id = $remoteId;
                    $memberProfile->save();

                    Alert::success('Member connected to the cloud data store.')->flash();
                    return Redirect::to('admin/clan/show/' . $clan_id);

                }
            } else {
                Alert::error('Members Arma 3 player id is probably faulty, have the player check their Arma 3 id.')->flash();
                return Redirect::to('admin/clan/show/' . $clan_id);
            }

        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Alert::error('User was not found.')->flash();
            return Redirect::to('user/login' . $id);
        }
    }

    /**
     * Connect to the cloud in debug mode
     *
     * @param int $id The ID of the clan to connect with
     * @return mixed
     */
    public function getConnectdebug($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        try {

            if ($auth['isAdmin']) {

                $clan = Clan::find($id);

                TempoDebug::dump($clan->toArray());

                TempoDebug::message('Connection test for group : ' . $clan->name . ' [' . $clan->tag . ']');

                if (!is_null($clan->remote_id)) {
                    TempoDebug::message('Group remote_id is set rev id: ' . $clan->remote_id);
                    TempoDebug::message('Get group couch profile..');

                    $couchAPI = new Alive\CouchAPI();
                    $result = $couchAPI->getClanUser($clan->key, $clan->password);

                    if (isset($result['response'])) {
                        $response = $result['response'];
                        if (isset($response->error)) {
                            TempoDebug::dump($response);
                        } else {
                            TempoDebug::dump($response);
                        }
                    }
                }

                $couchAPI = new Alive\CouchAPI();
                $couchAPI->debug = true;
                $couchAPI->cache = false;
                $result = $couchAPI->getClanUser($clan->key, $clan->password);

                if (isset($result['response'])) {
                    $response = $result['response'];
                    if (isset($response->error)) {

                        TempoDebug::message('Attempt to create couch group user..');
                        TempoDebug::dump($response);

                        $result = $couchAPI->createClanUser($clan->key, $clan->password, $clan->tag);

                        if (isset($result['response'])) {
                            if (isset($result['response']->rev)) {
                                $remoteId = $result['response']->rev;
                                $clan->remote_id = $remoteId;
                                $clan->save();

                                TempoDebug::message('Couch group user created!');
                            }
                        }
                    }
                }

                $result = $couchAPI->getOrbatRecentOperations($clan->tag);

                TempoDebug::message('Attempt to get orbat data..');
                TempoDebug::dump($result);

            } else {
                Alert::error('Sorry.')->flash();
                return Redirect::to('admin/user/show/' . $auth['userId']);
            }

        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Alert::error('User was not found.')->flash();
            return Redirect::to('admin/user/edit/' . $id);
        } catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e) {
            Alert::error('Trying to access unidentified Groups.')->flash();
            return Redirect::to('admin/user/edit/' . $id);
        }
    }

    /**
     * Generate a strong password
     *
     * @param int $length
     * @param int $strength
     * @return string
     */
    private function _generatePassword($length = 9, $strength = 4)
    {
        $vowels = 'aeiouy';
        $consonants = 'bcdfghjklmnpqrstvwxz';
        if ($strength & 1) {
            $consonants .= 'BCDFGHJKLMNPQRSTVWXZ';
        }
        if ($strength & 2) {
            $vowels .= "AEIOUY";
        }
        if ($strength & 4) {
            $consonants .= '23456789';
        }
        if ($strength & 8) {
            $consonants .= '@#$%';
        }

        $password = '';
        $alt = time() % 2;
        for ($i = 0; $i < $length; $i++) {
            if ($alt == 1) {
                $password .= $consonants[(rand() % strlen($consonants))];
                $alt = 0;
            } else {
                $password .= $vowels[(rand() % strlen($vowels))];
                $alt = 1;
            }
        }
        return $password;
    }

}
