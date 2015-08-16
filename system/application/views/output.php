<?php
echo $Status . "\r";

if (is_array($Messages))
{
	$data = array();
	foreach ($Messages as $item)
	{
		if (is_array($item))
		{
			// 多行数据
			$data[] = implode('|', $item);
		}
		else
		{
			// 单行数据直接输出
			echo implode('|', $Messages);
			break;
		}
	}

	if (!empty($data))
	{
		echo implode("\r", $data);
	}
}
else
{
	echo $Messages;
}