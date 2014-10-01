<?php

class Project_model extends CI_Model {
	
	function __construct()
    {
        parent::__construct();
    }
	
	function AddProject($userid, $project)
	{
		$response["status"] = "failed";
		$response["error"] = "";
		$response["id"] = "";
		
		$query = "SELECT * FROM tbl_project WHERE user_id=".$userid." AND project='".mysql_real_escape_string($project)."' ";
		$result = $this->db->query($query);
		if($result->num_rows() > 0)
		{
			$response["error"] = "Project already exits.";
			return $response;
		}
		else
		{
			$dt = date("Y-m-d H:i:s");
			$data = array('project'=> htmlspecialchars($project, ENT_QUOTES), 'user_id' => $userid, 'created_date' => $dt, 'timestamp' => $dt );
			$this->db->insert('tbl_project', $data);
			$error = $this->db->_error_message();
			if($error != "")
			{
				log_message('error', $error);
				$response['status'] = "failed";
				$response['error'] = "internal error";
				justlog($userid, "Add project failed in db error", 0, " userid:$userid '$project' adding to tbl_project");
			}
			else
			{
				$response['id'] = $this->db->insert_id();
				$response['status'] = "success";
			}
		}		
		return $response;
	}
	
	function getProjects()
	{
		$userid = $this->session->userdata("id");
		$query = "SELECT * FROM tbl_project WHERE user_id=".$userid." ";
		$result = $this->db->query($query);
		if($result->num_rows() > 0)
		{
			return $result;
		}
		else
		{
			return false;
		}		
	}
	
	function getProjectId($project)
	{
		if($project == "")
		{
			return false;
		}
		$userid = $this->session->userdata("id");
		$query = "SELECT id FROM tbl_project WHERE user_id=".$userid." AND project='".mysql_real_escape_string($project)."' LIMIT 0,1";
		$result = $this->db->query($query);
		if( $result->num_rows() > 0 )
		{
			foreach( $result->result() as $row )
			{
				return $row->id;
			}
		}
		else
		{
			return false;
		}
	}
	
}