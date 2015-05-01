<?php
	class Relation{
		private $id;
		private $myId;
		private $yourId;
		private $relationship;
		private $startDay;
		private $startMonth;
		private $startYear;
		private $endDay;
		private $endMonth;
		private $endYear;
		private $note;

		function __construct($myId, $yourId, $relationship, $startDay, $startMonth, $startYear, $endDay, $endMonth, $endYear, $note, $id=-1){		
			$this -> id = $id;
			$this -> myId = $myId;
			$this -> yourId = $yourId;
			$this -> relationship = $relationship;
			$this -> startDay = $startDay;
			$this -> startMonth = $startMonth;
			$this -> startYear = $startYear;
			$this -> endDay = $endDay;
			$this -> endMonth = $endMonth;
			$this -> endYear = $endYear;
			$this -> note = $note;
		}
		
		public function getId(){
			return $this -> id;
		}
									
		public function getMyId(){
			return $this -> myId;
		}
		
		public function getYourId(){
			return $this -> yourId;
		}
		
		public function getRelationship(){
			return $this -> relationship;
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
		
		public function getNote(){
			return $this -> note;
		}
		
		public function getNoun(){
			return $this -> noun;
		}
		
	}

	interface RelationFactory{
		function getRelationsByPerson($person);
		function getRelationById($id);
		function getRelationByPeople($me, $you);
		function getRelationTypes();
		function insertRelation($relation);
		function updateRelation($relation);
		function deleteRelation($relation);
	}
	
	class ReadOnlyRelationFactory implements RelationFactory {
		private $rf;
		
		function __construct($db) {
			$this -> rf = new SqlRelationFactory($db);
		}
		
		function getRelationsByPerson($person){
			return $this -> rf -> getRelationsByPerson($person);
		}
		
		function getRelationById($id){
			return $this -> rf -> getRelationById($id);
		}
		
		function getRelationByPeople($me, $you){
			return $this -> rf -> getRelationByPeople($me, $you);
		}
		
		function getRelationTypes(){
			return $this -> rf -> getRelationTypes();
		}
		
		function insertRelation($relation){
			return array('error' => 'can not insert relation: read only access');
		}
		
		function updateRelation($relation){
			return array('error' => 'can not update relation: read only access');
		}
		
		function deleteRelation($relation){
			return array('error' => 'can not delete relation: read only access');
		}
	}
	
	class SqlRelationFactory implements RelationFactory {// implements RelationFactory{
	
		private $db;

		function __construct($db){
			$this -> db = $db;
		}
	
		public function getRelationsByPerson($person){
			
			$pId = is_a($person, "Person") ? $person -> getId() : $person;
			$pId = $this -> db -> escapeString($pId);
			
			$sql = "SELECT * FROM Relation WHERE my_id=\"$pId\" ";
			
			$results = $this -> db -> execute($sql);
			
			$i = 0;
			$relationArray = array();

			while( $row = $this -> db -> resultArray($results) ){
				$relationArray[$i++] = new Relation( 
					trim($row['my_id']),
					trim($row['your_id']),
					trim($row['relationship']),
					trim($row['start_day']),
					trim($row['start_month']),
					trim($row['start_year']),
					trim($row['end_day']),
					trim($row['end_month']),
					trim($row['end_year']),
					trim($row['note']),
					trim($row['id'])
				);
			}
			
			return $relationArray;
		}
		
		public function getRelationById($id){
			$id = $this -> db -> escapeString($id);
			$sql = "SELECT * FROM Relation WHERE id=\"$id\"";

			$results = $this -> db -> execute($sql);
			$row = $this -> db -> resultArray($results); 

			return new Relation( 
				trim($row['my_id']),
				trim($row['your_id']),
				trim($row['relationship']),
				trim($row['start_day']),
				trim($row['start_month']),
				trim($row['start_year']),
				trim($row['end_day']),
				trim($row['end_month']),
				trim($row['end_year']),
				trim($row['note']),
				trim($row['id'])
			);
		}
		
		public function getRelationByPeople($me, $you){
			
			$myId = is_a($me, "Person") ? $me -> getId() : $me;
			$yourId = is_a($you, "Person") ? $you -> getId() : $you;
			
			$myId = $this -> db -> escapeString($myId);
			$yourId = $this -> db -> escapeString($yourId);

			$sql = "SELECT * FROM Relation WHERE my_id\"$myId\" AND your_id=\"$yourId\" ";

			$results = $this -> db -> execute($sql);
			$row = $this -> db -> resultArray($results); 

			return new Relation( 
				trim($row['my_id']),
				trim($row['your_id']),
				trim($row['relationship']),
				trim($row['start_day']),
				trim($row['start_month']),
				trim($row['start_year']),
				trim($row['end_day']),
				trim($row['end_month']),
				trim($row['end_year']),
				trim($row['note']),
				trim($row['id'])
			);
		}
		
		public function getRelationTypes(){
			$sql = "SELECT * FROM RelationType";

			$results = $this -> db -> execute($sql);

			$female = array();
			$male = array();

			while ($row = $this -> db -> resultArray($results)) {
				$female[ trim($row['id']) ] = array('id' => trim($row['id']), 'type' => trim($row['female']));
				$male[ trim($row['id']) ] = array('id' => trim($row['id']), 'type' => trim($row['male']));
			}
			
			return array('female' => $female, 'male' => $male);
		}
		
		public function insertRelation($relation){
			
			if ( is_a($relation, "Relation") ){
				
				if ( $relation -> getId() == -1 ) {
					
					$mi = $relation -> getMyId();
					$yi = $relation -> getYourId();
					$rel = $relation -> getRelationship();
					$sd = $relation -> getStartDay();
					$sm = $relation -> getStartMonth();
					$sy = $relation -> getStartYear();
					$ed = $relation -> getEndDay();
					$em = $relation -> getEndMonth();
					$ey = $relation -> getEndYear();
					$nt = $relation -> getNote();
					
					$mi = $this -> db -> escapeString($mi);
					$yi = $this -> db -> escapeString($yi);
					$rel = $this -> db -> escapeString($rel);
					$sd = $this -> db -> escapeString($sd);
					$sm = $this -> db -> escapeString($sm);
					$sy = $this -> db -> escapeString($sy);
					$ed = $this -> db -> escapeString($ed);
					$em = $this -> db -> escapeString($em);
					$ey = $this -> db -> escapeString($ey);
					$nt = $this -> db -> escapeString($nt);
					
					$sql = "SELECT * FROM RelationType WHERE id=\"$rel\"";
					$results = $this -> db -> execute($sql);
					$row = $this -> db -> resultArray($results);
					
					$inRel = trim($row['inverse']);

					$sql = "INSERT INTO Relation (my_id, your_id, relationship, start_day, start_month, start_year, end_day, end_month, end_year, note) ";
					$sql .= "VALUES (\"$mi\", \"$yi\", \"$rel\", \"$sd\", \"$sm\", \"$sy\", \"$ed\", \"$em\", \"$ey\", \"$nt\" ), ";
					$sql .= "(\"$yi\", \"$mi\", \"$inRel\", \"$sd\", \"$sm\", \"$sy\", \"$ed\", \"$em\", \"$ey\", \"$nt\" )";
					
					return array('row' => $row, 'inRel' => $inRel, 'success' => $this -> db -> execute($sql));

				} else {
					
					return $this -> updateRelation($relation);

				}
			}

			return array('error' => 'can not update relation: object was not a relation');

		}
		
		public function updateRelation($relation){
	
			if ( is_a($relation, "Relation") ){
				
					$mi = $relation -> getMyId();
					$yi = $relation -> getYourId();
					$rel = $relation -> getRelationship();
					$sd = $relation -> getStartDay();
					$sm = $relation -> getStartMonth();
					$sy = $relation -> getStartYear();
					$ed = $relation -> getEndDay();
					$em = $relation -> getEndMonth();
					$ey = $relation -> getEndYear();
					$nt = $relation -> getNote();

					$id = $relation -> getId();		
					
					$mi = $this -> db -> escapeString($mi);
					$yi = $this -> db -> escapeString($yi);
					$rel = $this -> db -> escapeString($rel);
					$sd = $this -> db -> escapeString($sd);
					$sm = $this -> db -> escapeString($sm);
					$sy = $this -> db -> escapeString($sy);
					$ed = $this -> db -> escapeString($ed);
					$em = $this -> db -> escapeString($em);
					$ey = $this -> db -> escapeString($ey);
					$nt = $this -> db -> escapeString($nt);
					
					$id = $this -> db -> escapeString($id);
					

					$sql = "SELECT * FROM RelationType WHERE id=\"$rel\"";
					$results = $this -> db -> execute($sql);
					$row = $this -> db -> resultArray($results);

					$inRel = trim($row['inverse']);

				if ( $id == -1 ) {
					
					$sql = "UPDATE Relation ";
					$sql .= "SET relationship=\"$rel\", start_day=\"$sd\", start_month=\"$sm\", start_year=\"$sy\", end_day=\"$ed\", end_month=\"$em\", end_year=\"$ey\", note=\"$nt\" ";
					$sql .= "WHERE my_id=\"$mi\" AND your_id=\"$yi\" ";
					$this -> db -> execute($sql);
					
					$sql = "UPDATE Relation ";
					$sql .= "SET relationship=\"$inRel\", start_day=\"$sd\", start_month=\"$sm\", start_year=\"$sy\", end_day=\"$ed\", end_month=\"$em\", end_year=\"$ey\", note=\"$nt\" ";
					$sql .= "WHERE my_id=\"$yi\" AND your_id=\"$mi\" ";
					
					return array('success' => $this -> db -> execute($sql));

				} else {
					
					$sql = "SELECT * FROM Relation WHERE id=\"$id\"";
					$results = $this -> db -> execute($sql);
					$row = $this -> db -> resultArray($results);
					
					$oldMi = trim($row['my_id']);
					$oldYi = trim($row['your_id']);
					
					if ($oldMi == $mi){
						if ($oldYi == $yi){
							
							$sql = "UPDATE Relation ";
							$sql .= "SET relationship=\"$rel\", start_day=\"$sd\", start_month=\"$sm\", start_year=\"$sy\", end_day=\"$ed\", end_month=\"$em\", end_year=\"$ey\", note=\"$nt\" ";
							$sql .= "WHERE my_id=\"$mi\" AND your_id=\"$yi\" ";
							$this -> db -> execute($sql);

							$sql = "UPDATE Relation ";
							$sql .= "SET relationship=\"$inRel\", start_day=\"$sd\", start_month=\"$sm\", start_year=\"$sy\", end_day=\"$ed\", end_month=\"$em\", end_year=\"$ey\", note=\"$nt\" ";
							$sql .= "WHERE my_id=\"$yi\" AND your_id=\"$mi\" ";
							
							return array('success' => $this -> db -> execute($sql));

						} else {
							
							$sql = "UPDATE Relation ";
							$sql .= "SET your_id=\"$yi\", relationship=\"$rel\", start_day=\"$sd\", start_month=\"$sm\", start_year=\"$sy\", end_day=\"$ed\", end_month=\"$em\", end_year=\"$ey\", note=\"$nt\" ";
							$sql .= "WHERE id=\"$id\" ";
							$this -> db -> execute($sql);

							$sql = "DELETE FROM Relation ";
							$sql .= "WHERE my_id=\"$oldYi\" AND your_id=\"$oldMi\" ";
							$this -> db -> execute($sql);

							$sql = "INSERT INTO Relation (my_id, your_id, relationship, start_day, start_month, start_year, end_day, end_month, end_year, note) ";
							$sql .= "VALUES (\"$yi\", \"$mi\", \"$inRel\", \"$sd\", \"$sm\", \"$sy\", \"$ed\", \"$em\", \"$ey\", \"$nt\" ) ";
							
							return array('success' => $this -> db -> execute($sql));

						}
					} else {
						if ($oldYi == $yi){
							
							$sql = "UPDATE Relation ";
							$sql .= "SET my_id=\"$mi\", relationship=\"$rel\", start_day=\"$sd\", start_month=\"$sm\", start_year=\"$sy\", end_day=\"$ed\", end_month=\"$em\", end_year=\"$ey\", note=\"$nt\" ";
							$sql .= "WHERE id=\"$id\" ";
							$this -> db -> execute($sql);

							$sql = "DELETE FROM Relation ";
							$sql .= "WHERE my_id=\"$oldYi\" AND your_id=\"$oldMi\" ";
							$this -> db -> execute($sql);

							$sql = "INSERT INTO Relation (my_id, your_id, relationship, start_day, start_month, start_year, end_day, end_month, end_year, note) ";
							$sql .= "VALUES (\"$yi\", \"$mi\", \"$inRel\", \"$sd\", \"$sm\", \"$sy\", \"$ed\", \"$em\", \"$ey\", \"$nt\" ) ";
							
							return array('success' => $this -> db -> execute($sql));

						} else {
							
							$sql = "UPDATE Relation ";
							$sql .= "SET my_id=\"$mi\", your_id=\"$yi\", relationship=\"$rel\", start_day=\"$sd\", start_month=\"$sm\", start_year=\"$sy\", end_day=\"$ed\", end_month=\"$em\", end_year=\"$ey\", note=\"$nt\" ";
							$sql .= "WHERE id=\"$id\" ";
							$this -> db -> execute($sql);

							$sql = "DELETE FROM Relation ";
							$sql .= "WHERE my_id=\"$oldYi\" AND your_id=\"$oldMi\" ";
							$this -> db -> execute($sql);

							$sql = "INSERT INTO Relation (my_id, your_id, relationship, start_day, start_month, start_year, end_day, end_month, end_year, note) ";
							$sql .= "VALUES (\"$yi\", \"$mi\", \"$inRel\", \"$sd\", \"$sm\", \"$sy\", \"$ed\", \"$em\", \"$ey\", \"$nt\" ) ";
							
							return array('success' => $this -> db -> execute($sql));

						}
					}				
				}
			}

			return array('error' => 'can not update relation: object was not a relation');
		}
		
		public function deleteRelation($relation){
	
			if ( is_a($relation, "Relation") ){

				$mi = $relation -> getMyId();
				$yi = $relation -> getYourId();
				
				$mi = $this -> db -> escapeString($mi);
				$yi = $this -> db -> escapeString($yi);

			} else {
				
				$relation = $this -> db -> escapeString($relation);
				
				$sql = "SELECT * FROM Relation WHERE id=\"$relation\"";
				$results = $this -> db -> execute($sql);
				$row = $this -> db -> resultArray($results);

				$mi = trim($row['my_id']);
				$yi = trim($row['your_id']);

			}
			
			$sql = "DELETE FROM Relation ";
			$sql .= "WHERE (my_id=\"$mi\" AND your_id=\"$yi\") OR (my_id=\"$yi\" AND your_id=\"$mi\") ";

			return array('success' => $this -> db -> execute($sql));

		}
	}

?>