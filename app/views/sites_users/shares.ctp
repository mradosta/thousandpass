<?php
	$this->pageTitle = '1000Pass.com - ' . __('Share my site', true);
?>

<div class="inner_container_border">

	<div class="inner_container">

		<div class="left no_bg">

<?php echo $html->tag('span', __('What do I share:', true), array('class' => 'label')); ?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php __('Site'); ?></th>
	<th><?php __('Description'); ?></th>
	<th><?php __('User'); ?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($myShares as $share):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td width="80px">
			<?php echo $share['ParentSitesUser']['Site']['title']; ?>
		</td>
		<td width="200px">
			<?php echo $share['ParentSitesUser']['description']; ?>
		</td>
		<td width="100px">
			<?php echo $share['User']['username']; ?>
		</td>
		<td class="actions" width="30px">
			<?php echo $html->link(
			$html->image('delete.png', array('alt' => __('Delete', true))), 
			array('controller' => 'sites_users', 'action' => 'delete_share', $share['SitesUser']['id'], $share['SitesUser']['sites_user_id']),
			array('title'	=> __('Delete', true)),
			sprintf(__('Are you sure you want to remove share of %s?', true), $share['ParentSitesUser']['Site']['title']),
			false); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>



<br/>

<?php echo $html->tag('span', __('What do others share to me:', true), array('class' => 'label')); ?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php __('Site'); ?></th>
	<th><?php __('Description'); ?></th>
	<th><?php __('User'); ?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($sharedToMe as $share):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td width="80px">
			<?php echo $share['ParentSitesUser']['Site']['title']; ?>
		</td>
		<td width="200px">
			<?php echo $share['ParentSitesUser']['description']; ?>
		</td>
		<td width="100px">
			<?php echo $share['ParentSitesUser']['User']['username']; ?>
		</td>
		<td class="actions" width="30px">
			<?php

			if ($share['SitesUser']['state'] == 'pendding') {
				echo $html->link(
					$html->image('accept.png', array('alt' => __('Accept', true))),
					array('controller' => 'sites_users', 'action' => 'accept_share', $share['SitesUser']['id']),
					array('title'	=> __('Accept', true)), null, false) . '&nbsp;';
			}

			echo $html->link(
				$html->image('delete.png', array('alt' => __('Delete', true))),
				array('controller' => 'sites_users', 'action' => 'delete_share', $share['SitesUser']['id']),
				array('title'	=> __('Delete', true)),
				sprintf(__('Are you sure you want to remove share of %s?', true), $share['ParentSitesUser']['Site']['title']),
				false);

			?>


		</td>
	</tr>
<?php endforeach; ?>
</table>



		</div> <!--left-->

		<div class="right">
			<div class="text_and_logo">
				<?php
					echo $html->tag('span', __('Share my site at', true));
					echo $html->image('logo_black.png');
				?>
				<span class="subtitle"><?php __('We order your home page') ?></span>
			</div>


			<?php echo $form->create('SitesUser', array('action' => 'shares'));?>
			<?php
				echo $form->input('id');

				$options = array();
				foreach ($mySites as $mySite) {
					if (!empty($mySite['Site']['id'])) {
						if (empty($mySite['SitesUser']['description'])) {
							$mySite['SitesUser']['description'] = $mySite['SitesUser']['username'];
						}
						$options[$mySite['SitesUser']['id']] = $mySite['Site']['title'] . ': ' . $mySite['SitesUser']['description'];
					}
				}
				echo $form->input('site_id', array('options' => $options));
				echo $form->input('user', array('type' => 'hidden'));
				echo $form->input('autocomplete', array('label' => __('User', true), 'id' => 'autoCompleteUser', 'after' => '<br/>' . __('Enter 1000pass.com username of the user to share with', true)));
				echo $form->end('Add');

				$javascript->link('jquery/jquery.autocomplete', false);
			?>
		</div> <!--right-->

	</div> <!--inner_container-->
</div> <!--inner_container_border-->


<script type="text/javascript">
	$(document).ready(function($) {

		var url = '<?php echo Router::url(array('controller' => 'sites_users', 'action' => 'autoCompleteUser')); ?>';
		$('#autoCompleteUser').autocomplete(url,
		{
			delay: 100,
			minChars: 3,
			onItemSelect: selectItem,
			onFindValue: findValue,
			formatItem: formatItem
		});
	});


	function selectItem(li) {
		findValue(li);
	}

	function findValue(li) {
		if( li == null ) {
			return alert('<?php __('No match!'); ?>');
		}

		// if coming from an AJAX call, let's use the site id as the value
		if( !!li.extra )
			var sValue = li.extra[0];
			// otherwise, let's just display the value in the text box
		else {
			var sValue = li.selectValue;
		}
		$('#SitesUserUser').val(sValue);
	}

	function formatItem(row) {
		if(row[1] == undefined) {
			return row[0];
		} else {
			return row[0];
		}
	}


</script>