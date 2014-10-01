<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to Justdoit-Mate</title>
	<link rel="stylesheet" type="text/css" href="/css/style.css">
	<script type="text/javascript" src="/js/task.js"></script>
</head>
<body>

<div id="container">
	<h1>Welcome to Justdoit-Mate!</h1>
	<div id="body">
	<div id='addtask'>
	<?php
	$mytask = null;
	foreach($task->result() as $row)
	{
		$mytask = $row; 
	}
	?>
	<form method='post' onSubmit='return savetask()' action='/index.php/justdoit/update'>
	<span id='error'></span>
	<table style='border:1px solid white;padding:2px;width:200px;'>
	<tr><td width='30%'><b>Task</b>:</td><td><input type='hidden' id='taskid' name='taskid' value='<?php echo $mytask->task_id;?>' /><input type='text' id='task' name='task' maxlength='30' size='30' value='<?php echo $mytask->task;?>' /></td></tr>
	<tr><td><b>Priority</b>:</td><td><div><img src='/images/star_yellow.png'><input type='radio' <?php echo ($mytask->priority == 0) ? "checked='checked'":"";?> id='priority0' name='priority' value='0'/>&nbsp;<img src='/images/star_blue.png'>:<input id='priority1' type='radio' <?php echo ($mytask->priority == 1) ? "checked='checked'":"";?>  name='priority' value='1'/>&nbsp;<img src='/images/star_red.png'>:<input type='radio' id='priority2' <?php echo ($mytask->priority == 2) ? "checked='checked'":"";?> name='priority' value='2'/></div></td></tr>
	<tr><td><b>Description</b>:</td><td><textarea type='text' id='description' name='description' rows='3' cols='25' /><?php echo $mytask->description;?></textarea></td></tr>
	<tr><td><input style='font-weight:bold;background-color:#5CB3FF;border:1px solid #5CB3FF;padding:2px;width:100px;' type='submit' name='save' value='Update'/></td><td><input style='font-weight:bold;background-color:#5CB3FF;border:1px solid #5CB3FF;padding:2px;width:100px;' type='button' name='cancel' onClick='window.location="<?php echo base_url('index.php/justdoit/');?>"' value='Cancel'/></td></tr></form>
	</table>	
	</div>
	</div>
	
	<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds</p>
</div>

</body>
</html>