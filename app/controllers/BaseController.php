<?php

use Tempo\TempoDebug;

/**
 * Class BaseController
 *
 * Extensions upon the standard Controller
 * for this app specifically.
 */
class BaseController extends Controller
{

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

    /**
     * @param $data
     * @param null $title
     */
    protected function dump($data, $title = null)
    {
        TempoDebug::dump($data, $title);
    }
}