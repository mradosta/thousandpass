<?php
	$this->pageTitle = '1000Pass.com - ' . __('My Sites', true);
?>

<div id="total_sites" style="display:none;"></div>
<div class="inner_container_vertical_scroll">
	<ul>
		<?php
			if ($session->read('add_on.state') == 'installed') {
				$add_on = true;
			} else {
				$add_on = false;
			}

			foreach ($sitesUsers as $sitesUser) {

				if ($sitesUser['SitesUser']['state'] == 'pendding') {
					continue;
				}

				echo '<li>';
				echo $this->element('plugin', array('data' => $sitesUser, 'add_on' => $add_on));
				echo '</li>';
			}


			/*
			array_unshift($missingDefaults, 0);
			$sites[0]['title'] = __('Add web site', true);
			$sites[0]['logo'] = 'other_sites.png';
			foreach ($missingDefaults as $missingDefault) {
				echo '<li>';
				echo sprintf('
					<div class="plugins">
						<div class="toolbar to_enable" title="%s">
							<div class="title" style="cursor:pointer;">%s</div>
						</div>
						<div>
							<img src="%s" class="remote_site_logo" title="%s" style="cursor: pointer; ">
						</div>
						<div class="description button">%s</div>
					</div>',
					$sites[$missingDefault]['title'],
					$sites[$missingDefault]['title'],
					$html->webroot(IMAGES_URL . 'logos/' . $sites[$missingDefault]['logo']),
					$sites[$missingDefault]['title'],
					//$html->link(__('Enable now!', true), array('action' => 'add', $missingDefault)));
					$html->link(__('Habilitar ahora!', true), array('action' => 'add', $missingDefault)));
				echo '</li>';
			}
			*/

		?>
	</ul>

	<div>
		<?php
			echo $html->image(Configure::read('Config.language') . '_add_new.jpg');
		?>
	</div>
</div> <!--inner_container_vertical_scroll-->



<?php
	$javascript->link(array('jquery/jquery.dragsort', 'jquery/jquery.browser'), false);
?>

<script type="text/javascript">

	var downloadAddOn = function(version) {
		var basePath = '<?php echo Router::url('/'); ?>';
		var browserName = $.browser.name;
		if (browserName == 'firefox') {
			//window.location.replace('https://addons.mozilla.org/en-US/firefox/downloads/file/86631/thousandpass-0.1-fx.xpi?src=addondetail&confirmed');
			window.location.replace(basePath + 'files/addons/firefox/1000pass_v' + version + '.xpi');
		} else if (browserName == 'msie') {
			//window.location.replace(basePath + 'files/addons/msie/<?php //echo substr( strtolower(Configure::read('Config.language')), 0, 3); ?>_1000pass.exe');
			window.location.replace(basePath + 'files/addons/msie/1000pass_v' + version + '.exe');
		} else if (browserName == 'chrome') {
			window.location.replace(basePath + 'files/addons/chrome/1000pass_v' + version + '.crx');
		}
	}

	jQuery(document).ready(function($) {

		setInterval(
			function() {


				if ($('#1000pass_add_on_version').html() == '') {
					alert('<?php __('Access to this site requires the 1000Pass.com add-on to be installed. Redirecting to the add-on download...');?>');

					downloadAddOn();
				}

				if ($('#1000pass_add_on_version').html() != '2.0') {

					// prevent warning the user again
					$('#1000pass_add_on_version').html('2.0');

					alert('<?php __('Existe una nueva version de la extension 1000pass.com. Por favor, acepte la descarga.');?>');
					downloadAddOn(2);
				}


				$.get('<?php echo Router::url(array('controller' => 'sites_users', 'action' => 'check')); ?>', function (data) {
					
					var total = $('#total_sites').text();
					if (total == '') {

						$('#total_sites').text(data);

					} else if (total != data) {

						$('#total_sites').text(data);

						var resp = confirm('Se han agregado nuevos sitios. Desea recargar la pagina actual?');
						if (resp == true) {
							window.location.reload();
						}
					}
				});
			},
			5000
		);
			


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


		/*
		$('.add_on_not_installed').css('cursor', 'pointer').click(
			function() {

				alert('<?php __('Access to this site requires the 1000Pass.com add-on to be installed. Redirecting to the add-on download...');?>');

				var basePath = '<?php echo Router::url('/'); ?>';
				var browserName = $.browser.name;
				if (browserName == 'firefox') {
					//window.location.replace('https://addons.mozilla.org/en-US/firefox/downloads/file/86631/thousandpass-0.1-fx.xpi?src=addondetail&confirmed');
					window.location.replace(basePath + 'files/addons/firefox/1000pass.xpi');
				} else if (browserName == 'msie') {
					//window.location.replace(basePath + 'files/addons/msie/<?php //echo substr( strtolower(Configure::read('Config.language')), 0, 3); ?>_1000pass.exe');
					window.location.replace(basePath + 'files/addons/msie/1000pass.exe');
				} else if (browserName == 'chrome') {
					window.location.replace(basePath + 'files/addons/chrome/1000pass.crx');
				}
			}
		);
		*/



	});

</script>