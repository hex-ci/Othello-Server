<?php

// 棋局控制器
class Table extends MY_Controller {

	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$this->load->view('welcome_message');
	}

	// 创建棋局
	function create()
	{
		$this->load->library('form_validation');
		$this->load->model('user_model');
		$this->load->model('table_model');

		try
		{
			$this->form_validation->set_rules('style', 'style', 'trim|required');
			$this->form_validation->set_rules('ver', 'ver', 'trim|required|integer');
			$this->form_validation->set_rules('username', '用户名', 'trim|required|min_length[3]|max_length[15]');
			$this->form_validation->set_rules('password', '密码', 'trim|required|min_length[32]|max_length[32]');
			$this->form_validation->set_rules('creator', '创建者', 'trim|required|min_length[3]|max_length[15]');
			$this->form_validation->set_rules('nickname', '昵称', 'trim|max_length[15]');
			$this->form_validation->set_rules('name', '棋局名', 'trim|required|min_length[1]|max_length[15]');
			$this->form_validation->set_rules('type', '棋局类型', 'trim|required|integer');
			$this->form_validation->set_rules('timer', '棋局计时器', 'trim|required|integer');
			$this->form_validation->set_rules('level', '晋级', 'trim|required|integer');
			$this->form_validation->set_rules('lanip', 'IP', 'trim|max_length[15]');
			$this->form_validation->set_rules('port', '端口', 'trim|required|integer');

			if ($this->form_validation->run() == FALSE)
			{
				throw new Exception();
			}

			$get = $this->form_validation->get_all_gets();

			if (!$this->user_model->checkServerPassword($get['username'], $get['style']))
			{
				$this->form_validation->set_error_string('非法访问！');
				throw new Exception();
			}

			if (!$this->checkVersion($get['ver']))
			{
				$this->form_validation->set_error_string('对不起！您的软件版本过低，请登陆幸福家园BBS，下载最新版本！网址：http://bbs.ourhf.com');
				throw new Exception();
			}

			if (!$this->user_model->checkUser($get['creator'], $get['password']))
			{
				$this->form_validation->set_error_string('您无权创建棋局！');
				throw new Exception();
			}

			$result = $this->table_model->create($get);

			if ($result)
			{
				$this->OutputStatus = STATUS_OK;
				$this->OutputArray = array(
					$result['TableName'],
					$result['Creator'],
					$result['CreatorName'],
					$result['Visitor'],
					$result['VisitorName'],
					$result['Type'],
					$result['GameTimer'],
					$result['Level'],
					$result['LastTime'],
					$result['IP'],
					$result['LANIP'],
					$result['Port']
				);
			}
			else
			{
				$this->OutputStatus = STATUS_ERROR;
				$this->OutputArray = '对不起！此棋局已经存在！请重新建立！';
			}
		}
		catch (Exception $e)
		{
			$this->setErrorOutput(validation_errors());
		}

		$this->loadView('output');
	}

	// 获取棋局列表
	function get()
	{
		$this->load->library('form_validation');
		$this->load->model('user_model');
		$this->load->model('table_model');

		try
		{
			$this->form_validation->set_rules('style', 'style', 'trim|required');
			$this->form_validation->set_rules('ver', 'ver', 'trim|required|integer');
			$this->form_validation->set_rules('username', '用户名', 'trim|required|min_length[3]|max_length[15]');

			if ($this->form_validation->run() == FALSE)
			{
				throw new Exception();
			}

			$get = $this->form_validation->get_all_gets();

			if (!$this->user_model->checkServerPassword($get['username'], $get['style']))
			{
				$this->form_validation->set_error_string('非法访问！');
				throw new Exception();
			}

			if (!$this->checkVersion($get['ver']))
			{
				$this->form_validation->set_error_string('对不起！您的软件版本过低，请访问幸福家园BBS，下载最新版本！网址：http://www.ourhf.com');
				throw new Exception();
			}

			$result = $this->table_model->get($get['username']);

			if ($result)
			{
				$this->OutputStatus = STATUS_OK;
				foreach ($result as $row)
				{
					$data = array(
						$row['TableName'],
						$row['Creator'],
						$row['CreatorName'],
						$row['Visitor'],
						$row['VisitorName'],
						$row['Type'],
						$row['GameTimer'],
						$row['Level'],
						$row['LastTime'],
						$row['IP'],
						$row['LANIP'],
						$row['Port']
					);
					$this->OutputArray[] = $data;
				}
			}
			else
			{
				$this->OutputStatus = STATUS_NONE;
				$this->OutputArray = '没有棋局！';
			}
		}
		catch (Exception $e)
		{
			$this->setErrorOutput(validation_errors());
		}

		$this->loadView('output');
	}

	// 加入棋局
	function join()
	{
		$this->load->library('form_validation');
		$this->load->model('user_model');
		$this->load->model('table_model');

		try
		{
			$this->form_validation->set_rules('style', 'style', 'trim|required');
			$this->form_validation->set_rules('ver', 'ver', 'trim|required|integer');
			$this->form_validation->set_rules('username', '用户名', 'trim|required|min_length[3]|max_length[15]');
			$this->form_validation->set_rules('password', '密码', 'trim|required|min_length[32]|max_length[32]');
			$this->form_validation->set_rules('name', '棋局名', 'trim|required|min_length[1]|max_length[15]');
			$this->form_validation->set_rules('visitor', '访问者', 'trim|required|min_length[3]|max_length[15]');
			$this->form_validation->set_rules('nickname', '昵称', 'trim|max_length[15]');

			if ($this->form_validation->run() == FALSE)
			{
				throw new Exception();
			}

			$get = $this->form_validation->get_all_gets();

			if (!$this->user_model->checkServerPassword($get['username'], $get['style']))
			{
				$this->form_validation->set_error_string('非法访问！');
				throw new Exception();
			}

			if (!$this->checkVersion($get['ver']))
			{
				$this->form_validation->set_error_string('对不起！您的软件版本过低，请登陆幸福家园BBS，下载最新版本！网址：http://bbs.ourhf.com');
				throw new Exception();
			}

			if (!$this->user_model->checkUser($get['visitor'], $get['password']))
			{
				$this->form_validation->set_error_string('您无权加入棋局！');
				throw new Exception();
			}

			$result = $this->table_model->join($get['name'], $get['visitor'], $get['nickname']);

			if ($result === false)
			{
				$this->OutputStatus = STATUS_ERROR;
				$this->OutputArray = '对不起！' . $get['visitor'] . ' 已加入此棋局！请重新选择其它棋局！';
			}
			elseif (empty($result))
			{
				$this->OutputStatus = STATUS_ERROR;
				$this->OutputArray = '对不起！此棋局不存在！请重新选择！';
			}
			else
			{
				$this->OutputStatus = STATUS_OK;
				$this->OutputArray = array(
					$result['TableName'],
					$result['Creator'],
					$result['CreatorName'],
					$result['Visitor'],
					$result['VisitorName'],
					$result['Type'],
					$result['GameTimer'],
					$result['Level'],
					$result['LastTime'],
					$result['IP'],
					$result['LANIP'],
					$result['Port']
				);
			}
		}
		catch (Exception $e)
		{
			$this->setErrorOutput(validation_errors());
		}

		$this->loadView('output');
	}

	// 自动加入棋局
	function autojoin()
	{
		$this->load->library('form_validation');
		$this->load->model('user_model');
		$this->load->model('table_model');

		try
		{
			$this->form_validation->set_rules('style', 'style', 'trim|required');
			$this->form_validation->set_rules('ver', 'ver', 'trim|required|integer');
			$this->form_validation->set_rules('username', '用户名', 'trim|required|min_length[3]|max_length[15]');
			$this->form_validation->set_rules('password', '密码', 'trim|required|min_length[32]|max_length[32]');

			if ($this->form_validation->run() == FALSE)
			{
				throw new Exception();
			}

			$get = $this->form_validation->get_all_gets();

			if (!$this->user_model->checkServerPassword($get['username'], $get['style']))
			{
				$this->form_validation->set_error_string('非法访问！');
				throw new Exception();
			}

			if (!$this->checkVersion($get['ver']))
			{
				$this->form_validation->set_error_string('对不起！您的软件版本过低，请登陆幸福家园BBS，下载最新版本！网址：http://bbs.ourhf.com');
				throw new Exception();
			}

			if (!$this->user_model->checkUser($get['username'], $get['password']))
			{
				$this->form_validation->set_error_string('您无权自动加入棋局！');
				throw new Exception();
			}

			$result = $this->table_model->autojoin($get['username']);

			if ($result)
			{
				$this->OutputStatus = STATUS_OK;
				foreach ($result as $row)
				{
					$data = array(
						$row['TableName'],
						$row['Creator'],
						$row['CreatorName'],
						$row['Visitor'],
						$row['VisitorName'],
						$row['Type'],
						$row['GameTimer'],
						$row['Level'],
						$row['LastTime'],
						$row['IP'],
						$row['LANIP'],
						$row['Port']
					);
					$this->OutputArray[] = $data;

					$data = array(
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
						$row['Score']
					);
					$this->OutputArray[] = $data;
				}
			}
			else
			{
				$this->OutputStatus = STATUS_NONE;
				$this->OutputArray = '没有棋局！';
			}
		}
		catch (Exception $e)
		{
			$this->setErrorOutput(validation_errors());
		}

		$this->loadView('output');
	}

	// 删除棋局
	function remove()
	{
		$this->load->library('form_validation');
		$this->load->model('user_model');
		$this->load->model('table_model');

		try
		{
			$this->form_validation->set_rules('style', 'style', 'trim|required');
			$this->form_validation->set_rules('ver', 'ver', 'trim|required|integer');
			$this->form_validation->set_rules('username', '用户名', 'trim|required|min_length[3]|max_length[15]');
			$this->form_validation->set_rules('password', '密码', 'trim|required|min_length[32]|max_length[32]');
			$this->form_validation->set_rules('name', '棋局名', 'trim|required|min_length[1]|max_length[15]');

			if ($this->form_validation->run() == FALSE)
			{
				throw new Exception();
			}

			$get = $this->form_validation->get_all_gets();

			if (!$this->user_model->checkServerPassword($get['username'], $get['style']))
			{
				$this->form_validation->set_error_string('非法访问！');
				throw new Exception();
			}

			if (!$this->checkVersion($get['ver']))
			{
				$this->form_validation->set_error_string('对不起！您的软件版本过低，请登陆幸福家园BBS，下载最新版本！网址：http://bbs.ourhf.com');
				throw new Exception();
			}

			if (!$this->user_model->checkUser($get['username'], $get['password']))
			{
				$this->form_validation->set_error_string('您无权删除棋局！');
				throw new Exception();
			}

			if ($this->table_model->remove($get['name'], $get['username']))
			{
				$this->OutputStatus = STATUS_OK;
				$this->OutputArray = '删除成功！';
			}
			else
			{
				$this->OutputStatus = STATUS_ERROR;
				$this->OutputArray = '对不起！此棋局已被删除！';
			}
		}
		catch (Exception $e)
		{
			$this->setErrorOutput(validation_errors());
		}

		$this->loadView('output');
	}

	// 退出棋局
	function quit()
	{
		$this->load->library('form_validation');
		$this->load->model('user_model');
		$this->load->model('table_model');

		try
		{
			$this->form_validation->set_rules('style', 'style', 'trim|required');
			$this->form_validation->set_rules('ver', 'ver', 'trim|required|integer');
			$this->form_validation->set_rules('username', '用户名', 'trim|required|min_length[3]|max_length[15]');
			$this->form_validation->set_rules('password', '密码', 'trim|required|min_length[32]|max_length[32]');
			$this->form_validation->set_rules('name', '棋局名', 'trim|required|min_length[1]|max_length[15]');
			$this->form_validation->set_rules('visitor', '访问者', 'trim|min_length[3]|max_length[15]');

			if ($this->form_validation->run() == FALSE)
			{
				throw new Exception();
			}

			$get = $this->form_validation->get_all_gets();

			if (!$this->user_model->checkServerPassword($get['username'], $get['style']))
			{
				$this->form_validation->set_error_string('非法访问！');
				throw new Exception();
			}

			if (!$this->checkVersion($get['ver']))
			{
				$this->form_validation->set_error_string('对不起！您的软件版本过低，请登陆幸福家园BBS，下载最新版本！网址：http://bbs.ourhf.com');
				throw new Exception();
			}

			if (!$this->user_model->checkUser($get['username'], $get['password']))
			{
				$this->form_validation->set_error_string('您无权退出棋局！');
				throw new Exception();
			}

			if ($this->table_model->quit($get['name'], $get['username'], $get['visitor']))
			{
				$this->OutputStatus = STATUS_OK;
				$this->OutputArray = '退出成功！';
			}
			else
			{
				$this->OutputStatus = STATUS_ERROR;
				$this->OutputArray = '非法退出棋局！';
			}
		}
		catch (Exception $e)
		{
			$this->setErrorOutput(validation_errors());
		}

		$this->loadView('output');
	}

	// 获取棋局详情
	function view()
	{
		$this->load->library('form_validation');
		$this->load->model('user_model');
		$this->load->model('table_model');

		try
		{
			$this->form_validation->set_rules('style', 'style', 'trim|required');
			$this->form_validation->set_rules('ver', 'ver', 'trim|required|integer');
			$this->form_validation->set_rules('username', '用户名', 'trim|required|min_length[3]|max_length[15]');
			$this->form_validation->set_rules('password', '密码', 'trim|required|min_length[32]|max_length[32]');
			$this->form_validation->set_rules('name', '棋局名', 'trim|required|min_length[1]|max_length[15]');

			if ($this->form_validation->run() == FALSE)
			{
				throw new Exception();
			}

			$get = $this->form_validation->get_all_gets();

			if (!$this->user_model->checkServerPassword($get['username'], $get['style']))
			{
				$this->form_validation->set_error_string('非法访问！');
				throw new Exception();
			}

			if (!$this->checkVersion($get['ver']))
			{
				$this->form_validation->set_error_string('对不起！您的软件版本过低，请登陆幸福家园BBS，下载最新版本！网址：http://bbs.ourhf.com');
				throw new Exception();
			}

			$result = $this->table_model->detail($get['name']);

			if ($result)
			{
				$this->OutputStatus = STATUS_OK;
				$data = array(
					$result['TableName'],
					$result['Creator'],
					$result['CreatorName'],
					$result['Visitor'],
					$result['VisitorName'],
					$result['Type'],
					$result['GameTimer'],
					$result['Level'],
					$result['LastTime'],
					$result['IP'],
					$result['LANIP'],
					$result['Port'],
					getUserIp(),
				);
				$this->OutputArray = $data;
			}
			else
			{
				$this->OutputStatus = STATUS_NONE;
				$this->OutputArray = '没有棋局！';
			}
		}
		catch (Exception $e)
		{
			$this->setErrorOutput(validation_errors());
		}

		$this->loadView('output');
	}

	// 编辑棋局
	function edit()
	{
		$this->load->library('form_validation');
		$this->load->model('user_model');
		$this->load->model('table_model');

		try
		{
			$this->form_validation->set_rules('style', 'style', 'trim|required');
			$this->form_validation->set_rules('ver', 'ver', 'trim|required|integer');
			$this->form_validation->set_rules('username', '用户名', 'trim|required|min_length[3]|max_length[15]');
			$this->form_validation->set_rules('password', '密码', 'trim|required|min_length[32]|max_length[32]');
			$this->form_validation->set_rules('creator', '创建者', 'trim|required|min_length[3]|max_length[15]');
			$this->form_validation->set_rules('nickname', '昵称', 'trim|max_length[15]');
			$this->form_validation->set_rules('name', '棋局名', 'trim|required|min_length[1]|max_length[15]');
			$this->form_validation->set_rules('type', '棋局类型', 'trim|required|integer');
			$this->form_validation->set_rules('timer', '棋局计时器', 'trim|required|integer');
			$this->form_validation->set_rules('level', '晋级', 'trim|required|integer');
			$this->form_validation->set_rules('lanip', 'IP', 'trim|max_length[15]');
			$this->form_validation->set_rules('port', '端口', 'trim|required|integer');

			if ($this->form_validation->run() == FALSE)
			{
				throw new Exception();
			}

			$get = $this->form_validation->get_all_gets();

			if (!$this->user_model->checkServerPassword($get['username'], $get['style']))
			{
				$this->form_validation->set_error_string('非法访问！');
				throw new Exception();
			}

			if (!$this->checkVersion($get['ver']))
			{
				$this->form_validation->set_error_string('对不起！您的软件版本过低，请登陆幸福家园BBS，下载最新版本！网址：http://bbs.ourhf.com');
				throw new Exception();
			}

			if (!$this->user_model->checkUser($get['creator'], $get['password']))
			{
				$this->form_validation->set_error_string('您无权修改棋局！');
				throw new Exception();
			}

			$result = $this->table_model->edit($get);

			if (!$result)
			{
				$this->form_validation->set_error_string('您无权修改棋局！');
				throw new Exception();
			}

			$result = $this->table_model->detail($get['name']);

			if ($result)
			{
				$this->OutputStatus = STATUS_OK;
				$data = array(
					$result['TableName'],
					$result['Creator'],
					$result['CreatorName'],
					$result['Visitor'],
					$result['VisitorName'],
					$result['Type'],
					$result['GameTimer'],
					$result['Level'],
					$result['LastTime'],
					$result['IP'],
					$result['LANIP'],
					$result['Port'],
					getUserIp(),
				);
				$this->OutputArray = $data;
			}
			else
			{
				$this->OutputStatus = STATUS_NONE;
				$this->OutputArray = '没有棋局！';
			}
		}
		catch (Exception $e)
		{
			$this->setErrorOutput(validation_errors());
		}

		$this->loadView('output');
	}
}

/* End of file table.php */
/* Location: ./system/application/controllers/table.php */