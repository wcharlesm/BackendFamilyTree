<?php
	class Person{
		private $id;
		private $identifier;
		private $firstName;
		private $middleName;
		private $lastName;
		private $surname;
		private $commonName;
		private $gender;
		private $birthDay;
		private $birthMonth;
		private $birthYear;
		private $deathDay;
		private $deathMonth;
		private $deathYear;

		function __construct($firstName, $middleName, $lastName, $surname, $commonName, $gender, $birthDay, $birthMonth, $birthYear, $deathDay, $deathMonth, $deathYear, $identifier, $id=-1){
				
			$this -> id = $id;
			$this -> identifier = $identifier;
			$this -> firstName = $firstName;
			$this -> middleName = $middleName;
			$this -> lastName = $lastName;
			$this -> surname = $surname;
			$this -> commonName = $commonName;
			$this -> gender = $gender;
			$this -> birthDay = $birthDay;
			$this -> birthMonth = $birthMonth;
			$this -> birthYear = $birthYear;
			$this -> deathDay = $deathDay;
			$this -> deathMonth = $deathMonth;
			$this -> deathYear = $deathYear;
		}
									
		public function getId(){
			return $this -> id;
		}
		
		public function getIdentifier(){
			return $this -> identifier;
		}
		
		public function getFirstName(){
			return $this -> firstName;
		}
		
		public function getMiddleName(){
			return $this -> middleName;
		}
		
		public function getLastName(){
			return $this -> lastName;
		}
		
		public function getSurname(){
			return $this -> surname;
		}
		
		public function getCommonName(){
			return $this -> commonName;
		}
		
		public function getGender(){
			return $this -> gender;
		}
		
		public function getBirthDay(){
			return $this -> birthDay;
		}
		
		public function getBirthMonth(){
			return $this -> birthMonth;
		}
		
		public function getBirthYear(){
			return $this -> birthYear;
		}
		
		public function getDeathDay(){
			return $this -> deathDay;
		}
		
		public function getDeathMonth(){
			return $this -> deathMonth;
		}
		
		public function getDeathYear(){
			return $this -> deathYear;
		}
		
	}

	interface PersonFactory{
		function getPersonById($id);
		function getPersonByIdentifier($identifier);
		function getPersonByName($firstName, $middleName, $lastName, $surname="");
		
		function searchPeople($firstName, $middleName, $lastName, $surname, $commonName, $gender, $birthDay, $birthMonth, $birthYear, $deathDay, $deathMonth, $deathYear, $type="OR");
		function searchPeopleDictionary($firstName, $middleName, $lastName, $surname, $commonName, $gender, $birthDay, $birthMonth, $birthYear, $deathDay, $deathMonth, $deathYear, $type="OR");
		function getAllPeople();
		function getAllPeopleDictionary();
		
		function insertPerson($person);
		function updatePerson($person);
		function deletePerson($person);
	}
	
	class ReadOnlyPersonFactory implements PersonFactory{
		private $pf;
		
		function __construct($db){
			$this -> pf = new SqlPersonFactory($db);
		}
		
		function getPersonById($id){
			return $this -> pf -> getPersonById($id);	
		}
		
		function getPersonByIdentifier($identifier){
			return $this -> pf -> getPersonByIdentifier($identifier);	
		}
		
		function getPersonByName($firstName, $middleName, $lastName, $surname=""){
			return $this -> pf -> getPersonByName($firstName, $middleName, $lastName, $surname="");	
		}
		
		function searchPeople($firstName, $middleName, $lastName, $surname, $commonName, $gender, $birthDay, $birthMonth, $birthYear, $deathDay, $deathMonth, $deathYear, $type="OR"){
			return $this -> pf -> searchPeople($firstName, $middleName, $lastName, $surname, $commonName, $gender, $birthDay, $birthMonth, $birthYear, $deathDay, $deathMonth, $deathYear, $type="OR");	
		}
		
		function searchPeopleDictionary($firstName, $middleName, $lastName, $surname, $commonName, $gender, $birthDay, $birthMonth, $birthYear, $deathDay, $deathMonth, $deathYear, $type="OR"){
			return $this -> pf -> searchPeopleDictionary($firstName, $middleName, $lastName, $surname, $commonName, $gender, $birthDay, $birthMonth, $birthYear, $deathDay, $deathMonth, $deathYear, $type="OR");	
		}
		
		function getAllPeople(){
			return $this -> pf -> getPersonById($id);	
		}
		
		function getAllPeopleDictionary(){
			return $this -> pf -> getAllPeopleDictionary();	
		}
		
		function insertPerson($person){
			return array('error' => 'can not insert person: read only access');
		}
		
		function updatePerson($person){
			return array('error' => 'can not update person: read only access');
		}
		
		function deletePerson($person){
			return array('error' => 'can not delete person: read only access');
		}
	}
	
	class SqlPersonFactory implements PersonFactory{
		private $db;

		function __construct($db){			
			$this -> db = $db;
		}
		
		public function getPersonById($id){
			$id = $this -> db -> escapeString($id);
			$sql = "SELECT * FROM Person WHERE id=\"$id\"";

			$results = $this -> db -> execute($sql);
			$row = $this -> db -> resultArray($results); 

			return new Person( 
				trim($row['first_name']),
				trim($row['middle_name']),
				trim($row['last_name']),
				trim($row['surname']),
				trim($row['common_name']),
				trim($row['gender']),
				trim($row['birth_day']),
				trim($row['birth_month']),
				trim($row['birth_year']),
				trim($row['death_day']),
				trim($row['death_month']),
				trim($row['death_year']),
				trim($row['identifier']),
				trim($row['id'])
			);
		}
		
		public function getPersonByIdentifier($identifier){
			$id = $this -> db -> escapeString($identifier);
			$sql = "SELECT * FROM Person WHERE identifier=\"$identifier\"";

			$results = $this -> db -> execute($sql);
			$row = $this -> db -> resultArray($results); 

			return new Person( 
				trim($row['first_name']),
				trim($row['middle_name']),
				trim($row['last_name']),
				trim($row['surname']),
				trim($row['common_name']),
				trim($row['gender']),
				trim($row['birth_day']),
				trim($row['birth_month']),
				trim($row['birth_year']),
				trim($row['death_day']),
				trim($row['death_month']),
				trim($row['death_year']),
				trim($row['identifier']),
				trim($row['id'])
			);
		}
		
		public function getPersonByName($firstName, $middleName, $lastName, $surname=""){
			$firstName = $this -> db -> escapeString($firstName);
			$middleName = $this -> db -> escapeString($middleName);
			$lastName = $this -> db -> escapeString($lastName);
			$surname = $this -> db -> escapeString($surname);
			
			$sql = "SELECT * FROM Person WHERE first_name\"$firstName\" AND middle_name=\"$middleName\" AND last_name=\"$lastName\" AND surname=\"$surname\"";

			$results = $this -> db -> execute($sql);
			$row = $this -> db -> resultArray($results); 
			
			return new Person( 
				trim($row['first_name']),
				trim($row['middle_name']),
				trim($row['last_name']),
				trim($row['surname']),
				trim($row['common_name']),
				trim($row['gender']),
				trim($row['birth_day']),
				trim($row['birth_month']),
				trim($row['birth_year']),
				trim($row['death_day']),
				trim($row['death_month']),
				trim($row['death_year']),
				trim($row['identifier']),
				trim($row['id'])
			);
		}
		
		private function searchPeopleWhereClause($firstName, $middleName, $lastName, $surname, $commonName, $gender, $birthDay, $birthMonth, $birthYear, $deathDay, $deathMonth, $deathYear, $type="OR"){
			$firstName = $this -> db -> escapeString($firstName);
			$middleName = $this -> db -> escapeString($middleName);
			$lastName = $this -> db -> escapeString($lastName);
			$surname = $this -> db -> escapeString($surname);
			$commonName = $this -> db -> escapeString($commonName);
			$gender = $this -> db -> escapeString($gender);
			$birthDay = $this -> db -> escapeString($birthDay);
			$birthMonth = $this -> db -> escapeString($birthMonth);
			$birthYear = $this -> db -> escapeString($birthYear);
			$deathDay = $this -> db -> escapeString($deathDay);
			$deathMonth = $this -> db -> escapeString($deathMonth);	
			$deathYear = $this -> db -> escapeString($deathYear);
			$type = $this -> db -> escapeString($type);
			 	
			$where = "";
			
			if ($firstName){	
				$where .= " first_name='$firstName' " + $type;
			}
			
			if ($middleName){	
				$where .= " middle_name='$middleName' " + $type;
			}
			
			if ($lastName){	
				$where .= " last_name='$lastName' " + $type;
			}
			
			if ($surname){	
				$where .= " surname='$surname' " + $type;
			}
			
			if ($commonName){	
				$where .= " common_name='$commonName' " + $type;
			}
			
			if ($gender){
				$where .= " gender='$gender' " + $type;
			}

			if ($birthDay){	
				$where .= " birth_day='$birthDay' " + $type;
			}
			
			if ($birthMonth){	
				$where .= " birth_month='$birthMonth' " + $type;
			}
			
			if ($birthYear){	
				$where .= " birth_year='$birthYear' " + $type;
			}
			
			if ($deathDay){	
				$where .= " death_day='$deathDay' " + $type;
			}
			
			if ($deathMonth){	
				$where .= " death_month='$deathMonth' " + $type;
			}
			
			if ($deathYear){	
				$where .= " death_year='$deathYear' " + $type;
			}
			
			return substr( $where, 1, strlen($where) - strlen($type) );
		}
		
		public function searchPeople($firstName, $middleName, $lastName, $surname, $commonName, $gender, $birthDay, $birthMonth, $birthYear, $deathDay, $deathMonth, $deathYear, $type="OR"){
			
			return $this -> peopleObjectArray(
				$this -> search(
					$this -> searchPeopleWhereClause($firstName, $middleName, $lastName, $surname, $commonName, $gender, $birthDay, $birthMonth, $birthYear, $deathDay, $deathMonth, $deathYear, $type)
				)
			);

		}
		
		public function searchPeopleDictionary($firstName, $middleName, $lastName, $surname, $commonName, $gender, $birthDay, $birthMonth, $birthYear, $deathDay, $deathMonth, $deathYear, $type="OR"){
			
			return $this -> peopleResultDictionary(
				$this -> search(
					$this -> searchPeopleWhereClause($firstName, $middleName, $lastName, $surname, $commonName, $gender, $birthDay, $birthMonth, $birthYear, $deathDay, $deathMonth, $deathYear, $type)
				)
			);

		}
		
		public function getAllPeople(){
			return $this -> peopleObjectArray( $this -> search() );
		}
		
		public function getAllPeopleDictionary(){
			return $this -> peopleResultDictionary( $this -> search() );
		}
		
		private function peopleObjectArray($results){
			$people = Array();
			$index = 0;
			
			while($row = $this -> db -> resultArray($results)){
				$people[$index++] = new Person( 
					trim($row['first_name']),
					trim($row['middle_name']),
					trim($row['last_name']),
					trim($row['surname']),
					trim($row['common_name']),
					trim($row['gender']),
					trim($row['birth_day']),
					trim($row['birth_month']),
					trim($row['birth_year']),
					trim($row['death_day']),
					trim($row['death_month']),
					trim($row['death_year']),
					trim($row['identifier']),
					trim($row['id'])
				);
			}
			
			return $people;
		}
		
		private function peopleResultDictionary($results){
			$people = Array();

			while($row = $this -> db -> resultArray($results)){
				
				 $people[trim($row['id'])] = array( 
					'firstName' => trim($row['first_name']),
					'middleName' => trim($row['middle_name']),
					'lastName' => trim($row['last_name']),
					'surname' => trim($row['surname']),
					'commonName' => trim($row['common_name']),
					'gender' => trim($row['gender']),
					'birthDay' => trim($row['birth_day']),
					'birthMonth' => trim($row['birth_month']),
					'birthYear' => trim($row['birth_year']),
					'deathDay' => trim($row['death_day']),
					'deathMonth' => trim($row['death_month']),
					'deathYear' => trim($row['death_year']),
					'identifier' => trim($row['identifier']),
					'id' => trim($row['id'])
				);

			}
			
			return $people;
		}
		
		private function search($where){
			
			$sql = "SELECT * FROM Person";
			
			if ($where != null) {
				$sql .= " WHERE " + $where;
			}
			
			$results = $this -> db -> execute($sql);

			return $results;
		}
		
		public function insertPerson($person){
			if ( is_a($person, "Person") ){
				
				if ( $person -> getId() == -1 ) {
					$response = 'no id';
					
					$fn = $person -> getFirstName();
					$mn = $person -> getMiddleName();
					$ln = $person -> getLastName();
					$sn = $person -> getSurname();
					$cn = $person -> getCommonName();
					$g = $person -> getGender();
					$bd = $person -> getBirthDay();
					$bm = $person -> getBirthMonth();
					$by = $person -> getBirthYear();
					$dd = $person -> getDeathDay();
					$dm = $person -> getDeathMonth();
					$dy = $person -> getDeathYear();
					$pi = $person -> getIdentifier();
					
					$fn = $this -> db -> escapeString($fn);
					$mn = $this -> db -> escapeString($mn);
					$ln = $this -> db -> escapeString($ln);
					$sn = $this -> db -> escapeString($sn);
					$cn = $this -> db -> escapeString($cn);
					$g = $this -> db -> escapeString($g);
					$bd = $this -> db -> escapeString($bd);
					$bm = $this -> db -> escapeString($bm);
					$by = $this -> db -> escapeString($by);
					$dd = $this -> db -> escapeString($dd);
					$dm = $this -> db -> escapeString($dm);
					$dy = $this -> db -> escapeString($dy);
					$pi = $this -> db -> escapeString($pi);

					$sql = "INSERT INTO Person (identifier, first_name, middle_name, last_name, surname, common_name, gender, birth_day, birth_month, birth_year, death_day, death_month, death_year) ";
					$sql .= "VALUES (\"$pi\", \"$fn\", \"$mn\", \"$ln\", \"$sn\", \"$cn\", \"$g\", \"$bd\", \"$bm\", \"$by\", \"$dd\", \"$dm\", \"$dy\" )";

					return Array('success' => $this -> db -> execute($sql), 'personId' => $this -> db -> lastInsertedId());

				} else {
					
					return $this -> updatePerson($person);
					
				}
			}

			return array('error' => 'can not insert person: object was not a person');
		}
		
		public function updatePerson($person){
			
			if ( is_a($person, "Person") ){
				
					$fn = $person -> getFirstName();
					$mn = $person -> getMiddleName();
					$ln = $person -> getLastName();
					$sn = $person -> getSurname();
					$cn = $person -> getCommonName();
					$g = $person -> getGender();
					$bd = $person -> getBirthDay();
					$bm = $person -> getBirthMonth();
					$by = $person -> getBirthYear();
					$dd = $person -> getDeathDay();
					$dm = $person -> getDeathMonth();
					$dy = $person -> getDeathYear();
					$pi = $person -> getIdentifier();
					
					$id = $person -> getId();
					
					$fn = $this -> db -> escapeString($fn);
					$mn = $this -> db -> escapeString($mn);
					$ln = $this -> db -> escapeString($ln);
					$sn = $this -> db -> escapeString($sn);
					$cn = $this -> db -> escapeString($cn);
					$g = $this -> db -> escapeString($g);
					$bd = $this -> db -> escapeString($bd);
					$bm = $this -> db -> escapeString($bm);
					$by = $this -> db -> escapeString($by);
					$dd = $this -> db -> escapeString($dd);
					$dm = $this -> db -> escapeString($dm);
					$dy = $this -> db -> escapeString($dy);
					$pi = $this -> db -> escapeString($pi);

					$id = $this -> db -> escapeString($id);

					$sql = "UPDATE Person ";

				if ( $id == -1 ) {
					
					$sql .= "SET identifier=\"$pi\", common_name=\"$cn\", gender=\"$g\", birth_day=\"$bd\", birth_month=\"$bm\", birth_year=\"$by\", death_day=\"$dd\", death_month=\"$dm\", death_year=\"$dy\" ";
					$sql .= "WHERE first_name=\"$fn\" AND middle_name=\"$mn\" AND last_name=\"$ln\" AND surname=\"$sn\" ";

				} else {
					
					$sql .= "SET identifier=\"$pi\", first_name=\"$fn\", middle_name=\"$mn\", last_name=\"$ln\", surname=\"$sn\", common_name=\"$cn\", gender=\"$g\", ";
					$sql .= "birth_day=\"$bd\", birth_month=\"$bm\", birth_year=\"$by\", death_day=\"$dd\", death_month=\"$dm\", death_year=\"$dy\" ";
					$sql .= "WHERE id=\"$id\" ";

				}
				
				return Array('success' => $this -> db -> execute($sql), 'personId' => $person -> getId());
			}

			return array('error' => 'can not update person: object was not a person');
			
		}
		
		public function deletePerson($person){
			
			if ( is_a($person, "Person") ){
								
				$id = $person -> getId();

				if ( $id == -1 ) {
					
					$fn = $person -> getFirstName();
					$mn = $person -> getMiddleName();
					$ln = $person -> getLastName();
					$sn = $person -> getSurname();
					
					$fn = $this -> db -> escapeString($fn);
					$mn = $this -> db -> escapeString($mn);
					$ln = $this -> db -> escapeString($ln);
					$sn = $this -> db -> escapeString($sn);

					$sql = "SELECT FROM Person ";
					$sql .= "WHERE first_name=\"$fn\" AND middle_name=\"$mn\" AND last_name=\"$ln\" AND surname=\"$sn\" ";

					$this -> db -> execute($sql);
					$results = $this -> db -> execute($sql);
					$row = $this -> db -> resultArray($results); 

					if ($row) {
						$id = trim($row['id']);
					} else {
						return false;
					}

				} 
				
			} else {
				
				$id = $person;

			}
			
			$id = $this -> db -> escapeString($id);
			
			$sql = "DELETE FROM Relation WHERE my_id=\"$id\" OR your_id=\"$id\"";
			$this -> db -> execute($sql);

			$sql = "DELETE FROM Event WHERE person_id=\"$id\"";
			$this -> db -> execute($sql);

			$sql = "DELETE FROM Note WHERE person_id=\"$id\"";
			$this -> db -> execute($sql);

			$sql = "DELETE FROM Person WHERE id=\"$id\"";
			return Array('success' => $this -> db -> execute($sql));
		}
	}

?>