<?php

/*

couchCookie - Dec 2011
Ronaldo Barbachano

Uses a $_COOKIE to manage a secure session via couchdb. 

Dependencies : 
PHP 5.3
couchCurl (custom version for couchCookie coming soon)
poform

*/

// application prefix, mostly for database names and session variable keys
define('APF','default');
// this is the name of the design document (for convention reason its advisible this
// is the same across all databases
define('DESIGN_DOC','cntrl');
class couchCookie{
	public $username;
	// $password='password ' is for poform to display this field as a password
	public $password= 'password ';

	public function destroy_session(){
		if(isset($_COOKIE[APF])){
			$couch_delete = json_decode(couchCurl::get($_COOKIE[APF],false,APF.'_session'),true);
			$couch_delete = couchCurl::delete($couch_delete['_id'],$couch_delete['_rev'],APF.'_session');
			$params = session_get_cookie_params();
			$time_val = (int) (time() - 36000);
			setcookie(APF, '', $time_val,$params["path"], $params["domain"],$params["secure"], $params["httponly"]);
		}		
	}
	public function __construct(){
	global $couch_queries;
		if($_COOKIE[APF]){
			// if the cookie is presnet check to see if it is in the DB and if not then we can deauthenticate 
			if(self::sess_check($_COOKIE[APF]) != false){
			}else{
				// PUT YOUR INVALID MESSAGE HERE ...
				return false;
			}
			unset($this->username,$this->password);
		}
		if(isset($_POST['username']) && $_POST['password'] != NULL){
			$result = couchCurl::view($_POST['username'],'username',DESIGN_DOC,APF.'_users',NULL);
			$couch_queries++;
			foreach($result['rows'] as $key=>$value){
				$uid = $key;
				$pass_hash = $value;
				// only process the top most row, this shouldn't have more than one entry
				end;
			}
			if($pass_hash == md5(trim($_POST['password']))){
				return self::gen_cookie($uid);
			}else{
				// SHOW INVALID LOGIN
				return false;
				die();
			}
		}
		
	}

	private function sess_check($id=null,$expire=NULL){
	// USE THIS instead of the lookup thingee this happens every page probably..
		if($id==null && isset($_COOKIE[APF]) ) $id = isset($_COOKIE[APF]);
		$sess = json_decode(couchCurl::get($id,false,APF.'_session'),true);
		return (isset($sess['_rev'])?$sess['_rev']:false);
	}
	private function gen_cookie($userid,$expire=null){
		$user_ip = $_SERVER["REMOTE_ADDR"];
		unset($this->_f,$this->_d, $this->username,$this->password); 
		$cookie_value = substr(md5(uniqid(rand(),true)),1,36);
		// this wont make for very simple lookups ... by any of the array values...
		$json = json_encode(array('u'=> $userid, 'ts'=> couchCurl::handle_couch_id(time()), 'ip'=>  $user_ip ) );
		couchCurl::put($json,$cookie_value,APF.'_session')  ;
		if($expire ==null)
			setcookie(APF, $cookie_value);
		else	
			setcookie(APF,$cookie_value,$expire);
	}		
}
