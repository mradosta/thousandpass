<?php
class SitesUsersController extends AppController {

	var $name = 'SitesUsers';


	function reassign($oldSiteId, $newSiteId, $userId) {
		$sitesUser = $this->SitesUser->find('first', array('conditions' => array(
			'SitesUser.site_id' => $oldSiteId,
			'SitesUser.user_id' => $userId
		)));
		if (!empty($sitesUser)) {
			$sitesUser['SitesUser']['site_id'] = $newSiteId;
			$this->SitesUser->save($sitesUser);
		}
		$this->autoRender = false;
	}

    function download($browser) {

		/**
		* firefox
		* msie
		* chrome
		*/

		if ($browser == 'msie') {
			$params = array(
				'id' 		=> Configure::read('Config.language') . '_1000pass.exe',
				'name' 		=> __('Install', true),
				'download' 	=> true,
				'extension' => 'exe',
				'path' 		=> APP . 'files' . DS
			);
		}

		$this->set($params);
        $this->view = 'Media';
    }


	/**
	 * Saves the order comming from ajax request.
	 */
	function reorder($newOrder) {

		$this->autoRender = false;

		foreach(explode('|', $newOrder) as $k => $id) {
			$data = null;
			$data['SitesUser']['order'] = $k;
			$data['SitesUser']['id'] = $id;
			$saveAll[] = $data;
		}

		if (!empty($saveAll)) {
			$this->SitesUser->saveAll($saveAll, array('validate' => false));
		}
	}


	/**
	 * 
	 */
	function autoComplete() {
		Configure::write('debug', 0);
		$this->layout = 'ajax';

		if (!empty($this->params['url']['q'])) {
			$q = '%' . preg_replace('/^www./', '', $this->params['url']['q']) . '%';

			$data = $this->SitesUser->Site->find('all', array(
				'recursive'		=> -1,
				'conditions'	=> array(
					'OR' => array(
						'Site.login_url LIKE' 	=> $q,
						'Site.title LIKE' 		=> $q)),
				'fields'		=> array('title', 'id')));

			$this->set('data', $data);
		}
	}


	function index() {

		$mySites = $this->SitesUser->find('all', array(
			'order' 		=> array('SitesUser.order' => 'asc'),
			'contain' 		=> array('Site', 'ParentSitesUser'),
			'conditions' 	=> array('SitesUser.user_id' => $this->Session->read('Auth.User.id'))));

		$myShares = $this->SitesUser->find('all',
			array(
				'contain' 		=> array('User', 'ParentSitesUser'),
				'conditions' 	=> array(
					array(
						'SitesUser.sites_user_id' => Set::extract('/SitesUser/id', $mySites)
					)
				)
			)
		);

		$sites = $this->SitesUser->Site->find('all',
			array(
				'recursive' 	=> -1,
				'conditions' 	=>
				array(
					array(
						'Site.id' => Set::filter(Set::extract('/ParentSitesUser/site_id', $mySites))
					)
				)
			)
		);
		$users = $this->SitesUser->User->find('all',
			array(
				'recursive' 	=> -1,
				'conditions' 	=>
				array(
					array(
						'User.id' => Set::filter(Set::extract('/ParentSitesUser/user_id', $mySites))
					)
				)
			)
		);


		$this->set('shares', Set::extract('/ParentSitesUser/id', $myShares));
		$this->set('sitesUsers', $mySites);
		$this->set('sites', Set::combine($sites, '{n}.Site.id', '{n}.Site'));
		$this->set('users', Set::combine($users, '{n}.User.id', '{n}.User'));
		
	}


	function add($first_time = false) {

		if (!empty($this->data)) {

			if ((empty($this->data['SitesUser']['site_id']) || in_array($this->data['SitesUser']['site_id'], array('No results', 'Sin resultados'))) && !empty($this->data['SitesUser']['autocomplete'])) {
				$this->SitesUser->Site->save(
					array('Site' => array(
						'title'		=> $this->data['SitesUser']['autocomplete'],
						'state'		=> 'pending',
						'login_url' => $this->data['SitesUser']['autocomplete']))
				, false);
				$this->data['SitesUser']['site_id'] = $this->SitesUser->Site->id;
			}

			$this->SitesUser->create();
			$this->data['SitesUser']['user_id'] = $this->Session->read('Auth.User.id');
			$this->data['SitesUser']['order'] = 1000;
			if ($this->SitesUser->save($this->data)) {
				$this->Session->setFlash(__('The SitesUser has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The SitesUser could not be saved. Please, try again.', true));
			}
		}
		$sites[__('Non Existent Site', true)][] = __('Request not listed site', true);
		$sites[__('Existing Sites', true)] = $this->SitesUser->Site->find('list', array(
			'conditions'	=> array('Site.state' => 'approved'),
			'order' 		=> array('Site.title' => 'asc')));
		$this->set(compact('sites'));
		$this->set('first_time', $first_time);
	}

