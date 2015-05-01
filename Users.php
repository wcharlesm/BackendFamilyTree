<?php

	class User {
		private $id;
		private $name;
		private $access;
		
		function __construct($i, $n, $a) {
			$this -> $id = $i;
			$this -> $name = $n;
			$this -> $access = $a;
		}
		
		public function getId(){
			return $this -> $id;
		}
		
		public function getName(){
			return $this -> $name;
		}
		
		public function getAccess(){
			return $this -> $access;
		}
	}
	
	interface UserFactory {
		function addUser($name, $password, $access);
		function getUsers();
		function updateUser($id, $name, $access);
	}
	
	class SqlUserFactory implements  UserFactory {
		private $db;
		
		function __construct($db){
			$this -> db = $db;
		}
		
		function addUser($name, $password, $access){
			
			$name = $this -> db -> escapeString($name);
			$access = $this -> db -> escapeString($access);
			
			$sql = "SELECT * FROM Authentication WHERE user_name=\"$name\"";
			$results = $this -> db -> execute($sql);
			$row = $this -> db -> resultArray($results);

			if ($row) {
				
				return array( 'error' => 'user name already exists', 'access' => 'none' );
				
			} else {
				
				$dbHash = hash('sha512', ($password));

				$sql = "INSERT INTO Authentication (user_name, password_hash, access)";
				$sql .= "VALUES (\"$name\", \"$dbHash\", \"$access\")";

				$this -> db -> execute($sql);

			}

		}
		
		public function getUsers(){
			echo "Start of getUsers <br>";
			
			$retVal = array();
			$sql = "SELECT * FROM Authentication;";
			$results = $this -> db -> execute($sql);
			
			echo "after db execute <br> ";
			
			echo json_encode($results);
			
			while( $rows = $this -> db -> resultArray($results) ){
				echo "in while loop ";
				
				$id = trim($row['id']);
				$userName = trim($row['user_name']);
				$access = trim($row['access']);
				
				echo "id: $id, userName: $userName, access: $access";
				
				$retVal[$id] = new User($id, $userName, $access);
			}
			
			echo "before retval <br> ";

			return retVal;
		}
		
		function updateUser($id, $name, $access){
			$id = $this -> db -> escapeString($id);
			$name = $this -> db -> escapeString($name);
			$access = $this -> db -> escapeString($access);
			
			$sql = "SELECT id FROM Authentication WHERE id=\"$id\"";
			$results = $this -> db -> execute($sql);
			$row = $this -> db -> resultArray($results);

			if ($row) {
				
				$sql = "UPDATE Authentication SET user_name=\"$name\", password_hash=\"$dbHash\", access=\"$access\" WHERE id=$id";
				$this -> db -> execute($sql);
				
			} else {
				
				return array( 'error' => 'user does not exist');

			}
		}
	}

?>