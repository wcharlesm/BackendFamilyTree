<?php
	class Event{
		private $id;
		private $personId;
		private $startDay;
		private $startMonth;
		private $startYear;
		private $endDay;
		private $endMonth;
		private $endYear;
		private $description;
		
		function __construct($personId, $startDay, $startMonth, $startYear, $endDay, $endMonth, $endYear, $description, $id=-1){
			$this -> id = $id;
			$this -> personId = $personId;
			$this -> startDay = $startDay;
			$this -> startMonth = $startMonth;
			$this -> startYear = $startYear;
			$this -> endDay = $endDay;
			$this -> endMonth = $endMonth;
			$this -> endYear = $endYear;
			$this -> description = $description;
		}
		
		public function getId(){
			return $this -> id;
		}
		
		public function getPersonId(){
			return $this -> personId;
		}
		
		public function getStartDay(){
			return $this -> startDay;
		}
		
		public function getStartMonth(){
			return $this -> startMonth;
		}
		
		public function getStartYear(){
			return $this -> startYear;
		}
		
		public function getEndDay(){
			return $this -> endDay;
		}
		
		public function getEndMonth(){
			return $this -> endMonth;
		}
		
		public function getEndYear(){
			return $this -> endYear;
		}
		
		public function getDescription(){
			return $this -> description;
		}
	}
	
	interface EventFactory{
		function getEventsByPerson($person);
		function getEventById($id);
		function insertEvent($event);
		function updateEvent($event);
		function deleteEvent($event);
	}
	
	class ReadOnlyEventFactory implements EventFactory{
		private $ef;
		
		function __construct($db){
			$this -> ef = new SqlEventFactory($db);
		}
		
		public function getEventsByPerson($person){
			return $this -> ef -> getEventsByPerson($person);
		}
		
		public function getEventById($id){
			return $this -> ef -> getEventById($id);
		}
		
		public function insertEvent($event){
			return array('error' => 'can not insert event: read only access');
		}
		
		public function updateEvent($event){
			return array('error' => 'can not update event: read only access');
		}
		
		public function deleteEvent($event){
			return array('error' => 'can not delete event: read only access');
		}
		
	}
	
	class SqlEventFactory implements EventFactory{
		private $db;
		
		function __construct($db){
			$this -> db = $db;
		}
		
		public function getEventsByPerson($person){
			
			$pId = is_a($person, "Person") ? $person -> getId() : $person;
			
			$pId = $this -> db -> escapeString($pId);
			
			$sql = "SELECT * FROM Event WHERE person_id=\"$pId\"";

			$results = $this -> db -> execute($sql);
			
			$i = 0;
			$eventArray = array();
			
			while( $row = $this -> db -> resultArray($results) ){
				$eventArray[$i++] = new Event( 
					trim($row['person_id']),
					trim($row['start_day']),
					trim($row['start_month']),
					trim($row['start_year']),
					trim($row['end_day']),
					trim($row['end_month']),
					trim($row['end_year']),
					trim($row['description']),
					trim($row['id'])
				);
			}
			
			return $eventArray;
		}
		
		public function getEventById($id){
			$id = $this -> db -> escapeString($id);
			
			$sql = "SELECT * FROM Event WHERE id=\"$id\"";

			$results = $this -> db -> execute($sql);
			$row = $this -> db -> resultArray($results);
			
			return new Event( 
				trim($row['person_id']),
				trim($row['start_day']),
				trim($row['start_month']),
				trim($row['start_year']),
				trim($row['end_day']),
				trim($row['end_month']),
				trim($row['end_year']),
				trim($row['description']),
				trim($row['id'])
			);
		}
		
		public function insertEvent($event){
			
			if ( is_a($event, "Event") ){
				
				if ( $event -> getId() == -1 ) {
					
					$pId = $event -> getPersonId();
					$sd = $event -> getStartDay();
					$sm = $event -> getStartMonth();
					$sy = $event -> getStartYear();
					$ed = $event -> getEndDay();
					$em = $event -> getEndMonth();
					$ey = $event -> getEndYear();
					$des = $event -> getDescription();

					$pId = $this -> db -> escapeString($pId);
					$sd = $this -> db -> escapeString($sd);
					$sm = $this -> db -> escapeString($sm);
					$sy = $this -> db -> escapeString($sy);
					$ed = $this -> db -> escapeString($ed);
					$em = $this -> db -> escapeString($em);
					$ey = $this -> db -> escapeString($ey);
					$des = $this -> db -> escapeString($des);

					$sql = "INSERT INTO Event (person_id, start_day, start_month, start_year, end_day, end_month, end_year, description) ";
					$sql .= "VALUES (\"$pId\", \"$sd\", \"$sm\", \"$sy\", \"$ed\", \"$em\", \"$ey\", \"$des\" )";
					
					return array('success' => $this -> db -> execute($sql));

				} else {
					
					return $this -> updateEvent($event);

				} 
			}

			return array('error' => 'can not insert event: object was not an event');
		}
		
		public function updateEvent($event){
			
			if ( is_a($event, "Event") ){
				
				$id = $event -> getId();

				if ( $id != -1 ) {
					
					$pId = $event -> getPersonId();
					$sd = $event -> getStartDay();
					$sm = $event -> getStartMonth();
					$sy = $event -> getStartYear();
					$ed = $event -> getEndDay();
					$em = $event -> getEndMonth();
					$ey = $event -> getEndYear();
					$des = $event -> getDescription();

					$pId = $this -> db -> escapeString($pId);
					$sd = $this -> db -> escapeString($sd);
					$sm = $this -> db -> escapeString($sm);
					$sy = $this -> db -> escapeString($sy);
					$ed = $this -> db -> escapeString($ed);
					$em = $this -> db -> escapeString($em);
					$ey = $this -> db -> escapeString($ey);
					$des = $this -> db -> escapeString($des);

					$id = $this -> db -> escapeString($id);

					$sql = "UPDATE Event ";

					$sql .= "SET person_id=\"$pId\", start_day=\"$sd\", start_month=\"$sm\", start_year=\"$sy\", end_day=\"$ed\", end_month=\"$em\", end_year=\"$ey\", description=\"$des\" ";
					$sql .= "WHERE id=\"$id\" ";

				}

				return array('success' => $this -> db -> execute($sql));
			}	

			return array('error' => 'can not update event: object was not an event');
		}
		
		public function deleteEvent($event){
			
			$sql = "DELETE FROM Event ";

			if ( is_a($event, "Event") ){
								
				$id = $event -> getId();

				if ( $id != -1 ) {
					
					$id = $this -> db -> escapeString($id);
					$sql .= "WHERE id=\"$id\"";

				}
				
			} else {
				
				$event = $this -> db -> escapeString($event);
				$sql .= "WHERE id=\"$event\"";

			}
			
			return array('success' => $this -> db -> execute($sql));
		}	
	}
?>















