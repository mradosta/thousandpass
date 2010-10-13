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
			if ($k === '##USER##') {
				$toSave['username_field'] = $v;
			} elseif ($k === '##PASS##') {
				$toSave['password_field'] = $v;
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


		if (preg_match_all('/<form([a-zA-Z0-9\s\.\_\=\/\?\:\;\(\)\'\"]+)>/', $html, $matches)) {

			foreach ($matches[0] as $form) {

				foreach (explode(' ', str_replace('\'', '"', str_replace('  ', ' ', $form))) as $v) {

					if (substr($v, 0, 2) == 'id') {
						$tmp = explode('"', $v);
						$formId = $tmp[1];
					}
					if (substr($v, 0, 4) == 'name') {
						$tmp = explode('"', $v);
						$formName = $tmp[1];
					}
					if (substr($v, 0, 6) == 'action') {
						$tmp = explode('"', $v);
						$formAction = $tmp[1];
					}
				}

				$f = null;
				if (!empty($formId)) {
					$f = 'id|' . $formId;
					//$this->Site->save(array('Site' => array('id' => $id, 'submit' => 'id|' . $formId)));
				} elseif (!empty($formName)) {
					$f = 'name|' . $formName;
					//$this->Site->save(array('Site' => array('id' => $id, 'submit' => 'name|' . $formName)));
				} elseif (!empty($formAction)) {
					$f = 'action|' . $formAction;
					//$this->Site->save(array('Site' => array('id' => $id, 'submit' => 'action|' . $formAction)));
				}

				if (!empty($f)) {
					$html = str_replace($form, '<form method="post" action="../save_info/' . $id . '"><input type="submit" value="1000pass" /><input value="##FORM##" type="hidden" name="' . $f . '">', $html);
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