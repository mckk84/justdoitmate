<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	public $user = null;
		
	public function __construct()
	{
		parent::__construct();
		parse_str($_SERVER['QUERY_STRING'], $_REQUEST);
		$this->load->model('user_model');
		$this->load->library('facebook', array('appId' => '676865195681086', 'secret' => '2883707b5f4b45ee01999648f40daf24',));
		$fblogin = $this->facebook->getLoginUrl(array("scope" => 'email', 'redirect_uri' => base_url().'login'));
		$this->user = $this->facebook->getUser();
	}
	
	public function index()
	{
		$fblogin = $this->facebook->getLoginUrl(array("scope" => 'email', 'redirect_uri' => base_url().'login'));
		$data['fblogin'] = $fblogin;
		$data['fblogout'] = $this->facebook->getLogoutUrl(array("next"=> base_url().'logout')); 
		$data['captcha'] = $this->user_model->getCaptcha();
		$data['login_error'] = '';
		$data['login_info'] = '';
		$data['login'] = 'yes';
		
		if($this->user)
		{
			try{
				$user_profile = $this->facebook->api('/me');
				$response = $this->user_model->fbuserlogin($user_profile);
				if($response['status'] == 'success')
				{
					session_destroy();
					$this->session->set_userdata('fblogout', $data['fblogout']);
				}
			}
			catch(FacebookApiException $e)
			{
				$data['login_error'] = 'facebook login failed.';
				log_message('error', 'facebook api exception:'.$r->getMessage());
				$this->user = null;
			}
		}
					
		if($this->session->userdata('logged_in') == FALSE) 
		{
			if( isset($_POST['email']) && $_POST['email'] != '' )
			{
				$response = $this->user_model->authenticate($_POST);
				if($response['status'] == 'success')
				{
					redirect('/justdoit/', 'refresh');
				}
				$data['login_error'] = $response['error'];
			}
									
			if( isset($_POST['otherlogin']) && $_POST['otherlogin'] != "" && $_POST['otherlogin'] == "facebook")
			{
				redirect(''.$fblogin.'', 'refresh');
			}
			
			// redirect to login
			$this->load->view('login', $data);			
		}
		else
		{
			redirect('/justdoit/', 'refresh');
		}		
	}
	
	public function signup()
	{
		$data['captcha'] = $this->user_model->getCaptcha();
		$data['login_error'] = '';
		$data['login_info'] = '';
		
		if( isset($_POST['email']) && $_POST['email'] != '' )
		{
			$response = $this->user_model->register($_POST);
			$data['login_error'] = $response['error'];
			$data['login_info'] = $response['info'];			
		}
		$this->load->view('login', $data);	
	}
	
	
	public function forgotpassword()
	{
		$data['captcha'] = $this->user_model->getCaptcha();
		$data['login_error'] = '';
		$data['login_info'] = '';
		
		if( isset($_POST['email']) && $_POST['email'] != '' )
		{
			$response = $this->user_model->forgotpassword($_POST);
			$data['login_error'] = $response['error'];
			$data['login_info'] = $response['info'];			
		}
		$this->load->view('login', $data);	
	}
	
	
	public function logout()
	{
		$userdata['id'] = FALSE;
		$userdata['email'] = FALSE;
		$userdata['username'] = FALSE;
		$userdata['logged_in'] = FALSE;
		$this->session->set_userdata($userdata);
		redirect('/login/', 'refresh');		
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */