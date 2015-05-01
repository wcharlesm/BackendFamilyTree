<?php
	//Header must be set before anything is writtent to the response body (I think).  There are redundant attempts to set the header in the rest folder files
	header('Content-type: application/json');
	
	//Full Path is required, path is relative to the initial php file.  If this is included in a file in another folder there will be errors.

	require_once("config/db.php");
	require_once("config/env.php");

	require_once(ENV_PATH."getVars.php");

	require_once(ENV_PATH."dbManager.php");
	$familyDb = new MySqlDatabase(DB_SERVER, DB_NAME, DB_USER, DB_PASS);

	require_once(ENV_PATH."Authenticator.php");
	$authenticator = new SqlAuthenticator($familyDb);

	require_once(ENV_PATH."authenticate.php");
	
	$access_level = authenticate($authenticator, $username, $password);
	
	switch($access_level) {
		case "administrator":
			require_once(ENV_PATH."Users.php");
			$familyUsers = new SqlUserFactory($familyDb);
			
		case "standard":
			
			require_once(ENV_PATH."Event.php");
			$familyEvents = new SqlEventFactory($familyDb);
	
			require_once(ENV_PATH."Note.php");
			$familyNotes = new SqlNoteFactory($familyDb);
			
			require_once(ENV_PATH."Person.php");
			$familyPeople = new SqlPersonFactory($familyDb);
	
			require_once(ENV_PATH."Relation.php");
			$familyRelations = new SqlRelationFactory($familyDb);
			
			break;
			
		case "readonly":

			require_once(ENV_PATH."Event.php");
			$familyEvents = new ReadOnlyEventFactory($familyDb);
	
			require_once(ENV_PATH."Note.php");
			$familyNotes = new ReadOnlyNoteFactory($familyDb);
			
			require_once(ENV_PATH."Person.php");
			$familyPeople = new ReadOnlyPersonFactory($familyDb);
	
			require_once(ENV_PATH."Relation.php");
			$familyRelations = new ReadOnlyRelationFactory($familyDb);
			
			break;
			
		default:
			exit();
			
	}
	

?>