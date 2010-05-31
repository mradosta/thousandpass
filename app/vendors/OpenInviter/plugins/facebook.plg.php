<?php
/*Import Friends from Facebook
 * You can send message to your Friends Inbox
 */
$_pluginInfo=array(
	'name'=>'Facebook',
	'version'=>'1.2.3',
	'description'=>"Get the contacts from a Facebook account",
	'base_version'=>'1.8.0',
	'type'=>'social',
	'check_url'=>'http://apps.facebook.com/causes/',
	'requirement'=>'email',
	'allowed_domains'=>false,
	);
/**
 * FaceBook Plugin
 * 
 * Imports user's contacts from FaceBook and sends
 * messages using FaceBook's internal system.
 * 
 * @author OpenInviter
 * @version 1.0.8
 */
class facebook extends openinviter_base
	{
	private $login_ok=false;
	public $showContacts=true;
	public $internalError=false;
	protected $timeout=30;
	
	public $debug_array=array(
				'initial_get'=>'pass',
				'login_post'=>'javascripts',
				'url_lite'=>'?viewer=',
				'get_friends'=>'payload',
				'update_status'=>'sectitle',
				'url_message'=>'form action="',
				'send_message'=>'"redirect":"\/inbox'
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
		$this->service='facebook';
		$this->service_user=$user;
		$this->service_password=$pass;
		if (!$this->init()) return false;
		
		$res=$this->get("http://apps.facebook.com/causes/",true);
		if ($this->checkResponse("initial_get",$res))
			$this->updateDebugBuffer('initial_get',"http://apps.facebook.com/causes/",'GET');
		else
			{
			$this->updateDebugBuffer('initial_get',"http://apps.facebook.com/causes/",'GET',false);
			$this->debugRequest();
			$this->stopPlugin();
			return false;
			}
			
		$form_action="https://login.facebook.com/login.php?login_attempt=1";
		$post_elements=array('email'=>$user,
							 'pass'=>$pass,
							 'next'=>'http://apps.facebook.com/causes/home?_method=GET',
							 'return_session'=>0,
							 'req_perms'=>0,
							 'session_key_only'=>0,
							 'api_key'=>$this->getElementString($res,'name="api_key" value="','"'),
							 'version'=>'1.0',
							 );
		$res=$this->post($form_action,$post_elements,true,true);	
		if ($this->checkResponse("login_post",$res))
			$this->updateDebugBuffer('login_post',"{$form_action}",'POST',true,$post_elements);
		else
			{
			$this->updateDebugBuffer('login_post',"{$form_action}",'POST',false,$post_elements);
			$this->debugRequest();
			$this->stopPlugin();
			return false;
			}
		
		$res=$this->get('http://lite.facebook.com/',true);
		if ($this->checkResponse("url_lite",$res))
			$this->updateDebugBuffer('url_lite',"http://lite.facebook.com/",'GET');
		else
			{
			$this->updateDebugBuffer('url_lite',"http://lite.facebook.com/",'GET',false);
			$this->debugRequest();
			$this->stopPlugin();
			return false;
			}
		
		
		$userId=$this->getElementString($res,"?viewer=",'"');
		if (empty($userId)) $this->login_ok=false;
		else $this->login_ok="http://lite.facebook.com/typeahead/search/?viewer={$userId}&__async__=true";
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
		$res=$this->get($url);
		if ($this->checkResponse("get_friends",$res))
			$this->updateDebugBuffer('get_friends',"{$url}",'GET');
		else
			{
			$this->updateDebugBuffer('get_friends',"{$url}",'GET',false);
			$this->debugRequest();
			$this->stopPlugin();
			return false;
			}
		$contacts=array();
		if (preg_match_all("#\[\"(.+)\"(.+)\,(.+)\]#U",$res,$matches))
			{
			if (!empty($matches[1]))
				foreach($matches[3] as $key=>$id) $contacts[$id]=(!empty($matches[1][$key])?$matches[1][$key]:false);
			}
		
		return $contacts;
		}

	/**
	 * Send message to contacts
	 * 
	 * Sends a message to the contacts using
	 * the service's inernal messaging system
	 * 
	 * @param string $session_id The OpenInviter user's session ID
	 * @param string $message The message being sent to your contacts
	 * @param array $contacts An array of the contacts that will receive the message
	 * @return mixed FALSE on failure.
	 */
	public function sendMessage($session_id,$message,$contacts)
		{
		$countMessages=0;
		foreach ($contacts as $id=>$name)
			{			
			$countMessages++;
			$res=$this->get("http://lite.facebook.com/inbox/compose/");
			if ($this->checkResponse("url_message",$res))
				$this->updateDebugBuffer('url_message',"http://lite.facebook.com/inbox/compose/",'GET');
			else
				{
				$this->updateDebugBuffer('url_message',"http://lite.facebook.com/inbox/compose/",'GET',false);
				$this->debugRequest();
				$this->stopPlugin();
				return false;
				}
			$form_action="http://lite.facebook.com".$this->getElementString($res,'form action="','"');
			$post_elements=array('to[0]'=>$id,'subject'=>$message['subject'],'message'=>$message['body'],'__async__'=>true);
			$res=$this->post($form_action,$post_elements);
			if ($this->checkResponse("send_message",$res))
				$this->updateDebugBuffer('send_message',"{$form_action}",'POST',true,$post_elements);
			else
				{
				$this->updateDebugBuffer('send_message',"{$form_action}",'POST',false,$post_elements);
				$this->debugRequest();
				$this->stopPlugin();
				return false;
				}
			sleep($this->messageDelay);
			if ($countMessages>$this->maxMessages) {$this->debugRequest();$this->resetDebugger();$this->stopPlugin();break;}
			}
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
		$res=$this->get("http://lite.facebook.com",true);
		if (!empty($res))
			{
			preg_match_all("#form\>\<a href\=\"(.+)\/logout\/#U",$res,$url_logout_array);
			if (!empty($url_logout_array[1][0]))
				{ 
				$url_logout="http://lite.facebook.com{$url_logout_array[1][0]}/logout/";
				$res=$this->get($url_logout,true);
				}
			}
		$this->debugRequest();
		$this->resetDebugger();
		$this->stopPlugin();
		return true;	
		}
	}	

?>