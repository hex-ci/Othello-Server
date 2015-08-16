<?php

// 聊天控制器
class Chat extends MY_Controller {

	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$this->load->view('welcome_message');
	}

	// 获取聊天信息
	function get()
	{
		$this->load->library('form_validation');
		$this->load->model('user_model');
		$this->load->model('chat_model');

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
				$this->form_validation->set_error_string('您无权聊天！');
				throw new Exception();
			}

			$result = $this->chat_model->get();

			if ($result)
			{
				$this->OutputStatus = STATUS_OK;
				foreach ($result as $row)
				{
					$data = array(
						$row['Name'],
						$row['ChatText'],
						$row['ChatDate']
					);
					$this->OutputArray[] = $data;
				}
			}
			else
			{
				$this->OutputStatus = STATUS_NONE;
				$this->OutputArray = '没有聊天数据！';
			}
		}
		catch (Exception $e)
		{
			$this->setErrorOutput(validation_errors());
		}

		$this->loadView('output');
	}

	// 发送聊天信息
	function send()
	{
		$this->load->library('form_validation');
		$this->load->model('user_model');
		$this->load->model('chat_model');

		try
		{
			$this->form_validation->set_rules('style', 'style', 'trim|required');
			$this->form_validation->set_rules('ver', 'ver', 'trim|required|integer');
			$this->form_validation->set_rules('username', '用户名', 'trim|required|min_length[3]|max_length[15]');
			$this->form_validation->set_rules('password', '密码', 'trim|required|min_length[32]|max_length[32]');
			$this->form_validation->set_rules('name', '昵称', 'trim|required|max_length[15]');
			$this->form_validation->set_rules('text', '内容', 'trim|required|max_length[100]');

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
				$this->form_validation->set_error_string('您无权聊天！');
				throw new Exception();
			}

			$result = $this->chat_model->send($get);

			if ($result)
			{
				$this->OutputStatus = STATUS_OK;
				foreach ($result as $row)
				{
					$data = array(
						$row['Name'],
						$row['ChatText'],
						$row['ChatDate']
					);
					$this->OutputArray[] = $data;
				}
			}
			else
			{
				$this->OutputStatus = STATUS_NONE;
				$this->OutputArray = '没有聊天数据！';
			}
		}
		catch (Exception $e)
		{
			$this->setErrorOutput(validation_errors());
		}

		$this->loadView('output');
	}
}

/* End of file chat.php */
/* Location: ./system/application/controllers/chat.php */