<?php

class AdminApplicationController extends BaseController
{

    public function __construct()
    {
        // Check CSRF token on POST
        $this->beforeFilter('csrf', array('on' => 'post'));

        // Authenticated access only
        $this->beforeFilter('auth');

    }

    /**
     * Get a list of user applications
     *
     * @return mixed
     */
    public function getIndex()
    {
        $data = get_default_data();
        $auth = $data['auth'];

        $data['allClans'] = Clan::where('allow_applicants', 1)->paginate(10);
        $data['applications'] = $auth['user']->applications;
        return View::make('admin/application.index')->with($data);

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

            $query = $input['query'];
            $type = $input['type'];

            switch ($type) {
                case 'name':
                    $clans = Clan::where('clans.name', 'LIKE', '%' . $query . '%');
                    break;
            }

            $clans = $clans->paginate(10);

            $data['links'] = $clans->links();
            $data['allClans'] = $clans;
            $data['applications'] = $auth['user']->applications;
            $data['query'] = $query;

            return View::make('admin/application.search')->with($data);

        }
    }

    /**
     * Show an applicant by ID
     *
     * @param int $id The ID of the applicant to show
     * @return mixed
     */
    public function getShowapplicant($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        $currentUser = $auth['user'];
        $profile = $auth['profile'];

        $application = Application::find($id);
        $clan = $application->clan;
        $user_id = $currentUser->getId();

        if (!$auth['isAdmin'] && $application->user_id != $user_id) {
            Alert::error('You don\'t have access to that application.')->flash();
            return Redirect::to('admin/user/show/' . $user_id);
        }

        $data['application'] = $application;
        $data['clan'] = $clan;

        return View::make('admin/application.show_applicant')->with($data);
    }

    /**
     * Show a recipient by ID
     *
     * @param int $id The ID of the recipient to show
     * @return mixed
     */
    public function getShowrecipient($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        $currentUser = $auth['user'];
        $profile = $auth['profile'];

        $application = Application::find($id);
        $clan = $application->clan;

        if (!$auth['isAdmin'] && $profile->clan_id != $clan->id) {
            Alert::error('You don\'t have access to that application.')->flash();
            return Redirect::to('admin/clan/show/' . $clan->id);
        }

        $data['application'] = $application;

        return View::make('admin/application.show_recipient')->with($data);
    }

    /**
     * Update an applicant by ID
     *
     * @param int $id The ID of the applicant
     * @return mixed
     */
    public function postUpdateapplicant($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        $input = array(
            'note' => Input::get('note'),
        );

        $rules = array(
            'note' => 'required',
        );

        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            return Redirect::to('admin/application/showapplicant/' . $id)->withErrors($v)->withInput()->with($data);
        } else {

            $currentUser = $auth['user'];
            $profile = $auth['profile'];

            $application = Application::find($id);
            $clan = $application->clan;
            $user_id = $currentUser->getId();

            if (!$auth['isAdmin'] && $application->user_id != $user_id) {
                Alert::error('You don\'t have access to that application.')->flash();
                return Redirect::to('admin/user/show/' . $user_id);
            }

            $application->note = $input['note'];
            $application->save();

            $data['application'] = $application;

            Alert::success('Application updated.')->flash();
            return View::make('admin/application.show_applicant')->with($data);

        }
    }

    /**
     * Update a recipient by ID
     *
     * @param int $id Update a recipient by ID
     * @return mixed
     */
    public function postUpdaterecipient($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        $input = array(
            'response' => Input::get('response'),
        );

        $rules = array(
            'response' => 'required',
        );

        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            return Redirect::to('admin/application/showrecipient/' . $id)->withErrors($v)->withInput()->with($data);
        } else {

            $currentUser = $auth['user'];
            $profile = $auth['profile'];

            $application = Application::find($id);
            $clan = $application->clan;

            if (!$auth['isAdmin'] && $profile->clan_id != $clan->id) {
                Alert::error('You don\'t have access to that application.')->flash();
                return Redirect::to('admin/clan/show/' . $clan->id);
            }

            $application->response = $input['response'];
            $application->save();

            $data['application'] = $application;

            Alert::success('Application updated.')->flash();
            return View::make('admin/application.show_recipient')->with($data);

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
    public function getLodge($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        $currentUser = $auth['user'];
        $profile = $auth['profile'];
        $applications = $currentUser->applications;

        if ($auth['isGrunt'] || $auth['isOfficer'] || $auth['isLeader']) {
            Alert::success('You already belong to an active group, you will need to leave this group to join a new one.')->flash();
            return Redirect::to('admin/clan/show/' . $profile->clan_id);
        }

        if ($profile->clan_id == 0) {
            $data['countries'] = DB::table('countries')->lists('name', 'iso_3166_2');
            $data['user'] = $currentUser;
            $data['profile'] = $profile;
            $data['applications'] = $applications;
            $data['clan'] = Clan::find($id);
            return View::make('admin/application.lodge')->with($data);
        } else {
            Alert::success('You already belong to an active group, you will need to leave this group to create a new one.')->flash();
            return Redirect::to('admin/clan/show/' . $profile->clan_id);
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
    public function postLodge($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        $input = array(
            'note' => Input::get('note'),
        );

        $rules = array(
            'note' => 'required'
        );

        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            return Redirect::to('admin/application/lodge/' . $id)->withErrors($v)->withInput()->with($data);
        } else {

            $currentUser = $auth['user'];
            $profile = $auth['profile'];

            if ($auth['isGrunt'] || $auth['isOfficer'] || $auth['isLeader']) {
                Alert::success('You already belong to an active group, you will need to leave this group to join a new one.')->flash();
                return Redirect::to('admin/clan/show/' . $profile->clan_id);
            }

            $user_id = $currentUser->getId();
            $applicationCount = $currentUser->applications->count();

            if ($applicationCount > 2) {
                Alert::success('You have reached your open application limit')->flash();
                return Redirect::to('admin/user/show/' . $user_id);
            }

            if ($profile->clan_id == 0) {

                $application = new Application;

                if ($profile->country != '') {
                    $countries = DB::table('countries')->lists('name', 'iso_3166_2');
                    $countryName = $countries[$profile->country];
                    $application->country = $profile->country;
                    $application->country_name = $profile->country_name;
                }

                $application->user_id = $user_id;
                $application->clan_id = $id;
                $application->username = $profile->username;
                $application->age_group = $profile->age_group;
                $application->note = $input['note'];

                $application->save();

                Alert::success('Application lodged.')->flash();
                return Redirect::to('admin/user/show/' . $user_id);

            } else {
                Alert::success('You already belong to an active group, you will need to leave this group to create a new one.')->flash();
                return Redirect::to('admin/clan/show/' . $profile->clan_id);
            }
        }
    }

    /**
     * Delete an applicant by ID
     *
     * @param int $id The ID of the applicant to update
     * @return mixed
     */
    public function postDeleteapplicant($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        $currentUser = $auth['user'];
        $profile = $auth['profile'];

        $application = Application::find($id);
        $clan = $application->clan;
        $user_id = $currentUser->getId();

        if (!$auth['isAdmin'] && $application->user_id != $user_id) {
            Alert::error('You don\'t have access to that application.')->flash();
            return Redirect::to('admin/user/show/' . $user_id);
        }

        $application->delete();

        Alert::success('Application deleted.')->flash();
        return Redirect::to('admin/user/show/' . $user_id);

    }

    /**
     * Delete a recipient by ID
     *
     * @param int $id The ID of the recipient
     * @return mixed
     */
    public function postDeleterecipient($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        $currentUser = $auth['user'];
        $profile = $auth['profile'];

        $application = Application::find($id);
        $clan = $application->clan;

        if (!$auth['isAdmin'] && $profile->clan_id != $clan->id) {
            Alert::error('You don\'t have access to that application.')->flash();
            return Redirect::to('admin/clan/show/' . $clan->id);
        }

        $application->delete();

        Alert::success('Application deleted.')->flash();
        return Redirect::to('admin/clan/show/' . $clan->id);

    }

    /**
     * Accept a user application
     *
     * @param int $id The ID of the application to delete
     * @return mixed
     */
    public function postAccept($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        $currentUser = $auth['user'];
        $profile = $auth['profile'];

        $application = Application::find($id);
        $clan = $application->clan;

        if (!$auth['isAdmin'] && $profile->clan_id != $clan->id) {
            Alert::error('You don\'t have access to that application.')->flash();
            return Redirect::to('admin/clan/show/' . $clan->id);
        }

        $applicant = Sentry::findUserById($application->user_id);
        $applicantProfile = $applicant->profile;

        if ($applicant->inGroup($auth['officerGroup'])) {
            $applicant->removeGroup($auth['officerGroup']);
        }
        if ($applicant->inGroup($auth['leaderGroup'])) {
            $applicant->removeGroup($auth['leaderGroup']);
        }

        if (!$applicant->inGroup($auth['adminGroup'])) {
            $applicant->addGroup($auth['gruntGroup']);
        }

        // Update CouchDB ServerGroup
        if (!is_null($applicantProfile->remote_id)) {
            $couchAPI = new Alive\CouchAPI();
            $result = $couchAPI->updateClanMember($applicantProfile->a3_id, $applicantProfile->username, $clan->tag,
                $applicantProfile->remote_id);

            if (isset($result['response'])) {
                if (isset($result['response']->rev)) {
                    $remoteId = $result['response']->rev;
                    $applicantProfile->remote_id = $remoteId;
                }
            }
        }

        $applicantProfile->clan_id = $clan->id;
        $applicantProfile->save();

        forEach ($applicant->applications as $application) {
            $application->delete();
        }

        Alert::success('You have accepted the applicant into your group.')->flash();
        return Redirect::to('admin/clan/show/' . $clan->id);

    }

    /**
     * Deny an application
     *
     * @param int $id The ID of the application to deny
     * @return mixed
     */
    public function postDeny($id)
    {

        $data = get_default_data();
        $auth = $data['auth'];

        $currentUser = $auth['user'];
        $profile = $auth['profile'];

        $application = Application::find($id);
        $clan = $application->clan;

        if (!$auth['isAdmin'] && $profile->clan_id != $clan->id) {
            Alert::error('You don\'t have access to that application.')->flash();
            return Redirect::to('admin/clan/show/' . $clan->id);
        }

        $application->denied = true;

        $application->save();

        Alert::success('Application denied.')->flash();
        return Redirect::to('admin/clan/show/' . $clan->id);

    }

}
