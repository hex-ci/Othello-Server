<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 我的 Controller 类
 *
 */
// ------------------------------------------------------------------------
class MY_Controller extends Controller
{
	function __construct()
	{
		parent::Controller();

		date_default_timezone_set('Asia/Shanghai');

		parse_str($_SERVER['QUERY_STRING'], $_GET);

		$this->OutputStatus = STATUS_OK;
		$this->OutputArray = array();
		//$this->load->library('validation');
	}

	protected function setErrorOutput($value)
	{
		if ($value != '')
		{
			$this->OutputStatus = STATUS_ERROR;
			$this->OutputArray[] = $value;
		}
	}

	// 标准化装载视图文件
	protected function loadView($name)
	{
		$this->load->view($name, array(
			'Status' => $this->OutputStatus,
			'Messages' => $this->OutputArray
		));
	}

	function checkVersion($version)
	{
		return !($version < OTHELLO_VERSION);
	}
}