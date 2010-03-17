<div class="inner_container_border">

	<div class="inner_container">

		<div class="left no_bg">

			<?php
				echo $html->image('add.png', array(
					'alt'	=> __('Add Note', true),
					'title'	=> __('Add Note', true),
					'url' 	=> array('controller' => 'notes', 'action' => 'add')));
				echo $html->tag('span', __('Add Note', true), array('class' => 'label'));
			?>

<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php __('Date'); ?></th>
	<th><?php __('Title'); ?></th>
	<th><?php __('Note'); ?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($notes as $note):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td width="80px">
			<?php echo substr($note['Note']['created'], 0, 10); ?>
		</td>
		<td width="100px">
			<?php echo $note['Note']['title']; ?>
		</td>
		<td width="200px">
			<?php
				if (strlen($note['Note']['note']) > 30) {
					echo substr($note['Note']['note'], 0, 30) . '...';
				} else {
					echo $note['Note']['note'];
				}
			?>
		</td>
		<td class="actions" width="30px">
			<?php echo $html->image('edit.png', array('url' => array('action' => 'edit', $note['Note']['id']))); ?>
			<?php echo $html->image('delete.png', array('url' => array('action' => 'delete', $note['Note']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $note['Note']['id']))); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>


		</div> <!--left-->

		<div class="right">
			<div class="text_and_logo">
				<?php
					echo $html->tag('span', __('Add a new web site to', true));
					echo $html->image('logo_black.png');
				?>
				<span class="subtitle"><?php __('We order your home page') ?></span>
			</div>

			<?php echo $form->create('Notes');?>
				<?php
					echo $form->input('Note.id');
					echo $form->input('Note.title', array('label' => __('Title', true)));
					echo $form->input('Note.note', array('type' => 'textarea', 'label' => __('Note', true)));
				?>
			<?php
				if (empty($this->data)) {
					echo $form->end('Add');
				} else {
					echo $form->end('Save');
				}
				//echo $html->link(__('Back', true), '/home');
			?>
			</div>
		</div> <!--right-->

	</div> <!--inner_container-->
</div> <!--inner_container_border-->