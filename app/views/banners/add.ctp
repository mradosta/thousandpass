<div class="sites form">
<?php echo $form->create('Banner', array('type' => 'file'));?>
	<fieldset>
 		<legend><?php __('Add Banner');?></legend>
	<?php
		echo $form->input('name');
		echo $form->input('image', array('type' => 'file'));
		echo $form->input('url');
		echo $form->input('location', array('options' => array('top' => 'Top', 'bottom_small' => 'Bottom Small', 'bottom_big' => 'Bottom Big')));
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>