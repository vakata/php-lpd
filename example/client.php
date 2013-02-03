<?php
set_time_limit(0);
error_reporting(E_ALL);
ob_implicit_flush();

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class.lpr.php');
$lpr = new PrintSendLPR();
$lpr->setHost("127.0.0.1");
$lpr->setData("Some text to test.");
echo $lpr->printJob("test");
