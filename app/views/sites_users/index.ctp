<?php
	$this->pageTitle = '1000Pass.com - ' . __('My Sites', true);
?>

<div class="inner_container_vertical_scroll">

	<div id="1000pass_add_on" class="not_installed"></div>
	<div id="1000pass_add_on_version" class="0"></div>

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
	$javascript->link(array('jquery/jquery.dragsort', 'jquery/jquery.browser'), false);
?>

<script type="text/javascript">

	jQuery(document).ready(function($) {

		$('.remote_site_logo_disabled').parent().attr('target', '_BLANK').css('padding-left', '15px');

		$('ul').dragsort({
			dragSelector: '.title',
			dragBetween: false,
			dragEnd: saveOrder,
			placeHolderTemplate: '<li class="placeHolder"><div></div></li>'
		});
		
		function saveOrder() {
			var newOrder = new Array();
			$('div.inner_container_vertical_scroll li').each(function(i, elm) {
				newOrder.push($('#plugin_identifier', elm).html());
				}
			);

			if (newOrder.length > 0) {
				$.get('<?php echo Router::url(array('controller' => 'sites_users', 'action' => 'reorder')); ?>' + '/' + newOrder.join('|'));
			}
		};


		setTimeout(function() {
			if ($('#1000pass_add_on').attr('class') == 'not_installed') {
				$('.requiere_add_on').css('cursor', 'pointer').click(
					function() {

						alert('<?php __('Access to this site requires the 1000Pass.com add-on to be installed. Redirecting to the add-on download...');?>');

						var basePath = '<?php echo Router::url('/'); ?>';
						var browserName = $.browser.name;
						if (browserName == 'firefox') {
							window.location.replace('https://addons.mozilla.org/en-US/firefox/downloads/file/86631/thousandpass-0.1-fx.xpi?src=addondetail');
						} else if (browserName == 'msie') {
							window.location.replace(basePath + 'files/addons/msie/<?php echo Configure::read('Config.language'); ?>_1000pass.exe');
						} else if (browserName == 'chrome') {
							window.location.replace(basePath + 'files/addons/chrome/1000pass.crx');
						}

						//window.location.replace('<?php echo Router::url(array('controller' => 'sites_users', 'action' => 'download_add_on'), true); ?>/' + $.browser.name);
					}
				);
			}
		}, 2000);
	});

</script>