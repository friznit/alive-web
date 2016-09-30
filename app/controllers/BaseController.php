<?php

use Tempo\TempoDebug;

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

    protected function dump($data,$title = null)
    {
        TempoDebug::dump($data, $title);
    }
}