<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Justdoit extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('tasks_model');
		$this->load->model('project_model');
		$this->load->model('user_model');
		$res = $this->user_model->regenSession();
	}
	
	public function index()
	{
		$order = 1;
		$default_project = "Inbox";
		$limit['tasks_limit'] = 5;
		$limit['tasks_offset'] = 0;
		
		$climit['tasks_limit'] = 5;
		$climit['tasks_offset'] = 0;
		
		if(isset($_POST['filteroptions']))
		{
			$data['filterdate'] = $filterdate = $_POST['filteroptions'];
		}
		else
		{
			$data['filterdate'] = $filterdate = "";
		}
		$data['project'] 		= $this->project_model->getProjectId($default_project);
		$data['tasks'] 			= $this->tasks_model->getTasks($order, $filterdate, $limit, $data['project']);
		$data['taskscomplete']	= $this->tasks_model->getTasksComplete($order, $climit);
		$data['projects'] 		= $this->project_model->getProjects();
		$data['user'] 			= $this->session->all_userdata();
		$data['limit'] 			= $limit;
		$data['climit'] 		= $climit;
		
		$data['logout'] = base_url('/logout');
		if(isset($user['fblogout']) && $user['fblogout'] != ''){
			$data['logout'] = $user['fblogout'];
		}
		
		$this->load->view('justdoit', $data);
	}
	
	public function fetch()
	{
		$r = array();
		if(intval($_POST["limit"]) != 0)
		{
			$limit['tasks_limit'] = intval($_POST["limit"]);
			$limit['tasks_offset'] = intval($_POST["offset"]);
			$projectid = intval($_POST['pid']);
			$response = $this->tasks_model->getTasks(1, $_POST["duedate"], $limit, $projectid);
			if($response['result'] != false)
			{
				$r['status']='success';
				$result = $response['result'];
				$tasks = array();
				foreach($result->result() as $row)
				{
					$task = array();
					$task['task_id'] = $row->task_id;
					$task['task'] = $row->task;
					$task['priority'] = $row->priority;
					$task['doby_date'] = date("d.m.Y G:i:s", strtotime($row->doby_date));
					$tasks[] = $task;
				}
				$r['tasks'] = $tasks;
				$r['allrows'] = $response['allrows'];
				echo json_encode($r);
				exit;
			}else{
				$r['status']='success';
				$r['allrows'] = 0;
				$r['error']='No tasks found';
				echo json_encode($r);
				exit;
			}
		}	
		$r['status']='failed';
		echo json_encode($r);
	}
	
	public function cfetch()
	{
		$r = array();
		if(intval($_POST["limit"]) != 0)
		{
			$limit['tasks_limit'] = intval($_POST["limit"]);
			$limit['tasks_offset'] = intval($_POST["offset"]);
			$response = $this->tasks_model->getTasksComplete(1, $limit);
			if($response['result'] != false)
			{
				$r['status']='success';
				$result = $response['result'];
				$tasks = array();
				foreach($result->result() as $row)
				{
					$task = array();
					$task['task_id'] = $row->task_id;
					$task['task'] = $row->task;
					$task['priority'] = $row->priority;
					$task['doby_date'] = date("d.m.Y G:i:s", strtotime($row->doby_date));
					$task['done_date'] = date("d.m.Y G:i:s", strtotime($row->done_date));
					$tasks[] = $task;
				}
				$r['tasks'] = $tasks;
				$r['allrows'] = $response['allrows'];
				echo json_encode($r);
				exit;
			}
		}	
		$r['status']='failed';
		echo json_encode($r);
		exit;
	}
	
	
	public function get()
	{
		if($_POST["id"] && intval($_POST["id"]) && $_POST['id'] != 0 )
		{
			$data['task'] = $this->tasks_model->getTask(intval($_POST['id']));
			if($data['task'] !== false)
			{
				foreach($data['task']->result() as $row)
				{
					$tz = $this->session->userdata('tz');
					$task['task'] = htmlspecialchars_decode($row->task,ENT_QUOTES);
					$task['description'] = htmlspecialchars_decode($row->description,ENT_QUOTES);
					$task['priority'] = $row->priority;
					$task['status'] = $row->status;
					
					if(!strstr("".$row->doby_date."", '0000-00-00'))
					{
						$task['doby_date'] = getuserdate($row->doby_date, $tz);
					} else {
						$task['doby_date'] = "";
					}
					
					$task['last_updated_date'] = getuserdateshow($row->timestamp, $tz);
					if(!strstr("".$row->done_date."", '0000-00-00'))
					{
						$task['done_date'] = getuserdateshow($row->done_date, $tz);						
					} else {
						$task['done_date'] = "";						
					}
					
					$task['since'] = getuserdateshow($row->created_date, $tz);
					$task['error'] = '';
					echo json_encode($task);
					exit;
				}
			}
		}
		$task['error'] = "Task not found.";
		echo json_encode($task);
	}
	
	/*
		Task save
	*/
	
	public function save()
	{
		$response['status'] = "failed";
		$response['error'] = "Invalid data";
		$data = $_POST;
		if(is_array($data) && $data['t'] != '')
		{
			if($data['id'] == 0)
			{
				$response = $this->tasks_model->addTask($data);
			}
			else
			{
				$response = $this->tasks_model->updateTask($data);				
			}
		}
		else
		{
			log_message('error', 'Invalid ajax request');
			$response['status'] = "failed";
			$response['error'] = "Invalid data";	
		}
		echo json_encode($response);
		exit;
	}
	
	/*
		Add Project
	*/
	public function project()
	{
		$request_response['status'] = "failed";
		$request_response['error'] = "";
		if( isset($_POST['newproject']) && $_POST['newproject'] != "")
		{
			$userid = $this->session->userdata('id');
			$response = $this->project_model->AddProject($userid, $_POST['newproject']);
			if( $response['status'] == "success" )
			{
				$request_response['status'] = "success";
				$request_response['error'] = "";
				$request_response['id'] = $response['id'];
			}
			else
			{
				$request_response['error'] = $response['error'];
			}
		}
		else
		{
			$request_response['error'] = "Invalid project name.";
		}
		echo json_encode($request_response);
	}
	
	/*
		Task status upldate - complete
	*/
	public function done()
	{
		$request_response['status'] = "failed";
		$request_response['id'] = 0;
		if($_POST['id'] && intval($_POST['id']) && $_POST['id'] != 0)
		{
			$response = $this->tasks_model->TaskDone($_POST['id']);
			if( $response )
			{
				$request_response['status'] = "success";
				$request_response['id'] = intval($_POST['id']);
			}
		}
		echo json_encode($request_response);
	}
	
	/*
		Task status upldate - recover
	*/
	public function undo()
	{
		$request_response['status'] = "failed";
		$request_response['id'] = 0;
		if($_POST['id'] && intval($_POST['id']) && $_POST['id'] != 0)
		{
			$response = $this->tasks_model->TaskUnDo($_POST['id']);
			if($response['status'] == 'success')
			{
				$request_response['status'] = "success";
				$request_response['pid'] = $response['pid'];
				$request_response['id'] = intval($_POST['id']);
			}else{
				$request_response['error'] = $response['error'];				
			}
		}
		echo json_encode($request_response);
	}
	
	public function userprofile()
	{
		$response['ustatus'] = $response['pstatus'] = $uresponse['status'] = $presponse['status'] = 'failed';
		$response['uerror'] = $uresponse['error'] = $presponse['error'] = '';
		$response['perror'] = '';
		if( isset($_POST["username"]) && $_POST["username"] != "")
		{
			$uresponse = $this->user_model->updateProfile($_POST["username"]);
		}
		
		if( isset($_POST["cp"]) && $_POST["cp"] != "" && $_POST['npass'] != "" && $_POST['cpass'] != "" && $_POST['npass'] == $_POST['cpass'] )
		{
			$presponse = $this->user_model->updatePassword($_POST["cp"], $_POST['npass']);
		}
		
		$response['ustatus'] = $uresponse['status'];
		$response['pstatus'] = $presponse['status'];
		$response['perror'] = $presponse['error'];
		$response['uerror'] = $uresponse['error'];
		echo json_encode($response);
	}
	
	public function delete()
	{
		if($this->uri->segment(3))
		{
			$this->tasks_model->DeleteTask($this->uri->segment(3));
		}
		redirect('/justdoit/', 'refresh');
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */