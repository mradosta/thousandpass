<?php

    /************************************************************************************************
     * AOL.com Contact List Grabber                                                                 *
     * Version 1.0                                                                                  *
     * Released 1th June, 2007                                                                      *
     * Author: Ma'moon Al-akash ( soosas@gmail.com )                                                *
     *                                                                                              *
     * This program is free software; you can redistribute it and/or                                *
     * modify it under the terms of the GNU General Public License                                  *
     * as published by the Free Software Foundation; either version 2                               *
     * of the License, or (at your option) any later version.                                       *
     *                                                                                              *
     * This program is distributed in the hope that it will be useful,                              *
     * but WITHOUT ANY WARRANTY; without even the implied warranty of                               *
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                                *
     * GNU General Public License for more details.                                                 *
     *                                                                                              *
     * You should have received a copy of the GNU General Public License                            *
     * along with this program; if not, write to the Free Software                                  *
     * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.              *
     ************************************************************************************************/



    // where to put the cookie file ...
    define( 'CPATH', $_SERVER['DOCUMENT_ROOT'].'contactgrabber/csvUpload/' );

    class grabAol extends baseFunction 
    {

        var $_username;
        var $_password;

        /**
         * Constructor of the class to initialize the privates
         * @param $username, the username for the aol account
         * @param $password, the password for the aol account
         * @return VOID, only used to initialize the privates
         */
        function grabAol( $username, $password ) {

            $username = trim( $username );
            $password = trim( $password );
            if( empty( $username ) || empty( $password ) ) {
                die( 'Please fill all the fields' );
            }
            $this->_setUsername( $username );
            $this->_setPassword( $password );
        }

        /**
         * @param $username, the login name of the target AOL account
         * @return VOID, nothing to be returned, only initialize $this->_username
         */
        function _setUsername( $username ) {
            $this->_username = $username;
        }

        /**
         * @param $password, the password of the target AOL account
         * @return VOID, nothing to be returned, only initialize $this->_password
         */
        function _setPassword( $password ) {
            $this->_password = $password;
        }


        /**
         * Returns username of the AOL account ( $this->_username )
         * @return String, $this->_username
         */
        function _getUsername() {
            return $this->_username;
        }

        /**
         * Returns password of the AOL account ( $this->_password )
         * @return String, $this->_password
         */
        function _getPassword() {
            return $this->_password;
        }
        
        /**
         * Grabs the Contact List from the target AOL account
         * @return Array, an array that contains the imported contact list from aol.com
         */
        function getContactList() { 
            $cookieFile = CPATH.$this->_getUsername().'_aol_cookiejar.txt';
            //incase this user has a cookies file in our cookies directory then lets remove
            // it before starting the whole process or otherwise it won't work!!
            if( file_exists( CPATH.$this->_getUsername().'_aol_cookiejar.txt' ) ) {
                $this->_rmFile( CPATH.$this->_getUsername().'_aol_cookiejar.txt' );
            }
            $ch     = curl_init();
            // try to login using the access that has been granted from the user ...
            curl_setopt( $ch, CURLOPT_URL, 'http://my.screenname.aol.com/_cqr/login/login.psp?sitedomain=sns.webmail.aol.com&authLev=2&siteState=ver%3A2%7Cac%3AWS%7Cat%3ASNS%7Cld%3Awebmail.aol.com%7Cuv%3AAOL%7Clc%3Aen-us&lang=en&locale=us&seamless=novl' );
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1');
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
            curl_setopt($ch, CURLOPT_COOKIEFILE,$cookieFile);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            $return     = curl_exec( $ch );
        
            $data       = $this->_parseContent( '/<form name="AOLLoginForm"(.*?)<\/form>/s', $return );
            $hidden     = explode( '<input type="hidden"', $data[1][0] );
            unset( $hidden[0] ); //remove the action since we don't really need it here
            curl_setopt($ch, CURLOPT_URL, 'https://my.screenname.aol.com/_cqr/login/login.psp');
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
            $uname      = 'loginId='.urlencode( $this->_getUsername() );
            $upass      = 'password='.urlencode( $this->_getPassword() );
            $postFields     = '';
            foreach( $hidden as $field ) {
                $field      = trim( $field );
                $tmp        = explode( ' ', $field );
                $removal    = array( 'name=', 'value=', '"', '>', '</div' );
                $tmp[0]     = str_replace( $removal, '', $tmp[0] );
                $tmp[1]     = str_replace( $removal, '', $tmp[1] );
                $postFields     .= trim($tmp[0].'='.$tmp[1]).'&';
                unset( $tmp );
            }
            $postFields .= $uname.'&'.$upass;
            curl_setopt($ch, CURLOPT_POST, 2);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            $return         = curl_exec( $ch );
            $logIn          = $this->_parseContent( '/LoginSuccess.aspx(.*?)\'/s', $return );
            $loginParts         = explode( '&', $logIn[1][0] );
            $targetSuccessUrl   = '';
            foreach( $loginParts as $part ) {
                $tmp = explode( '=', trim( $part ) );
                if( $tmp[0] == 'siteState' ) {
                    $tmp[1] = urlencode( $tmp[1] );
                }
                $targetSuccessUrl .= $tmp[0].'='.$tmp[1].'&';
            }
            $targetSuccessUrl   = substr_replace( $targetSuccessUrl , '', -1 );
            $loginSuccessUrl    = 'http://webmail.aol.com/_cqr/LoginSuccess.aspx'.$targetSuccessUrl;
             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_URL, $loginSuccessUrl);
            curl_setopt($ch, CURLOPT_REFERER, $loginSuccessUrl );
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
            curl_setopt($ch, CURLOPT_COOKIEFILE,$cookieFile);

            $successPath        = curl_exec( $ch );
            $tmpSuccessPath     = $this->_parseContent( '/ClickHereMessage(.*?)Try Again/s', $successPath );
            $gSuccessPath       = $this->_parseContent( '/webmail.aol.com(.*?)target/s', $tmpSuccessPath[1][0]);
            $path           = 'http://webmail.aol.com';
            $gSuccessfull       = trim( $gSuccessPath[1][0] );
            $path           .= str_replace( '"', '', $gSuccessfull );
            curl_setopt( $ch, CURLOPT_URL, $path );
             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 0 );
            
            // by here we have successfully logged in
            $weAreIn        = curl_exec( $ch );

            // from this point we are trying to go to the contacts page
            $homePage       = $this->_parseContent( '/padding(.*?)true/s', $weAreIn );
            $homePageLink       = ''.$homePage[0][0];
            $landingPageUrl     = $this->_parseContent( '/http(.*?)true/s', $homePageLink );
            
            // now go to the landing page
            $landingPageSubUrl  = $this->_parseContent( '/gSuccessPath(.*?)var/s', $landingPageUrl[0][0] );
            $landingPageSubUrl  = str_replace( array( '=', ' ', '"', ';' ), '', $landingPageSubUrl[1][0] );
            $landingPageSubUrl  = str_replace( 'Suite.aspx', '', $landingPageSubUrl );
            curl_setopt( $ch, CURLOPT_URL, 'http://webmail.aol.com'.trim( $landingPageSubUrl ).'Lite/ContactList.aspx?folder=New%20Mail&showUserFolders=False' );
            $contactsPage       = curl_exec( $ch );

            //we need the value of the "user" element in order to proceed
            $firstUserPattern   = $this->_parseContent( '/ProcessCommand(.*?)toolbarType/s', $contactsPage );
            $secondUserPattern  = $this->_parseContent( '/\.com(.*?)class/s', $firstUserPattern[1][0] );
            $userArray      = explode( ',', $secondUserPattern[1][0] );
            $user           = str_replace( array( '\'', ')', ';', '"' ), '', $userArray[1] );
            
            // now we need to get all the pages that contains the contacts
            // am not navigating through the whole pages, but rather i am using the print
            // functionality that AOL is providing for their users in order to get the whole
            // bunch of contacts in one single step.
            curl_setopt( $ch, CURLOPT_URL, 'http://webmail.aol.com/'.trim( $landingPageSubUrl ).'Lite/addresslist-print.aspx?command=all&sort=FirstLastNick&sortDir=Ascending&nameFormat=FirstLastNick&user='.$user );
             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $contactsRecords    = curl_exec( $ch );
            
            // ok, now we have the contacts bunch and we need to filter them ...
            $firstPhaseFilter   = $this->_parseContent( '/fullName(.*?)contactSeparator/s',$contactsRecords );
            $contactList        = array();
            foreach( $firstPhaseFilter[0] as $value ) {
                $value      = ''.$value; //don't ask me why i did that, it just didn't work except like this!!!
                // filter contact's full name
                $contactName    = $this->_parseContent( '/fullName">(.*?)<\/span/s', $value );
                // filter contact's email
                $contactEmail   = $this->_parseContent( '/<span>Email 1:<\/span> <span>(.*?)<\/span>/s', $value);
                $result['name'][] = strip_tags($contactName[1][0]);
                $result['email'][] = $contactEmail[1][0];
                
            }

            // Clean up and finalize everything ...
            $this->_rmFile( $cookieFile );
            
            return $result;
        }

        
    }
?>
