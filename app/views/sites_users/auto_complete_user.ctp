<?php
if (!empty($data)) {
	foreach($data as $user) {
		if (!empty($user['User']['country'])) {
			$user['User']['username'] .= ' (' . $user['User']['country'] . ')';
		}
  		echo $user['User']['name'] . ' ' . $user['User']['lastname'] . ' ' . $user['User']['username'] . '|' . $user['User']['id'] . "\n";
 	}
} else {
	__('No results');
}
?>