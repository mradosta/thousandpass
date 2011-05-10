<?php
	$this->pageTitle = '1000Pass.com - ' . __('Edit existing site', true);
?>


<div class="inner_container_border">

	<div class="inner_container">

		<div class="left"></div>

		<div class="right">
			<div class="text_and_logo">
				<?php
					echo $html->tag('span', __('Edit existing site in', true));
					echo $html->image('logo_black.png');
				?>
				<span class="subtitle"><?php __('We order your home page') ?></span>
			</div>

			<?php echo $form->create('SitesUser');?>
				<?php
					echo $form->input('id');
					$colours = array(
						array('name' => __('Green', true), 'value' => '#B2C200', 'style' => 'background:#B2C200;'),
						array('name' => __('Ligth Blue', true), 'value' => '#0065FE', 'style' => 'background:#0065FE;'),
						array('name' => __('Pink', true), 'value' => '#FE0065', 'style' => 'background:#FE0065;'),
						array('name' => __('Red', true), 'value' => '#C20000', 'style' => 'background:#C20000;'),
						array('name' => __('Violet', true), 'value' => '#BB00C2', 'style' => 'background:#BB00C2;'),
						array('name' => __('Yellow', true), 'value' => '#FED700', 'style' => 'background:#FED700;')
					);
					if (!empty($this->data['SitesUser']['group'])) {
						echo $form->input('group', array('label' => __('Group', true), 'empty' => true, 'options' => $colours, 'style' => 'background:' . $this->data['SitesUser']['group'] . ';'));
					} else {
						echo $form->input('group', array('label' => __('Group', true), 'empty' => true, 'options' => $colours));
					}

					echo $form->input('username');
					echo $form->input('password', array('after' => '<span title="' . __('Show hidden password', true) . '" id="show">' . __('Show', true) . '</span>'));
					echo $form->input('extra');
					echo $form->input('description');
				?>
			<?php
				echo $form->end('Save');
				//echo $html->link(__('Back', true), '/home');
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

		$('#show').css('cursor', 'pointer').click(function() {
			var password = prompt('<?php __('Please, verify your 1000pass.com password'); ?>');
			$.get('<?php echo Router::url(array('controller' => 'users', 'action' => 'check_password')); ?>' + '/' + password, function(data) {
				if (data == 'ok') {
					$('#SitesUserPassword').parent().append(
						$('<input/>').attr('name', 'data[SitesUser][password]').attr('type', 'text').attr('id', 'SitesUserPassword').val($('#SitesUserPassword').val())
					);
					$('#SitesUserPassword').remove();
					$('#show').remove();
				} else {
					alert('<?php __('Could not verify current password. Please, try again.'); ?>');
				}
			});
			
		});
	});

</script>