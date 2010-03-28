<?php
if (!empty($data)) {
	foreach($data as $site) {
  		echo $site['Site']['title'] . '|' . $site['Site']['id'] . "\n";
 	}
} else {
	__('No results');
}
?>