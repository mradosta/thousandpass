<?php

/**
  * Contact Grabber
  * Version 0.4
  * Released 8th January, 2008
  * Author: Magnet Technologies, vishal.kothari@magnettechnologies.com
  * Credits: Jatin Dwivedi, Jignesh Patel, Kajal Goziya, Nimesh Shah, Twinkle Panchal
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

	class baseFunction
	{
		/**
		 * Returns an array that contains the filtered $subject according to the giving $pattern
		 * @param $pattern, the string pattern that we'll filter upon
		 * @param $subject, the content that we shall filter
		 * @return Array, an array that contains the filtered content
		 * Used in AOL and Lycos. 
		 */
		function _parseContent( $pattern, $subject ) 
		{
			$array = array();
			preg_match_all( $pattern, $subject, $array );
			return $array;
		}

		/**
		 * Remove the target file
		 * @param $fileName, the full path and name of the target file to be removed
		 * @return VOID, nothing to be returned
		 * Used in AOL and Lycos. 
		 */
		function _rmFile( $fileName ) 
		{
			@unlink( $fileName );
		}
				
		#read_header is essential as it processes all cookies and keeps track of the current location url
		#leave unchanged, include it with getAddressbook
		# Used in GMail,Orkut
		function read_header($ch, $string)
		{
		   global $location;
		   global $cookiearr;
		   global $ch;
		   
		   $length = strlen($string);
		   if(!strncmp($string, "Location:", 9))
		   {
		     $location = trim(substr($string, 9, -1));
		   }
		   if(!strncmp($string, "Set-Cookie:", 11))
		   {
		     $cookiestr = trim(substr($string, 11, -1));
		     $cookie = explode(';', $cookiestr);
		     $cookie = explode('=', $cookie[0]);
		     $cookiename = trim(array_shift($cookie)); 
		     $cookiearr[$cookiename] = trim(implode('=', $cookie));
		   }
		   $cookie = "";
		   if(trim($string) == "" && !empty($cookiearr)) 
		   {
		     foreach ($cookiearr as $key=>$value)
		     {
		       $cookie .= "$key=$value; ";
		     }
		     curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		   }
		
		   return $length;
		}
		
		#function to trim the whitespace around names and email addresses
		#used by getAddressbook when parsing the csv file
		# Used in GMail
		function trimvals($val)
		{
		 return trim ($val, "\" \n");
		}

		# Used in GMail,Rediff
	    function splitPage($response,$header,$body)
	    {
	    	$totalLength=strlen($response);
	        $pos=stripos($response,"<html>");
	        $header = substr($response,0,$pos);
	        $body =substr($response,$pos,$totalLength-1);
	        $body=str_replace("\n","",$body);
	        $body=str_replace("\r","",$body);
	        $body = str_replace(" ","",$body);
	    }
	    
	    # Used in AOL
	    function getHeader($response)
	    {
	    	$totalLength=strlen($response);
			$pos=strpos($response,"<html");
	        $header = substr($response,0,$pos);
	        return $header;
	    }
	    
	    # Used in GMail,Indiatimes,Linkedin,Rediff
	    function getCookies($header)
	    { 
	    	$cookies=array();
	        $cookie=""; 
	        $returnar=explode("\r\n",$header);
	        for($ind=0;$ind<count($returnar);$ind++) 
	        {
	        	if(ereg("Set-Cookie: ",$returnar[$ind]) || ereg("Cookies ",$returnar[$ind])) 
	           	{
	            	$cookie=str_replace("Set-Cookie: ","",$returnar[$ind]);
	                $cookie=explode(";",$cookie);
	                $cookies[trim($cookie[0])]=trim($cookie[0]);
	            }
			}
	            
	        $cookie=array();
	        foreach ($cookies as $key=>$value)
	        {
	        	array_push($cookie,"$value");
			}
	        $cookie=implode(";",$cookie);
	        return $cookie; 
	    }       
	    
	    # Used in GMail,Rediff
	    function getLocation($header)
	    {
	    	$returnar=explode("\r\n",$header);
	        for($ind=0;$ind<count($returnar);$ind++) 
	        {
	        	if(ereg("Location: ",$returnar[$ind])) 
	            {
					$location=str_replace("Location: ","",$returnar[$ind]);
	                $location = trim($location);
	                break;
	            }

	            $this->splitPage($response, &$header, &$body);
	            $cookies_phase1 =$this->getCookies($header);
	        }
	        return $location;
	    }
	    
	    # Used in Rediff
	     function getSocket($host,$service_url,$method,$port, $fakeProxy, $cookie='',$postData='',$referer='') 
		 {
		        $header  = "$method $service_url HTTP/1.0\r\n";
		        $header .= "Host: $host\r\n";
		
		        if($referer){
		                $header .= "Referer: $referer\r\n";
		        }
		        if($cookie){
		                $header.="Cookie: ".$cookie.";\r\n";
		        }
		        $header .="User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1) Gecko/20061010 Firefox/2.0\r\n";
		        $header .="Content-type: application/x-www-form-urlencoded\r\n";
		        $header .="Content-length: ".strlen($postData)."\r\n";
		        $header .="Proxy-Connection: Keep-Alive\r\n";
		        $header .="\r\n";
		        if($port==443) {
		             $fp = pfsockopen("ssl://".$host, $port, &$errno, &$errstr);
		        }
		        else {
		             $fp = pfsockopen($host,$port, &$errno, &$errstr);
		        }
		        $response="";
		
		         if(!$fp) {
		            echo "not read"."<br>";
		         } 
		         else {
		           fwrite($fp, $header.$postData);
		           while (!feof($fp)) {
		                 $response .= @fread($fp, 200);
		            }
		           fclose($fp);
		        }
			
		  return $response;
		}
	
	}

?>
