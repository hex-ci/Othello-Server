<?php

// 用户控制器
class User extends MY_Controller {

	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$this->load->view('welcome_message');
	}

	// 用户登陆
	function login()
	{
		$this->load->library('form_validation');
		$this->load->model('security_model');
		$this->load->model('user_model');

		try
		{
			$this->form_validation->set_rules('id', 'id', 'trim|required|integer');
			$this->form_validation->set_rules('style', 'style', 'trim|required');
			$this->form_validation->set_rules('ver', 'ver', 'trim|required|integer');

			$this->form_validation->set_rules('username', '用户名', 'trim|required');
			$this->form_validation->set_rules('password', '密码', 'trim|required|min_length[32]|max_length[32]');
			$this->form_validation->set_rules('lanip', 'IP', 'trim|required');
			$this->form_validation->set_rules('port', '端口', 'trim|required|integer');

			if ($this->form_validation->run() == FALSE)
			{
				throw new Exception();
			}

			$get = $this->form_validation->get_all_gets();

			if (!$this->security_model->checkCommonPassword($get['id'], $get['style']))
			{
				$this->form_validation->set_error_string('非法访问！');
				throw new Exception();
			}

			if (!$this->checkVersion($get['ver']))
			{
				$this->form_validation->set_error_string('对不起！您的软件版本过低，请登陆幸福家园BBS，下载最新版本！网址：http://bbs.ourhf.com');
				throw new Exception();
			}

			$users = $this->user_model->login($get['username'], $get['password'], $get['lanip'], $get['port']);

			if (!$users)
			{
				$this->form_validation->set_error_string('请注意您的用户名与密码拼写是否正确！或者您的帐户已被删除！');
				throw new Exception();
			}

			$this->OutputStatus = STATUS_OK;
			$this->OutputArray = $users;
		}
		catch (Exception $e)
		{
			$this->setErrorOutput(validation_errors());
		}

		$this->loadView('output');
	}

	// 用户注册
	function register()
	{
		$this->load->library('form_validation');
		$this->load->model('security_model');
		$this->load->model('user_model');

		try
		{
			$this->form_validation->set_rules('id', 'id', 'trim|required|integer');
			$this->form_validation->set_rules('style', 'style', 'trim|required');
			$this->form_validation->set_rules('ver', 'ver', 'trim|required|integer');

			$this->form_validation->set_rules('username', '用户名', 'trim|required|min_length[3]|max_length[15]');
			$this->form_validation->set_rules('password', '密码', 'trim|required|min_length[32]|max_length[32]');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|max_length[30]|valid_email');
			$this->form_validation->set_rules('face', '肖像', 'trim|integer');
			$this->form_validation->set_rules('name', '昵称', 'trim|max_length[15]');
			$this->form_validation->set_rules('sex', '性别', 'trim|integer');
			$this->form_validation->set_rules('age', '年龄', 'trim|integer');
			$this->form_validation->set_rules('country', '国家/地区', 'trim|max_length[20]');
			$this->form_validation->set_rules('state', '省份', 'trim|max_length[20]');
			$this->form_validation->set_rules('city', '城市', 'trim|max_length[20]');

			if ($this->form_validation->run() == FALSE)
			{
				throw new Exception();
			}

			$get = $this->form_validation->get_all_gets();

			if (!$this->security_model->checkCommonPassword($get['id'], $get['style']))
			{
				$this->form_validation->set_error_string('非法访问！');
				throw new Exception();
			}

			if (!$this->checkVersion($get['ver']))
			{
				$this->form_validation->set_error_string('对不起！您的软件版本过低，请登陆幸福家园BBS，下载最新版本！网址：http://bbs.ourhf.com');
				throw new Exception();
			}

			if (!$this->user_model->register($get))
			{
				$this->form_validation->set_error_string('对不起，用户名或 E-mail 已被注册！请重新注册！');
				throw new Exception();
			}

			$this->OutputStatus = STATUS_OK;
			$this->OutputArray = '注册成功！';
		}
		catch (Exception $e)
		{
			$this->setErrorOutput(validation_errors());
		}

		$this->loadView('output');
	}

	// 用户退出
	function logout()
	{
		$this->load->library('form_validation');
		$this->load->model('user_model');

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

			if (!$this->user_model->logout($get['username'], $get['password']))
			{
				$this->form_validation->set_error_string('对不起！您无权进行此操作！');
				throw new Exception();
			}

			$this->OutputStatus = STATUS_OK;
			$this->OutputArray = '退出成功！';
		}
		catch (Exception $e)
		{
			$this->setErrorOutput(validation_errors());
		}

		$this->loadView('output');
	}

	// 查看用户资料
	function view()
	{
		$this->load->library('form_validation');
		$this->load->model('user_model');

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
				$this->form_validation->set_error_string('对不起！您的软件版本过低，请登陆幸福家园BBS，下载最新版本！网址：http://bbs.ourhf.com');
				throw new Exception();
			}

			$result = $this->user_model->get($get['username']);
			if ($result)
			{
				$this->OutputStatus = STATUS_OK;
				$this->OutputArray = array(
					$result['Email'],
					$result['UserClass'],
					$result['Face'],
					$result['Name'],
					$result['Sex'],
					$result['Age'],
					$result['Country'],
					$result['State'],
					$result['City'],
					$result['Win'],
					$result['Lose'],
					$result['Draw'],
					$result['GameTimes'],
					$result['Score']
				);
			}
			else
			{
				$this->OutputStatus = STATUS_NONE;
				$this->OutputArray = '无此用户！';
			}
		}
		catch (Exception $e)
		{
			$this->setErrorOutput(validation_errors());
		}

		$this->loadView('output');
	}

	// 编辑用户资料
	function edit()
	{
		$this->load->library('form_validation');
		$this->load->model('user_model');

		try
		{
			$this->form_validation->set_rules('style', 'style', 'trim|required');
			$this->form_validation->set_rules('ver', 'ver', 'trim|required|integer');

			$this->form_validation->set_rules('username', '用户名', 'trim|required|min_length[3]|max_length[15]');
			$this->form_validation->set_rules('password', '新密码', 'trim|min_length[32]|max_length[32]');
			$this->form_validation->set_rules('oldpassword', '旧密码', 'trim|required|min_length[32]|max_length[32]');
			$this->form_validation->set_rules('email', 'Email', 'trim|max_length[30]|valid_email');
			$this->form_validation->set_rules('face', '肖像', 'trim|integer');
			$this->form_validation->set_rules('name', '昵称', 'trim|max_length[15]');
			$this->form_validation->set_rules('sex', '性别', 'trim|integer');
			$this->form_validation->set_rules('age', '年龄', 'trim|integer');
			$this->form_validation->set_rules('country', '国家/地区', 'trim|max_length[20]');
			$this->form_validation->set_rules('state', '省份', 'trim|max_length[20]');
			$this->form_validation->set_rules('city', '城市', 'trim|max_length[20]');

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

			if (!$this->user_model->checkUser($get['username'], $get['oldpassword']))
			{
				$this->form_validation->set_error_string('您无权编辑用户资料！');
				throw new Exception();
			}

			if (!empty($get['email']) && $this->user_model->checkEmail($get['username'], $get['email']))
			{
				$this->form_validation->set_error_string('对不起，此 E-mail 已被注册！请重新选择 E-mail！');
				throw new Exception();
			}

			if (!$this->user_model->edit($get))
			{
				$this->form_validation->set_error_string('无此用户！');
				throw new Exception();
			}

			$this->OutputStatus = STATUS_OK;
			$this->OutputArray = '修改成功！';
		}
		catch (Exception $e)
		{
			$this->setErrorOutput(validation_errors());
		}

		$this->loadView('output');
	}
}

/* End of file user.php */
/* Location: ./system/application/controllers/user.php */