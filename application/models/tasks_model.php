<?php

class Tasks_model extends CI_Model {
	
	function __construct()
    {
        parent::__construct();
    }
	
	function addTask($data)
	{
		$response['status'] = "success";
		$response['error'] = "";
		$response['taskid'] = 0;
		$response['taskstatus'] = 0;
		$response['action'] = "";
		
		$userid = $this->session->userdata('id');
	
		// check for existing task on pending tasks
		$query = "SELECT t.task_id FROM tbl_task t LEFT JOIN tbl_justdoit j ON t.task_id=j.task_id WHERE j.project_id=".$data['pid']." AND j.user_id=".$userid." AND t.task='".mysql_real_escape_string($data['t'])."' ";
		$result = $this->db->query($query);
		if($result->num_rows() > 0)
		{
			$response['status'] = "failed";
			$response['error'] = "Task already exits";
			justlog($userid, "Add Task failed", 0, "'".$data['t']."' Task already exits");
		}
		else
		{
			// adding to tbl_task
			$tz = $this->session->userdata("tz");
			$doitby = '';
			if($data['tde'] != '')
			{
				$doitby = setuserdate($data['tde'], $tz); 
			}
			$created_date = date("Y-m-d H:i:s");
			$idata = array( 'task' => htmlspecialchars($data['t'], ENT_QUOTES), 'description' => htmlspecialchars($data['td'], ENT_QUOTES), 'priority' => $data['tp'], 'created_date' => $created_date, 'doby_date' => $doitby, 'timestamp' => $created_date);
			$this->db->insert('tbl_task', $idata);
			$error = $this->db->_error_message();  
			if($error == ""){
				// adding to tbl_justdoit
				$taskid = $this->db->insert_id();	
				$response['taskid'] = $taskid;
				$response['action'] = "insert";
				$response['done_date'] = "";
				$idata = array( 'project_id' => $data['pid'], 'task_id' => $taskid, 'user_id' => $userid);
				$this->db->insert('tbl_justdoit', $idata);
				$error = $this->db->_error_message();
				if($error != ""){
					log_message('error', $error);
					$response['status'] = "failed";
					$response['error'] = "internal error";
					justlog($userid, "Add task failed in db error", 0, "'".$data['t']."' adding to tbl_justdoit");
				}
			}else{
				log_message('error', $error);
				$response['status'] = "failed";
				$response['error'] = "internal error";
				justlog($userid, "Add task failed in db error", 0, "'".$data['t']."' adding to tbl_task");
			}
		}
		return $response;
	}
	
	
	function updateTask($data)
	{
		$response['status'] = "success";
		$response['error'] = "";
		$response['taskid'] = 0;
		$response['taskstatus'] = 0;
		$response['action'] = "";
		$userid = $this->session->userdata('id');
		
		$query = "SELECT t.* FROM tbl_task t LEFT JOIN tbl_justdoit j ON t.task_id=j.task_id WHERE j.project_id=".$data['pid']." AND j.user_id=".$userid." AND j.task_id=".$data['id']." ";
		$result = $this->db->query($query);
		if($result->num_rows() == 0)
		{
			$response['status'] = "failed";
			$response['error'] = "Task not found";
		}
		else
		{
			// update task
			foreach($result->result() as $row)
			{
				$response['taskstatus'] = $row->status;
				if(strtotime($row->done_date)){
					$response['done_date'] = date("d.m.Y G:i:s", strtotime($row->done_date));
				}else{
					$response['done_date'] = "";
				}				
			}
			
			$doitby = '';
			if($data['tde'] != '' && strtotime($data['tde']))
			{
				$doitby = date("Y-m-d H:i:s", strtotime($data['tde']));   
			}
			else
			{
				$response['status'] = "failed";
				$response['error'] = "Task date not valid";
			}
			
			if($response['status'] == 'success') 
			{
				$idata = array(	'task' => htmlspecialchars($data['t'], ENT_QUOTES), 
								'description' => htmlspecialchars($data['td'], ENT_QUOTES), 
								'priority' => intval($data['tp']), 
								'doby_date' => $doitby,
								'timestamp' => date("Y-m-d H:i:s")
							);
				$this->db->where('task_id', $data['id']);
				$this->db->update('tbl_task', $idata); 
				$response['taskid'] = $data['id'];
				$response['action'] = "update";
				$error = $this->db->_error_message();  
				if($error != "") 
				{
					log_message('error', $error);
					$response['status'] = "failed";
					$response['error'] = "Task not found";
					justlog($userid, "Update task resulted in db error", 0, "'".$data['t']."' updating to tbl_task");
				}					
			}
		}
		return $response;
	}
	
	
	function getTasks($order, $filterdate, $limit, $projectid="0")
	{
		$response = array();
		$mthNames = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
		$userid = $this->session->userdata('id');
		
		$query_c = "SELECT t.task_id FROM tbl_justdoit as j LEFT JOIN tbl_task t ON j.task_id=t.task_id";
		$query = "SELECT t.task_id, t.task,t.doby_date, t.priority FROM tbl_justdoit as j LEFT JOIN tbl_task t ON j.task_id=t.task_id";
		$query_c .= " WHERE j.user_id=".$userid." AND j.project_id=".$projectid." AND t.status=0 ";
		$query .= " WHERE j.user_id=".$userid." AND j.project_id=".$projectid." AND t.status=0 ";
		
		if($filterdate != "" && $filterdate != "all"){
			$f = explode(" ", $filterdate);
			if(is_array($f)){
				$query_c .= " AND DATE(t.doby_date) <= '".$f[2]."-".(array_search($f[1], $mthNames) + 1)."-".$f[0]."' ";
				$query .= " AND DATE(t.doby_date) <= '".$f[2]."-".(array_search($f[1], $mthNames) + 1)."-".$f[0]."' ";
			}
		}
		// allrows for pagination
		log_message('info', $query_c);
		$result_c = $this->db->query($query_c);
		$response['allrows'] = $result_c->num_rows();
		if($response['allrows'] == 0)
		{
			$response['result'] = false;
			return $response;
		}
				
		if($order == 1){
			$query .= " ORDER BY t.doby_date ASC";
		}else{
			$query .= " ORDER BY FIELD(priority,".$order.") DESC, t.doby_date DESC";
		}
		$query .= " LIMIT ".$limit['tasks_offset'].",".$limit['tasks_limit']."";
		log_message('info', $query);
		$result = $this->db->query($query);
		$error = $this->db->_error_message();  
		if($error != "")
		{
			log_message('info', $error);
			log_message('error', $error);
		}
		$response['result'] = $result;
		return $response;
	}
	
	
	function getTasksComplete($order,$climit)
	{
		$response['result'] = false;
		$response['allrows'] = 0;
		
		$userid = $this->session->userdata('id');
		$query = "SELECT t.task_id,t.task,t.doby_date,t.priority,t.done_date FROM tbl_justdoit as j LEFT JOIN tbl_task t ON j.task_id=t.task_id WHERE j.user_id=".$userid." AND t.status=1 ORDER BY t.done_date DESC";
		$cquery = "SELECT count(*) as count FROM tbl_justdoit as j LEFT JOIN tbl_task t ON j.task_id=t.task_id WHERE j.user_id=".$userid." AND t.status=1 ORDER BY t.done_date DESC";
		
		$cresult = $this->db->query($cquery);
		$error = $this->db->_error_message();  
		if($error != "")
		{
			log_message('info', $query);
			log_message('error', $error);
			return $response;
		}
		$row = $cresult->row();
		$allrow = $row->count;
		$response['allrows'] = $allrow;
		if($allrow == 0)
		{
			$response['result'] = false;
			return $response;
		}
		else
		{
			$query .= " LIMIT ".$climit['tasks_offset'].",".$climit['tasks_limit']."";
			$result = $this->db->query($query);
			$error = $this->db->_error_message();  
			if($error != "")
			{
				log_message('info', $query);
				log_message('error', $error);
				$response['result'] = false;
			}
			else
			{
				$response['result'] = $result;
			}
			return $response;
		}
	}
	
