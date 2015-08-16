<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| 常量
| -------------------------------------------------------------------
|
|
| 2009.10.24 by Hex
|
*/

// 与客户端通信用的密钥，很重要，需要保密，可能需要定期更换
define('AZDG_PRIVATE_KEY', 'key');

// 版本
define('OTHELLO_VERSION', 1);

// 注册以后所给的分数
define('REGISTER_SCORE', 50);

// 单位：分钟
define('KICK_TIME', 30);

// 保留的聊天记录时间，单位：分钟
define('CHAT_TIME', 10080);

// 最大安全字段记录数量
define('MAX_SECURITY', 100);

// 安全检查字段长度(字符)
define('SECURITY_LENGTH', 20);

define('STATUS_OK', '0');
define('STATUS_ERROR', '1');
define('STATUS_NONE', '2');

define('GAME_LOSE', '0');
define('GAME_DRAW', '1');
define('GAME_WIN', '2');

define('SCORE_WIN', +3);
define('SCORE_DRAW', +1);
define('SCORE_LOSE', -2);
