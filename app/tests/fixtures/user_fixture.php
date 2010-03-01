<?php 
/* SVN FILE: $Id$ */
/* User Fixture generated on: 2010-02-25 21:42:49 : 1267144969*/

class UserFixture extends CakeTestFixture {
	var $name = 'User';
	var $table = 'users';
	var $fields = array(
		'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'username' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'password' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $records = array(array(
		'id'  => 1,
		'username'  => 'Lorem ipsum dolor sit amet',
		'password'  => 'Lorem ipsum dolor sit amet'
	));
}
?>