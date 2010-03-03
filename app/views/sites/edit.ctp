<div class="sites form">
<?php echo $form->create('Site');?>
	<fieldset>
 		<legend><?php __('Edit Site');?></legend>
	<?php
		echo $form->input('id');
		echo $form->input('title');
		echo $form->input('logo');
		echo $form->input('login_url');
		echo $form->input('username_field');
		echo $form->input('password_field');
		echo $form->input('submit');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action' => 'delete', $form->value('Site.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Site.id'))); ?></li>
		<li><?php echo $html->link(__('List Sites', true), array('action' => 'index'));?></li>
		<li><?php echo $html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>
