<?php
class UsersController extends AppController {

	var $name = 'Users';

	var $components = array('Email');

 
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
		/**
		* Allows a user to sign up for a new account
		*/
		$this->Auth->allow('register');
		return parent::beforeFilter();
	}


	private function __sendConfirmationEmail($user) {

		/* SMTP Options */
		$this->Email->smtpOptions = array(
				'port'=>'25',
				'timeout'=>'30',
				'host' => 'smtp.pragmatia.com',
				'username'=>'mradosta@pragmatia.com.ar',
				'password'=>'NatachaLia0',
				'client' => 'smtp_helo_hostname'
		);
		$this->Email->delivery = 'smtp';

		$this->Email->to = $user['User']['email'];
		$this->Email->subject = 'Welcome to 1000Pass.com';
		$this->Email->replyTo = 'support@1000pass.com';
		$this->Email->from = '1000Pass.com <support@1000pass.com>';
		$this->Email->template = 'sign_up';
		$this->Email->sendAs = 'both'; // because we like to send pretty mail
		$this->set('user', $user);

		$this->Email->send();
	}


	function register() {
		// If the user submitted the form.
		if (!empty($this->data)) {
				// Turn the supplied password into the correct Hash.
				// and move into the 'password' field so it will get saved.
				$this->data['User']['password'] = $this->Auth->password($this->data['User']['passwrd']);

				// Always Sanitize any data from users!
				App::import('core', 'Sanitize');
				$this->User->data = Sanitize::clean($this->data);
				if ($this->User->save()) {
					// Use a private method to send a confirmation
					// email to the new user (code not shown)
					$this->__sendConfirmationEmail($this->data);
					$this->Session->setFlash(__('Thanks for signing up at 1000Pass.com.', true));
					$this->redirect('/');
				} else {
					$this->Session->setFlash(__('The User could not be saved. Please, try again.', true));
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