<div class="sites form">
<?php echo $form->create('Banner', array('type' => 'file'));?>
	<fieldset>
 		<legend><?php __('Edit Banner');?></legend>
	<?php
		echo $form->input('id');
		echo $form->input('name');
		echo $form->input('image', array('type' => 'file'));

		if (!empty($this->data['Banner']['image'])) {
			echo $html->image('banners' . DS . $this->data['Banner']['image']);
			echo $form->input('image_delete', array('type' => 'checkbox', 'label' => 'Delete Banner'));
		}

		echo $form->input('url');
		echo $form->input('location', array('options' => array('top' => 'Top', 'bottom' => 'Bottom')));
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>