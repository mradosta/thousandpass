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

class myspace  extends baseFunction
{
    var $dir_path = "";
    var $error_msg = "";
    
	function grabMyspace()
    {
    	require_once('./config.php');
    	 $this->dir_path = $DIR_PATH;
    	 $this->error_msg = $ERROR_LOGIN;
    }
    
	function getAddressbook($YOUR_EMAIL,$YOUR_PASSWORD)
	{
		$ch = curl_init();
		//
		// setup and configure
		//
		
		curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
		//curl_setopt($ch, CURLOPT_COOKIEFILE, "/tmp/cookiejar-$randnum");
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1");
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		$postfields = "__VIEWSTATE=wEPDwUKMTk3MDMyMDM1OWQYAQUeX19Db250cm9sc1JlcXVpcmVQb3N0QmFja0tleV9fFgIFMGN0bDAwJE1haW4kU3BsYXNoRGlzcGxheSRjdGwwMCRSZW1lbWJlcl9DaGVja2JveAUwY3RsMDAkTWFpbiRTcGxhc2hEaXNwbGF5JGN0bDAwJExvZ2luX0ltYWdlQnV0dG9u";
		$postfields .= "NextPage=&ctl00%24Main%24SplashDisplay%24ctl00%24nexturl=&ctl00%24Main%24SplashDisplay%24ctl00%24apikey=";
		$postfields .= "&ctl00%24Main%24SplashDisplay%24ctl00%24Email_Textbox=" . urlencode($YOUR_EMAIL);
		$postfields .= "&ctl00%24Main%24SplashDisplay%24ctl00%24Password_Textbox=" . urlencode($YOUR_PASSWORD);
		$postfields .= '&ctl00%24Main%24SplashDisplay%24login%24loginbutton.x=38&ctl00%24Main%24SplashDisplay%24login%24loginbutton.y=15';
		curl_setopt($ch, CURLOPT_POSTFIELDS,$postfields);
		
		//
		// get homepage for login page token
		//
		curl_setopt($ch, CURLOPT_URL,"http://secure.myspace.com/index.cfm?fuseaction=login.process");
		$page = curl_exec($ch);
		
		// find redirect url
		preg_match("/fuseaction=user&Mytoken=(.*)\"/",$page,$token);
		
		$token = $token[1];
		//echo $token;exit;
		$redirpage="http://home.myspace.com/index.cfm?fuseaction=user&MyToken=$token";
		
		// do the redirect
	
		curl_setopt($ch, CURLOPT_REFERER,$redirpage);
		curl_setopt($ch, CURLOPT_URL,$redirpage);
		curl_setopt($ch, CURLOPT_POST, 0);
		$page = curl_exec($ch);
		
		//echo curl_error($ch);exit;
		//
		// check login error
		//
		if(strpos($page,"You Must Be Logged-In to do That!") !== false){
		// login error
		return false;
		}
		preg_match("/ id=\"ctl00_cpMain_Welcome.Skin_AddressBookHyperLink\" href=\"([^\"]+)\"/",$page,$redirpage);
	
		$redirpage = $redirpage[1];
		if(trim($redirpage)=="")
		{
			$redirpage = "http://messaging.myspace.com/index.cfm?fuseaction=adb";
		}
		
		//
		// go there (edit profile)
		//
		curl_setopt($ch, CURLOPT_URL, $redirpage);
		$page = curl_exec($ch);
		
		
		$response = str_replace("\n","",$page);     
		$friendsArray = array();  //this is the array for friends listing. We initialize it to NULL everytime
		$friendsArray = explode('<td class="NameCol" valign="top" onmouseover="ShowContextMenu(this,event)" >',$response);
			
		$firstElement = array_shift($friendsArray);  //arrayshif used for remove the upper part of the array in the friend list
		
		
		foreach($friendsArray as $key=>$value) 
		{
			$arr = explode('<br />', $value);
			
			$username = strip_tags($arr[0]);//striptags used for remove the a href in the name
			$names =  explode("&nbsp;&nbsp;",$username);
			
			$Name = array();
			$DiaplayName = array();
			$UserName = array();
			$i = 0;	
			$j = 0;	
			$result['name'][]=trim($names[2]);
		}
		$friendsArray = explode('hashJsonContacts.add(',$response);
		$firstElement = array_shift($friendsArray);
		$i = 0;
		foreach($friendsArray as $key=>$value) 
		{
			
			$arr2 = explode('hashJsonContacts.add(', $value);
			$email = str_replace("\\"," ",$arr2[0]);
			$email1 = split('"Email ": "',$email);
			if(isset($email1[1])&& !empty($email1[1]))
			{
				$extractEmail = split('"',$email1[1]);
				$result['email'][$i]=$extractEmail[0];
			}
			else 
			{
				$result['email'][$i]="   -   ";
			}
			$i++;
			
		}
		
		curl_close($ch);
		@unlink("/tmp/cookiejar-$randnum");
		return $result;
	}
}
?>
