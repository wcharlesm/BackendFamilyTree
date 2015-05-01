<?php
	interface SqlDatabase {
		function execute($sql);
	}
	
	class MySqlDatabase implements SqlDatabase{
		private $connection;
		private $lastquery;
		private $err_opening = "<font color=\"red\"><b>ERROR!</b><br/> ";
		
		function __construct($serv, $db, $un, $pw){
			
			$this -> connection = mysql_connect($serv, $un, $pw);
			
			if(!$this -> connection){
			
				die($this -> err_opening . "Database connection failed: " . mysql_error() . "</font>");
			
			}else{
			
				$db_select = mysql_select_db($db, $this -> connection);
			
				if(!$db_select){
						
					die($this -> err_opening . "Database selection failed: " . mysql_error() . "</font>");
		
				}
		
	
			}
		}
		
		function __destruct(){
			if( isset($this -> connection) ){
		
				mysql_close($this -> connection);
				
				unset($connection);
			}
		}
		
		public function execute($sql){
			$this -> lastQuery = $sql;
		
			$result = mysql_query($sql, $this -> connection);
			
			if(!$result){
		
				$output = $this -> err_opening;
				$output .= "Database query failed: " . mysql_error() . "<br/>";
				$output .= "<small>Last SQL query: <b>" . $this -> last_query;
				$output .= "</b></small></font>";
			
				die($output);
				
			}
			
			return $result;
		}
		
		public function lastInsertedId(){
			return mysql_insert_id();
		}
		
		public function resultArray($result_set){
	
			return mysql_fetch_array($result_set);
		
		}
		
		public function numRows($result_set){
		
			return mysql_num_rows($result_set);
		
		}
		
		public function escapeString($string){
			
			return mysql_real_escape_string($string);
			
		}
	}
?>




