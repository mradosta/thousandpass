<?php
class UsersController extends AppController {

	var $name = 'Users';

	var $components = array('Email', 'Captcha');


	function captcha() {
		$this->Captcha->show();
	}


    /**
     *  The AuthComponent provides the needed functionality
     *  for login, so you can leave this function blank.
     */
    function login() {
		$this->render('register');
    }

    function logout() {
		$this->redirect($this->Auth->logout());
    }


	function beforeFilter() {
		/**
		* Allows a user to sign up for a new account
		*/
		$this->Auth->allow(array('get_contacts', 'captcha', 'register', 'recover_password', 'terms_of_service'));
		return parent::beforeFilter();
	}



	function get_contacts($id) {

		$this->User->SitesUser->recursive = -1;
		$sitesUser = $this->User->SitesUser->findById($id);

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


	/**
	 * Reset users password and send it by email.
	 */
	function recover_password() {

		if (!empty($this->data)) {
			if (!$this->Captcha->protect()) {
				$this->User->invalidate('captcha', __('Validation text error. Try again', true));
			} elseif (empty($this->data['User']['username'])) {
				$this->User->invalidate('username', __('Must enter the username.', true));
			} elseif (empty($this->data['User']['email'])) {
				$this->User->invalidate('email', __('Must enter the email.', true));
			}
			
			$user = $this->User->find('first', array('conditions' => array(
				'User.username' => $this->data['User']['username'],
				'User.email' 	=> $this->data['User']['email'])));
			if (!empty($user)) {
				$uppercase  = range('A', 'Z');
				$numeric    = range(0, 9);
				$charPool   = array_merge($uppercase, $numeric);

				$poolLength = count($charPool) - 1;
				$newPassword = '';
				for ($i = 0; $i < 8; $i++) {
					$newPassword .= $charPool[mt_rand(0, $poolLength)];
				}

				App::import('Core', 'Security');
				$this->User->save(array('User' => array(
					'id' 			=> $user['User']['id'],
					'password'		=> Security::hash($newPassword, null, true))));

				$this->__sendEmail(
					array('template' => 'recover_password', 'subject' => __('1000Pass.com - Password Recovery Service', true)),
					array($user['User']['username'] => $user['User']['email']),
					array('newpassword' => $newPassword));
				$this->Session->setFlash(__('Your password has been send to your email.', true));
			} else {
				$this->Session->setFlash(__('Username and/or email does not exists.', true));
			}
		}
	}

	private function __sendEmail($mailInfo, $destinations, $data) {

		/* SMTP Options */
		$this->Email->smtpOptions = array(
				'port'		=> '25',
				'timeout'	=> '300',
				'host' 		=> 'smtp.pragmatia.com',
				'username'	=> 'mradosta@pragmatia.com.ar',
				'password'	=> 'NatachaLia0',
				'client' 	=> 'smtp_helo_hostname'
		);
		$this->Email->delivery = 'smtp';

		foreach ($destinations as $name => $email) {
			$this->Email->reset();
			$this->Email->to = $name . ' <' . $email . '>';
			$this->Email->subject = $mailInfo['subject'];
			$this->Email->replyTo = 'support@1000pass.com';
			$this->Email->from = '1000Pass.com <support@1000pass.com>';
			$this->Email->template = $mailInfo['template'];
			$this->Email->sendAs = 'html';
			$this->set('data', $data);
			$this->Email->send();
		}
	}


    function terms_of_service() {
        $this->view = 'Media';
        $params = array(
              'id' => Configure::read('Config.language') . '1000pass.pdf',
              'name' => __('Terms_Of_Service', true),
              'download' => true,
              'extension' => 'pdf',
              'path' => APP . 'files' . DS
       );
       $this->set($params);
    }


	function register() {

		if (!empty($this->data)) {
			if (!$this->Captcha->protect()) {
				$this->User->invalidate('captcha', __('Validation text error. Try again', true));
			} elseif (empty($this->data['User']['terms_of_service'])) {
				$this->User->invalidate('terms_of_service', __('Must accept the Terms of Service', true));
			} else {
				App::import('core', 'Sanitize');
				$this->User->data = Sanitize::clean($this->data);
				if ($this->User->save()) {
					$this->__sendEmail(
						array('template' => 'sign_up', 'subject' => __('Welcome to 1000Pass.com', true)),
						array($this->data['User']['name'] . ' ' . $this->data['User']['lastname'] => $this->data['User']['email']),
						$this->data);
					$this->Session->setFlash(__('Thanks for signing up at 1000Pass.com.', true));
					$this->data['User']['id'] = $this->User->id;
					$this->Session->write('Auth', array('User' => $this->data['User']));
					$this->redirect(array('controller' => 'sites_users', 'action' => 'add'));
				} else {
					$this->Session->setFlash(__('The User could not be saved. Please, try again.', true));
				}
			}

			$this->data['User']['password'] = null;
			$this->data['User']['repassword'] = null;
		}
	}


	function index() {
		$this->layout = 'admin';
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

	function view($id = null) {
		$this->layout = 'admin';
		if (!$id) {
			$this->Session->setFlash(__('Invalid User.', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

	function add() {
		$this->layout = 'admin';
		if (!empty($this->data)) {
			$this->User->create();
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The User has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The User could not be saved. Please, try again.', true));
			}
		}
		$sites = $this->User->Site->find('list');
		$this->set(compact('sites'));
	}

	function edit($id = null) {
		$this->layout = 'admin';
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid User', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The User has been saved', true));
				$this->redirect(array('action' => 'index'));
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