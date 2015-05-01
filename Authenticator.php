<?php
	interface Authenticator {
		function authenticate($un, $pw);
		function addUser($newUn, $newPw, $adminUn, $adminPw);
		function updateUserPassword($un, $oldPw, $newPw);
	}
	
	class SqlAuthenticator implements Authenticator {
		
		private $db;

		function __construct($db) {
			$this -> db = $db;
		}
		
		public function authenticate($un, $pw) {
			
			$un = $this -> db -> escapeString($un);
			
			$sql = "SELECT * FROM Authentication WHERE user_name=\"$un\"";
			$results = $this -> db -> execute($sql);
			$row = $this -> db -> resultArray($results);

			if ($row) {
				
				$dbHash = trim($row['password_hash']);

				$pwHash = hash('sha512', $pw);

				if ($pwHash == $dbHash) {
					
					return array( 'success' => 'authentication succeeded', 'access' => trim($row['access']) );

				} else {
					
					return array( 'error' => 'incorrect password', 'access' => 'none',
						'password' => $pw, 'pwHash' => $pwHash, 'dbHash' => $dbHash );

				}
				
			} else {
				
				return array( 'error' => 'user name not found', 'access' => 'none' );

			}

		}
		
		public function addUser($newUn, $newPw, $adminUn, $adminPw){
			
			$adminAuth = $this -> authenticate($adminUn, $adminPw);

			if ($adminAuth['access'] == 'administrator') {
				
				$newUn = $this -> db -> escapeString($newUn);
				
				$sql = "SELECT * FROM Authentication WHERE user_name=\"$newUn\"";
				$results = $this -> db -> execute($sql);
				$row = $this -> db -> resultArray($results);

				if ($row) {
					
					return array( 'error' => 'user name already exists', 'access' => 'none' );
					
				} else {
					
					$dbHash = hash('sha512', $newPw);

					$sql = "INSERT INTO Authentication (user_name, password_hash, access)";
					$sql .= "VALUES (\"$newUn\", \"$dbHash\", \"standard\")";

					$this -> db -> execute($sql);

				}
			} else {
				
				return array( 'error' => 'only administrators can create new users', 'access' => 'none' );
				
			}
			
			
		}
		
		public function updateUserPassword($un, $oldPw, $newPw){
			$un = $this -> db -> escapeString($un);
			
			$sql = "SELECT * FROM Authentication WHERE user_name=\"$un\"";
			$results = $this -> db -> execute($sql);
			$row = $this -> db -> resultArray($results);

			if ($row) {
				
				$id = trim($row['id']);

				$dbHash = trim($row['password_hash']);

				$pwHash = hash('sha512', $pw);

				if ($pwHash == $dbHash) {
					
					$dbHash = hash('sha512', $pw);

					$sql = "UPDATE Authentication ";
					$sql .= "SET password_hash=\"$dbHash\"";

					$this -> db -> execute($sql);

					return array( 'success' => 'user password updated', 'access' => trim($row['access']) );;

				} else {
					
					return array( 'error' => 'incorrect password', 'access' => 'none' );

				}
						
			} else {
				
				return array( 'error' => 'user name does not exist', 'access' => 'none' );

			}
		}
		
	}
?>