<?php
$filepath="csvUpload/".$_GET['filename'];
$filename = $_GET['filename'];
                header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
                header("Last-Modified: " . gmdate('D,d M Y H:i:s') . ' GMT');
                header("Cache-Control: no-cache, must-revalidate");
                header("Pragma: no-cache");
                header("Content-Type: text/x-comma-separated-values");
                header("Content-Disposition: attachment; filename=$filename");
readfile($filepath);
exit;
?>