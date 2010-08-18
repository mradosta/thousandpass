<?php
class UsersController extends AppController {

	var $name = 'Users';

	var $components = array('Email', 'Captcha');


	function captcha() {
		$this->Captcha->show();
	}



	function help($what = null) {

		if (!empty($what)) {
			$links = null;
			$links['spa']['register'] = 'http://www.youtube.com/v/XQ35djMbjWs';
			$links['spa']['add_site'] = 'http://www.youtube.com/v/9ZGi-oa-MLQ';
			$links['spa']['add_note'] = 'http://www.youtube.com/v/PXcotlz8we8';
			$links['eng']['register'] = 'http://www.youtube.com/v/q-lUjh3hzyA';
			$links['eng']['add_site'] = 'http://www.youtube.com/v/9P_LBEPOCao';
			$links['eng']['add_note'] = 'http://www.youtube.com/v/5p_jCCCmbfU';
			$selectedLanguage = Configure::read('Config.language');

			$this->set('movie', $links[$selectedLanguage][$what]);
		}
	}



	function change_password() {

		if (!empty($this->data)) {

			App::import('core', 'Sanitize');
			$user = $this->User->findById($this->Session->read('Auth.User.id'));
			$data = Sanitize::clean($this->data);

			if ($user['User']['password'] != Security::hash($data['User']['current_password'], null, true)) {
				$this->User->invalidate('current_password', __('Your current password do not match.', true));
			} elseif ($data['User']['password'] != $data['User']['repassword']) {
				$this->User->invalidate('repassword', __('Passwords do not match.', true));
			}


			if ($this->User->updateAll(array('User.password' => "'" . Security::hash($data['User']['password'], null, true) . "'"), array('User.id' => $user['User']['id']))) {

				$this->Session->setFlash(__('Your password has been changed.', true));
			} else {
				$this->Session->setFlash(__('Your password could not be changed.', true));
			}
		}
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
		$this->Auth->allow(array('get_contacts', 'captcha', 'register', 'recover_password', 'terms_of_service', 'help'));

		if (!empty($this->params['action']) && $this->params['action'] == 'login' && !empty($this->data['User']['username'])) {
			$sql = 'DELETE FROM cake_sessions WHERE data LIKE \'%' . 's:8:"username";s:' . strlen($this->data['User']['username']) . ':"' . $this->data['User']['username'] . '"' . '%\'';
			$this->User->query($sql);

			$this->Session->write('add_on', array(
				'state' 	=> $this->data['User']['1000pass_add_on'],
				'version' 	=> $this->data['User']['1000pass_add_on_version']));
		}

		return parent::beforeFilter();
	}



	function get_contacts($id) {

		$this->User->SitesUser->recursive = -1;
		$sitesUser = $this->User->SitesUser->findById($id);

		if (!empty($sitesUser)) {


			$domain = array_pop(explode('@', $sitesUser['SitesUser']['username']));

			if ($domain == 'gmail.com') {
				$plugin = 'gmail';
			} elseif ($domain == 'hotmail.com') {
				$plugin = 'hotmail';
			} elseif (strpos($domain, 'yahoo') !== false) {
				$plugin = 'yahoo';
			}

			App::import('Vendor', 'OpenInviter', array('file' => 'openinviter.php'));
			$inviter=new OpenInviter();
			$inviter->getPlugins();
			$inviter->startPlugin($plugin);
			$inviter->login($sitesUser['SitesUser']['username'], $sitesUser['SitesUser']['password']);
			$errs = $inviter->getInternalError();

			if (!empty($errs)) {
				$this->Session->setFlash($errs, true);
				$this->redirect(array('controller' => 'sites_users', 'action' => 'index', 'true'));
			} else {
				$contacts = $inviter->getMyContacts();
			}

			$this->__sendEmail(
				array('template' => 'invite', 'subject' => $this->Session->read('Auth.User.name') . ' ' . $this->Session->read('Auth.User.lastname') . ' ' . __('Invites you to 1000Pass.com', true)),
				$contacts);

			$this->Session->setFlash(__('Thanks for inviting your friends to 1000pass.com!', true), true);
			$this->redirect(array('controller' => 'sites_users', 'action' => 'index', 'true'));
		}
	}


