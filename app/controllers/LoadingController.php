<?php

use Alive\CouchAPI;
use Tempo\TempoDebug;

class LoadingController extends BaseController {

    public function __construct()
    {
        // Check CSRF token on POST
        $this->beforeFilter('csrf', array('on' => 'post'));

        // Authenticated access only
        $this->beforeFilter('auth');

    }

   public function getIndex()
    {
        $data = get_default_data();
        return View::make('loading/home.index')->with($data);

    }

}