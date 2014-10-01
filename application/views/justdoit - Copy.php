<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to Justdoit-Mate</title>
	<link rel="stylesheet" type="text/css" href="/css/style.css">
	<link rel="stylesheet" type="text/css" href="/css/cal.css">
	<script type="text/javascript" src="/js/task.js"></script>
</head>
<body>
<div id='head'>
<table width='100%' >
<tr><td style="text-align:left;height:40px;"><a href="<?php echo base_url();?>"><b style="margin:5px;padding:15px 15px 10px 15px;font-size:20px;color:darkslategray;font-size:20px;font-weight:bold;" >Justdoit-Mate!</b></a></td>
<td style="text-align:right;"><a id='hlink' style='font-size:13px;font-weight:600;font-family:"Segoe UI", Arial;background-color:#99C68E;border:1px outset grey;padding:5px;margin-right:5px;width:120px;border-radius:3px;color:black;' href='<?php echo base_url('index.php/login/logout');?>'>Logout</a></td></tr></table>
</div>
<div id="container">
	<div id="body" style='float:left;border-right:2px solid white;' >
	<table id='add' width='99%' style='padding:0px;margin:0px;'>
	<tr>
	<td><a href='<?php echo base_url('index.php/justdoit/index/-1');?>'><img title='Order By Time' style='width:16px;height:16px;' src='/images/clock.png' /></a></td>
	<?php if($tasks0 != 0) { ?>
	<td><b style='font-size:14px;padding-right:1px;' ><?php echo $tasks0;?></b><a href='<?php echo base_url('index.php/justdoit/index/0');?>'><img title='Order By Yellow' src='/images/star_yellow.png' /></a></td>
	<?php } ?>
	<?php if($tasks1 != 0) { ?>
	<td><b style='font-size:14px;padding-right:1px;' ><?php echo $tasks1;?></b><a href='<?php echo base_url('index.php/justdoit/index/1');?>'><img title='Order By Blue' src='/images/star_blue.png' /></a></td>
	<?php } ?>
	<?php if($tasks2 != 0) { ?>
	<td><b style='font-size:14px;padding-right:1px;'><?php echo $tasks2;?></b><a href='<?php echo base_url('index.php/justdoit/index/2');?>'><img title='Order By Red' src='/images/star_red.png' /></a></td>
	<?php } ?>
	</tr>
	</table>
	<div style='padding:2px;' ><b style='font-size:15px;font-weight:bold;color:black;'>Today's tasks</b></div>
	<table id='tasks' bgcolor='grey' cellspacing='0' cellpadding='2px' style='margin:2px;border-spacing:0px;border:1px solid grey;border-collapse:collapse;' >
	<?php $rows = 0;foreach($tasks->result() as $row) { $rows++; ?>
	<tr onclick="showtask('<?php echo base_url('index.php/justdoit/show/'.$row->task_id);?>', <?php echo $row->task_id;?> )" onmouseout="this.style.backgroundColor='white'" onmouseover="this.style.backgroundColor='#C3FDB8'" >
	<td style='text-align:left;'>&nbsp;<b><?php echo $row->task;?></b></td>
	<td><img src='/images/star_<?php echo ($row->priority == 0) ? 'yellow': (($row->priority == 1) ? 'blue' : 'red');?>.png' /></td>
	<td><a href='<?php echo base_url('index.php/justdoit/done/'.$row->task_id);?>'><img title='Done' src='/images/done.png' style='padding:1px;margin:0px;' /></a></td>
	<td><a href='<?php echo base_url('index.php/justdoit/delete/'.$row->task_id);?>'><img title='Delete' src='/images/delete.png' style='padding:1px;margin:0px;' /></a></td>
	</tr>
	<?php } 
	if($rows == 0) { ?>
	<tr><td><b>0 Tasks Found.</b></td></tr>
	<?php } ?>
	</table>
	<br/>
	<!-- Completed Tasks -->
	<div style='padding:2px;' ><b style='font-size:15px;font-weight:bold;color:black;'>Completed tasks</b></div>
	<table id='tasks' bgcolor='grey' cellspacing='0' cellpadding='2px' style='margin:2px;border-spacing:0px;border:1px solid grey;border-collapse:collapse;' >
	<?php $rows = 0;foreach($taskscomplete->result() as $row) { $rows++; ?>
	<tr onclick="showtask('<?php echo base_url('index.php/justdoit/show/'.$row->task_id);?>', <?php echo $row->task_id;?> )" onmouseout="this.style.backgroundColor='white'" onmouseover="this.style.backgroundColor='#C3FDB8'" >
	<td style='text-align:left;'>&nbsp;<b><?php echo $row->task;?></b></td>
	<td><img src='/images/star_<?php echo ($row->priority == 0) ? 'yellow': (($row->priority == 1) ? 'blue' : 'red');?>.png' /></td>
	<td><a href='<?php echo base_url('index.php/justdoit/undo/'.$row->task_id);?>'><img title='Done' src='/images/undo.png' style='padding:1px;margin:0px;' /></a></td>
	<td><a href='<?php echo base_url('index.php/justdoit/delete/'.$row->task_id);?>'><img title='Delete' src='/images/delete.png' style='padding:1px;margin:0px;' /></a></td>
	</tr>
	<?php } 
	if($rows == 0) { ?>
	<tr><td><b>0 Tasks Found.</b></td></tr>
	<?php } ?>
	</table>
	</div>
	<div id='taskbar'>
	<div id='at'>Add Task</div>
	<span id='error'></span>
	<form method='post' onSubmit='return savetask()' action='/index.php/justdoit/add'>
	<table width='99%' cellpadding='1' cellspacing='4' ><tr><td><input type='hidden' id='taskid' name='taskid' value='0' /><input type='text' id='task' name='task' maxlength='30' style='width:400px;' /></td></tr>
	<tr><td><div><img src='/images/star_yellow.png'><input type='radio' checked='checked' id='priority0' name='priority' value='0'/>&nbsp;<img src='/images/star_blue.png'>:<input id='priority1' type='radio' name='priority' value='1'/>&nbsp;<img src='/images/star_red.png'>:<input type='radio' id='priority2' name='priority' value='2'/></div></td></tr>
	<tr><td><textarea type='text' id='description' name='description' rows='15' cols='50' /></textarea></td></tr>
	<tr><td><b>Do it Before: </b><input type='text' id='datepick' name='taskdate' maxlength='10' style='border:1px solid #99C68E;width:80px;font-weight:bold;background-color:white;border-radius:3px;padding:2px;' /></td></tr>
	<tr><td colspan='2' ><input style='margin:3px auto;font-size:13px;font-weight:600;font-family:"Segoe UI", Arial;background-color:#99C68E;border:1px outset grey;padding:2px;width:120px;border-radius:3px;' type='submit' name='save' value='Save'/></td></tr></table></form>
	</div>
</div>
<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds</p>
<script type="text/javascript" src="/js/datepickr.js"></script>
<script type="text/javascript">
	new datepickr('datepick', {'dateFormat':'d.m.Y'});
	
	/*new datepickr('datepick2', {
		'dateFormat': 'm/d/y'
	});
	
	new datepickr('datepick3', {
		'fullCurrentMonth': false,
		'dateFormat': 'l, F j'
	});*/
</script>
</body>
</html>