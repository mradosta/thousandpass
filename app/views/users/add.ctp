<div class="users form">
<?php echo $form->create('User');?>
	<fieldset>
 		<legend><?php __('Add User');?></legend>
	<?php
		echo $form->input('username');
		echo $form->input('password');
		echo $form->input('email');
		echo $form->input('state', array('options' => array(
			'pending' 	=> __('Pending', true),
			'approved' 	=> __('Approved', true))));
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Users', true), array('action' => 'index'));?></li>
		<li><?php echo $html->link(__('List Sites', true), array('controller' => 'sites', 'action' => 'index')); ?> </li>
		<li><?php echo $html->link(__('New Site', true), array('controller' => 'sites', 'action' => 'add')); ?> </li>
	</ul>
</div>
