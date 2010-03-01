<?php 
/* SVN FILE: $Id$ */
/* SitesUser Test cases generated on: 2010-02-25 21:44:25 : 1267145065*/
App::import('Model', 'SitesUser');

class SitesUserTestCase extends CakeTestCase {
	var $SitesUser = null;
	var $fixtures = array('app.sites_user', 'app.site', 'app.user');

	function startTest() {
		$this->SitesUser =& ClassRegistry::init('SitesUser');
	}

	function testSitesUserInstance() {
		$this->assertTrue(is_a($this->SitesUser, 'SitesUser'));
	}

	function testSitesUserFind() {
		$this->SitesUser->recursive = -1;
		$results = $this->SitesUser->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('SitesUser' => array(
			'id'  => 1,
			'site_id'  => 1,
			'user_id'  => 1
		));
		$this->assertEqual($results, $expected);
	}
}
?>