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

	function info($id = null) {

		$data = $_GET + $_POST;

		if (is_numeric($id)) {

			$site = $this->Site->findById($id);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $site['Site']['login_url']);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			$html = curl_exec($ch);


			preg_match('/<form([a-zA-Z0-9\s\.\_\=\/\?\:\'\"]+)>/', $html, $matches);
			foreach (explode(' ', str_replace('\'', '"', str_replace('  ', ' ', $matches[0]))) as $v) {
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

			if (!empty($formId)) {
				$this->Site->save(array('Site' => array('id' => $id, 'submit' => 'id|' . $formId)));
			} elseif (!empty($formName)) {
				$this->Site->save(array('Site' => array('id' => $id, 'submit' => 'name|' . $formName)));
			} elseif (!empty($formAction)) {
				$this->Site->save(array('Site' => array('id' => $id, 'submit' => 'action|' . $formAction)));
			}

			$this->set('data',
				preg_replace('/action=["\']([a-z0-9\.\_\=\/\?\:]+)["\']/', 'action="info/'.$id.'"', $html)
			);

			$this->render('info', 'ajax');
		} else {

			$id = array_pop(explode('/', $data['url']));

			foreach (array_flip($data) as $k => $v) {
				if ($k == '##USER##') {
					$data['username_field'] = $v;
				} elseif ($k == '##PASS##') {
					$data['password_field'] = $v;
				}
			}

			if (!empty($data)) {
				$data['id'] = $id;
				$this->Site->save(array('Site' => $data));
			}

			$this->redirect(array('action' => 'index'));
		}
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