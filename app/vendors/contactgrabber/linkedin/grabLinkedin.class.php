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

class linkedin extends baseFunction
{
	
	function getAddressbook($YOUR_EMAIL,$YOUR_PASSWORD)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://www.linkedin.com/secure/login');
		curl_setopt($ch, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);		
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt ($ch, CURLOPT_POST, 4);
		$postfields="session_key=".$YOUR_EMAIL."&session_password=".$YOUR_PASSWORD."&session_login=Sign+In&session_rikey=";
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_HEADER, 1);
		$fp = fopen("cookie.txt","w+");
		curl_setopt ($ch, CURLOPT_FILE, $fp);
		$res = curl_exec($ch);
		fclose($fp);
		
		$cook = file_get_contents("cookie.txt");		
		$cookies1 = explode("<html",$cook);
		$cookies = $this->getCookies($cookies1[0]);
	   
        curl_setopt($ch, CURLOPT_URL, 'http://www.linkedin.com/addressBookExport');
        curl_setopt($ch, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_COOKIE,$cookies);
        curl_setopt($ch,CURLOPT_POST,1);
        $fields = "outputType=outlook_express&exportNetwork=";
        curl_setopt($ch,CURLOPT_POSTFIELDS,$fields);
       	$fp = fopen("cookie.txt","w+");
		curl_setopt ($ch, CURLOPT_FILE, $fp);
	
		$res = curl_exec($ch);
		fclose($fp);
	
	 	$fileContentArr       = file("cookie.txt");
	      
	      // Sets the address book column headings
	      $abColumnHeadLine     = trim($fileContentArr[0]);
	      $abColumnHeadLine     = str_replace("\"", "", $abColumnHeadLine);
	      
	      // Sets the address book column headings into an array
	      $abColumnHeadArr      = explode(",", $abColumnHeadLine);
	      
	      // Unsets the heading line from the file content array
	      unset($fileContentArr[0]);
	      
	      foreach ($fileContentArr as $key => $value)
	      {
	        // Sets the address book list individually
	        $listColumnLine     = trim($value);
	        $listColumnLine     = str_replace("\"", "", $listColumnLine);
	        
	        // Sets the individual list into an array
	        $listColumnArr      = explode(",", $listColumnLine);
	        
	        // Iterates through each item of individual address in the list
	        foreach ($listColumnArr as $listColumnKey => $listColumnValue)
	        {
	          // Sets the column heading as key
	          $listKey          = $abColumnHeadArr[$listColumnKey];
	          
	          // Sets the value for the key respectively
	          $list_[$listKey]  = $listColumnValue;
	        }
	        
	        // Sets the address book list in an array
	    	$list[]             = $list_;
	    }
	      
	      
		$fp = fopen("cookie.txt","w+");
		fwrite($fp,"");
		fclose($fp);
		if(!empty($list))
		{
	      foreach($list as $lists)
	      {
	      	if($lists['E-mail Address'] != '')
	      	{
		      	$result['name'][] = $lists['First Name']." ".$lists['Last Name'];
		      	$result['email'][] = $lists['E-mail Address'];
	      	}
	      }
	      
		    
		}
		
		return $result;   
    }    
}

?>
