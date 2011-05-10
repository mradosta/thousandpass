<?php
	$this->pageTitle = '1000Pass.com - ' . __('Add new site', true);
?>

<div class="inner_container_border">

	<div class="inner_container">

		<?php
			echo '<div class="left add"></div>';
		?>

		<div class="right">

			<div class="text_and_logo">
				<?php
					echo $html->tag('span', __('Add new site to', true));
					echo $html->image('logo_black.png');
				?>
				<span class="subtitle"><?php __('We order your home page') ?></span>
			</div>

		<div class="right_logos">
		<?php
			echo $html->image('logos/' . Configure::read('Config.language') . '_other_sites.png', array('url' => array('action' => 'add')));
			$logos = array('1' => 'hotmail', '2' => 'gmail', '3' => 'yahoo', '4' => 'facebook', '5' => 'youtube', '6' => 'twitter');
			foreach ($logos as $k => $logo) {
				echo $html->image('logos/' . $logo . '.png', array('url' => array('action' => 'add', $k)));
			}
		?>
		</div> <!--right_logos-->

		<div style="float:left;width:200px;">
			<?php echo $form->create('SitesUser');?>
				<?php
					$value = 'www.';
					$hiddenValue = null;
					$disabled = false;
					if (!empty($site)) {
						$value = $site['Site']['title'];
						$hiddenValue = $site['Site']['id'];
						$disabled = true;
					}


					$colours = array(
						array('name' => __('Green', true), 'value' => '#B2C200', 'style' => 'background:#B2C200;'),
						array('name' => __('Ligth Blue', true), 'value' => '#0065FE', 'style' => 'background:#0065FE;'),
						array('name' => __('Pink', true), 'value' => '#FE0065', 'style' => 'background:#FE0065;'),
						array('name' => __('Red', true), 'value' => '#C20000', 'style' => 'background:#C20000;'),
						array('name' => __('Violet', true), 'value' => '#BB00C2', 'style' => 'background:#BB00C2;'),
						array('name' => __('Yellow', true), 'value' => '#FED700', 'style' => 'background:#FED700;')
					);
					echo $form->input('group', array('label' => __('Group', true), 'options' => $colours));


					echo $form->input('autocomplete', array('value' => $value, 'label' => __('Site', true), 'id' => 'autoComplete', 'disabled' => $disabled));
					echo $form->input('site_id', array('type' => 'hidden', 'value' => $hiddenValue));
					echo $form->input('username', array('label' => __('Username', true)));
					echo $form->input('password', array('label' => __('Password', true)));
					echo $form->input('extra', array('label' => __('Extra', true)));
					echo $form->input('description', array('type' => 'text', 'label' => __('Description', true)));

				?>


			<?php
				echo $form->end(__('Add', true));
				$javascript->link('jquery/jquery.autocomplete', false);
			?>
		</div>

		</div> <!--right-->

	</div> <!--inner_container-->
</div> <!--inner_container_border-->

<script type="text/javascript">
	$(document).ready(function($) {


		$('#SitesUserGroup').bind('change', function() {
			$(this).attr('style', 'background-color:' + $(this).val() + ';');
		});


		var url = '<?php echo Router::url(array('controller' => 'sites_users', 'action' => 'autoComplete')); ?>';
		$('#autoComplete').autocomplete(url,
		{
			delay: 100,
			onItemSelect: selectItem,
			onFindValue: findValue,
			formatItem: formatItem
		});

		$('#SitesUserAddForm').submit(
			function () {

				if (($('#SitesUserSiteId').val() == 'No results' || $('#SitesUserSiteId').val() == '') && $('#autoComplete').val() != '') {
					if (confirm('<?php __('You are about to request a new site ( '); ?>' + $('#autoComplete').val() + ' ). Are you sure?')) {
					} else {
						return false;
					}
				} else if ($('#SitesUserSiteId').val() == '') {
					alert('<?php __('Must select the site'); ?>');
					return false;
				}
			}
		);

	});




	function selectItem(li) {
		findValue(li);
	}

	function findValue(li) {
		if( li == null ) {
			return alert('<?php __('No match!'); ?>');
		}

		// if coming from an AJAX call, let's use the site id as the value
		if( !!li.extra )
			var sValue = li.extra[0];
			// otherwise, let's just display the value in the text box
		else {
			var sValue = li.selectValue;
		}
		$('#SitesUserSiteId').val(sValue);
	}

	function formatItem(row) {
		if(row[1] == undefined) {
			return row[0];
		} else {
			return row[0];
		}
	}

</script>