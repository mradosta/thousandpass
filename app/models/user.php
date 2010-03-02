<?php
class User extends AppModel {

	var $name = 'User';


	var $validate = array(
		'username' 	=> array(
			'alphanumeric' => array(
				'rule' 		=> 'alphaNumeric',
				'message' 	=> 'Only the letters A-z and digits 0-9 are allowed',
			),
			'length' => array(
				'rule' 		=> array('between', 4, 20),
				'message' 	=> "Your username must be between 4 and 20 characters long",
			),
		),
		'passwrd' 	=> array(
			'rule' 		=> array('minLength', 6),
			'message' 	=> 'Your password must be at least 6 characters long',
		),
		'email' 	=> 'email',
	);


	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $hasAndBelongsToMany = array(
		'Site' => array(
			'className' => 'Site',
			'joinTable' => 'sites_users',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'site_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);

}
?>