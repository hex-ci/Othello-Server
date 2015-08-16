<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| 实用工具辅助函数
| -------------------------------------------------------------------
| 包含程序中用到的最常用的函数。
|
| 2008.7.10 by Hex
|
*/

// AzDG 加密
function AzDG_crypt($t, $key = '')
{
	//mt_srand((double)microtime()*1000000);
	$r = md5(mt_rand(0, 32000));
	$c = 0;
	$v = '';
	$len = strlen($t);

	for ($i=0; $i<$len; $i++)
	{
		if ($c == strlen($r))
		{
			$c = 0;
		}
		$v .= substr($r, $c, 1) . (substr($t, $i, 1) ^ substr($r, $c, 1));
		$c++;
	}

	return base64_encode(AzDG_encode($v, $key));
}

// AzDG 解密
function AzDG_decrypt($t, $key = '')
{
	$t = AzDG_encode(base64_decode($t), $key);
	$v = '';
	$len = strlen($t);

	for ($i=0; $i<$len; $i++)
	{
		$md5 = substr($t, $i, 1);
		$i++;
		$v.= (substr($t, $i, 1) ^ $md5);
	}

	return $v;
}

// AzDG 私有，请不要使用
function AzDG_encode($t, $key = '')
{
	if ($key)
	{
		$r = md5($key);
	}
	else
	{
		$r = md5(AZDG_PRIVATE_KEY);
	}
	$c = 0;
	$v = '';
	$len = strlen($t);

	for ($i=0; $i<$len; $i++)
	{
		if ($c == strlen($r))
		{
			$c = 0;
		}
		$v .= substr($t, $i, 1) ^ substr($r, $c, 1);
		$c++;
	}

	return $v;
}

// 智能取用户 IP 地址
function getUserIp()
{
	$CI = get_instance();

	//$real_ip = $CI->input->server('HTTP_X_CLIENT_ADDRESS', true);
	$real_ip = $CI->input->server('HTTP_X_REAL_IP', true);
	$remote_addr = $CI->input->server('REMOTE_ADDR', true);

	return (empty($real_ip) ? $remote_addr : $real_ip);
}
