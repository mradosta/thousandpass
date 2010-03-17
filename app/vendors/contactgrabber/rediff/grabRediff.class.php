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


class rediff extends baseFunction
{
	
  var $dir_path = "";
  var $error_msg = "";
  var $fileName = "";
  var $total = "";
  
  function grabRediff()
  {
  	     require_once('./config.php');
    	 $this->dir_path = $DIR_PATH;
    	 $this->error_msg = $ERROR_LOGIN;
  }
  function getAddressbook($login,$password)
  {
		    $ch = curl_init();
		    curl_setopt($ch, CURLOPT_URL, "http://mail.rediff.com/cgi-bin/login.cgi");
		    $postString = "login=$login&passwd=$password&submit=GO&FormName=existing";
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
		    curl_setopt($ch, CURLOPT_REFERER, true);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($ch, CURLOPT_HEADER, true);
		    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
		   $res = curl_exec($ch);
		    $myarr = explode("><U>Go to Inbox",$res);
		    $myarr = explode('HREF=&quot;',htmlentities($myarr[0]));
		    
		   	$url = str_replace('&quot;','',$myarr[2]);
		
		 	
		 	////////////////////////////////////////////GET INBOX ///////////////////////////////////
		
			$postData = explode("?",$url);
		 	$strPostData = $postData[1];
		  	$addUrl = explode("http://",$url);
		  	$addUrl = explode("/",$addUrl[1]);
		 	$addPagingUrl = "http://".$addUrl[0];
		 	preg_match_all('/Inbox&amp;(.*?)&amp;SrtFld/s',$strPostData,$addPost);
		 	$addressPost = $addPost[1][0];
		 	
		  	$addressUrl = "http://".$addUrl[0]."/bn/address.cgi?".$addressPost;
		  
		  	
		  
		 	curl_setopt($ch, CURLOPT_URL, $url);
		  	$postString = $strPostData;
		  	$postString .= "login=$login&passwd=$password&submit=GO&FormName=existing";
		  
		    curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
		    curl_setopt($ch, CURLOPT_REFERER, true);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($ch, CURLOPT_HEADER, true);
		    curl_setopt($ch, CURLOPT_COOKIEFILE,"cookie.txt");
		    curl_setopt($ch, CURLOPT_COOKIEJAR,"cookie.txt");
		    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		   	$res1=curl_exec($ch);
		   	 	
		    if(strpos($res1,'Error.') !== false)
		    {
		    	ob_end_flush();
		    	return false;
		    }
		 
		
			do
			{
				$last='';
				
		
		       ////////////////// START OF ADDRESS BOOK ///////////////////////////////////
			
			    curl_setopt($ch, CURLOPT_URL, $addressUrl);
			    $postString="$addressPost";
			    curl_setopt($ch, CURLOPT_POST, 1);
			    curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
			    curl_setopt($ch, CURLOPT_REFERER, true);
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			    curl_setopt($ch, CURLOPT_HEADER, true);
			    curl_setopt($ch, CURLOPT_COOKIEFILE,  "cookie.txt");
			    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			     
			    $res2=curl_exec($ch);
				preg_match('/a HREF=".*Next/',$res2,$match1);
				$match1=str_replace('a HREF="',$addPagingUrl,$match1[0]);
				$match1=str_replace('">Next','',$match1);
			
				$last=$match1;
				$addressUrl=$last;
				$first=stripos($res2,'<INPUT TYPE=hidden NAME=tempnicks VALUE="">');
			    $first1=substr($res2,$first);
			    $sList2 = explode("</TD>",$first1);
			    //////////////////////////////////Display of contents ///////////////////////////////////////
		            
			  	  for ($i=0; $i < count($sList2); $i++)
				  {       
                                              
						$sList3 = explode("<TD class=sb2>&nbsp;&nbsp;", $sList2[$i]);
                                   
			            if ($sList3[1]!="")
			            {
                                        
				     		$totalRecords= $totalRecords +1;
			              	$sList3[1]=str_replace("\n","",$sList3[1]);
			                $result['name'][]=$sList3[1];
			            }
			          
			            if (strpos($sList3[0],"@") && !strpos($sList3[0],"<input type=checkbox") && !strpos($sList3[0],"<TABLE") && $sList3)
			            {
			              	$sList3[0]=str_replace(array("<TD class=sb2>","\n"),"",$sList3[0]);
			           		$result['email'][]=$sList3[0];
			            }
			 	  }  
                            
		   }while($last!='');
		   
		  echo "</table>";
		  
                  return $result;
	 
     }
}

?>

