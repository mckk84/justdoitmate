<?php
/*
$this->email->from('charan@justdoitmate.in', 'Charan');
		$this->email->to('justdoitmatein@gmail.com');
		$this->email->cc('mckk84@gmail.com');
		//$this->email->bcc('them@their-example.com');

		$this->email->subject('Email Test');
		$this->email->message('<div style="color:red;">Testing the email class.<img src="'.base_url().'images/done.png'.'"/></div>');

		$this->email->send();

		echo $this->email->print_debugger();
		exit;*/

class Template_model extends CI_Model {
	
	function __construct()
    {
        parent::__construct();
    }
	
	public function registrationTemplate($link)
	{
		$head = '<html><body style="font-family:Candara; margin: 0; padding: 0; background: #fff; color: #707070; font-weight: 200; font-size: 14px;">
		<div style="width: 700px; margin: 0 auto;"><div style="background: #475F77; padding: 10px 20px;"><p style="font-size: 20px; font-weight: 200; color: #fff; text-align: center; margin:0;">Welcome to Jusdoit Mate!</p></div><div style="padding: 0 20px; border: 1px solid #ddd; margin-bottom: 20px;"><div style="margin-bottom: 30px;line-height: 20px;"><p>Welcome to join <a href="http://www.justdoitmate.in" style="color: #5f7fb0;">Justdoitmate.in</a>, your daily must-have to get everything perfectly done and keep life well organized. Please first confirm your email address to ensure full access to <a href="http://www.justdoitmate.in" style="color: #5f7fb0;">Justdoitmate.in</a>.</p><a href="{$$}" style="color:#5f7fb0;">Verification Link</a></div><div style="margin-bottom: 30px; line-height: 20px;">JusdoitMate.in enables you to:<ul><li>Easily manage to-do list on web</li></ul></div><div style="font-size: 14px; padding: 0 0 20px;"><div><p>Follow us to be the first to get latest news!</p><a href="https://www.facebook.com/justdoit.mate" alt="facebook" style="color:#898989;text-decoration:none;display:inline-block;margin-right:10px;"><i style="background: url(http://www.justdoitmate.in/images/facebook-icon.png) no-repeat; display: inline-block; width: 30px; height: 30px;vertical-align: middle;margin-right:3px;"></i>Facebook</a><a href="https://plus.google.com/u/0/102258478944564023381/posts" alt="google+" style="color:#898989;text-decoration:none;display:inline-block;"><i style="background: url(http://www.justdoitmate.in/images/google-plus.png) no-repeat; display: inline-block; width: 30px; height: 30px;vertical-align: middle;margin-right:3px;"></i>Google+</a></div><p style="margin: 0 0 5px 0;">Sincerely yours,</p><p style="margin: 0 0 5px 0;">Jusdoit Mate Team</p><p style="margin: 0 0 5px 0;"><a href="http://www.justdoitmate.in" style="font-size:12px;color:#999;text-decoration:none;">&copy;Justdoitmate.in</a></p>
				</div></div></div></body></html>';
		
		$head = str_replace('{$$}', $link, $head);
		return $head;
	}
	
