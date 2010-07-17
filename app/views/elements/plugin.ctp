<?php

	$out = $toolbar = null;
	if (empty($data['Site']['state']) || $data['Site']['state'] == 'approved') {

		if (empty($data['ParentSitesUser']['id'])) {
			$toolbar[] = $html->tag('div', $data['Site']['title'], array('class' => 'title'));
		} else {

			$data['Site'] = $sites[$data['ParentSitesUser']['site_id']];
			$data['SitesUser']['description'] = __('Shared Site by ', true) . $html->tag('span', $users[$data['ParentSitesUser']['user_id']]['username'], array('class' => 'sharer_user'));

			if ($data['SitesUser']['state'] == 'unshared') {
				$data['SitesUser']['username'] = '';
				$data['SitesUser']['password'] = '';
			} else {
				$data['SitesUser']['username'] = $data['ParentSitesUser']['username'];
				$data['SitesUser']['password'] = $data['ParentSitesUser']['password'];
			}
			$toolbar[] = $html->tag('div', $sites[$data['ParentSitesUser']['site_id']]['title'], array('class' => 'title'));
		}

		if (in_array($data['SitesUser']['id'], $shares)) {
			$toolbar[] = $html->image('share.png', array(
					'alt'	=> __('Shared', true),
					'title'	=> __('Shared', true),
					'url' 	=> array('controller' => 'sites_users', 'action' => 'shares'),
					'class' => 'action'));
		}

		$toolbar[] = $html->link(
			$html->image('delete.png', array('alt' => __('Delete', true))),
			array('controller' => 'sites_users', 'action' => 'delete', $data['SitesUser']['id']),
			array('title'	=> __('Delete', true)),
			sprintf(__('Are you sure you want to delete %s?', true), $data['Site']['title']),
			false);

		if (empty($data['ParentSitesUser']['id'])) {

			$toolbar[] = $html->image('edit.png', array(
					'alt'	=> __('Edit', true),
					'title'	=> __('Edit', true),
					'url' 	=> array('controller' => 'sites_users', 'action' => 'edit', $data['SitesUser']['id']),
					'class' => 'action'));
		}

		if (empty($data['ParentSitesUser']['id']) &&
			in_array($data['Site']['title'], array('Hotmail', 'Yahoo', 'Gmail'))) {
			$toolbar[] = $html->image('contacts.png', array(
					'alt'	=> __('Get contacts', true),
					'title'	=> __('Get contacts', true),
					'url' 	=> array('controller' => 'users', 'action' => 'get_contacts', $data['SitesUser']['id']),
					'class' => 'action'));
		}


		$extraClass = '';
		if (!empty($data['ParentSitesUser']['id'])) {
			$extraClass = ' shared';
			if ($data['SitesUser']['state'] == 'unshared') {
				$extraClass = ' unshared';
			}
		}

		$out[] = $html->tag('div', implode("\n", $toolbar), array('class' => 'toolbar' . $extraClass, 'title' => $data['Site']['title']));

	} else {
		if ($data['Site']['state'] == 'pending') {
			$toolbar[] = $html->tag('div', __('Pending of approval', true), array('class' => 'title'));
			$title = __('Your new site will approved by our admins within the next 24 hs.', true);
		} else {
			$toolbar[] = $html->tag('div', __('Site blocked', true), array('class' => 'title'));
			$title = __('Your site is blocked by our admins.', true);
		}

		$toolbar[] = $html->link(
			$html->image('delete.png', array('alt' => __('Delete', true))),
			array('controller' => 'sites_users', 'action' => 'delete', $data['SitesUser']['id']),
			array('title'	=> __('Delete', true)),
			sprintf(__('Are you sure you want to delete %s?', true), $data['SitesUser']['description']),
			false);

		$out[] = $html->tag('div', implode("\n", $toolbar), array(
			'title' => $title,
			'class' => 'toolbar disabled'));

	}
	

	if (empty($data['Site']['logo']) || !file_exists(IMAGES . DS . 'logos' . DS . $data['Site']['logo'])) {
		$data['Site']['logo'] = 'default.png';
	}


	if ($data['Site']['require_add_on'] == 'no' && !$add_on) {
		$imgOptions = array('class' => 'remote_site_logo_disabled', 'title' => $data['Site']['title']);
		$logo = $html->link($html->image('logos/' . $data['Site']['logo'], $imgOptions), $data['Site']['login_url'], array('target' => '_blank', 'onclick' => 'if($.browser.name == "chrome") {window.open (this.href, ""); return false;} else {return true;}'), null, false);
		$out[] = $html->tag('div', $logo, array('class' => 'drag_selector'));
	} elseif ($data['Site']['state'] == 'approved' || (!empty($sites[$data['ParentSitesUser']['site_id']]['state']) && $sites[$data['ParentSitesUser']['site_id']]['state'] == 'approved' )) {
		$class = '';
		if (!$add_on) {
			$class = ' add_on_not_installed';
		}
		$imgOptions = array('class' => 'remote_site_logo', 'title' => $data['Site']['title']);
		$logo = $html->image('logos/' . $data['Site']['logo'], $imgOptions);
		$out[] = $html->tag('div', $logo, array('class' => 'drag_selector requiere_add_on' . $class));
	} else {
		if (preg_match('/^http:\/\//', $data['Site']['login_url']) || preg_match('/^https:\/\//', $data['Site']['login_url'])) {
			$url = $data['Site']['login_url'];
		} else {
			$url = 'http://' . $data['Site']['login_url'];
		}

		$imgOptions = array('class' => 'remote_site_logo_disabled', 'title' => $data['Site']['title']);
		$logo = $html->link($html->image('logos/' . $data['Site']['logo'], $imgOptions), $url, array('target' => '_blank', 'onclick' => 'if($.browser.name == "chrome") {window.open (this.href, ""); return false;} else {return true;}'), null, false);
		$out[] = $html->tag('div', $logo, array('class' => 'drag_selector'));
	}

	$out[] = $html->tag('div', (empty($data['SitesUser']['description']))?$data['SitesUser']['username']:$data['SitesUser']['description'], array('class' => 'description'));

	$out[] = '<div class="hidden">';
	$out[] = $html->tag('div', $data['SitesUser']['id'], array('id' => 'plugin_identifier'));
	if ($data['Site']['state'] == 'approved') {
		$out[] = $html->tag('div', $data['Site']['title'], array('id' => 'title'));
		$out[] = $html->tag('div', $data['Site']['login_url'], array('id' => 'url'));
		$logoutUrl = explode('|', $data['Site']['logout_url']);
		if (empty($logoutUrl[1])) {
			$logoutUrl[1] = $logoutUrl[0];
			$logoutUrl[0] = 'link';
		}
		$out[] = $html->tag('div', $logoutUrl[1], array('id' => 'logout_url', 'class' => $logoutUrl[0]));
		$out[] = $html->tag('div', $data['SitesUser']['username'], array('id' => 'username', 'class' => $data['Site']['username_field']));
		$out[] = $html->tag('div', $data['SitesUser']['password'], array('id' => 'password', 'class' => $data['Site']['password_field']));
		$out[] = $html->tag('div', $data['Site']['extra'], array('id' => 'extra'));
		$out[] = $html->tag('div', '', array('id' => 'submit', 'class' => $data['Site']['submit']));
	}
	$out[] = '</div>';

	echo $html->tag('div', implode("\n", $out), array('class' => 'plugins'));

?>