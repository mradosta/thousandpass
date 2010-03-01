<?php

class PagesController extends AppController {

	var $uses = array();

	function display() {
	}

	function xdisplay() {

//public function __construct($url,$followlocation = true,$timeOut = 30,$maxRedirecs = 4,$binaryTransfer = false,$includeHeader = false,$noBody = false)

		//$myCurl = new mycurl('http://www.gmail.com');
		$cc = new cURL();
		$html = $cc->get('http://www.gmail.com');

		$html = str_replace("\r", '', $html);
		$html = str_replace("\n", '', $html);
		//$html = str_replace('  ', ' ', $html);

//<input type="text" name="Email"  id="Email"  size="18" value=""        class='gaia le val'    />

		$x = str_replace('<input type="text" name="Email"  id="Email"  size="18" value=""        class=\'gaia le val\'    />', '<input type="text" class="gaia le val" value="juancarlosfelman@gmail.com" size="18" id="Email" name="Email">', $html);

$this->set('htmlCode', $x);

//$x = str_replace('Learn more', '<input type="text" class="gaia le val" value="juancarlosfelman@gmail.com" size="18" id="Email" name="Email">', $html, $c);
//$x = str_replace('Learn more', 'XXXXXXXXXX', $html, $c);
//d($c);
		//d($x);
		//d(array_pop(explode('<input type="text" class="gaia le val" value="" size="18" id="Email" name="Email">', $html)));
		//$cc->post('http://www.example.com','foo=bar');
	}

}

?>