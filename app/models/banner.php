<?php
class Banner extends AppModel {

	var $name = 'Banner';



	function getBannersForLayout() {

		$banners['top'] = $this->find('all',
			array(
				'conditions' 	=> array('Banner.location' => 'top'),
				'order'			=> 'rand()',
				'limit'			=> 1
			)
		);

		$banners['bottom'] = $this->find('all',
			array(
				'conditions' 	=> array('Banner.location' => 'bottom'),
				'order'			=> 'rand()',
				'limit'			=> 2
			)
		);

		return $banners;
	}


}

?>