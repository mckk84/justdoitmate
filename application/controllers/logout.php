<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
	}
	 
	public function index()
	{
		$userdata['id'] = $this->session->userdata('id');
		$userdata['tz'] = $this->session->userdata('tz');
		$this->user_model->loginlog($userdata, 'logout link', 0);
		
		$userdata['id'] = FALSE;
		$userdata['email'] = FALSE;
		$userdata['username'] = FALSE;
		$userdata['logged_in'] = FALSE;
		$this->session->set_userdata($userdata);
		$this->session->sess_destroy();
		redirect('/login', 'refresh');		
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */