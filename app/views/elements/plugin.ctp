<?php

	$out = $toolbar = null;

	$toolbar[] = $html->tag('span', $data['Site']['title'], array('class' => 'title'));
	$toolbar[] = $html->image('delete.png', array(
			'alt'	=> __('Delete', true),
			'title'	=> __('Delete', true),
			'url' 	=> array('controller' => 'sites_users', 'action' => 'delete', $data['SitesUser']['id']),
			'class' => 'action edit'));
	$toolbar[] = $html->image('edit.png', array(
			'alt'	=> __('Edit', true),
			'title'	=> __('Edit', true),
			'url' 	=> array('controller' => 'sites_users', 'action' => 'edit', $data['SitesUser']['id']),
			'class' => 'action edit'));
	$out[] = $html->tag('div', implode("\n", $toolbar), array('class' => 'toolbar'));


	if (empty($data['Site']['logo']) || !file_exists(IMAGES . DS . 'logos' . DS . $data['Site']['logo'])) {
		$data['Site']['logo'] = 'default.png';
	}

	if (empty($data['Site']['submit'])) {
		$out[] = $html->link($html->image('logos' . DS . $data['Site']['logo'], array('class' => 'remote_site_logo_disabled')), $data['Site']['login_url'], array('target' => '_BLANK'), false, false);
	} else {
		$out[] = $html->image('logos' . DS . $data['Site']['logo'], array('class' => 'remote_site_logo'));
	}

	$out[] = $html->tag('div', (empty($data['SitesUser']['description']))?$data['SitesUser']['username']:$data['SitesUser']['description'], array('class' => 'description'));

	$out[] = '<div class="hidden">';
	$out[] = $html->tag('div', $data['SitesUser']['id'], array('id' => 'plugin_identifier'));
		$out[] = $html->tag('div', $data['Site']['login_url'], array('id' => 'url'));
		$out[] = $html->tag('div', $data['SitesUser']['username'], array('id' => 'username', 'class' => $data['Site']['username_field']));
		$out[] = $html->tag('div', $data['SitesUser']['password'], array('id' => 'password', 'class' => $data['Site']['password_field']));
		$out[] = $html->tag('div', '', array('id' => 'submit', 'class' => $data['Site']['submit']));
	$out[] = '</div>';

	echo $html->tag('div', implode("\n", $out), array('class' => 'plugins'));

?>