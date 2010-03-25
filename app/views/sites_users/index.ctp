<div class="inner_container_vertical_scroll">

	<div id="1000pass_add_on"></div>

	<?php
		foreach ($sitesUsers as $sitesUser) {
			echo $this->element('plugin', array('data' => $sitesUser));
		}
	?>

</div> <!--inner_container_vertical_scroll-->


<script type="text/javascript">

	jQuery(document).ready(function($) {

		setTimeout(function() {
			if ($('#1000pass_add_on').attr('class') == 'installed') {
				
			}
		}, 1000);
	});

</script>