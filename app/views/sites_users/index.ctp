<div class="inner_container_vertical_scroll">

<?php
	foreach ($sitesUsers as $sitesUser) {
		echo $this->element('plugin', array('data' => $sitesUser));
	}
?>

</div>