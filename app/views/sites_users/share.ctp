<?php
	$this->pageTitle = '1000Pass.com - ' . __('Share my site', true);
?>


<div class="inner_container_border">

	<div class="inner_container">

		<div class="left"></div>

		<div class="right">
			<div class="text_and_logo">
				<?php
					echo $html->tag('span', __('Share my site at', true));
					echo $html->image('logo_black.png');
				?>
				<span class="subtitle"><?php __('We order your home page') ?></span>
			</div>

			<?php echo $form->create('SitesUser', array('action' => 'share'));?>
				<?php
					echo $form->input('id');
					echo $form->input('site_id', array('value' => $this->data['SitesUser']['site_id'], 'type' => 'hidden'));
					echo $form->input('user', array('label' => 'UserName', 'after' => '<br/>Enter 1000pass.com username of the user to share with'));
				?>
			<?php
				echo $form->end('Save');
			?>
			</div>
		</div> <!--right-->

	</div> <!--inner_container-->
</div> <!--inner_container_border-->

<script type="text/javascript">

	$(document).ready(function($) {
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