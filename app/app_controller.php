<?php
/* SVN FILE: $Id$ */
/**
 * Short description for file.
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.controller
 * @since         CakePHP(tm) v 0.2.9
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * This is a placeholder class.
 * Create the same file in app/app_controller.php
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       cake
 * @subpackage    cake.cake.libs.controller
 */
class AppController extends Controller {

	var $helpers = array('Html', 'Form', 'Javascript');
	var $components = array('Auth', 'Cookie');
	//var $components = array('Cookie');


	function beforeFilter() {


		$this->Auth->loginRedirect = '/home';
		$this->Auth->logoutRedirect = array('controller' => 'users', 'action' => 'register');

		/** if admin url requested */
		if (isset($this->params['admin']) && $this->params['admin']) {

			if ($this->Session->read('Auth.User.username') !== 'root') {
				$this->Session->setFlash(__('You must be logged in as an admin user to enter this section.', true));
				$this->redirect('/');
			}
			$this->layout = 'admin';
		}
 

		/** Language settings */
		$language = $this->Cookie->read('language');
		if (empty($language)) {

			App::import('Vendor', 'Pragmatia', array('file' => 'l10nextended.php'));

			$L10nExtended = new L10nExtended();
			$L10nExtended->get();
			$locale = $L10nExtended->locale;

			$language = $L10nExtended->getLanguage();

			$availableLanguages = Configure::read('Config.languages');
			if (in_array($language, $availableLanguages)) {
				$availableLanguages = array_flip($availableLanguages);
				$language = $availableLanguages[$language];
			} else {
				$language = 'eng';
			}

			$this->Cookie->write('language', $language, false, '10 year');
		}
		Configure::write('Config.language', $language);

		$sharedToMe = ClassRegistry::init('SitesUser')->find('all',
			array(
				'contain' 		=> array('ParentSitesUser' => array('User', 'Site')),
				'conditions' 	=> array(
					'SitesUser.state' 				=> 'pendding',
					'SitesUser.sites_user_id !=' 	=> null,
					'SitesUser.user_id' 			=> $this->Session->read('Auth.User.id')
				)
			)
		);
		if (!empty($sharedToMe)) {
			$this->set('news', true);
		}

	}


}
?>