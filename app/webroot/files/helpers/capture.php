<?php

$url = $argv[1];
$saveFileAs = $argv[2];

include('webthumb.php');

$Webthumb = new Webthumb();
if ($Webthumb->getAndSave($saveFileAs, $url)) {
	return true;
} else {
	return false;
}


?>