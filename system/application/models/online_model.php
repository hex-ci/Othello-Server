<?php

// 用户在线 模型

class Online_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

	function update($username)
	{
		$this->db->select('count(id) as count')->from('online')->where('UserName', $username);

		$row = $this->db->get()->row_array();

 		if ($row['count'] > 0)
 		{
			$data = array('LastTime' => date('Y-m-d H:i:s'));
			$this->db->where('UserName', $username);
			$this->db->update('online', $data);
 		}
 		else
 		{
 			return false;
 		}

	}

	// 删除 KICK_TIME 分钟不活动的人
	function removeInactive()
	{
		$this->db->delete('online', array('DATE_ADD(LastTime,INTERVAL ' . KICK_TIME .' MINUTE)<' => date('Y-m-d H:i:s')));
	}

	function get($username)
	{
		$this->update($username);

		$this->removeInactive();

		$CI = get_instance();
		$CI->load->model('table_model');
		$CI->table_model->removeInactive();

		$this->db->select('*')
				 ->from('online')
				 ->join('user', 'online.UserName = user.UserName');

		$query = $this->db->get();

 		if ($query->num_rows() > 0)
 		{
 			return $query->result_array();
 		}
 		else
 		{
 			return false;
 		}
	}
}