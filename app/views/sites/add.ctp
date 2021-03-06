<div class="sites form">
<?php echo $form->create('Site', array('type' => 'file'));?>
	<fieldset>
 		<legend><?php __('Add Site');?></legend>
	<?php
		echo $form->input('title');
		echo $form->input('require_add_on', array('options' => array('yes' => 'Yes', 'no' => 'No')));
		echo $form->input('logo_field', array('type' => 'file'));
		echo $form->input('state', array('options' => array('pending' => 'Pending', 'approved' => 'Approved', 'blocked' => 'Blocked')));
		echo $form->input('login_url');
		echo $form->input('logout_url');
		echo $form->input('username_field');
		echo $form->input('password_field');
		echo $form->input('test_username');
		echo $form->input('test_password');
		echo $form->input('extra');
		echo $form->input('submit');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Sites', true), array('action' => 'index'));?></li>
		<li><?php echo $html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>
