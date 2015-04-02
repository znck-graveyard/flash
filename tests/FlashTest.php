<?php

use Mockery as m;
use Znck\Flash\FlashNotifier;

class FlashTest extends PHPUnit_Framework_TestCase {

    protected $session;

    protected $flash;

	public function setUp()
	{
        $this->session = m::mock('Znck\Flash\SessionStore');
	}

}
