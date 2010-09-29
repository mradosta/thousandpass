<div class="sites index">
<h2><?php __('Sites');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th class="actions"><?php __('Actions');?></th>
	<th><?php echo $paginator->sort('id');?></th>
	<th><?php echo $paginator->sort(__('Add On', true), 'require_add_on');?></th>
	<th><?php echo $paginator->sort('title');?></th>
	<th><?php echo $paginator->sort('state');?></th>
	<th><?php echo $paginator->sort('login_url');?></th>
</tr>
<?php
$i = 0;
foreach ($sites as $site):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td class="actions">
			<?php echo $html->link(__('I', true), array('action' => 'info', $site['Site']['id']), array('target' => '_BLANK', 'title' => __('Info', true))); ?>
			<?php echo $html->link(__('V', true), array('action' => 'view', $site['Site']['id']), array('title' => __('View', true))); ?>
			<?php echo $html->link(__('E', true), array('action' => 'edit', $site['Site']['id']), array('title' => __('Edit', true))); ?>
			<?php echo $html->link(__('D', true), array('action' => 'delete', $site['Site']['id']), array('title' => __('Delete', true)), sprintf(__('Are you sure you want to delete # %s?', true), $site['Site']['id'])); ?>
		</td>
		<td>
			<?php echo $site['Site']['id']; ?>
		</td>
		<td>
			<?php echo $site['Site']['require_add_on']; ?>
		</td>
		<td>
			<?php echo $site['Site']['title']; ?>
		</td>
		<td>
			<?php echo $site['Site']['state']; ?>
		</td>
		<td>
			<?php echo substr($site['Site']['login_url'], 0, 50); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class' => 'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New Site', true), array('action' => 'add')); ?></li>
		<li><?php echo $html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>
