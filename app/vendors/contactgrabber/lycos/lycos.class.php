<?php

	/************************************************************************************************
	 * Lycos.com Contact List Grabber								*
	 * Version 1.0											*
	 * Released 1th June, 2007									*
	 * Author: Ma'moon Al-akash ( soosas@gmail.com )						*
	 *												*
	 * This program is free software; you can redistribute it and/or				*
	 * modify it under the terms of the GNU General Public License					*
	 * as published by the Free Software Foundation; either version 2				*
	 * of the License, or (at your option) any later version.					*
	 *												*
	 * This program is distributed in the hope that it will be useful,				*
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of				*
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the				*
	 * GNU General Public License for more details.							*
	 *												*
	 * You should have received a copy of the GNU General Public License				*
	 * along with this program; if not, write to the Free Software					*
	 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.		*
	 ************************************************************************************************/


	// where to put the cookie file ...
	define( 'CPATH', $_SERVER['DOCUMENT_ROOT'].'contactgrabber/csvUpload/' );

	/**
	 * This Class is used to grab the Contact List of a lycos.com account
	 */
	class grabLycos extends baseFunction
	{
		// Privates
		var $_username;
		var $_password;

		/**
		 * Constructor of the class to initialize the privates
		 * @param $username, the username for the lycos account
		 * @param $password, the password for the lycos account
		 * @return VOID, only used to initialize the privates
		 */
		function grabLycos( $username, $password ){

			if ( !empty( $username ) )
				$this->_username = $username;
			else
				die( 'Please provide your Lycos username' );
			if ( !empty( $password ) )
				$this->_password = $password;
			else
				die( 'Please provide your Lycos password' );
			
		}

		/**
		 * Returns username of the Lycos account ( $this->_username )
		 * @return String, $this->_username
		 */
		function _getUsername(){
			return $this->_username;
		}

		/**
		 * Returns password of the Lycos account ( $this->_password )
		 * @return String, $this->_password
		 */
		function _getPassword(){
			return $this->_password;
		}

		/**
		 * Grabs the Contact List from the Lycos account
		 * @return Array, an array that contains the grabbed contact list from Lycos.com
		 */
		function getContactList(){
			// login to lycos and authenticate the user ...
			$cookieFile	= CPATH.$this->_getUsername().'_lycos_cookiejar.txt';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://registration.lycos.com/login.php?m_PR=27");
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
			curl_setopt($ch, CURLOPT_COOKIEFILE,$cookieFile);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1");
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			$name = urlencode( $this->_getUsername() );
			$pass = urlencode( $this->_getPassword() );
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,"m_PR=27&m_CBURL=http%3A%2F%2Fmail.lycos.com&m_U=$name&m_P=$pass&login=Sign+In");
			curl_exec($ch);

		
			$url = "http://mail.lycos.com/lycos/addrbook/ExportAddr.lycos?ptype=act&fileType=EXPRESS";
			$postField = "ftype=EXPRESS";	
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_POST,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$postField);
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
