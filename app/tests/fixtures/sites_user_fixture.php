<?php 
/* SVN FILE: $Id$ */
/* SitesUser Fixture generated on: 2010-02-25 21:44:25 : 1267145065*/

class SitesUserFixture extends CakeTestFixture {
	var $name = 'SitesUser';
	var $table = 'sites_users';
	var $fields = array(
		'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'site_id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'user_id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'key' => 'unique'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'user_id' => array('column' => 'user_id', 'unique' => 1), 'site_id' => array('column' => 'site_id', 'unique' => 0))
	);
	var $records = array(array(
		'id'  => 1,
		'site_id'  => 1,
		'user_id'  => 1
	));
}
?>