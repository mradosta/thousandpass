<?php
class GrabYahoo {

	function getAddressbook($login, $password) {

		$return = array();

        //  Create URL
        $url = "https://login.yahoo.com/config/login?";
        $query_string = ".tries=2&.src=ym&.md5=&.hash=&.js=&.last=&promo=&.intl=us&.bypass=";
        $query_string .= "&.partner=&.u=4eo6isd23l8r3&.v=0&.challenge=gsMsEcoZP7km3N3NeI4mX";
        $query_string .= "kGB7zMV&.yplus=&.emailCode=&pkg=&stepid=&.ev=&hasMsgr=1&.chkP=Y&.";
        $query_string .= "done=http%3A%2F%2Fmail.yahoo.com&login=$login&passwd=$password";
        $url_login = $url . $query_string;
        //  End Create URL
   
        //  Execute Curl For Login
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url_login);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookie.txt');
        curl_setopt($ch, CURLOPT_HEADER , 1);
        ob_start();
        $response = curl_exec ($ch);
        ob_end_clean();
        curl_close ($ch);
        unset($ch);
        //  End Execute Curl For Login
   
        //  Call Address Book Page Through Curl
        $url_addressbook = "http://address.yahoo.com/yab/us";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/tmp/cookie.txt");
        curl_setopt($ch, CURLOPT_HEADER , 1);
        curl_setopt($ch, CURLOPT_URL, $url_addressbook);
        $result = curl_exec ($ch);
        curl_close ($ch);
        unset($ch);
        //  End Call Address Book Page Through Curl
        foreach (explode(',', trim(array_shift(explode('var InitialBucket', array_pop(explode('InitialContacts =', $result)))))) as $v) {
			list($key, $value) = explode(':', str_replace('"', '', $v));
			if ($key == 'contactName') {
				$contactName = $value;
			} elseif ($key == 'email') {
				$return[$contactName] = $value;
			}
        }
	    return $return;
	}

}
?>