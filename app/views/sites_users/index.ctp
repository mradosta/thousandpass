<?php

foreach ($sitesUsers as $sitesUser) {
	echo $this->element('plugin', array('data' => $sitesUser));
}

?>