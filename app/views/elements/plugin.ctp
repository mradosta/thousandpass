<?php

	$out = $toolbar = null;

	if ($data['SitesUser']['state'] == 'confirmed') {
		$toolbar[] = $html->tag('span', $data['Site']['title'], array('class' => 'title'));
		$toolbar[] = $html->image('delete.png', array(
				'alt'	=> __('Delete', true),
				'title'	=> __('Delete', true),
				'url' 	=> array('controller' => 'sites_users', 'action' => 'delete', $data['SitesUser']['id']),
				'class' => 'action'));
		$toolbar[] = $html->image('edit.png', array(
				'alt'	=> __('Edit', true),
				'title'	=> __('Edit', true),
				'url' 	=> array('controller' => 'sites_users', 'action' => 'edit', $data['SitesUser']['id']),
				'class' => 'action'));

		$domain = array_pop(explode('@', $data['SitesUser']['username']));
		if (!empty($domain) && in_array($domain, array('hotmail.com', 'yahoo.com', 'gmail.com'))) {
			$toolbar[] = $html->image('contacts.png', array(
					'alt'	=> __('Get contacts', true),
					'title'	=> __('Get contacts', true),
					'url' 	=> array('controller' => 'users', 'action' => 'get_contacts', $data['SitesUser']['id']),
					'class' => 'action'));
		}
		$out[] = $html->tag('div', implode("\n", $toolbar), array('class' => 'toolbar'));
	} else {
		$out[] = $html->tag('div', __('Pending of approval', true), array(
			'title' => __('Your new site will approved by our admins within the next 24 hs.', true),
			'class' => 'toolbar disabled'));
	}
	

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