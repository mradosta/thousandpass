<?php
class SitesUsersController extends AppController {

	var $name = 'SitesUsers';



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

		$this->pageTitle = __('My sites at 1000Pass.com', true);

		$this->set('sitesUsers', $this->SitesUser->find('all', array(
			'order' 		=> array('SitesUser.order' => 'asc'),
			'contain' 		=> array('Site'),
			'conditions' 	=> array('SitesUser.user_id' => $this->Session->read('Auth.User.id'))))
			//'conditions' 	=> array('SitesUser.user_id' => 1)))
		);
	}


	function add($first_time = false) {

		$this->pageTitle = __('Add new site to 1000Pass.com', true);

		if (!empty($this->data)) {

			if ((empty($this->data['SitesUser']['site_id']) || in_array($this->data['SitesUser']['site_id'], array('No results', 'Sin resultados'))) && !empty($this->data['SitesUser']['autocomplete'])) {
				$this->SitesUser->Site->save(
					array('Site' => array(
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

		$this->pageTitle = __('Edit my site at 1000Pass.com', true);

		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid SitesUser', true));
			$this->redirect(array('action' => 'index'));
		}

		if (!empty($this->data)) {
			if ($this->SitesUser->save($this->data)) {
				$this->Session->setFlash(__('The SitesUser has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The SitesUser could not be saved. Please, try again.', true));
			}
		}

		if (empty($this->data)) {
			$this->data = $this->SitesUser->read(null, $id);
		}
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

}
?>