	public function facebookLoginTemplate($user, $email, $password)
	{
		$head = '<html><body style="font-family:Candara; margin: 0; padding: 0; background: #fff; color: #707070; font-weight: 200; font-size: 14px;"><div style="width: 700px; margin: 0 auto;"><div style="background: #475F77; padding: 10px 20px;"><p style="font-size: 20px; font-weight: 200; color: #fff; text-align: center; margin:0;">Welcome to Jusdoit Mate!</p></div><div style="padding: 0 20px; border: 1px solid #ddd; margin-bottom: 20px;"><div style="margin-bottom: 30px;line-height: 20px;"><p>Dear {$U},</p><p>Welcome to join <a href="http://www.justdoitmate.in" style="color: #5f7fb0;">Justdoitmate.in</a>, your daily must-have to get everything perfectly done and keep life well organized. Please first confirm your email address to ensure full access to <a href="http://www.justdoitmate.in" style="color: #5f7fb0;">Justdoitmate.in</a>.</p>		<p style="font-family:Calibri;" >Login Info:<br/><b>Email</b>:{$E}<br/><b>Password</b>:{$P}</p></div><div style="margin-bottom: 30px; line-height: 20px;">JusdoitMate.in enables you to:<ul><li>Easily manage to-do list on web</li></ul></div><div style="font-size: 14px; padding: 0 0 20px;"><div><p>Follow us to be the first to get latest news!</p><a href="https://www.facebook.com/justdoit.mate" alt="facebook" style="color:#898989;text-decoration:none;display:inline-block;margin-right:10px;"><i style="background: url(http://www.justdoitmate.in/images/facebook-icon.png) no-repeat; display: inline-block; width: 30px; height: 30px;vertical-align: middle;margin-right:3px;"></i>Facebook</a><a href="https://plus.google.com/u/0/102258478944564023381/posts" alt="google+" style="color:#898989;text-decoration:none;display:inline-block;"><i style="background: url(http://www.justdoitmate.in/images/google-plus.png) no-repeat; display: inline-block; width: 30px; height: 30px;vertical-align: middle;margin-right:3px;"></i>Google+</a></div><p style="margin: 0 0 5px 0;">Sincerely yours,</p><p style="margin: 0 0 5px 0;">Jusdoit Mate Team</p><p style="margin: 0 0 5px 0;"><a href="http://www.justdoitmate.in" style="font-size:12px;color:#999;text-decoration:none;">&copy;Justdoitmate.in</a></p></div></div></div></body></html>';
		
		$user = ($user == '')? 'User' : $user;
		
		$head = str_replace('{$U}', $user, $head);
		$head = str_replace('{$E}', $email, $head);
		$head = str_replace('{$P}', $password, $head);
		return $head;
	}
	
	public function passwordTemplate($user, $email, $password)
	{
		$head = '<html><body style="font-family:Candara; margin: 0; padding: 0; background: #fff; color: #707070; font-weight: 200; font-size: 14px;"><div style="width: 700px; margin: 0 auto;"><div style="background: #475F77; padding: 10px 20px;"><p style="font-size: 20px; font-weight: 200; color: #fff; text-align: center; margin:0;">Welcome to Jusdoit Mate!</p></div><div style="padding: 0 20px; border: 1px solid #ddd; margin-bottom: 20px;"><div style="margin-bottom: 30px;line-height: 20px;"><p>Dear {$U},</p><p>Welcome to join <a href="http://www.justdoitmate.in" style="color: #5f7fb0;">Justdoitmate.in</a>, your daily must-have to get everything perfectly done and keep life well organized.</p>		<p style="font-family:Calibri;" >Password Reset:<br/><b>Email</b>:{$E}<br/><b>Password</b>:{$P}</p></div><div style="font-size: 14px; padding: 0 0 20px;"><div><p>Follow us to be the first to get latest news!</p><a href="https://www.facebook.com/justdoit.mate" alt="facebook" style="color:#898989;text-decoration:none;display:inline-block;margin-right:10px;"><i style="background: url(http://www.justdoitmate.in/images/facebook-icon.png) no-repeat; display: inline-block; width: 30px; height: 30px;vertical-align: middle;margin-right:3px;"></i>Facebook</a><a href="https://plus.google.com/u/0/102258478944564023381/posts" alt="google+" style="color:#898989;text-decoration:none;display:inline-block;"><i style="background: url(http://www.justdoitmate.in/images/google-plus.png) no-repeat; display: inline-block; width: 30px; height: 30px;vertical-align: middle;margin-right:3px;"></i>Google+</a></div><p style="margin: 0 0 5px 0;">Sincerely yours,</p><p style="margin: 0 0 5px 0;">Jusdoit Mate Team</p><p style="margin: 0 0 5px 0;"><a href="http://www.justdoitmate.in" style="font-size:12px;color:#999;text-decoration:none;">&copy;Justdoitmate.in</a></p></div></div></div></body></html>';
		
		$user = ($user == '')? 'User' : $user;
		
		$head = str_replace('{$U}', $user, $head);
		$head = str_replace('{$E}', $email, $head);
		$head = str_replace('{$P}', $password, $head);
		return $head;
	}
	
}

?>