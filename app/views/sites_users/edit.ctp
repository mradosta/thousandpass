<div class="sitesUsers form">
<?php echo $form->create('SitesUser');?>
	<fieldset>
 		<legend><?php __('Edit SitesUser');?></legend>
	<?php
		echo $form->input('id');
		echo $form->input('site_id');
		echo $form->input('username');
		echo $form->input('password');
		echo $form->input('description');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action' => 'delete', $form->value('SitesUser.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('SitesUser.id'))); ?></li>
		<li><?php echo $html->link(__('List SitesUsers', true), array('action' => 'index'));?></li>
		<li><?php echo $html->link(__('List Sites', true), array('controller' => 'sites', 'action' => 'index')); ?> </li>
		<li><?php echo $html->link(__('New Site', true), array('controller' => 'sites', 'action' => 'add')); ?> </li>
		<li><?php echo $html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>
