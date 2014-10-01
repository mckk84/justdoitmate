<?php

class User_model extends CI_Model {
	
	function __construct()
    {
        parent::__construct();
		$this->load->model('template_model');
		$this->load->model('project_model');
    }
	
	public function regenSession()
	{
		$userid = $this->session->userdata('id');
		$result = $this->db->get_where('tbl_user', array('id' => $userid), 1, 0);
		$userdata = array();
		foreach ( $result->result_array() as $row )
		{
			$this->session->set_userdata('username', $row['username']);
			$this->session->set_userdata('timezone', $row['timezone']);
			$this->session->set_userdata('request_hash', do_hash($row['id'].$row['email'].date("Y-m-d H:i:s")));
		}
		return true;
	}
	
	// public function getMethods()
	// {
		// $fs = array("fetch", "cfetch", "done", "get", "save", "project", "userprofile", "undo");
		// $functions = array();
		// foreach($fs as $i => $f)
		// {
			// $rand = "_".$i.$this->RandomStringC(10);
			// $functions[$f] = $rand;			
		// }
		// return json_encode($functions);		
	// }
	
	public function authenticate($REQUEST)
	{
		$response['status'] = 'success';
		$response['error'] = '';
		
		$this->load->helper('email');
		if ( !valid_email($REQUEST['email']) )
		{
			$response['status'] = 'failed';
			$response['error'] = 'Email is not valid';
		}
		if ($REQUEST['password'] == "")
		{
			$response['status'] = 'failed';
			$response['error'] = 'Password is not valid';
		}
		
		if( $response['status'] == 'success')
		{
			$result = $this->db->get_where('tbl_user', array('email' => $REQUEST['email'], 'password' => md5($REQUEST['password'])), 1, 0);
			$error = $this->db->_error_message();  
			if($error != "") 
			{
				log_message('error', $error);
				justlog(0, "login error", 0, "user login: ".mysql_real_escape_string($REQUEST['email']).",".mysql_real_escape_string($REQUEST['password']).": caused db error:".$error);
				$response['status'] = 'failed';
				$response['error'] = 'Login error';
			}
			else
			{
				if($result->num_rows() > 0)
				{
					$res = $this->validRegistration($REQUEST['email']);					
					if( $res['status'] == 'failed')
					{
						$response['status'] = 'failed';
						$response['error'] = 'Login error';
					}
					else if( $res['status'] == 'success' && $res['validated'] == 0) 
					{
						$response['status'] = 'failed';
						$response['error'] = 'Please verify your Email.';
					}
					else
					{
						$userdata = array();
						foreach ($result->result_array() as $row)
						{
							$userdata['id'] = $row['id'];
							$userdata['email'] = $row['email'];
							$userdata['username'] = $row['username'];
							$userdata['tz'] = ((isset($REQUEST['ltz']) && $REQUEST['ltz'] != 0) ? $REQUEST['ltz'] : $row['timezone']);
							$userdata['logged_in'] = TRUE;
							$userdata['request_hash'] = do_hash($row['id'].$row['email'].date("Y-m-d H:i:s"));
						}
						$this->session->set_userdata($userdata);
						$this->loginlog($userdata, 'form login', 1);
						$response['status'] = 'success';
						$response['error'] = '';
					}
				}
				else
				{
					$response['status'] = 'failed';
					$response['error'] = 'Login failed.';
				}
			}
		}
		return $response;
	}
	
