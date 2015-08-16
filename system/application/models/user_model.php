<?php

// 用户 模型

class User_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

	// 取得并设置安全字符串
	function makeSecurity($name)
	{
		$this->db->select('UserName')->from('user')->where('UserName', $name);

		$query = $this->db->get();

 		if ($query->num_rows() > 0)
 		{
			$Security1 = '';
			for ($i = 0; $i < SECURITY_LENGTH; $i++)
			{
				$Security1 .= chr(mt_rand(48, 122));
			}

			$Security2 = '';
			for ($i = 0; $i < SECURITY_LENGTH; $i++)
			{
				$Security2 .= chr(mt_rand(48, 122));
			}
			$data = array('Security1' => $Security1, 'Security2' => $Security2);

			$this->db->where('UserName', $name);
			$this->db->update('user', $data);

 			return true;
 		}
 		else
 		{
 			return false;
 		}
	}

	// 检查某用户的安全字符串
	function checkServerPassword($username, $text)
	{
		$this->db->select('Security1,Security2')->from('user')->where('UserName', $username);

		$query = $this->db->get();

 		if ($query->num_rows() > 0)
 		{
 			$row = $query->row_array();
			return (AzDG_decrypt($text, $row['Security2']) == $row['Security1']);
 		}
 		else
 		{
 			return false;
 		}
	}

	// 检查用户的用户名和密码是否匹配
	function checkUser($username, $password)
	{
		$this->db->select('count(id) as count')->from('user')->where('UserName', $username)->where('Password', $password);

		$row = $this->db->get()->row_array();

 		return ($row['count'] > 0);
	}

	// 检查 Email 是否已注册
	function checkEmail($username, $email)
	{
		$this->db->select('count(id) as count')->from('user')->where('UserName <>', $username)->where('Email', $email);

		$row = $this->db->get()->row_array();

 		return ($row['count'] > 0);
	}

	// 检查用户是否存在
	function checkUserExist($username)
	{
		$this->db->select('count(id) as count')->from('user')->where('UserName', $username);

		$row = $this->db->get()->row_array();

 		return ($row['count'] > 0);
	}

	// 删除负分用户
	function removeUserByScore($score = 0)
	{
		$this->db->delete('user', array('Score <' => $score));
	}

	// 登陆
	function login($username, $password, $lanip, $port)
	{
		// 产生安全码，用于登陆以后的操作
		if ($this->makeSecurity($username))
		{
			$this->db->select('*')->from('user')->where('UserName', $username)->where('Password', $password);

			$query = $this->db->get();

	 		if ($query->num_rows() > 0)
	 		{
	 			$row = $query->row_array();

				$playTimes = $row['Win'] + $row['Lose'] + $row['Draw'];
				$disconnect = $row['GameTimes'] - $playTimes;

				if ($disconnect > $row['DisconnectTimes'] and ($disconnect % 2) == 0)
				{
					$reduce = true;
					$row['DisconnectTimes'] = $row['GameTimes'] - $playTimes;
					$row['Score']--;

					$data = array(
						'Score' => $row['Score'],
						'DisconnectTimes' => $row['DisconnectTimes']
					);
					$this->db->where('UserName', $username);
					$this->db->update('user', $data);
				}
				else
				{
					$reduce = false;
				}

				$this->db->select('UserName')->from('online')->where('UserName', $username);
				$query_online = $this->db->get();
				$data = array(
					'UserName' => $username,
					'Name' => $row['Name'],
					'IP' => getUserIp(),
					'LANIP' => $lanip,
					'Port' => $port,
					'LastTime' => date('Y-m-d H:i:s'),
				);
		 		if ($query_online->num_rows() > 0)
		 		{
					$this->db->where('UserName', $username);
					$this->db->update('online', $data);
		 		}
		 		else
		 		{
					$this->db->insert('online', $data);
		 		}

				$data = array('LastLogin' => date('Y-m-d H:i:s'));
				$this->db->where('UserName', $username);
				$this->db->update('user', $data);

				// 100 天未登陆者，用户数据将被删除！
				//$this->db->delete('user', array('DATE_ADD(LastLogin,INTERVAL 100 DAY)<' => date('Y-m-d H:i:s')));

				$ret = array(
					$row['Email'],
					$row['UserClass'],
					$row['Face'],
					$row['Name'],
					$row['Sex'],
					$row['Age'],
					$row['Country'],
					$row['State'],
					$row['City'],
					$row['Win'],
					$row['Lose'],
					$row['Draw'],
					$row['GameTimes'],
					$row['Score'],
					getUserIp(),
					AzDG_crypt($row['Security1']),
					AzDG_crypt($row['Security2'])
				);

				if ($reduce)
				{
					$ret[] = '请注意：由于您又断线了 2 次，所以扣除 1 分！ 当前分数: ' . $row['Score'];
				}
	 		}
	 		else
	 		{
				$ret = false;
	 		}
		}
		else
		{
			$ret = false;
		}

		$CI = get_instance();
		$CI->load->model('online_model');

		$CI->online_model->removeInactive();

		return $ret;
	}

	// 注册
	function register($param)
	{
		$this->db->select('*')->from('user')->where('UserName', $param['username'])->or_where('Email', $param['email']);

		$query = $this->db->get();

 		if ($query->num_rows() > 0)
 		{
 			return false;
 		}
 		else
 		{
			$data = array(
				'UserName' => $param['username'],
				'Password' => $param['password'],
				'Email' => $param['email'],
				'RegisterDate' => date('Y-m-d H:i:s'),
				'LastLogin' => date('Y-m-d H:i:s'),
				'UserClass' => 0,
				'Face' => empty($param['face']) ? '1' : $param['face'],
				'Name' => $param['name'],
				'Sex' => $param['sex'],
				'Age' => $param['age'],
				'Country' => $param['country'],
				'State' => $param['state'],
				'City' => $param['city'],
				'Win' => 0,
				'Lose' => 0,
				'Draw' => 0,
				'Score' => REGISTER_SCORE,
				'GameTimes' => 0,
				'DisconnectTimes' => 0,
				'Security1' => '',
				'Security2' => ''
			);
			$this->db->insert('user', $data);

			return true;
 		}
	}

	// 退出
	function logout($username, $password)
	{
		// 删除 30 分钟不活动的人
		$CI = get_instance();
		$CI->load->model('online_model');
		$CI->online_model->removeInactive();

		$this->db->select('*')->from('user')->where('UserName', $username)->where('Password', $password);

		$query = $this->db->get();

 		if ($query->num_rows() > 0)
 		{
			$data = array('Security1' => '', 'Security2' => '');

			$this->db->where('UserName', $username);
			$this->db->update('user', $data);

			$this->db->delete('online', array('UserName' => $username));
			$this->db->delete('table', array('Creator' => $username));

 			return true;
 		}
 		else
 		{
			return false;
 		}
	}

	// 取用户资料
	function get($username)
	{
		// 删除 30 分钟不活动的人
		$CI = get_instance();
		$CI->load->model('online_model');
		$CI->online_model->removeInactive();

		$this->db->select('*')->from('user')->where('UserName', $username);

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

	// 编辑用户资料
	function edit($param)
	{
		// 删除 30 分钟不活动的人
		$CI = get_instance();
		$CI->load->model('online_model');
		$CI->online_model->removeInactive();

		$this->db->select('*')->from('user')->where('UserName', $param['username']);

		$query = $this->db->get();

 		if ($query->num_rows() == 0)
 		{
			return false;
 		}

		$data = array(
			'Name' => $param['name'],
			'Sex' => $param['sex'],
			'Country' => $param['country'],
			'State' => $param['state'],
			'City' => $param['city'],
		);

		if (!empty($param['password']))
		{
			$data['Password'] = $param['password'];
		}

		if (!empty($param['email']))
		{
			$data['Email'] = $param['email'];
		}

		if (!empty($param['face']))
		{
			$data['Face'] = $param['face'];
		}

		if (!empty($param['age']))
		{
			$data['Age'] = $param['age'];
		}

		$this->db->where('UserName', $param['username']);
		$this->db->update('user', $data);

		$data = array(
			'VisitorName' => $param['name'],
			'LastTime' => date('Y-m-d H:i:s'),
		);
		$this->db->where('Visitor', $param['username']);
		$this->db->update('table', $data);

		$data = array(
			'CreatorName' => $param['name'],
			'LastTime' => date('Y-m-d H:i:s'),
		);
		$this->db->where('Creator', $param['username']);
		$this->db->update('table', $data);

		return true;
	}
}