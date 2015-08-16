<?php

// 用户在线控制器
class Online extends MY_Controller {

	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$this->load->view('welcome_message');
	}

	// 获取在线信息
	function get()
	{
		$this->load->library('form_validation');
		$this->load->model('user_model');
		$this->load->model('online_model');

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

			$result = $this->online_model->get($get['username']);

			if ($result)
			{
				$this->OutputStatus = STATUS_OK;
				foreach ($result as $row)
				{
					$data = array(
						$row['UserName'],
						$row['Name'],
						$row['Face'],
						$row['GameTimes'],
						$row['Win'] + $row['Lose'] + $row['Draw'],
						$row['Score'],
						$row['IP'],
						$row['LANIP'],
						$row['Port'],
					);
					$this->OutputArray[] = $data;
				}
			}
			else
			{
				$this->OutputStatus = STATUS_NONE;
				$this->OutputArray = '没有用户在线！';
			}
		}
		catch (Exception $e)
		{
			$this->setErrorOutput(validation_errors());
		}

		$this->loadView('output');
	}
}

/* End of file online.php */
/* Location: ./system/application/controllers/online.php */