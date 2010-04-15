<?php
	$this->pageTitle = '1000Pass.com - ' . __('Change Password', true);
?>

<div class="inner_container_border">

	<div class="inner_container">

		<div class="left"></div>

		<div class="right">
			<div class="text_and_logo">
				<?php
					echo $html->tag('span', __('Change Password for', true));
					echo $html->image('logo_black.png');
				?>
				<span class="subtitle"><?php __('We order your home page') ?></span>
			</div>

			<?php echo $form->create('User', array('action' => 'change_password'));

				echo $form->input('current_password', array(
					'label' => __('Current Password', true),
					'type' => 'password',
					'error' => array(
						'alphanumeric'	=> __('Only the letters A-z and digits 0-9 are allowed', true),
						'length'		=> __('Your password must be at least 6 characters long', true),
					)
				));
				echo $form->input('password', array(
					'label' => __('New Password', true),
					'type' => 'password',
					'error' => array(
						'alphanumeric'	=> __('Only the letters A-z and digits 0-9 are allowed', true),
						'length'		=> __('Your password must be at least 6 characters long', true),
					)
				));
				echo $form->input('repassword', array(
					'label' => __('Retype New Password', true),
					'type' 	=> 'password',
					'error' => array(
						'repeated'	=> __('Passwords do not match', true),
					)
				));

				?>
			<?php
				echo $form->end(__('Change', true));
				//echo $html->link(__('Back', true), '/home');
			?>
		</div> <!--right-->

	</div> <!--inner_container-->
</div> <!--inner_container_border-->