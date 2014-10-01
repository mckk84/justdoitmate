<?php

Class MY_Controller extends CI_Controller
{
	public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('UTC');
		if(!$this->session->userdata("logged_in"))
		{
			 redirect('/login');
		}
    }
	
}