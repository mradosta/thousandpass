<?php 
/* SVN FILE: $Id$ */
/* SitesController Test cases generated on: 2010-02-25 21:45:59 : 1267145159*/
App::import('Controller', 'Sites');

class TestSites extends SitesController {
	var $autoRender = false;
}

class SitesControllerTest extends CakeTestCase {
	var $Sites = null;

	function startTest() {
		$this->Sites = new TestSites();
		$this->Sites->constructClasses();
	}

	function testSitesControllerInstance() {
		$this->assertTrue(is_a($this->Sites, 'SitesController'));
	}

	function endTest() {
		unset($this->Sites);
	}
}
?>