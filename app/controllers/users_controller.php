<?php
class UsersController extends AppController {

	var $name = 'Users';
 
    /**
     *  The AuthComponent provides the needed functionality
     *  for login, so you can leave this function blank.
     */
    function login() {
    }

    function logout() {
        $this->redirect($this->Auth->logout());
    }


	function beforeFilter() {
		$this->Auth->allow('register');
	}


	/**
		* Allows a user to sign up for a new account
	*/
	function register() {
		// If the user submitted the form.
		if (!empty($this->data)) {
				// Turn the supplied password into the correct Hash.
				// and move into the 'password' field so it will get saved.
				$this->data['User']['password'] = $this->Auth->password($this->data['User']['passwrd']);

				// Always Sanitize any data from users!
				$this->User->data = Sanitize::clean($this->data);
				if ($this->User->save()) {
						// Use a private method to send a confirmation
						// email to the new user (code not shown)
						$this->__sendConfirmationEmail();

						// Success! Redirect to a thanks page.
						$this->redirect('/users/thanks');
				}

				// The plain text password supplied has been hashed into the 'password' field so
				// should now be nulled so it doesn't get render in the HTML if the save() fails
				$this->data['User']['passwrd'] = null;
		}
	}


	function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid User.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->User->create();
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The User has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The User could not be saved. Please, try again.', true));
			}
		}
		$sites = $this->User->Site->find('list');
		$this->set(compact('sites'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid User', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The User has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The User could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->User->read(null, $id);
		}
		$sites = $this->User->Site->find('list');
		$this->set(compact('sites'));
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