	function getTask($taskid)
	{
		$userid = $this->session->userdata('id');
		$query = "SELECT t.* FROM tbl_justdoit j LEFT JOIN tbl_task t ON j.task_id = t.task_id";
		$query .= " WHERE j.user_id=".$userid." AND j.task_id=".$taskid." ";
		$result = $this->db->query($query);
		if($result->num_rows() == 0)
		{
			return false;
		}
		
		$error = $this->db->_error_message();  
		if($error != "")
		{
			log_message('error', $error);
		}
		return $result;
	}
	
	function TaskDone($taskid)
	{
		$userid = $this->session->userdata('id');
		$query = "SELECT t.* FROM tbl_justdoit j LEFT JOIN tbl_task t ON j.task_id = t.task_id";
		$query .= " WHERE j.user_id=".$userid." AND j.task_id=".$taskid." ";
		$result = $this->db->query($query);
		if($result->num_rows() == 0)
		{
			return false;
		}
		
		$error = $this->db->_error_message();  
		if($error != "")
		{
			log_message('error', $error);
			return false;
		}
		
		$d = date("Y-m-d H:i:s");
		
		$data = array( 'status' => 1, 'done_date' => $d, 'timestamp' => $d);
		$this->db->where('task_id', $taskid);
		$this->db->update('tbl_task', $data); 
		$error = $this->db->_error_message();  
		if($error != "") 
		{
			log_message('error', $error);
		}		
		return true;
	}
	
