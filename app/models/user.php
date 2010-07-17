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
		),
		'country' 	=> 	array(
			'not_empty'	=> array(
				'rule' 		=> 'notEmpty',
				'message' 	=> 'Must select the Country.',
			),
		),
		'sex' 	=> 	array(
			'not_empty'	=> array(
				'rule' 		=> 'notEmpty',
				'message' 	=> 'Must select the Sex.',
			),
		),/*
		'birthdate' => 	array(
			'valid'	=> array(
				'rule' 		=> 'date',
				'message' 	=> 'Must select the Birth Date.',
			),
		),*/
		'username' 	=> 	array(
			'valid' 	=> array(
				'rule' 		=> 'email',
				'message' 	=> 'Your email is not valid',
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
/*
		'email'	=> array(
			'valid' 	=> array(
				'rule' 		=> 'email',
				'message' 	=> 'Your email is not valid',
			),
		),
*/
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
		return (Security::hash($data['repassword'], null, true) == $this->data[$this->name][$field]);
	}

	/**
	 * Take care of birthdate when saving.
	 */
	function beforeSave($options = array()) {
		if (!empty($this->data['User']['birthdate']['year']) && !empty($this->data['User']['birthdate']['month']) && !empty($this->data['User']['birthdate']['day'])) {
			$tmp = $this->data['User']['birthdate']['year'] . '-' . $this->data['User']['birthdate']['month'] . '-' . $this->data['User']['birthdate']['day'];
			$this->data['User']['birthdate'] = null;
			$this->data['User']['birthdate'] = $tmp;
		}
		return parent::beforeSave($options);
	}

}
?>