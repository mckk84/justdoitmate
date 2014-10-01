<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="keywords" content="todo,gtd,to do,list,task manager,checklist,tasks,note,notes">
<meta name="description" content="JustdoitMate is a web application for task management and helps you to get all things done and make life well organized.">
<title>Justdoit Mate : Either you run the day or the day runs you.</title>
<link rel="stylesheet" type="text/css" href="/css/style.css">
</head>
<?php flush(); ?>
<body>
<div id='loading'>Loading</div>
<div id="popup_box">    <!-- OUR PopupBox DIV-->
<table width='98%' border='0' class='poptable' cellpadding='3px'  >
<tr><th>User Profile</th><th><a id="popupBoxClose">X</a></th></tr>
<tr><td>Account:</td><td><span class='acc' ><?php echo $user['email'];?></span></td></tr>
<tr><td>User Name:</td><td><input type='text' class='inp' name='username' maxlength='50' value='<?php echo (isset($user['username']))?$user['username']:'';?>' /></td></tr>
<tr><th style='vertical-align:middle;' colspan='2'>Change Password</th></tr>
<tr><td>Current Password:</td><td><input type='password' class='inp' name='upassword' maxlength='15' /></td></tr>
<tr><td>New Password:</td><td><input type='password' class='inp' name='password' maxlength='15' /></td></tr>
<tr><td>Confirm Password:</td><td><input type='password' class='inp' name='cpassword' maxlength='15' /></td></tr>
<tr><td><input type='button' style='padding:2px;' class='sbun' id='isave' name='save' value='Save' /></td><td><span id='uerror' class='uerror'></span>
</td></tr>
</table>
</div>
<div id='head'>
<table class="thead">
<tr><td class="theadtd1"><div id="jwname"><a href='<?php echo base_url();?>'><b><i>J</i>ustdoit Mate</b></a></div></td>
<td><div id='ltd'></div></td>
<td class="theadtd2"></td>
<td class="theadtd2" >
	<a title='User profile' alt='User Profile' id='iduserprofile' class='userprofile' >&nbsp;&nbsp;</a>
    &nbsp;&nbsp;<a title='Logout' alt='Logout' class='logoff' href='<?php echo $logout;?>'>&nbsp;&nbsp;</a></td>
</tr></table>
</div>
<?php 
$selectedproject = "";
$plist = array();
if($projects){
	foreach($projects->result() as $row){
		$class = "";
		if($row->id == $project){
			$selectedproject = $row->project;
		}
		if( strlen($row->project) > 20 ){
			$projecttitle = substr($row->project, 0, 20)."..";
		}else{
			$projecttitle = $row->project;
		}
		$plist[] = "<li><span class='projects' id='p".$row->id."' >".$projecttitle."</span></li>";
	}
}
?>
<table border='0' id='add' width='100%'>
<tr>
<td><b class='tblhead'>Pending tasks</b>(<span class='ctc' id='tcount' ><?php echo $tasks['allrows'];?></span>)</td>
<td width="150px"><div class='views'>
<span title='Show tasks in table view' class='myview' id='view1'>&nbsp;Table&nbsp;</span>&nbsp;&nbsp;<span title='Show tasks split by date' class='myview' id='view2'>&nbsp;Date&nbsp;</span>
<a id='selectedview' data-code=''>&nbsp;</a></div></td>
<td width="200px" ><span id='hash' style='visibility:hidden' data-code='<?php echo $user['request_hash'];?>'></span>
<div class="dropdown" id="dropdown">
<input type='hidden' name='filteroptions' id="filteroptions" value="" />
<label for="drop1" class="dropdown_button">
	<i id='selectedproject'><?php echo $selectedproject;?></i>
</label>
	<ul class="dropdown_content" id='projectfilter'>
	<li><b class='plus'>+</b><input type='text' style='padding:1px;margin:2px 0px;height:18px;float:right;width:155px;border-radius:2px;color:lightgrey;border:1px solid grey;' title='Add New Project and press enter to save' class='inp' name='newproject' maxlength='30' id='iproject' value='new project' /></li>
	<?php 
	foreach($plist as $row){
		echo $row;
	}
	?>
	</ul>
</div>	
</td>
<td width='220px' style='text-align:right;border-right:1px solid lightgrey;'>
<?php 
	$attributes = array('name' => 'filterbydate','id'=>'filterbydate');
	echo form_open('', $attributes);
?>
<div class="dropdown" id="dropdown">
<input type='hidden' name='filteroptions' id="filteroptions" value="" />
<label for="drop1" class="dropdown_button">
	<i>filter</i>
</label>
	<ul class="dropdown_content" id="taskfilter">
	</ul>
</div>
<div id='fd'>&nbsp;</div>
</td>
<td width='250px' style='text-align:left;'><span id='th' class="tblhead">Add Task</span></td>
<td style='text-align:center;width:50px;'><div id='tstatus'></div></td>
<td style='text-align:center;width:120px;' ><label id='datelabel'></label><input type='text' id='datepick' title="click to set due date for task" alt="click to set due date for task" name='taskdate' value="" readonly /></td>
<td style='text-align:center;width:40px;'><div id='tstar'><img id="taskstar"  title='star it' alt='star it' src='/images/star_white.png'></div></td>
<td style='text-align:center;width:50px;' ><div title='Form Reset' onclick="cleartask()" id="tc">Reset</div></td>
</tr>
</table>
<div id="container">
	<div id='antibox' style='float:left;width:60%;height:500px;' class="box-wrap antiscroll-wrap">
    <div style='float:left;width:100%;height:100%;' class="box">
    <div class="antiscroll-inner">
	<div id="body">
	&nbsp;
	</div>
	</div>
    </div>
    </div>
	<!-- // Add Task // -->
	<div id='taskbar'>
	<div id='taskinfo'><span class='lastup' id='created'></span><span class='lastup' id='lastupdate'></span></div>
	<form id="taskform">
	<table width='95%' style='float:right;margin:0px 4px;' cellpadding='1' cellspacing='4' >
	<tr><td colspan='2'><input type='hidden' id='taskid' name='taskid' value='0' /><input type='hidden' id='priority' name='priority' value='0' /><input type='hidden' id='projectid' name='projectd' value='<?php echo $project;?>' /><input title="Press 'Enter' to add" type='text' id='task' name='task' maxlength='100' style='width:100%;' /></td></tr>
	<tr><td colspan='2'><textarea title="Notes" type='text' id='description' name='description' style="width:100%;overflow:hidden" rows='3' cols='50' /></textarea></td></tr>
	<tr><td colspan='2'><input style='float:left;' class="sbun" id="tsave" title="Save task" type='button' name='save' value='Save'/><span style='padding-left:10px;vertical-align:middle;' id='error'></span></td></tr></table>
	</form>
	</div>
</div>
<?php flush(); ?>
<script type="text/javascript" src="/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/js/task.js"></script>	
<script src="/js/jquery-mousewheel.js"></script>
<script src="/js/antiscroll.js"></script>
</body>
</html>