	public function register($REQUEST)
	{
		$response['status'] = 'success';
		$response['error'] = '';
		$response['info'] = '';
		
		$captcha = $this->verifyCaptcha($REQUEST);
		if(!$captcha)
		{
			$response['status'] = 'failed';
			$response['error'] = 'Captcha is not valid';
			return $response;
		}
		
		$this->load->helper('email');
		if ( !valid_email($REQUEST['email']) )
		{
			$response['status'] = 'failed';
			$response['error'] = 'Email is not valid';
			return $response;
		}
		
		if( $REQUEST['password'] == '' 
			|| $REQUEST['cpassword'] == '' 
			|| ($REQUEST['password'] != $REQUEST['cpassword'])
			)
		{
			$response['status'] = 'failed';
			$response['error'] = 'Password is not valid';
			return $response;
		}
		
		$user_registered = $this->verifyRegistration($REQUEST['email']);
		if ( $user_registered )
		{
			$response['status'] = 'failed';
			$response['error'] = 'Email already registered.';
			return $response;
		}
		
		//verification successful
		// create user
		$register_date = date('Y-m-d H:i:s');
		$insert = array( 'username' => '', 'email' => $REQUEST['email'], 'password' => md5($REQUEST['password']), 'timezone' => $REQUEST['tz'], 'timestamp' => $register_date);
		$this->db->insert('tbl_user', $insert);
		$error = $this->db->_error_message();
		if( $error != "" )
		{
			log_message('error', "registeration: create user insert error:".$error);
			$response['status'] = 'failed';
			$response['error'] = 'Registration unsuccessful.';
		}
		else
		{
			//insert into user info and send for email confirmation
			$key = sha1($this->RandomString(6).$REQUEST['email']);
			$email_sent = 1;
			$email_sent_date = $register_date;
			$userid = $this->db->insert_id();
			$insert = array( 'user_id' => $userid, 'username' => '', 'email' => $REQUEST['email'], 'register_type' => 'website', 'registered_date' => $register_date, 'created_date' => $register_date, 'key' => $key, 'email_sent' => $email_sent, 'email_sent_date' => $email_sent_date, 'timestamp' => $register_date);
			$this->db->insert('tbl_userinfo', $insert);
			$error = $this->db->_error_message();
			if( $error != "" )
			{
				log_message('error', "register tbl_userinfo insert error:".$error);
				$response['status'] = 'failed';
				$response['error'] = 'Registration unsuccessful.';
			}
			else
			{
				// send mail here and update userinfo table				
				// $userinfoid = $this->db->insert_id(); for update
				// send mail here and update userinfo table	
				
				// create default project
				$pres = $this->project_model->AddProject($userid, "Inbox");
								
				$mail_response = $this->SendNewRegisterMail($key, $REQUEST['email']);
				if(!$mail_response)
				{
					log_message('error', "register mail send failed:".$key.":".$REQUEST['email']);
				}
				
				$response['status'] = 'success';
				$response['info'] = 'Registration successful. Please confirm your email.';
			}
		}
		return $response;
	}
	
	public function SendNewRegisterMail($key, $to)
	{
		$url = base_url().'email_verification.php?key='.$key;
		$messsage = $this->template_model->registrationTemplate($url);
		
		$this->email->from('doit@justdoitmate.in', 'Justdoit Mate');
		$this->email->to($to);
		$this->email->bcc('justdoitmatein@gmail.com');
		
		$this->email->subject('Confirm your Email');
		$this->email->message($messsage);
		$this->email->send();
		$email_debug = $this->email->print_debugger();		
		if( strstr($email_debug, "Your message has been successfully sent using the following protocol: smtp")){
			return true;
		}
		else{
			false;
		}
	}
	
	public function forgotpassword($REQUEST)
	{
		$response['status'] = 'success';
		$response['error'] = '';
		$res = $this->verifyRegistration($REQUEST['email']);
		if($res == false)
		{
			$response['status'] = 'failed';
			$response['error'] = 'Invalid Email.';
		}
		else
		{
			// regenrate pass and send mail.
			$password = $this->RandomString(6);
			$email = $REQUEST['email'];
			$idata = array('password' => md5($password), 'timestamp' => date("Y-m-d H:i:s"));
			$this->db->where('email', $email);
			$this->db->update('tbl_user', $idata); 
			if( $this->db->affected_rows() > 0 ) {
				
				$res = $this->SendPassMail($email, $password);
			
				$response['status'] = 'success';
				$response['info'] = 'Password sent to your Email.';
			} else {
				$response['error'] = 'Password request failed.';
			}			
		}	
		return $response;
	}
	
	
	public function updateProfile( $username )
	{
		$response['status'] = 'failed';
		$response['error'] = '';
		$user_id = $this->session->userdata('id');
		$idata = array('username' => $username, 'timestamp' => date("Y-m-d H:i:s"));
		$this->db->where('id', $user_id);
		$this->db->update('tbl_user', $idata); 
		if( $this->db->affected_rows() > 0 ) {
			$this->session->userdata('username', $username);
			$response['status'] = 'success';
		} else {
			$response['error'] = 'User update failed.';
		}
		return $response;
	}
	
