<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Justdoit Mate : Either you run the day or the day runs you.</title>
	<meta name="keywords" content="todo,gtd,to do,list,task manager,checklist,tasks,note,notes">
<meta name="description" content="JustdoitMate is a web application for task management and helps you to get all things done and make life well organized.">
	<link rel="stylesheet" type="text/css" href="/css/style.css">
</head>
<body style="background-color:#fff;" >
<?php 
$this->load->view('header');
?>
<div id="container1">
	<div id="lbody">
		<div id='loginw'><div class='sin'>Sign In</div></div>
		<div id='login'>
		<div class='othersign'>
		<div class='fblogin'><img id='fblogin' data-fb='<?php echo $fblogin;?>' style='display:block;' src='/images/fb-login.gif' /></div>
		<div class='glogin'></div>
		</div>
		<?php $attributes = array('id' => 'formlogin', 'name' => 'login');
		echo form_open('login', $attributes);
		?>
		<input type='hidden' id='ltz' name='ltz' value='0' />
		<input type='hidden' id='otherlogin' name='otherlogin' value='' />
		<table class='ltable' cellpadding='5px'>
		<tr><td colspan='2'><span id='error'><?php echo $login_error;?></span><span id='info'><?php echo $login_info;?></span></td></tr>
		<tr><td class='tdleft' width='35%'> Email: </td><td><input class='inp' tabindex='1' type='text' id='emailid' name='email' maxlength='50' /></td></tr>
		<tr id='pass'><td class='tdleft'> Password: </td><td><input class='inp' type='password' tabindex='2' name='password' id='idpassword' maxlength='15' /></td></tr>
		<tr><td colspan='2' style='text-align:left;'>
		<div style='float:left;margin-top:10px;display:block;vertical-align:middle;'>
		<a id='fpass' name='fpass'>Forget password</a>
		</div>
		<div style='float:right;margin-right:10px;'>
		<input class="myButton" title='Sign In' type='button' id='signin' name='btnsubmit' tabindex='5' value='Sign in' />
		</div>
		</td>
		</tr>
		</table>
		</form>
		</div>
		<div id='footer'></div>
	</div>
	<div id="sbody">
		<div id='loginw'><div class='sin'>Sign Up</div></div>
		<div id='slogin'>
		<?php $attributes = array('id' => 'iformlogin', 'name' => 'ilogin');
		echo form_open('login/signup', $attributes);
		?>
		<input type='hidden' id='tz' name='tz' value='0' />
		<table class='sltable' border='0' cellpadding='4px'>
		<tr>
		<td colspan='2'><span id='serror'><?php echo $login_error;?></span><span id='sinfo'><?php echo $login_info;?></span></td></tr>
		<tr>
		<td class='tdleft' width='50%' > Email: </td>
		<td class='tdleft'><input class='inp' tabindex='1' type='text' id='emailids' name='email' maxlength='50' /></td>
		</tr>
		<tr id='pass'>
		<td class='tdleft'> Password: </td>
		<td class='tdleft' ><input class='inp' type='password' tabindex='2' name='password' id='idpasswords' maxlength='15' /></td>
		</tr>
		<tr>
		<td class='tdleft'> Re-enter Password: </td>
		<td class='tdleft' ><input class='inp' type='password' tabindex='3' name='cpassword' id='idcpassword' maxlength='15' /></td>
		</tr>
		<tr>
		<td class='tdleft'> Captcha: </td>
		<td class='tdleft'><?php echo $captcha['image'];?><input style='float:right;' class='inp' type="text" tabindex='4' name="captcha" value="" /></td>
		</tr>
		<tr>
		<td><a id='backtosignin' class='backtosignin' >Back to Sign In</a></td>
		<td style='text-align:center;'><input class="myButton" title='Enter email for Sign Up' type='button' name='btnsubmit' id='signup' tabindex='6' value='Sign up' /></td>
		</tr>
		</table>
		</form>
		</div>
	</div>
	<div id="fbody">
		<div id='loginw'><div class='sin'>Recover Password</div></div>
		<div id='flogin'>
		<?php $attributes = array('id' => 'fformlogin', 'name' => 'flogin');
		echo form_open('login/forgotpassword', $attributes);
		?>
		<table class='fltable' border='0' cellpadding='4px'>
		<tr>
		<td colspan='2'><span id='ferror'><?php echo $login_error;?></span><span id='finfo'><?php echo $login_info;?></span></td></tr>
		<tr>
		<td class='tdleft' width='50%' > Email: </td>
		<td class='tdleft'><input class='inp' tabindex='1' type='text' id='emailidf' name='email' maxlength='50' /></td>
		</tr>
		<tr>
		<td><a id='backtosign' class='backtosignin' >Back to Sign In</a></td>
		<td style='text-align:center;'><input style='padding:5px;width:150px;' class="myButton" title='Enter email for Password Recovery' type='button' name='btnsubmit' id='fgotpass' tabindex='6' value='Recover Password' /></td>
		</tr>
		</table>
		</form>
		</div>
	</div>
	<div class='features'>
	<span class='hf'>Features</span>
	<div class='featuressub'><span class='hfe'> - It's Free </span><br/> </div>
	<div class='featuressub' ><span class='hfe'> - Checklist </span><p>&nbsp;&nbsp;Ideal for creating shopping list, packing list or making any type of subtasks</p></div>
	<div class='featuressub'><span class='hfe'> - Multi-level priority</span><p>&nbsp;&nbsp;Set different priorities for your to-do's so as to better manage your time</p></div>
	<div class='featuressub'><span class='hfe'> - Group tasks as Projects</span><p>&nbsp;&nbsp;Group tasks into multiple projects and access them easily.</p></div>
	</div>
</div>
<div class='homefooter'>
<div style='display:block;vertical-align:bottom;margin:0px 20px;float:left;margin-left:20px;font-family:calibri;'><span>&#169; COPYRIGHT 2013. ALL RIGHTS ARE RESERVED.</span><br/>
<a style='font-size:15px;' href='<?php echo base_url().'termsofuse' ?>'>Terms</a><b>&nbsp;|&nbsp;</b>
<a style='font-size:15px;' href='<?php echo base_url().'privacy' ?>'>Privacy</a>
</div>
<?php
$this->load->view('follow');
 ?>
</div>
<script type="text/javascript" src="/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="/js/home.js"></script>
</body>
</html>