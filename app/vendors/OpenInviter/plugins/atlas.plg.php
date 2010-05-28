<?php
$_pluginInfo=array(
	'name'=>'Atlas',
	'version'=>'1.0.2',
	'description'=>"Get the contacts from a Atlas account",
	'base_version'=>'1.8.0',
	'type'=>'email',
	'check_url'=>'http://www.atlas.cz/',
	'requirement'=>'user',
	'allowed_domains'=>false,
	);
/**
 * Atlas.cz Plugin
 * 
 * Imports user's contacts from Atlas.cz AddressBook
 * 
 * @author OpenInviter
 * @version 1.0.0
 */
class atlas extends openinviter_base
	{
	private $login_ok=false;
	public $showContacts=true;
	public $internalError=false;
	protected $timeout=30;
	
	public $debug_array=array(
				'initial_get'=>'name',
				'login_post'=>'jAjaxValidatorManager',
				'url_contacts'=>'rm'
				);
	
	/**
	 * Login function
	 * 
	 * Makes all the necessary requests to authenticate
	 * the current user to the server.
	 * 
	 * @param string $user The current user.
	 * @param string $pass The password for the current user.
	 * @return bool TRUE if the current user was authenticated successfully, FALSE otherwise.
	 */
	public function login($user,$pass)
		{
		$this->resetDebugger();
		$this->service='atlas';
		$this->service_user=$user;
		$this->service_password=$pass;
		if (!$this->init()) return false;
					
		$res=$this->get("http://profil.centrum.cz/login.aspx");
		if ($this->checkResponse("initial_get",$res))
			$this->updateDebugBuffer('initial_get',"http://profil.centrum.cz/login.aspx",'GET');
		else
			{
			$this->updateDebugBuffer('initial_get','http://profil.centrum.cz/login.aspx','GET',false);	
			$this->debugRequest();
			$this->stopPlugin();
			return false;
			}
		
		$form_action='http://profil.centrum.cz/verify.aspx';
		$post_elements=array('name'=>$user,'password'=>$pass);
		$res=$this->post($form_action,$post_elements,true);
		if ($this->checkResponse('login_post',$res))
			$this->updateDebugBuffer('login_post',$form_action,'POST',true,$post_elements);
		else
			{
			$this->updateDebugBuffer('login_post',$form_action,'POST',false,$post_elements);	
			$this->debugRequest();
			$this->stopPlugin();
			return false;
			}
		
		$url_contacts='http://amail.centrum.cz/addressbook.aspx';
		$this->login_ok=$url_contacts;
		return true;
		}

	/**
	 * Get the current user's contacts
	 * 
	 * Makes all the necesarry requests to import
	 * the current user's contacts
	 * 
	 * @return mixed The array if contacts if importing was successful, FALSE otherwise.
	 */	
	public function getMyContacts()
		{
		if (!$this->login_ok)
			{
			$this->debugRequest();
			$this->stopPlugin();
			return false;
			}
		else $url=$this->login_ok;
		$res=$this->get($url,true);
		if ($this->checkResponse("url_contacts",$res))
			$this->updateDebugBuffer('contacts_file',$url,'GET');
		else
			{
			$this->updateDebugBuffer('contacts_file',$url,'GET',false);	
			$this->debugRequest();
			$this->stopPlugin();
			return false;
			}
		
		$contacts=array();
		if (preg_match_all("#abeditcontact.aspx\?contactid\=(.+)\"\>#U", $res, $matches))
			{
			$matches=array_unique($matches[1]);
			foreach($matches as $matchValue)
				{
				$last_name=false;$first_name=false;
				preg_match_all("#{$matchValue}\"\>(.+)</a>#U", $res, $matches2);
				if (!empty($matches2[1]))
					{
					$last_name=(!empty($matches2[1][1])?$matches2[1][1]:false);
					$first_name=(!empty($matches2[1][0])?$matches2[1][0]:false);
					if(!empty($matches2[1][2])) $contacts[$matches2[1][2]]=array('first_name'=>$first_name,'last_name'=>$last_name,'email_1'=>$matches2[1][2]);
					}
				}
			}
		foreach ($contacts as $email=>$name) if (!$this->isEmail($email)) unset($contacts[$email]);
		return $this->returnContacts($contacts);	
		}

	/**
	 * Terminate session
	 * 
	 * Terminates the current user's session,
	 * debugs the request and reset's the internal 
	 * debudder.
	 * 
	 * @return bool TRUE if the session was terminated successfully, FALSE otherwise.
	 */	
	public function logout()
		{
		if (!$this->checkSession()) return false;		
		$res=$this->get('http://www.atlas.cz/r/?ump',true);
		$this->debugRequest();
		$this->resetDebugger();
		$this->stopPlugin();
		return true;	
		}
	
	}	

?>