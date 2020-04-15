<?php
header("Content-Type: text/plain");
	$time2 = microtime(true);
	$file = 'log.txt';
	$logmsg = file_get_contents($file);
	$exe_time = "0".(int)($time2 - $_SERVER["REQUEST_TIME_FLOAT"])* 1000;
	$logmsg = $_SERVER['REQUEST_METHOD']. "\t\t".$_SERVER['REQUEST_URI']. "\t\t".http_response_code()."\t\t". $exe_time."ms";

	$outputMessage =file_put_contents($file, $logmsg."\n", FILE_APPEND | LOCK_EX);

	$fileopen = fopen($file, 'r');
	$log = fread($fileopen, filesize($file));
	fclose($fileopen);
	echo $log;

?>