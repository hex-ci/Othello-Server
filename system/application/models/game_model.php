<?php

// Game 模型

class Game_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

	function start($username)
	{
		$CI = get_instance();
		$CI->load->model('online_model');
		$CI->load->model('table_model');

		$CI->online_model->removeInactive();
		$CI->table_model->removeInactive();

		$this->db->set('GameTimes', 'GameTimes+1', FALSE);
		$this->db->where('UserName', $username);
		$this->db->update('user');

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

	function cancel($username)
	{
		$CI = get_instance();
		$CI->load->model('online_model');
		$CI->load->model('table_model');

		$CI->online_model->removeInactive();
		$CI->table_model->removeInactive();

		$this->db->set('GameTimes', 'GameTimes-1', FALSE);
		$this->db->where('UserName', $username);
		$this->db->update('user');

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

	function over($username, $partner, $tablename, $state)
	{
		$CI = get_instance();
		$CI->load->model('online_model');
		$CI->load->model('table_model');
		$CI->load->model('user_model');

		//$CI->online_model->removeInactive();
		$CI->table_model->removeInactive();

		$user_info = $CI->user_model->get($username);
		$partner_info = $CI->user_model->get($partner);
		$table_info = $CI->table_model->detail($tablename);

		if (empty($table_info))
		{
			return false;
		}

		$PlayTimes = $user_info['Win'] + $user_info['Draw'] + $user_info['Lose'];
		$PartnerPlayTimes = $partner_info['Win'] + $partner_info['Draw'] + $partner_info['Lose'];

		$Bouns = 0;

		if (abs($PlayTimes - $PartnerPlayTimes) < 2)
		{
			if ($state == GAME_WIN && $user_info['Score'] < $partner_info['Score'])
			{
				$Bouns = floor(($partner_info['Score'] - $user_info['Score']) * 0.2);
				$user_info['Score'] += $Bouns;
			}
			if ($state == GAME_LOSE && $user_info['Score'] > $partner_info['Score'])
			{
				$Bouns = floor(($user_info['Score'] - $partner_info['Score']) * 0.2);
				$user_info['Score'] -= $Bouns;
			}
			if ($state == GAME_DRAW && $user_info['Score'] < $partner_info['Score'])
			{
				$Bouns = floor(($partner_info['Score'] - $user_info['Score']) * 0.1);
				$user_info['Score'] += $Bouns;
			}
		}

		switch ($state)
		{
			case GAME_WIN:
				$user_info['Win']++;
				$user_info['Score'] += SCORE_WIN;
				break;

			case GAME_DRAW:
				$user_info['Draw']++;
				$user_info['Score'] += SCORE_DRAW;
				break;

			// 其它值都代表输(GAME_LOSE)
			default:
				$user_info['Lose']++;
				// 注意: 此为加一个负值(-2)
				$user_info['Score'] += SCORE_LOSE;
				break;
		}

		$data = array(
			'Win' => $user_info['Win'],
			'Draw' => $user_info['Draw'],
			'Lose' => $user_info['Lose'],
		);

		if ($table_info['Level'] != '0')
		{
			$data['Score'] = $user_info['Score'];
		}

		$this->db->where('UserName', $username);
		$this->db->update('user', $data);

		// 删除负分用户，暂时先不执行这条规定
		//$this->user_model->removeUserByScore();

		return array(
			'Level' => $table_info['Level'],
			'Score' => $user_info['Score'],
			'Bouns' => $Bouns,
		);
	}
}