	function check_password($password) {

		Configure::write('debug', 0);
		$this->layout = 'ajax';

		App::import('Core', 'Security');
		$user = $this->User->find('first', array(
			'recursive' 	=> -1,
			'conditions' 	=> array(
				'User.username' => $this->Session->read('Auth.User.username'),
				'User.password' => Security::hash($password, null, true))));

		if (empty($user)) {
			$this->set('data', 'err');
		} else {
			$this->set('data', 'ok');
		}
	}


	/**
	 * Reset users password and send it by email.
	 */
	function recover_password() {

		if (!empty($this->data)) {
			if (!$this->Captcha->protect()) {
				$this->User->invalidate('captcha', __('Validation text error. Try again', true));
			//} elseif (empty($this->data['User']['username'])) {
			//	$this->User->invalidate('username', __('Must enter the username.', true));
			} elseif (empty($this->data['User']['email'])) {
				$this->User->invalidate('email', __('Must enter the email.', true));
			}
			
			$user = $this->User->find('first', array('conditions' => array(
				//'User.username' => $this->data['User']['username'],
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
					array($user['User']['username'] => $user['User']['username']),
					array('username' => $user['User']['username'], 'newpassword' => $newPassword));
				$this->Session->setFlash(__('Your password has been send to your email.', true));
			} else {
				$this->Session->setFlash(__('Username and/or email does not exists.', true));
			}
		}
	}

	private function __sendEmail($mailInfo, $destinations, $data = null) {

		/* SMTP Options
		$this->Email->smtpOptions = array(
				'port'		=> '25',
				'timeout'	=> '300',
				'host' 		=> 'mail.1000pass.com',
				'username'	=> 'info@1000pass.com',
				'password'	=> 'info2010',
				'client' 	=> 'smtp_helo_hostname'
		);
		*/
		/*
		$this->Email->smtpOptions = array(
				'port'		=> '25',
				'timeout'	=> '300',
				'host' 		=> 'mail.riesgoonline.com',
				'username'	=> 'info@1000pass.com',
				'password'	=> 'info2010',
				'client' 	=> 'smtp_helo_hostname'
		);
		*/
		$this->Email->delivery = 'smtp';

		foreach ($destinations as $name => $email) {
			$this->Email->reset();
			$this->Email->to = $name . ' <' . $email . '>';
			$this->Email->subject = $mailInfo['subject'];
			$this->Email->replyTo = 'info@1000pass.com';
			$this->Email->from = '1000Pass.com <info@1000pass.com>';
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
			// relax validation
			if (strlen($this->data['User']['captcha']) != 6) {
			//if (!$this->Captcha->protect()) {
				$this->User->invalidate('captcha', __('Validation text error. Try again', true));
			} elseif (empty($this->data['User']['terms_of_service'])) {
				$this->User->invalidate('terms_of_service', __('Must accept the Terms of Service', true));
			} else {
				App::import('core', 'Sanitize');
				$this->User->data = Sanitize::clean($this->data);
				if ($this->User->save()) {
					$this->__sendEmail(
						array('template' => 'sign_up', 'subject' => __('Welcome to 1000Pass.com', true)),
						array($this->data['User']['name'] . ' ' . $this->data['User']['lastname'] => $this->data['User']['username']),
						$this->data);
					$this->Session->setFlash(__('Thanks for signing up at 1000Pass.com.', true));
					$this->data['User']['id'] = $this->User->id;
					$this->Session->write('Auth', array('User' => $this->data['User']));
					$this->redirect(array('controller' => 'sites_users', 'action' => 'index'));
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