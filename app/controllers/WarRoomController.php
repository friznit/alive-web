<?php

class WarRoomController extends BaseController {

    public function __construct()
    {
        //Check CSRF token on POST
        $this->beforeFilter('csrf', array('on' => 'post'));

        // Authenticated access only
        $this->beforeFilter('auth');

    }

    // Home ------------------------------------------------------------------------------------------------------------

    public function getIndex()
    {
        $data = get_default_data();

        $data['allAOs'] = AO::all();

        $devs = Profile::where('remark', '=', 'Developer')->get();

        $profiles = array();

        forEach($devs as $profile) {
            $clan = $profile->clan;
            $profile->orbat = $clan->orbat();
            array_push($profiles, $profile);
        }

        $data['devs'] = $profiles;

        return View::make('warroom/home.index')->with($data);
    }

    // Personnel -------------------------------------------------------------------------------------------------------

    public function getPersonnel()
    {
        $data = get_default_data();
        return View::make('warroom/personnel.index')->with($data);
    }

    // Operations ------------------------------------------------------------------------------------------------------

    public function getOperations()
    {
        $data = get_default_data();
        return View::make('warroom/operations.index')->with($data);
    }
}