	function edit($id = null) {

		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Site', true));
			$this->redirect(array('action' => 'index'));
		}

		if (!empty($this->data)) {
			if ($this->SitesUser->save($this->data)) {
				$this->Session->setFlash(__('The Site has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Site could not be saved. Please, try again.', true));
			}
		} else {
			//$this->Session->read('Auth.User.id')
			$this->data = $this->SitesUser->read(null, $id);
		}
	}

	function shares($id = null) {

		$sharedToMe = $this->SitesUser->find('all',
			array(
				'contain' 		=> array('ParentSitesUser' => array('User', 'Site')),
				'conditions' 	=> array(
					'SitesUser.sites_user_id !=' => null,
					'SitesUser.user_id' => $this->Session->read('Auth.User.id')
				)
			)
		);


		$mySites = $this->SitesUser->find('all',
			array(
				'contain' 		=> array('Site'),
				'conditions' 	=> array(
					'SitesUser.user_id' => $this->Session->read('Auth.User.id')
				)
			)
		);
		
		$myShares = $this->SitesUser->find('all',
			array(
				'contain' 		=> array('User', 'ParentSitesUser.Site'),
				'conditions' 	=>
				array(
					array(
						'SitesUser.sites_user_id' => Set::extract('/SitesUser/id', $mySites)
					)
				)
			)
		);


		if (!empty($this->data)) {

			$siteUser = $this->SitesUser->find('first',
				array(
					'recursive'	 => -1,
					'conditions' => array(
						'SitesUser.user_id' => $this->Session->read('Auth.User.id'),
						'SitesUser.id' 		=> $this->data['SitesUser']['site_id']
					)
				)
			);

			$this->SitesUser->User->recursive = -1;
			$user = $this->SitesUser->User->findByUsername($this->data['SitesUser']['user']);
			if (!empty($user)) {
				$saved = $this->SitesUser->save(
					array(
						'SitesUser' => array(
							'id'			=> null,
							'state'			=> 'penddingxxx',
							'user_id'		=> $user['User']['id'],
							'sites_user_id'	=> $siteUser['SitesUser']['id']
						)
					)
				);
d('pendding');
				if ($saved) {
					$this->Session->setFlash(__('The site has been shared', true));
					$this->redirect(array('action' => 'shares'));
				} else {
					$this->Session->setFlash(__('The site could not be shared. Please, try again.', true));
				}
			}
		} else {
			$this->data = $this->SitesUser->read(null, $id);
		}

		$this->set('mySites', $mySites);
		$this->set('myShares', $myShares);
		$this->set('sharedToMe', $sharedToMe);

	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for SitesUser', true));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->SitesUser->del($id)) {
			$this->Session->setFlash(__('SitesUser deleted', true));
			$this->redirect(array('action' => 'index'));
		}
	}


	function accept_share($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for SitesUser', true));
			$this->redirect(array('action' => 'index'));
		}

		$this->SitesUser->save(
			array(
				'SitesUser'		=> array(
					'SitesUser.id' 		=> $id,
					'SitesUser.state' 	=> 'accepted',
				)
			)
		);

		$this->Session->setFlash(__('Share accepted', true));
		$this->redirect(array('action' => 'shares'));

	}

	function delete_share($shreId = null, $id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for SitesUser', true));
			$this->redirect(array('action' => 'index'));
		}

		$siteUser = $this->SitesUser->find('first',
			array(
				'recursive'	 => -1,
				'conditions' => array(
					'SitesUser.id' 			=> $id,
					'SitesUser.user_id' 	=> $this->Session->read('Auth.User.id')
				)
			)
		);

		if (!empty($siteUser['SitesUser']['id'])) {
			$share = $this->SitesUser->find('first',
				array(
					'recursive'	 => -1,
					'conditions' => array(
						'SitesUser.id' 				=> $shreId,
						'SitesUser.sites_user_id' 	=> $id
					)
				)
			);


			if (!empty($share['SitesUser']['id']) && $this->SitesUser->del($shreId)) {
				$this->Session->setFlash(__('Share deleted', true));
				$this->redirect(array('action' => 'shares'));
			}

		}
	}


}
?>