<div class="inner_container_border">

	<div class="inner_container">

	<div class="left"></div>

	<div class="right">


		<div class="text_and_logo">
			<?php
				echo $html->tag('span', __('Add new web site to', true));
				echo $html->image('logo_black.png');
			?>
		</div>
		<h3><?php __('We order your home page') ?></h3>

		<?php echo $form->create('SitesUser');?>
			<?php
				echo $form->input('site_id');
				//echo $form->input('user_id', array('type' => 'hidden', 'value' => $session->read('User')));
				echo $form->input('username');
				echo $form->input('password');
				echo $form->input('description');
			?>
		<?php echo $form->end('Add');?>
		</div>
		<div class="actions">
			<ul>
				<li><?php echo $html->link(__('List SitesUsers', true), array('action' => 'index'));?></li>
				<li><?php echo $html->link(__('List Sites', true), array('controller' => 'sites', 'action' => 'index')); ?> </li>
				<li><?php echo $html->link(__('New Site', true), array('controller' => 'sites', 'action' => 'add')); ?> </li>
				<li><?php echo $html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
				<li><?php echo $html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
			</ul>
		</div>

	</div> <!--right-->
</div> <!--inner_container_border-->