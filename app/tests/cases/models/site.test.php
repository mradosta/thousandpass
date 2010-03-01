<?php 
/* SVN FILE: $Id$ */
/* Site Test cases generated on: 2010-02-25 21:43:08 : 1267144988*/
App::import('Model', 'Site');

class SiteTestCase extends CakeTestCase {
	var $Site = null;
	var $fixtures = array('app.site');

	function startTest() {
		$this->Site =& ClassRegistry::init('Site');
	}

	function testSiteInstance() {
		$this->assertTrue(is_a($this->Site, 'Site'));
	}

	function testSiteFind() {
		$this->Site->recursive = -1;
		$results = $this->Site->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('Site' => array(
			'id'  => 1,
			'title'  => 'Lorem ipsum dolor sit amet',
			'description'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida,phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam,vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit,feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.'
		));
		$this->assertEqual($results, $expected);
	}
}
?>