<?php
/*
require_once(ENV_PATH."Authenticator.php");
$authenticator = new SqlAuthenticator($familyDb);
interface Authenticator {
	function authenticate($un, $pw);
	function addUser($newUn, $newPw, $adminUn, $adminPw);
	function updateUserPassword($un, $oldPw, $newPw);
}
 * */

function authenticate($authenticator, $username, $password){

	$auth = $authenticator -> authenticate($username, $password);
	
	if ($auth['access'] && $auth['access'] != 'none'){
		
		return $auth['access'];
		
	} else {
		
		header('Content-type: application/json');
	
		echo json_encode($auth);
		
		return 'none';
		
	}

}

?>