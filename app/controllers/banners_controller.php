<?php
class BannersController extends AppController {

	var $name = 'Banners';

	function beforeRender() {
		$this->layout = 'admin';
		return parent::beforeRender();
	}

	function index() {
		$this->Banner->recursive = -1;
		$this->set('banners', $this->paginate());
	}


	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Banner.', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('banner', $this->Banner->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {

			$this->Banner->create();

			if (is_uploaded_file($this->data['Banner']['image']['tmp_name'])) {
				$target_path = WWW_ROOT . 'img' . DS . 'banners' . DS . $this->data['Banner']['image']['name'];
				if (move_uploaded_file($this->data['Banner']['image']['tmp_name'], $target_path)) {
					$this->data['Banner']['image'] = $this->data['Banner']['image']['name'];
				} else {
					$this->data['Banner']['image'] = '';
				}
			}

			if ($this->Banner->save($this->data)) {
				$this->Session->setFlash(__('The Banner has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Banner could not be saved. Please, try again.', true));
			}
		}

	}


	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Banner', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {

			if (!empty($this->data['Banner']['image_delete'])) {
				$this->data['Banner']['image'] = '';
			}

			if (is_uploaded_file($this->data['Banner']['image']['tmp_name'])) {
				$target_path = WWW_ROOT . 'img' . DS . 'banners' . DS . $this->data['Banner']['image']['name'];
				if (move_uploaded_file($this->data['Banner']['image']['tmp_name'], $target_path)) {
					$this->data['Banner']['image'] = $this->data['Banner']['image']['name'];
				} else {
					$this->data['Banner']['image'] = '';
				}
			}

			if ($this->Banner->save($this->data)) {
				$this->Session->setFlash(__('The Banner has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Banner could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Banner->read(null, $id);
		}
	}


	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Banner', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Banner->del($id)) {
			$this->Session->setFlash(__('Banner deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>