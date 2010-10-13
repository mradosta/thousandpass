<?php
class SitesController extends AppController {

	var $name = 'Sites';

	function beforeRender() {
		$this->layout = 'admin';
		return parent::beforeRender();
	}

	function index() {
		$this->Site->recursive = -1;
		$this->paginate['order'] = array('Site.state' => 'desc', 'Site.title');
		$this->set('sites', $this->paginate());
	}


	function save_info($id = null) {

		$data = $_GET + $_POST;
		$id = array_pop(explode('/', $data['url']));

		foreach (array_flip($data) as $k => $v) {

			$tmp = explode('|', $k);
			$k = array_pop($tmp);
			$prefix = array_shift($tmp);
			if (empty($prefix)) {
				$prefix = 'name';
			}
			if ($k === '##USER##') {
				$toSave['username_field'] = $prefix . '|' . $v;
			} elseif ($k === '##PASS##') {
				$toSave['password_field'] = $prefix . '|' . $v;
			} elseif ($k === '##FORM##') {
				$toSave['submit'] = $v;
			}
		}

		if (!empty($toSave)) {
			$toSave['id'] = $id;
			if (count($toSave) == 4) {
				$toSave['state'] = 'approved';
			}
			$this->Site->save(array('Site' => $toSave));
		}

		$this->redirect(array('action' => 'index'));

	}

	function info($id) {

		$site = $this->Site->findById($id);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $site['Site']['login_url']);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$html = curl_exec($ch);


		if (preg_match_all('/<(form|input)([a-zA-Z0-9\s\.\_\=\/\?\:\;\(\)\'\"]+)>/', $html, $matches)) {

			foreach ($matches[0] as $k => $element) {

				$elementId = $elementName = $elementAction = null;
				foreach (explode(' ', str_replace('\'', '"', str_replace('  ', ' ', $element))) as $v) {

					if (substr($v, 0, 2) == 'id') {
						$tmp = explode('"', $v);
						$elementId = $tmp[1];
					}
					if (substr($v, 0, 4) == 'name') {
						$tmp = explode('"', $v);
						$elementName = $tmp[1];
					}
					if (substr($v, 0, 6) == 'action') {
						$tmp = explode('"', $v);
						$elementAction = $tmp[1];
					}
				}

				$f = null;
				if (!empty($elementId)) {
					$f = 'id|' . $elementId;
				} elseif (!empty($elementName)) {
					$f = 'name|' . $elementName;
				} elseif (!empty($elementAction)) {
					$f = 'action|' . $elementAction;
				}

				if (!empty($f)) {
					if ($matches[1][$k] == 'form') {
						$html = str_replace(
							$element,
							'<form method="post" action="../save_info/' . $id . '"><input type="submit" value="1000pass" /><input value="##FORM##" type="hidden" name="' . $f . '">',
							$html
						);
					} else {
						$html = str_replace(
							$element,
							str_replace('<input', '<input value="' . $f . '|"', $element),
							$html
						);
					}
				}
			}
		}
		$html = preg_replace('@<script[^>]*?>.*?</script>@si', '', $html);

		$this->set('data', $html);
		$this->render('info', 'ajax');

	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Site.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('sites', $this->Site->find('list'));
		$this->set('site', $this->Site->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {

			$this->Site->create();

			if (is_uploaded_file($this->data['Site']['logo_field']['tmp_name'])) {
				$this->data['Site']['logo'] = $this->data['Site']['logo_field']['name'];
				$target_path = WWW_ROOT . 'img' . DS . 'logos' . DS . $this->data['Site']['logo'];
				if (move_uploaded_file($this->data['Site']['logo_field']['tmp_name'], $target_path)) {
					$this->data['Site']['logo'] = $this->data['Site']['logo_field']['name'];
				}
			}

			if ($this->Site->save($this->data)) {
				$this->Session->setFlash(__('The Site has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Site could not be saved. Please, try again.', true));
			}
		}

		$users = $this->Site->User->find('list');
		$this->set(compact('users'));
	}


	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Site', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {

			if (!empty($this->data['Site']['logo_delete'])) {
				$this->data['Site']['logo'] = '';
			}

			if (is_uploaded_file($this->data['Site']['logo_field']['tmp_name'])) {
				$this->data['Site']['logo'] = $this->data['Site']['logo_field']['name'];
				$target_path = WWW_ROOT . 'img' . DS . 'logos' . DS . $this->data['Site']['logo'];
				if (move_uploaded_file($this->data['Site']['logo_field']['tmp_name'], $target_path)) {
					$this->data['Site']['logo'] = $this->data['Site']['logo_field']['name'];
				}
			}

			if ($this->Site->save($this->data)) {
				$this->Session->setFlash(__('The Site has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Site could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Site->read(null, $id);
		}
		$users = $this->Site->User->find('list');
		$this->set(compact('users'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Site', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Site->del($id)) {
			$this->Session->setFlash(__('Site deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>