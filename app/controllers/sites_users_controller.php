<?php
class SitesUsersController extends AppController {

	var $name = 'SitesUsers';


	function get_contacts($id) {

		$this->SitesUser->recursive = -1;
		$sitesUser = $this->SitesUser->findById($id);

		if (!empty($sitesUser)) {

			$domain = array_pop(explode('@', $sitesUser['SitesUser']['username']));

			App::import('Vendor', 'contactgrabber', array('file' => 'baseclass' . DS . 'baseclass.php'));
			if ($domain == 'gmail.com') {
				App::import('Vendor', 'contactgrabber' . DS . 'gmail', array('file' => 'libgmailer.php'));
				$obj = new GMailer();
			} elseif ($domain == 'hotmail.com') {
				App::import('Vendor', 'contactgrabber' . DS . 'hotmail', array('file' => 'msn_contact_grab.class.php'));
				$obj = new hotmail();
			} elseif ($domain == 'yahoo.com') {
				App::import('Vendor', 'contactgrabber'. DS . 'yahoo', array('file' => 'class.GrabYahoo.php'));
				$obj = new GrabYahoo();
			}

			$contacts = $obj->getAddressbook($sitesUser['SitesUser']['username'], $sitesUser['SitesUser']['password']);
			d($contacts);
		}
	}

	function index() {
		$this->SitesUser->recursive = 0;
		$this->paginate['conditions'] = array('SitesUser.user_id' => $this->Session->read('Auth.User.id'));
		$this->set('sitesUsers', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid SitesUser.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('sitesUser', $this->SitesUser->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->SitesUser->create();
			$this->data['SitesUser']['user_id'] = $this->Session->read('Auth.User.id');
			if ($this->SitesUser->save($this->data)) {
				$this->Session->setFlash(__('The SitesUser has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The SitesUser could not be saved. Please, try again.', true));
			}
		}
		$sites[__('Non Existent Site', true)][] = __('Request not listed site', true);
		$sites[__('Existing Sites', true)] = $this->SitesUser->Site->find('list', array('order' => array('Site.title' => 'asc')));
		$this->set(compact('sites'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid SitesUser', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->SitesUser->save($this->data)) {
				$this->Session->setFlash(__('The SitesUser has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The SitesUser could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->SitesUser->read(null, $id);
		}
		$sites = $this->SitesUser->Site->find('list');
		$users = $this->SitesUser->User->find('list');
		$this->set(compact('sites','users'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for SitesUser', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->SitesUser->del($id)) {
			$this->Session->setFlash(__('SitesUser deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>