<?php

// 棋局 模型

class Table_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

	// 删除 KICK_TIME 分钟不活动的棋局
	function removeInactive()
	{
		$this->db->delete('table', array('DATE_ADD(LastTime,INTERVAL ' . KICK_TIME .' MINUTE)<' => date('Y-m-d H:i:s')));
	}

	// 创建棋局
	function create($param)
	{
		$this->removeInactive();

		$CI = get_instance();
		$CI->load->model('online_model');
		$CI->online_model->removeInactive();

		$this->db->select('id')
				 ->from('table')
				 ->where('TableName', $param['name'])
				 ->where('Creator <>', $param['creator']);
 		if ($this->db->count_all_results() > 0)
 		{
 			return false;
 		}
 		else
 		{
			$data = array(
				'TableName' => $param['name'],
				'Creator' => $param['creator'],
				'CreatorName' => $param['nickname'],
				'Visitor' => '',
				'VisitorName' => '',
				'Type' => $param['type'],
				'GameTimer' => $param['timer'],
				'Level' => $param['level'],
				'IP' => getUserIp(),
				'LANIP' => ($param['lanip'] == '' or $param['lanip'] == getUserIp()) ? '' : $param['lanip'],
				'Port' => $param['port'],
				'LastTime' => date('Y-m-d H:i:s')
			);

			$this->db->select('id')
					 ->from('table')
					 ->where('Creator', $param['creator']);
 			if ($this->db->count_all_results() > 0)
 			{
				$this->db->where('Creator', $param['creator']);
				$this->db->update('table', $data);
 			}
 			else
 			{
				$this->db->insert('table', $data);
 			}

			return $data;
 		}
	}

	// 获取棋局列表
	function get($username)
	{
		$this->removeInactive();

		$CI = get_instance();
		$CI->load->model('online_model');
		$CI->online_model->removeInactive();

		$this->db->select('*')->from('table');

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

	// 加入棋局
	function join($name, $visitor, $visitor_name)
	{
		$this->removeInactive();

		$CI = get_instance();
		$CI->load->model('online_model');
		$CI->online_model->removeInactive();

		$this->db->select('*')->from('table')->where('TableName', $name);

		$query = $this->db->get();

 		if ($query->num_rows() > 0)
 		{
 			$row = $query->row_array();

 			if (!empty($row['Visitor']) and $row['Visitor'] != $visitor)
 			{
 				return false;
 			}
 			else
 			{
				$data = array(
					'Visitor' => $visitor,
					'VisitorName' => $visitor_name,
					'LastTime' => date('Y-m-d H:i:s')
				);
				$this->db->where('TableName', $name);
				$this->db->update('table', $data);

				$row['Visitor'] = $data['Visitor'];
				$row['VisitorName'] = $data['VisitorName'];
				$row['LastTime'] = $data['LastTime'];

				return $row;
 			}
 		}
 		else
 		{
 			return array();
 		}
	}

	// 自动加入棋局
	function autojoin($username)
	{
		$this->removeInactive();

		$CI = get_instance();
		$CI->load->model('online_model');
		$CI->online_model->removeInactive();

		$this->db->select('*')->from('table');
		$this->db->join('user', 'table.Creator = user.UserName');

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

	// 删除棋局
	function remove($name, $username)
	{
		$this->removeInactive();

		$CI =& get_instance();
		$CI->load->model('online_model');
		$CI->online_model->removeInactive();

		$this->db->select('*')->from('table')->where('TableName', $name)->where('Creator', $username);

		if ($this->db->count_all_results() > 0)
 		{
			$this->db->delete('table', array('TableName' => $name, 'Creator' => $username));

 			return true;
 		}
 		else
 		{
 			return false;
 		}
	}

	// 退出棋局
	function quit($name, $username, $visitor)
	{
		$this->removeInactive();

		$CI =& get_instance();
		$CI->load->model('online_model');
		$CI->online_model->removeInactive();

		$this->db->select('*')->from('table')->where('TableName', $name);

		$query = $this->db->get();

 		if ($query->num_rows() > 0)
 		{
 			$row = $query->row_array();
		    $Creator = $row['Creator'];

		    if ($username == $Creator)
		    {
				$this->db->delete('table', array('TableName' => $name));
				return true;
		    }
		    elseif (empty($visitor) or $visitor == $row['Visitor'])
		    {
				$data = array(
					'Visitor' => '',
					'VisitorName' => '',
					'LastTime' => date('Y-m-d H:i:s')
				);
				$this->db->where('TableName', $name);
				$this->db->update('table', $data);
				return true;
		    }
		    else
		    {
		    	return false;
		    }
 		}
 		else
 		{
 			return true;
 		}
	}

	// 获取棋局详情
	function detail($name)
	{
		$this->removeInactive();

		$CI =& get_instance();
		$CI->load->model('online_model');
		$CI->online_model->removeInactive();

		$this->db->select('*')->from('table')->where('TableName', $name);

		$query = $this->db->get();

 		if ($query->num_rows() > 0)
 		{
 			return $query->row_array();
 		}
 		else
 		{
 			return false;
 		}
	}

	// 编辑棋局
	function edit($param)
	{
		$this->removeInactive();

		$CI = get_instance();
		$CI->load->model('online_model');
		$CI->online_model->removeInactive();

		$this->db->select('id')
				 ->from('table')
				 ->where('TableName', $param['name'])
				 ->where('Creator <>', $param['creator']);
 		if ($this->db->count_all_results() > 0)
 		{
 			return false;
 		}
 		else
 		{
			$data = array(
				'Creator' => $param['creator'],
				'CreatorName' => $param['nickname'],
				'Type' => $param['type'],
				'GameTimer' => $param['timer'],
				'Level' => $param['level'],
				'IP' => getUserIp(),
				'LANIP' => ($param['lanip'] == '' or $param['lanip'] == getUserIp()) ? '' : $param['lanip'],
				'Port' => $param['port'],
				'LastTime' => date('Y-m-d H:i:s')
			);

			$this->db->select('id')
					 ->from('table')
					 ->where('Creator', $param['creator']);
 			if ($this->db->count_all_results() > 0)
 			{
				$this->db->where('Creator', $param['creator']);
				$this->db->update('table', $data);
 			}
 			else
 			{
				$data['TableName'] = $param['name'];
				$data['Visitor'] = '';
				$data['VisitorName'] = '';
				$this->db->insert('table', $data);
 			}

			return $data;
 		}
	}
}