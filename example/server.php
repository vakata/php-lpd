<?php
set_time_limit(0);
error_reporting(E_ALL);
ob_implicit_flush();

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '\..\class.lpd.php');
$printer = new LPD(function ($data) { 
	echo $data;
	file_put_contents(dirname(__FILE__).'/dump.txt', $data);
});