	function TaskUnDo($taskid)
	{
		$response['status'] = 'failed';
		$response['error'] = '';
		$response['pid'] = 0;
		
		$userid = $this->session->userdata('id');
		$query = "SELECT j.* FROM tbl_justdoit j LEFT JOIN tbl_task t ON j.task_id = t.task_id";
		$query .= " WHERE j.user_id=".$userid." AND j.task_id=".$taskid." ";
		$result = $this->db->query($query);
		if($result->num_rows() == 0)
		{
			$response['error'] = 'Task not found.';
			return $response;
		}
		else
		{
			foreach($result->result() as $row)
			{
				$response['pid'] = $row->project_id;
			}
		}
		$error = $this->db->_error_message();  
		if($error != "")
		{
			log_message('error', $error);
			$response['error'] = 'Task not found.';
			return $response;
		}
		$data = array( 'status' => 0, 'done_date' => '0000-00-00 00:00:00', 'timestamp' => date("Y-m-d H:i:s") );
		$this->db->where('task_id', $taskid);
		$this->db->update('tbl_task', $data); 
		$error = $this->db->_error_message();  
		if($error != "") 
		{
			log_message('error', $error);
			$response['error'] = 'Task not found.';
			return $response;
		}		
		$response['status'] = 'success';
		$response['error'] = '';
		return $response;
	}
	
	function getPriorityTasks($priority)
	{
		$userid = $this->session->userdata('id');
		$query = "SELECT count(*) as count FROM tbl_justdoit as j left join tbl_task t ON j.task_id=t.task_id WHERE t.priority=".$priority." AND t.status=0 AND j.user_id=".$userid;
		$result = $this->db->query($query);
		$error = $this->db->_error_message();  
		if($error != "")
		{
			log_message('error', $error);
		}
		if($result->num_rows() > 0)
		{
			foreach($result->result() as $row)
			{
				return $row->count;
			}
		}
		else
		{
			return 0;
		}
	}
	
	function DeleteTask($id)
	{
		$this->db->delete('tbl_justdoit', array('task_id' => $id)); 
		$this->db->delete('tbl_task', array('task_id' => $id));
	}
	
}