	public function updatePassword($current, $new)
	{
		$response['status'] = 'failed';
		$response['error'] = '';
		
		$user_id = $this->session->userdata('id');
		
		$result = $this->db->get_where('tbl_user', array('id' => $user_id, 'password' => md5($current)), 1, 0);
		if( $result->num_rows() > 0 )
		{
			$response['status'] = 'success';
		}else{
			$response['error'] = 'Current password is invalid.';
		}
		
		if($response['status'] == 'success')
		{
			$idata = array('password' => md5($new), 'timestamp' => date("Y-m-d H:i:s"));
			$this->db->where('id', $user_id);
			$this->db->update('tbl_user', $idata); 
			if( $this->db->affected_rows() > 0 ) {
				$response['status'] = 'success';
			} else {
				$response['status'] = 'failed';
				$response['error'] = 'Password update failed.';
			}
		}
		return $response;
	}
	
	
	public function verifyRegistration( $email )
	{
		$result = $this->db->get_where('tbl_user', array('email' => $email), 1, 0);
		$error = $this->db->_error_message(); 
		if( $error != "" ) 
		{
			log_message('error', $error);
			justlog(0, "email verification error", 0, "user verification: ".mysql_real_escape_string($email).": caused db error:".$error);
			return false;
		}
		// verification for email registeration
		return ($result->num_rows() > 0)? true: false;
	}
	
	
	// Check for valid user sign up
	public function validRegistration( $email )
	{
		$response['validated'] = 0;
		$response['status'] = 'failed';
		$response['error'] = '';
		
		if(base_url() == 'http://local.justdoit-mate.com/')
		{
			$response['status'] = 'success';
			$response['validated'] = 1;
			return true;
		}
			
		$result = $this->db->get_where('tbl_userinfo', array('email' => $email), 1, 0);
		$error = $this->db->_error_message(); 
		if( $error != "" ) 
		{
			log_message('error', $error);
			justlog(0, "email verification error", 0, "user verification: ".mysql_real_escape_string($email).": caused db error:".$error);
			$response['error'] = $error;
			return $response;
		}
		// verification for email registeration
		if( $result->num_rows() > 0){
			foreach ($result->result_array() as $row){
				$response['status'] = 'success';
				$response['validated'] = $row['validated'];
			}
		}
		return $response;
	}
	
	
	public function verifyCaptcha($REQUEST)
	{
		$expiration = time() - 7200; // Two hour limit
		$this->db->query("DELETE FROM captcha WHERE captcha_time < ".$expiration);
		// Then see if a captcha exists:
		$sql = "SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?";
		$binds = array($REQUEST['captcha'], $this->input->ip_address(), $expiration);
		$query = $this->db->query($sql, $binds);
		$row = $query->row();
		return ($row->count == 0)? false : true;
	}
	
	
	public function getCaptcha()
	{
		$this->load->helper('captcha');
		$vals = array(
				'word' => $this->RandomStringC(6),
				'img_path' => './images/captcha/',
				'img_url' => base_url().'/images/captcha/',
				'font_path' => base_url().'images/SIXTY.TTF',
				'img_width' => '200',
				'img_height' => 50,
				'expiration' => 7200
			);
		
		$cap = create_captcha($vals);
		
		$data = array(
				'captcha_time' => $cap['time'],
				'ip_address' => $this->input->ip_address(),
				'word' => $cap['word']
			);
		
		$query = $this->db->insert_string('captcha', $data);
		$this->db->query($query);
		
		return $cap;
	}
	
	public function fbuserlogin($user_profile)
	{
		$response['status'] = 'success';
		$response['error'] = '';
		
		$fresult = $this->db->get_where('tbl_facebook', array('email' => $user_profile['email']), 1, 0);
		$error = $this->db->_error_message();  
		if($error != "") 
		{
			log_message('error', $error);
			$response['status'] = 'failed';
			$response['error'] = 'Login error';
		}
		else
		{
			if($fresult->num_rows() > 0)
			{
				// user already registered
				$result = $this->db->get_where('tbl_user', array('email' => $user_profile['email']), 1, 0);
				$error = $this->db->_error_message(); 
				if($error != "") 
				{
					log_message('error', $error);
					justlog(0, "login error", 0, "user login: ".mysql_real_escape_string($user_profile['email']).": caused db error:".$error);
					$response['status'] = 'failed';
					$response['error'] = 'Login error';
				}
				else
				{
					if($result->num_rows() > 0)
					{
						$userdata = array();
						foreach ($result->result_array() as $row)
						{
							$userdata['id'] = $row['id'];
							$userdata['email'] = $row['email'];
							$userdata['username'] = $row['username'];
							$userdata['tz'] = (isset($REQUEST['tz']) ? -$REQUEST['tz'] : 0);
							$userdata['logged_in'] = TRUE;
							$userdata['request_hash'] = do_hash($row['id'].$row['email'].date("Y-m-d H:i:s"));
						}
						$this->session->set_userdata($userdata);
						$this->loginlog($userdata, 'form login', 1);
						$response['status'] = 'success';
						$response['error'] = '';
					}
					else
					{
						$response['status'] = 'failed';
						$response['error'] = 'Email already registered.';
					}
				}
			}
			else
			{
				$register_date = date('Y-m-d H:i:s');
				$insert = array( 'fbid' => $user_profile['id'], 'email' => $user_profile['email'], 'first_name' => $user_profile['first_name'],'last_name' => $user_profile['last_name'], 'gender' => $user_profile['gender'], 'timestamp' => $register_date);
				$this->db->insert('tbl_facebook', $insert);
				$error = $this->db->_error_message();
				if($error != "")
				{
					log_message('error', "facbook login insert error:".$error);
					$response['status'] = 'failed';
					$response['error'] = 'Registration unsuccessful.';
				}
				else
				{
					$password = $this->RandomString(6);
					$insert = array( 'username' => $user_profile['name'], 'email' => $user_profile['email'], 'password' => md5($password), 'timestamp' => $register_date );
					$this->db->insert('tbl_user', $insert);
					$error = $this->db->_error_message();
					if( $error != "" )
					{
						log_message('error', "facbook login insert error:".$error);
						$response['status'] = 'failed';
						$response['error'] = 'Registration unsuccessful.';
					}
					else
					{
						$userdata['id'] = $this->db->insert_id();
						$res = $this->addUserinfo($userdata['id'], $user_profile, $register_date);
						$res = $this->project_model->AddProject($userdata['id'], "Inbox");
						$user_profile['password'] = $password;
						$res = $this->SendFBMail($user_profile);
						$userdata['email'] = $user_profile['email'];
						$userdata['username'] = $user_profile['name'];
						$userdata['tz'] = 0;
						$userdata['logged_in'] = TRUE;
						$userdata['request_hash'] = do_hash($this->db->insert_id().$user_profile['email'].$register_date);
						$this->session->set_userdata($userdata);
						$response['status'] = 'success';
						$response['error'] = '';
					}
				}
			}
		}
		return $response;
	}
	
	public function RandomString($length) 
	{
        $original_string = array_merge(range(0,9), range('a','z'), range('A', 'Z'));
        $original_string = implode("", $original_string);
        return substr(str_shuffle($original_string), 0, $length);
    }
	
	public function RandomStringC($length) 
	{
        $original_string = array_merge(range(0,9), range('A', 'Z'));
        $original_string = implode("", $original_string);
        return substr(str_shuffle($original_string), 0, $length);
    }
	
	public function addUserinfo($userid, $user_profile, $register_date)
	{
		$data = array('user_id' => $userid, 'username' => $user_profile['name'], 'email' => $user_profile['email'], 'register_type' => 'facbook', 'validated' => 1, 'registered_date' => $register_date, 'created_date' => $register_date, 'timestamp' => $register_date);
		$this->db->insert('tbl_userinfo', $data);
		$error = $this->db->_error_message();  
		if($error != "") 
		{
			log_message('error', $error);
			justlog($userid, "userinfo insert error", 0, "user register:".mysql_real_escape_string($user_profile['email']).": caused db error:".$error);
		}
		return true;
	}
	
	public function SendFBMail($userinfo)
	{
		$messsage = $this->template_model->facebookLoginTemplate($userinfo['name'], $userinfo['email'], $userinfo['password']);
		
		$this->email->from('doit@justdoitmate.in', 'Justdoit Mate');
		$this->email->to($userinfo['email']);
		$this->email->bcc('justdoitmatein@gmail.com');
		
		$this->email->subject('Registration Successful');
		$this->email->message($messsage);
		//$email_debug = $this->email->print_debugger();		
		if( $this->email->send() ){
			return true;
		}else{
			return false;
		}
	}
	
	
	public function SendPassMail($email, $password)
	{
		$messsage = $this->template_model->passwordTemplate('User', $email, $password);
		$this->email->from('doit@justdoitmate.in', 'Justdoit Mate');
		$this->email->to($email);
		$this->email->bcc('justdoitmatein@gmail.com');
		$this->email->subject('Password Reset');
		$this->email->message($messsage);
		if( $this->email->send() ){
			return true;
		}else{
			return false;
		}		
	}
	
	
	public function loginlog($user, $mode='', $status)
	{
		$idata = array( 'user_id' => $user['id'], 'timezoneoffset' => $user['tz'], 'mode' => $mode, 'status' => $status, 'timestamp' => date("Y-m-d H:i:s"));
		$this->db->insert('loginlog', $idata);
		$error = $this->db->_error_message();  
		if($error != "") 
		{
			log_message('error', $error);
		}	
		return true;
	}	
	
}