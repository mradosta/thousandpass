<?php

/**
  * Contact Grabber
  * Version 0.4
  * Released 8th January, 2008
  * Author: Magnet Technologies, vishal.kothari@magnettechnologies.com
  * Credits: Binish Philip, Jaldip Upadhyay, Jatin Dwivedi, Jignesh Patel, Kajal Goziya, Mayur Sharma, Nimesh Shah, Pravin Shukla, Syed Haider, Twinkle Panchal
  * Copyright (C) 2008

  * This program is free software; you can redistribute it and/or
  * modify it under the terms of the GNU General Public License
  * as published by the Free Software Foundation; either version 2
  * of the License, or (at your option) any later version.

  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  * GNU General Public License for more details.

  * You should have received a copy of the GNU General Public License
  * along with this program; if not, write to the Free Software
  * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
  **/

    
class orkut extends baseFunction
{
   var $dir_path = "";
   var $error_msg = "";
   var $fileName ="";

   function grabOrkut()
   {
         require_once('./config.php');
    	 $this->dir_path = $DIR_PATH;
    	 $this->error_msg = $ERROR_LOGIN;
   }
   
   function getAddressbook($YOUR_EMAIL,$YOUR_PASSWORD)
   {
  
	  #the globals will be updated/used in the read_header function
		global $location;
		global $cookiearr;
		global $ch;
	
	  #initialize the curl session
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"http://www.orkut.com/Home.aspx");	
		curl_setopt($ch, CURLOPT_REFERER, "");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'read_header'));	
		curl_setopt($ch, CURLOPT_HEADER, 1);
		
	  #get the html from gmail.com
	  	$html = curl_exec($ch);
	
		$matches = array();
		$actionarr = array();
		
		$action = "https://www.google.com/accounts/ServiceLoginAuth";
	
	  #parse the login form:
	  #parse all the hidden elements of the form
		preg_match_all('/<input type\="hidden" name\="([^"]+)".*?value\="([^"]*)"[^>]*>/si', $html, $matches);
		$values = $matches[2];
		$params = "";
		
		$i=0;
		foreach ($matches[1] as $name)
		{
		  $params .= "$name=" . urlencode($values[$i]) . "&";
		  ++$i;
		}
	
		$login = urlencode($YOUR_EMAIL);
		$password = urlencode($YOUR_PASSWORD);
	  
	  #submit the login form:
		curl_setopt($ch, CURLOPT_URL,$action);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params ."Email=$login&Passwd=$password&PersistentCookie=");
		curl_setopt($ch, CURLOPT_HEADER, 1);
		$html = curl_exec($ch);
			  
	    if (preg_match('/url=([^"]*)/', $html, $actionarr)!=0)
		{
			$location = $actionarr[1];
		}
		else
		{
			return 1;
		}
		
		$location = str_replace("&quot;", '', $location);
		$location = str_replace("&amp;", '&', $location);
		$location = trim ($location,"'\"");
		$fp = fopen("cookie.txt", "w+");  
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_URL, $location);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$h = curl_exec($ch);
		$ork_cookie = explode("orkut_state=",$h);
		$orkut_cookie = split(";",$ork_cookie[1]);
		$orkut_state = "orkut_state=".$orkut_cookie[0];
	
		$handle = fopen($filename, "w+");
		fwrite($handle,"");
		
		$location = "http://www.orkut.com/Friends.aspx";
		#follow the location specified after login
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_URL, "$location");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_COOKIE,$orkut_state);
			
		$html = curl_exec($ch);
	
		$regexp = "showing <B>[^<]*<\/b> of <b>(.*?)<\/b>";  
		preg_match_all("/$regexp/s", $html, $matches);
		$noOfContacts = $matches[1][0];
	
		$noOfPages = ceil(($noOfContacts / 20));//find out the no of pages of friends
	
		for ($i = 1 ; $i <= $noOfPages ; $i++)
		{
	                    
			$friendsPage = "http://www.orkut.com/Friends.aspx?show=all&pno=$i";
			$html = "";
			
			$ch6 = "";
			$ch6 = curl_init();
			curl_setopt($ch6, CURLOPT_URL, $friendsPage);
			
			curl_setopt($ch6, CURLOPT_REFERER, true);
			curl_setopt($ch6, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch6, CURLOPT_HEADER, true);
			curl_setopt($ch6, CURLOPT_COOKIE,  $orkut_state);
						
			$html = curl_exec($ch6);
			
			$html = str_replace("\n","",$html);     
			$friendsArray = array();  //this is the array for friends listing. We initialize it to NULL everytime
			$friendsArray = explode('<h3 class="smller">',$html);
			$firstElement = array_shift($friendsArray);  //arrayshif used for remove the upper part of the array in the friend list
	                            
			foreach($friendsArray as $key=>$value) 
			{
				$arr = explode('</h3>', $value);
	
				$username = strip_tags($arr[0]);//striptags used for remove the a href in the name
				
				$emailE = explode('<div class="nor">', $value);
				$emailE = explode('<br>', $emailE[1]);
				
				$emails = $emailE[0];	 
				$domain = strstr($emails,"@");
				
				if(isset($domain) && !empty($domain))
				{
					$result['name'][]=$username; 
		            $result['email'][]=$emails;
				}
			}
		}
		return	$result;
	}
}
?>
