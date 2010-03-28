<div class="inner_container_vertical_scroll">

	<div id="1000pass_add_on"></div>

	<ul>
	<?php
		foreach ($sitesUsers as $sitesUser) {
			echo '<li>';
			echo $this->element('plugin', array('data' => $sitesUser));
			echo '</li>';
		}
	?>
	</ul>
</div> <!--inner_container_vertical_scroll-->



<?php
	$javascript->link('jquery/jquery.dragsort', false);
?>

<script type="text/javascript">

	jQuery(document).ready(function($) {

		//$("ul:first").dragsort();

		$("ul").dragsort({
			dragSelector: ".drag_selector",
			dragBetween: false,
			dragEnd: saveOrder,
			placeHolderTemplate: "<li class='placeHolder'><div></div></li>"
		});
		
		function saveOrder() {
			var newOrder = new Array();
			$("div.inner_container_vertical_scroll li").each(function(i, elm) {
				newOrder.push($('#plugin_identifier', elm).html());
				}
			);

			if (newOrder.length > 0) {
				$.get('<?php echo Router::url(array('controller' => 'sites_users', 'action' => 'reorder')); ?>' + '/' + newOrder.join('|'));
			}
		};


		setTimeout(function() {
			if ($('#1000pass_add_on').attr('class') == 'installed') {
				
			}
		}, 1000);
	});

</script>