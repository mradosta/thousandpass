<?php
class User extends AppModel {

	var $name = 'User';


	var $validate = array(
		'name' 	=> 	array(
			'not_empty'	=> array(
				'rule' 		=> 'notEmpty',
				'message' 	=> 'Must enter the Username.',
			),
		),
		'lastname' 	=> 	array(
			'not_empty'	=> array(
				'rule' 		=> 'notEmpty',
				'message' 	=> 'Must enter the Lastname.',
			),
		),/*
		'birthdate' => 	array(
			'valid'	=> array(
				'rule' 		=> 'date',
				'message' 	=> 'Must select the Birth Date.',
			),
		),*/
		'username' 	=> 	array(
			'unique'	=> array(
				'rule' 		=> 'isUnique',
				'message' 	=> 'This username has already been taken.',
			),
			'alphanumeric' => array(
				'rule' 		=> 'alphaNumeric',
				'message' 	=> 'Only the letters A-z and digits 0-9 are allowed',
			),
			'length' 	=> array(
				'rule' 		=> array('between', 4, 20),
				'message' 	=> 'Your username must be between 4 and 20 characters long',
			),
		),
		'passwrd' 		=> 	array(
			'length' => array(
				'rule' 		=> array('minLength', 6),
				'message' 	=> 'Your password must be at least 6 characters long',
			),
			'alphanumeric' => array(
				'rule' 		=> 'alphaNumeric',
				'message' 	=> 'Only the letters A-z and digits 0-9 are allowed',
			),
		),
		'repassword'	=> array(
			'repeated' 	=> array(
				'rule' 		=> array('compareData', 'password'),
				'message' 	=> 'Passwords do not match',
			),
		),
		'email'	=> array(
			'valid' 	=> array(
				'rule' 		=> 'email',
				'message' 	=> 'Your email is not valid',
			),
		),
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


	/**
	* Used to compare the two entered passwords are the same.
	*/
	function compareData($data, $field) {
		return ($data == $this->data[$this->name][$field]);
	}

}
?>