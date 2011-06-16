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

		$banners['bottom_small'] = $this->find('all',
			array(
				'conditions' 	=> array('Banner.location' => 'bottom_small'),
				'order'			=> 'rand()',
				'limit'			=> 3
			)
		);

		$banners['bottom_big'] = $this->find('all',
			array(
				'conditions' 	=> array('Banner.location' => 'bottom_big'),
				'order'			=> 'rand()',
				'limit'			=> 3
			)
		);

		return $banners;
	}


}

?>