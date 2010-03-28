<div class="inner_container_border">

	<div class="inner_container">

		<div class="left"></div>

		<div class="right">
			<div class="text_and_logo">
				<?php
					echo $html->tag('span', __('Recover your pass for', true));
					echo $html->image('logo_black.png');
				?>
				<span class="subtitle"><?php __('We order your home page') ?></span>
			</div>

			<?php echo $form->create('User', array('action' => 'recover_password'));

					echo $form->input('username', array('label' => __('Username', true)));
					echo $form->input('email', array('label' => __('email', true)));

					echo $html->tag('div', $html->image('/' . $this->params['controller'] . '/captcha', array('id' => 'captcha_image')), array('class' => 'captcha'));
					echo $form->input('captcha', array('label' => __('Type the numbers you see in the picture', true)));

				?>
			<?php
				echo $form->end(__('Recover', true));
				//echo $html->link(__('Back', true), '/home');
			?>
		</div> <!--right-->

	</div> <!--inner_container-->
</div> <!--inner_container_border-->

<script type="text/javascript">
	$(document).ready(function($) {

		$('#SitesUserNewRequest').parent().hide();

		$('#SitesUserSiteId').change(
			function() {
				if ($(this).val() == 0) {
					$('#SitesUserNewRequest').parent().show();
				} else {
					$('#SitesUserNewRequest').parent().hide();
				}
			}
		);
	});
</script>