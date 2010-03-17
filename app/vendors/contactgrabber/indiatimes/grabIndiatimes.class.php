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

class indiatimes extends baseFunction
{
	function paging($response,$cookies,$rlink)
	{
		$paginglinks = explode('<a accesskey="n" href="/h/search?si=0&so=',$response);
		if(count($paginglinks)>1)
		{
			$nxtlink = explode('">',$paginglinks[1]);
			$ch1 = curl_init();
		
	
			curl_setopt($ch1,CURLOPT_URL,$rlink."/h/search?si=0&so=".$nxtlink[0]);
			curl_setopt($ch1,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch1, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
			curl_setopt($ch1,CURLOPT_HEADER,1);
			curl_setopt($ch1,CURLOPT_COOKIE,$cookies);
		 	curl_setopt($ch1, CURLOPT_FOLLOWLOCATION, 1);
			 	
			$response = curl_exec($ch1);
			curl_close($ch1);
			
			$this->getemails($response,$cookies,$rlink);
			$this->paging($response,$cookies,$rlink);
		}
		else 
		{
			return true;
		}
	}

	function getAddressbook($YOUR_EMAIL,$YOUR_PASSWORD)
	{
		global  $nameresult;
		global  $emailresult;
		global $rlink;
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,"http://integra.indiatimes.com/Times/Logon.aspx?ru=".urlencode('http://infinite.indiatimes.com/cgi-bin/gateway')."&IS=058f3c27-6793-41c7-a676-81e3f3594a5c&NS=email&HS=kSVLJ96CWWzEmTwPZa1LD6YR7NM=");
		$postFields = "op=login&login=".$YOUR_EMAIL."&passwd=".$YOUR_PASSWORD."&rememberme=&rememberPwd=&Sign+In=";
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$postFields);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch,CURLOPT_HEADER,1);
		curl_setopt($ch,CURLOPT_COOKIEJAR,"cookie.txt");
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$response = curl_exec($ch);
		$nxturl =split("Location: http://",$response);
		$nxturl =split("\/",$nxturl[1]);
	
		$rlink = $nxturl[0];
			
		$totalLength=strlen($response);
        $pos=stripos($response,"<html>");
        $header = substr($response,0,$pos);
        $body =substr($response,$pos,$totalLength-1);
        $body=str_replace("\n","",$body);
        $body=str_replace("\r","",$body);
        $body = str_replace(" ","",$body);
		$cookies = $this->getCookies($header);
		curl_close($ch);
	
		$ch1 = curl_init();
		
		curl_setopt($ch1,CURLOPT_URL,$rlink."/h/search?st=contact");
		curl_setopt($ch1,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch1, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch1,CURLOPT_HEADER,1);
		curl_setopt($ch1,CURLOPT_COOKIE,$cookies);
		curl_setopt($ch1, CURLOPT_FOLLOWLOCATION, 1);
		 	
		$response = curl_exec($ch1);
		
		curl_close($ch1);
		
		$this->getemails($response,$cookies,$rlink);
		$this->paging($response,$cookies,$rlink);
		global $emailresult;
		if(!empty($emailresult))
		{
			global $emailresult;
			foreach ($emailresult as $emailres)
			{
				$result['email'][]=$emailres; 
			}
		}
		global $nameresult;
		if(!empty($nameresult))
		{
			global $nameresult;
			foreach ($nameresult as $nameres)
			{
				$result['name'][]=$nameres; 
			}
		}
		$fp = fopen("cookie.txt","w+");
		fwrite($fp,"");
		fclose($fp);
		return $result;
	}
	function getemails($response,$cookies,$rlink)
	{
		$mynames = explode("<span style='padding:3px'>",$response);
			preg_match_all("/a href=\"(.*?)\"/s",$response,$mylinks);
			
			foreach($mylinks[1] as $mylink)
			{
				$temp = strstr($mylink,"&st=contact&id");
				if(!empty($temp))
				{
					$links[] = $rlink.$mylink;
				}
			}
			$email = split("<td class=\"contactOutput\">",$response);
			$email = split("<br/>",$email[1]);
			global $emailresult;
			$emailresult[]=trim($email[0]);
			
			
			if(!empty($mynames))
			{
				for($a=1;$a<count($mynames);$a++)
				{
					$mynames[$a] = strip_tags($mynames[$a]);
					$comname = split(",",$mynames[$a]);
					global  $nameresult;
					$nameresult[]= trim(trim($comname[0]))." ".trim($comname[1]);
					
				}
			}
			$j=1;
			for($i=1;$i<count($links);$i++)
			{
				$ch = "\$ch".$i;
				$ch = curl_init();
				curl_setopt($ch,CURLOPT_URL,$links[$i]);
				curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1");
				curl_setopt($ch,CURLOPT_HEADER,1);
				curl_setopt($ch,CURLOPT_COOKIE,$cookies);
				 	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				 	
				$response = curl_exec($ch);
				curl_close($ch);
				$extraemail = split("<td class=\"contactOutput\">",$response);
				$extraemail = split("<br/>",$extraemail[1]);
				$email[$j]=trim($extraemail[0]);
				global $emailresult;
				$emailresult[]= $email[$j];
				$j++;
			}
	}
}

?>
