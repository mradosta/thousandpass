<?php
class SitesUser extends AppModel {

	var $name = 'SitesUser';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
		'Site' => array(
			'className' => 'Site',
			'foreignKey' => 'site_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);


	function afterFind($results, $primary = false) {
		foreach ($results as $k => $v) {
			if (!empty($v['Site'])) {
				if (empty($v['Site']['submit'])) {
					$replaces['##username##'] = $v['SitesUser']['username'];
					$replaces['##password##'] = $v['SitesUser']['password'];
					$results[$k]['Site']['login_url'] = str_replace(array_keys($replaces), $replaces, $v['Site']['login_url']);
				}
			}
		}
		return parent::afterFind($results, $primary);
	}


	/**
	* Prevent empty foreing key error.
	*/
	function beforeSave($options = array()) {
		if (empty($this->data[$this->name]['site_id'])) {
			unset($this->data[$this->name]['site_id']);
			$this->data[$this->name]['state'] = 'pending';
		} else {
			$this->data[$this->name]['state'] = 'confirmed';
		}
		return parent::beforeSave($options);
	}

}
?>