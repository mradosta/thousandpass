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
					echo $form->input('username');
					echo $form->input('password', array('after' => '<span title="' . __('Show hidden password', true) . '" id="show">' . __('Show', true) . '</span>'));
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
		$('#show').css('cursor', 'pointer').click(function() {
			var password = prompt('<?php __('Please, verify your password'); ?>');
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