<?php

$file = fopen('../log.txt', 'r');
$log = fread($file, filesize('../log.txt'));
fclose($file);
echo $log;

?>