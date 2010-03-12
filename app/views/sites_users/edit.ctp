<div class="inner_container_border">

	<div class="inner_container">

		<div class="left"></div>

		<div class="right">
			<div class="text_and_logo">
				<?php
					echo $html->tag('span', __('Edit web site in', true));
					echo $html->image('logo_black.png');
				?>
				<span class="subtitle"><?php __('We order your home page') ?></span>
			</div>

			<?php echo $form->create('SitesUser');?>
				<?php
					echo $form->input('id');
					echo $form->input('site_id');
					echo $form->input('username');
					echo $form->input('password');
					echo $form->input('description');
				?>
			<?php
				echo $form->end('Save');
				echo $html->link(__('Back', true), '/home');
			?>
			</div>
		</div> <!--right-->

	</div> <!--inner_container-->
</div> <!--inner_container_border-->