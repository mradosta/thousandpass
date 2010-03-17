<?php
class NotesController extends AppController {

	var $name = 'Notes';

	function add() {
		if (!empty($this->data)) {
			$this->Note->create();
			$this->data['Note']['user_id'] = $this->Session->read('Auth.User.id');
			if ($this->Note->save($this->data)) {
				$this->Session->setFlash(__('The Note has been saved', true));
				$this->redirect(array('action' => 'add'));
			} else {
				$this->Session->setFlash(__('The Note could not be saved. Please, try again.', true));
			}
		}
		$this->set('notes', $this->Note->find('all', array(
			'order'			=> array('Note.created' => 'DESC'),
			'conditions' 	=> array(
				'Note.user_id' => $this->Session->read('Auth.User.id')))));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Note', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Note->save($this->data)) {
				$this->Session->setFlash(__('The Note has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Note could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Note->read(null, $id);
		}

		$this->set('notes', $this->Note->find('all', array(
			'order'			=> array('Note.created' => 'DESC'),
			'conditions' 	=> array(
				'Note.user_id' => $this->Session->read('Auth.User.id')))));

		$this->render('add');
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for User', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->User->del($id)) {
			$this->Session->setFlash(__('User deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>