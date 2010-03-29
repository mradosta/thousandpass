<?php
class SitesUsersController extends AppController {

	var $name = 'SitesUsers';



    function download_add_on($browser) {

		if ($browser == 'firefox') {
		} elseif ($browser == 'msie') {
		} elseif ($browser == 'chrome') {
		}

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

		$data = $this->SitesUser->Site->find('all', array(
			'conditions'	=> array('Site.title LIKE' => $this->params['url']['q'] . '%'),
			'fields'		=> array('title', 'id')));

		$this->set('data', $data);
	}


	function index() {

		$this->pageTitle = __('My sites at 1000Pass.com', true);

		$this->paginate['order'] = array('SitesUser.order' => 'asc');
		$this->paginate['contain'] = array('Site');
		$this->paginate['conditions'] = array('SitesUser.user_id' => $this->Session->read('Auth.User.id'));
		//$this->paginate['conditions'] = array('SitesUser.user_id' => 1);
		$this->set('sitesUsers', $this->paginate());
	}


	function add() {

		$this->pageTitle = __('Add new site to 1000Pass.com', true);

		if (!empty($this->data)) {

			if (!empty($this->data['SitesUser']['new_request'])) {
				$this->SitesUser->Site->save(
					array('Site' => array(
						'state'		=> 'pending',
						'login_url' => $this->data['SitesUser']['new_request']))
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