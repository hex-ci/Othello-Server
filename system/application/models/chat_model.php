<?php

// 聊天 模型

class Chat_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

	// 删除 CHAT_TIME 分钟以前的聊天信息
	function removeInactive()
	{
		$this->db->delete('chat', array('DATE_ADD(ChatDate,INTERVAL ' . CHAT_TIME .' MINUTE)<' => date('Y-m-d H:i:s')));
	}

	function get()
	{
		$this->removeInactive();

//		$CI = get_instance();
//		$CI->load->model('online_model');
//		$CI->online_model->removeInactive();

		$this->db->select('*')->from('chat')->order_by('chatdate');

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

	function send($param)
	{
		$this->removeInactive();

//		$CI = get_instance();
//		$CI->load->model('online_model');
//		$CI->online_model->removeInactive();

		$data = array(
			'Name' => $param['name'],
			'ChatText' => $param['text'],
			'ChatDate' => date('Y-m-d H:i:s')
		);

		$this->db->insert('chat', $data);

		return $this->get();
	}
}