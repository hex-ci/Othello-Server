<?php

// 游戏控制器
class Game extends MY_Controller {

	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$this->load->view('welcome_message');
	}

	// 开始游戏
	function start()
	{
		$this->load->library('form_validation');
		$this->load->model('user_model');
		$this->load->model('game_model');

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
				$this->form_validation->set_error_string('非法开始游戏！请检查您的用户身份！');
				throw new Exception();
			}

			$result = $this->game_model->start($get['username']);

			if ($result)
			{
				$this->OutputStatus = STATUS_OK;
				$this->OutputArray = array(
					$result['GameTimes'] - 1,
				);
			}
			else
			{
				$this->OutputStatus = STATUS_ERROR;
				$this->OutputArray = '开始游戏出错！';
			}
		}
		catch (Exception $e)
		{
			$this->setErrorOutput(validation_errors());
		}

		$this->loadView('output');
	}

	// 取消游戏
	function cancel()
	{
		$this->load->library('form_validation');
		$this->load->model('user_model');
		$this->load->model('game_model');

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
				$this->form_validation->set_error_string('非法中断游戏！请检查您的用户身份！');
				throw new Exception();
			}

			$result = $this->game_model->cancel($get['username']);

			if ($result)
			{
				$this->OutputStatus = STATUS_OK;
				$this->OutputArray = array(
					$result['GameTimes'] + 1,
				);
			}
			else
			{
				$this->OutputStatus = STATUS_ERROR;
				$this->OutputArray = '中断游戏出错！';
			}
		}
		catch (Exception $e)
		{
			$this->setErrorOutput(validation_errors());
		}

		$this->loadView('output');
	}

	// 结束棋局
	function over()
	{
		$this->load->library('form_validation');
		$this->load->model('user_model');
		$this->load->model('game_model');

		try
		{
			$this->form_validation->set_rules('style', 'style', 'trim|required');
			$this->form_validation->set_rules('ver', 'ver', 'trim|required|integer');
			$this->form_validation->set_rules('username', '用户名', 'trim|required|min_length[3]|max_length[15]');
			$this->form_validation->set_rules('password', '密码', 'trim|required|min_length[32]|max_length[32]');
			$this->form_validation->set_rules('tablename', '棋局名', 'trim|required|min_length[1]|max_length[15]');
			$this->form_validation->set_rules('partner', '访问者', 'trim|required|min_length[3]|max_length[15]');
			$this->form_validation->set_rules('state', '状态', 'trim|required|integer');

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
				$this->form_validation->set_error_string('非法结束游戏！请检查您的用户身份！');
				throw new Exception();
			}

			if (!$this->user_model->checkUserExist($get['partner']))
			{
				$this->form_validation->set_error_string('无此用户！');
				throw new Exception();
			}

			$result = $this->game_model->over($get['username'], $get['partner'], $get['tablename'], $get['state']);

			if ($result)
			{
				$this->OutputStatus = STATUS_OK;
				if ($result['Level'] == '0')
				{
					$this->OutputArray = array(
						'---',
						'---',
					);
				}
				else
				{
					$this->OutputArray = array(
						$result['Score'],
						$result['Bouns'],
					);
				}
			}
			else
			{
				$this->OutputStatus = STATUS_ERROR;
				$this->OutputArray = '结束游戏出错！';
			}
		}
		catch (Exception $e)
		{
			$this->setErrorOutput(validation_errors());
		}

		$this->loadView('output');
	}
}

/* End of file game.php */
/* Location: ./system/application/controllers/game.php */