<div class="sites view">
<h2><?php  __('Site');?></h2>

	<dl>
		<dd>
			<?php
				$site['SitesUser'] = array(
					'id' 		=> 10000,
					'username' 	=> $site['Site']['test_username'],
					'password' 	=> $site['Site']['test_password']);

				$add_on = true;
				if (!empty($site['Site']['require_add_on']) && $site['Site']['require_add_on'] == 'no') {

					$add_on = false;

					if (empty($site['SitesUser']['username'])) {
						foreach ($site['User'] as $user) {
							if (!empty($user['SitesUser']['username'])) {
								$site['SitesUser'] = array(
									'id' 		=> 10000,
									'username' 	=> $user['SitesUser']['username'],
									'password' 	=> $user['SitesUser']['password']);
							}
						}
					}
					$replaces['##username##'] = $site['SitesUser']['username'];
					$replaces['##password##'] = $site['SitesUser']['password'];
					$site['Site']['login_url'] = str_replace(array_keys($replaces), $replaces, $site['Site']['login_url']);
				}

				echo $this->element('plugin', array('data' => $site, 'add_on' => $add_on));
			?>
		</dd>
		<?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $site['Site']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Title'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $site['Site']['title']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Require Add On'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $site['Site']['require_add_on']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Logo'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $site['Site']['logo']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('State'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $site['Site']['state']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Login Url'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $site['Site']['login_url']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Test Username'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $site['Site']['test_username']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Test Password'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $site['Site']['test_password']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="related">
	<h3><?php __('Related Users');?></h3>
	<?php if (!empty($site['User'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Username'); ?></th>
		<th>Re-Assign</th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($site['User'] as $user):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $user['id'];?></td>
			<td><?php echo $user['username'];?></td>
			<td><?php echo $form->input('sites', array('id' => $user['id'], 'class' => 'reassign', 'label' => false, 'empty' => true, 'options' => $sites));?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller' => 'users', 'action' => 'view', $user['id'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller' => 'users', 'action' => 'edit', $user['id'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller' => 'users', 'action' => 'delete', $user['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $user['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New User', true), array('controller' => 'users', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>

<?php
	$javascript->link('jquery/jquery-1.4.1.min', false);
?>

<script>
	$('.reassign').change(
		function() {
			var user = $(this).attr('id');
			var val = $(this).val();
			$.get('<?php echo Router::url(array('controller' => 'sites_users', 'action' => 'reassign', $site['Site']['id'])); ?>/' + val + '/' + user);
		}
	);
</script>