<div class="sitesUsers view">
<h2><?php  __('SitesUser');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $sitesUser['SitesUser']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Description'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $sitesUser['SitesUser']['description']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Site'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($sitesUser['Site']['title'], array('controller' => 'sites', 'action' => 'view', $sitesUser['Site']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('User'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($sitesUser['User']['id'], array('controller' => 'users', 'action' => 'view', $sitesUser['User']['id'])); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit SitesUser', true), array('action' => 'edit', $sitesUser['SitesUser']['id'])); ?> </li>
		<li><?php echo $html->link(__('Delete SitesUser', true), array('action' => 'delete', $sitesUser['SitesUser']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $sitesUser['SitesUser']['id'])); ?> </li>
		<li><?php echo $html->link(__('List SitesUsers', true), array('action' => 'index')); ?> </li>
		<li><?php echo $html->link(__('New SitesUser', true), array('action' => 'add')); ?> </li>
		<li><?php echo $html->link(__('List Sites', true), array('controller' => 'sites', 'action' => 'index')); ?> </li>
		<li><?php echo $html->link(__('New Site', true), array('controller' => 'sites', 'action' => 'add')); ?> </li>
		<li><?php echo $html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>
