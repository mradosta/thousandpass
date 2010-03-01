<?php 
/* SVN FILE: $Id$ */
/* SitesUsersController Test cases generated on: 2010-02-25 21:52:54 : 1267145574*/
App::import('Controller', 'SitesUsers');

class TestSitesUsers extends SitesUsersController {
	var $autoRender = false;
}

class SitesUsersControllerTest extends CakeTestCase {
	var $SitesUsers = null;

	function startTest() {
		$this->SitesUsers = new TestSitesUsers();
		$this->SitesUsers->constructClasses();
	}

	function testSitesUsersControllerInstance() {
		$this->assertTrue(is_a($this->SitesUsers, 'SitesUsersController'));
	}

	function endTest() {
		unset($this->SitesUsers);
	}
}
?>