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