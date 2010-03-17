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

	ob_start();
	set_time_limit(0);
	$dir = 'csvUpload';
	$dp = opendir($dir) or die ('Fatal Error: ');
	while ($file = readdir($dp)) 
	{
		if ((eregi('.csv',$file)) && (filemtime($dir."/".$file)) < (strtotime('yesterday'))) 
		{
			$del=@unlink($dir."/".$file);
		}
	}
	if(isset($_POST['domain']) && !empty($_POST['domain']))
	{
		$usrdomain 	= $_POST['domain'];
	}
	?>
	<html>
	<head>
	<title>Contact Grabber</title>
	<style>
	body,td,div,select,a
	{
		#font-family:arial,sans-serif;
		#font-size:13px;
		
		font-family:Verdana, Arial, Helvetica, sans-serif;
		font-size:13px;
		color:#765E1B;
		#padding:35px 50px 0px 50px ;
	}
	</style>
	<script language="javascript">
	function checkEmpty(frm)
	{
		if (frm.username.value == "" || frm.password.value == "")
		{
			alert("Please enter username & password.");
			frm.username.focus();
			return false;
		}
		return true;
	}
	</script>
	</head>
	<body onLoad="document.loginForm.username.focus();">
	<form action="index.php" method="POST" onSubmit="return checkEmpty(this);" name="loginForm">
	<table border="0" align="center" cellpadding="2" cellspacing="0">
	  <tr>
		<td colspan="3" align="center">Enter login details to fetch your contacts</td>
	  </tr>
	  <tr>
	  	<td>Username</></td>
	  	<td><input type="text" name="username" value="<?php echo @$_POST['username']; ?>" /></td>
	    <td>	
	      <select name="domain" size="1">
			<option value="gmail.com" <?php if ($usrdomain=="gmail.com") echo selected; ?>>gmail</option>
			<option value="hotmail.com" <?php if ($usrdomain=="hotmail.com") echo selected; ?>>hotmail</option>
			<option value="rediff.com" <?php if ($usrdomain=="rediff.com") echo selected; ?>>rediff</option>		
			<option value="yahoo.com" <?php if ($usrdomain=="yahoo.com") echo selected; ?>>yahoo</option>
			<option value="orkut.com" <?php if ($usrdomain=="orkut.com") echo selected; ?>>orkut</option>			
			<option value="myspace.com" <?php if ($usrdomain=="myspace.com") echo selected; ?>>myspace</option>
			<option value="indiatimes.com" <?php if ($usrdomain=="indiatimes.com") echo selected; ?>>indiatimes</option>
			<option value="linkedin.com" <?php if ($usrdomain=="linkedin.com") echo selected; ?>>linkedin</option>
			<option value="aol.com" <?php if ($usrdomain=="aol.com") echo selected; ?>>aol</option>
			<option value="lycos.com" <?php if ($usrdomain=="lycos.com") echo selected; ?>>lycos</option>
		  </select>
	        </td>
	  </tr>
	  <tr>
	  	  <td>Password</td>
	      <td colspan="2"><input type="password" name="password" /></td>
	  </tr>
	  <tr>
	  	  <td colspan="3" align="center"><input type="submit" name="submit" value="Fetch My Contacts" /></td>
	  </tr>    
	  <tr>
	  	 <td colspan="3" align="center"><small>No details are stored</small></td>
	  </tr>    
	</table>
	</form>
	</body>
	</html>
	
	<?php
	
	
	
	if(isset($_POST['submit']) && !empty($_POST['submit'])) 
	{
		if(!extension_loaded(curl))
		{
			die('<p align="center"><font color="#FF0000">Curl is not installed on your server, Please contact to your server administrator.</font></p>');
		}	
	
		$YOUR_EMAIL		 = $_POST['username'];
		$YOUR_PASSWORD 	 = $_POST['password'];
		
		require("baseclass/baseclass.php");
		if($usrdomain=="aol.com")
	    {
		     require("aol/aol.class.php");
			 $obj = new grabAol($YOUR_EMAIL,$YOUR_PASSWORD);
	    }
	         
        if($usrdomain=="lycos.com")
        {
		     require("lycos/lycos.class.php");
			 $obj = new grabLycos($YOUR_EMAIL,$YOUR_PASSWORD);
        }
		
		if($usrdomain=="indiatimes.com")
	    {
		     require("indiatimes/grabIndiatimes.class.php");
			 $obj = new indiatimes();
	    }
	         
	    if($usrdomain=="linkedin.com")
	    {
		     require("linkedin/grabLinkedin.class.php");
			 $obj = new linkedin();
	    }
	         
		if($usrdomain=="rediff.com")
	    {
		     require("rediff/grabRediff.class.php");
			 $obj = new rediff();
        }
	
	    if($usrdomain=="gmail.com")
	    {
		     require("gmail/libgmailer.php");
			 $YOUR_EMAIL = $_POST['username']."@".$usrdomain;
			 $obj = new GMailer();
	    }
	
	    if($usrdomain=="orkut.com")
	    {
	         require("orkut/grabOrkut.class.php");
	         $obj = new orkut();
	    }
	
	    if($usrdomain=="myspace.com")
	    {
	         require("myspace/grabMyspace.class.php");
			 $obj = new myspace();
    	}
		
		if($usrdomain=="yahoo.com")
	    {
        	require("yahoo/class.GrabYahoo.php");
	 		$obj = new GrabYahoo();
				
	    }
	
		if($usrdomain=="hotmail.com")
	    {
        	require("hotmail/msn_contact_grab.class.php");
	 		$YOUR_EMAIL = $_POST['username']."@".$usrdomain;
	 		$obj = new hotmail();
	    }
		
		if($usrdomain=='aol.com' ||  $usrdomain=='lycos.com')
		{
		 	$contacts = $obj->getContactList();
		}
		else 
		{
			$contacts = $obj->getAddressbook($YOUR_EMAIL,$YOUR_PASSWORD);
		}
		$fp = fopen("cookie.txt","w+");
		fwrite($fp,"");				
		fclose($fp);
	 	if(!is_array($contacts))
	 	{
	 		die('<p align="center"><font color="#FF0000">No contacts found</font></p>');
	 	}
	 	$str="";
		if(is_array($contacts))
		{
			$totalRecords=0;
			$actualfile = $YOUR_EMAIL.time().".csv";
        	$fileName="csvUpload/".$actualfile;
        
			$handler= fopen($fileName,"a");
			fwrite($handler,"NAME".","."EMAIL"."\n");
		
			$total = sizeof($contacts['name']);
		
			//print the addressbook 
			$str.= "<table border='1'><tr><td align='center'><b>Name</b></td><td align='center'><b>Email Address</b></td></tr>";
			for ($i=0;$i< $total;$i++) 
			{
				$totalRecords = $totalRecords+1;
				$rep 		  = array("<br>","&nbsp;");
				
				$str.="<tr><td style='Font-Family:verdana;Font-Size:14'>".$contacts['name'][$i]."</td><td style='Font-Family:verdana;Font-Size:14'>".$contacts['email'][$i]."</td></tr>";
				$contacts['email'][$i] = str_replace($rep, "",$contacts['email'][$i]);
				$contacts['name'][$i]  = str_replace($rep, "",$contacts['name'][$i]);
				fwrite($handler,$contacts['name'][$i].",".$contacts['email'][$i]."\n");
			}
			$str.= "</table>";
			fclose($handler);
		}
		      
		echo "<a href='header.php?filename=$actualfile'><font color='blue'>Save contacts as a CSV file</font></a>&nbsp;&nbsp; You have total <font color='blue'>$totalRecords</font> contacts</p>";
		echo $str;				
}
?>
