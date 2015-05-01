<?php
	class Note{
		private $id;
		private $personId;
		private $startDate;
		private $endDate;
		private $note;
		
		function __construct($personId, $note, $id=-1){
			$this -> id = $id;
			$this -> personId = $personId;
			$this -> note = $note;
		}
		
		public function getId(){
			return $this -> id;
		}
		
		public function getPersonId(){
			return $this -> personId;
		}
		
		public function getNote(){
			return $this -> note;
		}
	}
	
	interface NoteFactory{
		function getNotesByPerson($person);
		function getNoteById($id);
		function insertNote($note);
		function updateNote($note);
		function deleteNote($note);
	}
	
	class ReadOnlyNoteFactory implements NoteFactory{
		private $nf;
		
		function __construct($db) {
			$this -> nf = new SqlNoteFactory($db);
		}
		
		public function getNotesByPerson($person){
			return $this -> nf -> getNotesByPerson($person);
		}
		
		public function getNoteById($id){
			return $this -> nf -> getNoteById($id);
		}
		
		public function insertNote($note){
			return array('error' => 'can not insert note: read only access');
		}
		
		public function updateNote($note){
			return array('error' => 'can not update note: read only access');
		}
		
		public function deleteNote($note){
			return array('error' => 'can not delete note: read only access');
		}
	}
	
	class SqlNoteFactory implements NoteFactory{
		private $db;
		
		function __construct($db){
			$this -> db = $db;
		}
		
		public function getNotesByPerson($person){
			
			$pId = is_a($person, "Person") ? $person -> getId() : $person;
			
			$pId = $this -> db -> escapeString($pId);
			
			$sql = "SELECT * FROM Note WHERE person_id=\"$pId\"";

			$results = $this -> db -> execute($sql);
			
			$i = 0;
			$noteArray = array();
			
			while( $row = $this -> db -> resultArray($results) ){
				$noteArray[$i++] = new Note( 
					trim($row['person_id']),
					trim($row['note']),
					trim($row['id'])
				);
			}
			
			return $noteArray;
		}
		
		public function getNoteById($id){
			$id = $this -> db -> escapeString($id);
			
			$sql = "SELECT * FROM Note WHERE id=\"$id\"";

			$results = $this -> db -> execute($sql);
			$row = $this -> db -> resultArray($results);
			
			return new Note( 
				trim($row['person_id']),
				trim($row['note']),
				trim($row['id'])
			);
		}
		
		public function insertNote($note){
			
			if ( is_a($note, "Note") ){
				
				if ( $note -> getId() == -1 ) {
					
					$pId = $note -> getPersonId();
					$no = $note -> getNote();
					
					$pId = $this -> db -> escapeString($pId);
					$no = $this -> db -> escapeString($no);

					$sql = "INSERT INTO Note (person_id, note) ";
					$sql .= "VALUES (\"$pId\", \"$no\" )";

					return array('success' => $this -> db -> execute($sql));

				} else {
					
					return $this -> updateNote($note);
					
				}
				
			}
			
			return array('error' => 'can not insert note: object was not a note');
			
		}
		
		public function updateNote($note){
			
			if ( is_a($note, "Note") ){
				
				$id = $note -> getId();

				if ( $id != -1 ) {
					$pId = $note -> getPersonId();
					$no = $note -> getNote();

					$pId = $this -> db -> escapeString($pId);
					$no = $this -> db -> escapeString($no);

					$id = $this -> db -> escapeString($id);

					$sql = "UPDATE Note ";
					$sql .= "SET person_id=\"$pId\", note=\"$no\" ";
					$sql .= "WHERE id=\"$id\" ";
					
					return array('success' => $this -> db -> execute($sql));
				}
			}
			
			return array('error' => 'can not update note: object was not a note');
			
		}
		
		public function deleteNote($note){
			
			$sql = "DELETE FROM Note ";

			if ( is_a($note, "Note") ){
								
				$id = $note -> getId();

				if ( $id != -1 ) {
					
					$id = $this -> db -> escapeString($id);
					$sql .= "WHERE id=\"$id\"";

				}
				
			} else {
				
				$note = $this -> db -> escapeString($note);
				$sql .= "WHERE id=\"$note\"";

			}
			
			return array('success' => $this -> db -> execute($sql));
		}
		
	}

?>