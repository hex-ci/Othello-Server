<?php

// °²È«¿ØÖÆÆ÷
class Security extends MY_Controller {

	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$this->load->view('welcome_message');
	}

	function get()
	{
		$this->load->model('security_model');

		$security = $this->security_model->makeCommonPassword();

		$this->OutputStatus = STATUS_OK;
		$this->OutputArray = $security;

		$this->loadView('output');
	}
}

/* End of file security.php */
/* Location: ./system/application/controllers/security.php */