<?php

# Database Class
class DB {

	# instance connection
	public $connection;

	# last connected database
	public $database;

	# singleton DB instance
	private static $instance;
	
	# toggle whether to always re-select the database -- it is a performance drain
	public static $always_select = FALSE;

	# debugging, don't send queries
	public static $debug = FALSE;

	# store all queries
	public $query_history = array();
	
	# store all query benchmarks
	public $query_benchmarks = array();

	# private constructor to enforce singleton access
	private function __construct($db = NULL) {
		
		# connect to database using credentials supplied by environment.php
		$this->connection = mysql_connect(DB_HOST, DB_USER, DB_PASS, TRUE);
		
		# If there are problems connecting...Show full message on local, email message and die gracefully on live
		if(mysql_error()) {
			if (IN_PRODUCTION) {
	
					# Email app owner
					$subject = "SQL Error";
					$body    = "<h2>SQL Error</h2> ".$sql." ".mysql_error($this->connection);
					$body   .= "<h2>Query History</h2>";
					foreach($this->query_history as $k => $v) {
						$body .= $k." = ".$v."<br>";
					}
					Utils::alert_admin($subject, $body);
					
					# Show a nice cryptic error
				    die("<h2>There's been an error processing your request (#DB46)</h2>");
			
				} else {
			 		die(Debug::dump("SQL Error: ".$sql." ".mysql_error()));
				}
		} 
	
		# use utf8 character encoding
		mysql_set_charset('utf8', $this->connection);

	}


	/*-------------------------------------------------------------------------------------------------
	singleton pattern:
	DB::instance(DB_NAME)->query('...');
	-------------------------------------------------------------------------------------------------*/
	public static function instance($db = NULL) {

		# use existing instance
		if (! isset(self::$instance)) {

			# create a new instance
			self::$instance = new DB($db);
		}

		# select database
		
		self::$instance->select_db($db);

		# return instance
		return self::$instance;

	}


	/*-------------------------------------------------------------------------------------------------

	-------------------------------------------------------------------------------------------------*/
	public function select_db($db = NULL) {
		
		# start benchmark	
		$this->benchmark_start = microtime(TRUE);
	
		# only select database if it hasn't already or a new database was specified
		if ($this->database === NULL || $db != $this->database || self::$always_select === TRUE) {
			
			# store specified database
			$this->database = $db;

			# select database
			mysql_select_db($this->database, $this->connection);
			
		}

	}


	/*-------------------------------------------------------------------------------------------------
	Perform a query with connected database
	This method is the go-to method for all the other methods in this class,
	Essentially a wrapper for PHP's mysql_query()
	-------------------------------------------------------------------------------------------------*/
	public function query($sql) {

		# if debugging, just return the query (if you want to see what the query looks like before executing it)
		# TODO: this should return an EXPLAIN of the query which gives us the benchmark as well
		if (self::$debug)
			return $sql;

		# store query history
		$this->query_history[] = $sql;
			
		# send query
		$result = mysql_query($sql, $this->connection);
		
		# store query benchmark
		$this->query_benchmarks[] = number_format(microtime(TRUE) - $this->benchmark_start, 4);
		
		# handle MySQL errors
		if (! $result) {
			
			# don't show error and sql query in production
			if (IN_PRODUCTION) {

				# Email app owner
				$subject = "SQL Error";
				$body    = "<h2>SQL Error</h2> ".$sql." ".mysql_error($this->connection);
				$body   .= "<h2>Query History</h2>";
				foreach($this->query_history as $k => $v) {
					$body .= $k." = ".$v."<br>";
				}
				Utils::alert_admin($subject, $body);
				
				# Show a nice cryptic error
			    die("<h2>There's been an error processing your request (#DB138)</h2>");
		
			} else {
		 		die(Debug::dump("SQL Error: ".$sql." ".mysql_error($this->connection)));
			}
		}		
		
		# return sucessful result
		return $result;

	}


	/*-------------------------------------------------------------------------------------------------
	Dump the last query
	-------------------------------------------------------------------------------------------------*/
	public function last_query($dump = TRUE) {
		
		# last query
		$last_query = end($this->query_history);

		# last query benchmarks
		$last_query_benchmark = end($this->query_benchmarks);

		# toggle dumping output or just returning query string
		return ($dump) ? Debug::dump("($last_query_benchmark sec) ".$last_query, "Last MySQL Query") : $last_query;

	}
	

	/*-------------------------------------------------------------------------------------------------
	Show entire query history w/benchmarks
	-------------------------------------------------------------------------------------------------*/
	public function query_history($dump = TRUE) {
		
		$history = array();
		
		# store total execution time
		$total_execution = 0;
		
		# build array with benchmarks
		foreach ($this->query_history as $i => $query) {
			
			if (isset($this->query_benchmarks[$i])) {

				$query = '('.$this->query_benchmarks[$i].' sec) '.$query;
				$total_execution += $this->query_benchmarks[$i];
				
			}
				
			$history[] = $query;
		}
		
		# add total query execution time to end
		$history[] = "MySQL Total Execution: $total_execution sec";
		
		# toggle dumping output or just returning query history array
		return ($dump) ? Debug::dump($history, "MySQL Query History", FALSE) : $history;

	}


	/*-------------------------------------------------------------------------------------------------
	When you just want to get one single value from the database
	Does *not* sanitize
	Returns the value (no array)
	
	Ex:
	$user_id = DB::instance(DB_NAME)->select_field("SELECT user_id FROM users WHERE id = 55");
	-------------------------------------------------------------------------------------------------*/
	public function select_field($sql) {

		$result = $this->query($sql);
		$row 	= mysql_fetch_array($result);
		$field  = $row[0];
		return $field;

	}


	/*-------------------------------------------------------------------------------------------------
	Select a single row from the database
	Optional $type can be 'assoc', 'array' or 'object'
	Does *not* sanitize
	Returns an array
	
	Ex:
	$user_details = DB::instance(DB_NAME)->select_row("SELECT * FROM users WHERE id = 55");
	-------------------------------------------------------------------------------------------------*/	
	public function select_row($sql, $type = 'assoc') {

		$result = $this->query($sql);
		$mysql_fetch = 'mysql_fetch_'.$type;
		return $mysql_fetch($result);

	}
	
	
	/*-------------------------------------------------------------------------------------------------
	Returns all the rows in an array
	Does *not* sanitize
	Optional $type can be 'assoc', 'array' or 'object'
	-------------------------------------------------------------------------------------------------*/
	public function select_rows($sql, $type = 'assoc') {

		$rows = array();
		$mysql_fetch = 'mysql_fetch_'.$type;

		$result = $this->query($sql);

		while($row = $mysql_fetch($result)) {
			$rows[] = $row;
		}

		return $rows;

	}
	
	
	/*-------------------------------------------------------------------------------------------------
	Alias to select_row for objects
	Does *not* sanitize
	-------------------------------------------------------------------------------------------------*/
	public function select_object($sql) {
		
		return $this->select_row($sql, 'object');
		
	}
		
		
	/*-------------------------------------------------------------------------------------------------
	Return a key->value array given two columns
	Does *not* sanitize
	Ex:
	$users = DB::instance(DB_NAME)->select_kv("SELECT user_id, first_name FROM users", 'user_id', 'name');
	-------------------------------------------------------------------------------------------------*/
	public function select_kv($sql, $key_column, $value_column) {
				
		$array = array();
		
		foreach ($this->select_rows($sql) as $row) {
			
			# avoid empty keys, but 0 is okay
			if ($row[$key_column] !== NULL && $row[$key_column] !== "")
				$array[$row[$key_column]] = $row[$value_column];
		}
		
		return $array;
		
	}
	
	
	/*-------------------------------------------------------------------------------------------------
	Takes select_rows one step further by making the index of the results array some specified field
	For example, if you wanted a full array of users where the index was the user_id, you could use this.
	Key column must be unique, otherwise data will overwrite itself in the array.
	Does *not* sanitize
	
	Ex: 
	$users = DB::instance(DB_NAME)->select_array('SELECT * FROM users', 'user_id');
	-------------------------------------------------------------------------------------------------*/
	public function select_array($sql, $key_column) {
	
		$array = array();
		
		foreach ($this->select_rows($sql) as $row) {
			
			# avoid empty keys, but 0 is okay
			if ($row[$key_column] !== NULL && $row[$key_column] !== "")
				$array[$row[$key_column]] = $row;
		}
		
		return $array;
	
	}


	/*-------------------------------------------------------------------------------------------------
	Insert a row given an array of key => values
	Returns the id of the row that was inserted
	Does sanitize
	
	Ex:
	$data    = Array("first_name" => "Joe", "last_name" => "Smith");
	$user_id = DB::instance(DB_NAME)->insert("users", $data);
	-------------------------------------------------------------------------------------------------*/
	# Alias 
	public function insert($table, $data) { return self::insert_row($table, $data); }
	public function insert_row($table, $data) {
						
		# setup insert statement
		$sql = "INSERT INTO $table SET";

		# add columns and values
		foreach ($data as $column => $value)
			$sql .= " $column = '".mysql_real_escape_string($value)."',";

		# remove trailing comma
		$sql = substr($sql, 0, -1);

		# perform query
		$this->query($sql);

		# return auto_increment id
		return mysql_insert_id();

	}
	
	
	/*-------------------------------------------------------------------------------------------------
	Accepts multi-dimensional $data array of rows
	Returns number of rows affected
	Does sanitize
	
	Ex:
	$data[] = Array("first_name" => "John", "last_name" => "Smith");
	$data[] = Array("first_name" => "Jane", "last_name" => "Doe");
		
	$results = DB::insert(DB_NAME)->insert_rows("users", $data);
	-------------------------------------------------------------------------------------------------*/
	public function insert_rows($table, $data) {
	
		# Fields
			$fields = "";
			foreach($data[0] as $field => $row) {
				$fields .= $field.",";
			}
			
			$fields = substr($fields, 0, -1);
							
		# Rows
			$row_string = "";
			$rows_string = "";
			foreach($data as $row) {				
				$row_string = "(";
				foreach($row as $field => $value) {
					$row_string .= "'".mysql_real_escape_string($value)."',";
				}	
				$row_string   = substr($row_string, 0, -1);
				$row_string  .= "),";
				$rows_string .= $row_string;
			}
			
			$rows_string = substr($rows_string, 0, -1);
			
		# Query
			$q = "INSERT INTO ".$table."
				  (".$fields.")
				VALUES
				  ".$rows_string;
				  				
		# Run it
			$run = $this->query($q);
			return mysql_affected_rows();		  
				 
	}


	/*-------------------------------------------------------------------------------------------------
	Update a single row given an array of key => values
	example $where_condition: "WHERE id = 1 LIMIT 1"
	Does sanitize
	
	Ex:
	$data = Array("first_name" => "John");
	DB::instance("users", $data, "WHERE user_id = 56");
	-------------------------------------------------------------------------------------------------*/
	# Alias
	public function update($table, $data, $where_condition) { return self::update_row($table, $data, $where_condition); }
	public function update_row($table, $data, $where_condition) {
	
		# setup update statement
		$sql = "UPDATE $table SET";

		# add columns and values
		foreach ($data as $column => $value) {
			# allow setting columns to NULL
			if ($value === NULL) {
				$sql .= " $column = NULL,";
			} else {
				$sql .= " $column = '".mysql_real_escape_string($value)."',";
			}
		}

		# remove trailing comma
		$sql = substr($sql, 0, -1);

		# Add condition
		$sql .= " ".$where_condition;

		# perform query
		$this->query($sql);
		
		return mysql_affected_rows();
		
	}	


	/*-------------------------------------------------------------------------------------------------
	If the primary key exists update row, otherwise insert row
	Requires primary id be first part of the data array - that's what it uses to check for duplicate
	Returns the created id
	Does sanitize
	
	Ex:
	$data    = Array("user_id" => 50", "first_name" => "Joe", "last_name" => "Smith");
	$user_id = DB::instance(DB_NAME)->update_or_insert_row("users", $data);
	-------------------------------------------------------------------------------------------------*/
	public function update_or_insert_row($table, $data) {
	
		# Build fields and values
			$fields = "";
			$values = "";
			$dup    = "";
			
			foreach($data as $field => $value) {
				$fields .= $field.",";
				$values .= "'".mysql_real_escape_string($value)."',";
				$dup    .= $field."="."'".mysql_real_escape_string($value)."',";
			}
			
			$fields = substr($fields, 0, -1);
			$values = substr($values, 0, -1);
			$dup    = substr($dup, 0, -1);
												
		# Query
			$q = "INSERT INTO ".$table."
				  (".$fields.")
				VALUES
				  (".$values.")
				 ON DUPLICATE KEY UPDATE ".$dup; 
				  ;
				  			
			$this->query($q);
		
		return mysql_insert_id();
	}


	/*-------------------------------------------------------------------------------------------------
	Just like above method, but for multiple rows
	If the primary key exists update, otherwise insert
	
	Requires primary id be first part of the data array - that's what it uses to check for duplicate
	Requires all fields to be present, otherwise a missing field will get set to blank
	Does sanitize
	
	Example SQL string result:
	
		INSERT INTO tasks (person_id,first_name,email) 
		VALUES (1,'Ethel','ethel@aol.com'),(3,'Leroy','leroy@hotmail.com'),(3,'Francis','francis@gmail.com')
		ON DUPLICATE KEY UPDATE first_name=VALUES(first_name),email=VALUES(email)'
	
	Ex:
		$data[] = Array("person_id" => 1, "first_name" => 'Ethel', "email" => 'ethel@aol.com');
		$data[] = Array("person_id" => 2, "first_name" => 'Leroy', "email" => 'leroy@hotmail.com');
		$data[] = Array("person_id" => 3, "first_name" => 'Francis', "email" => 'francis@gmail.com.com');	
		$update = DB::instance("courses_webstartwomen_com")->update_or_insert_rows('people', $data);						
	-------------------------------------------------------------------------------------------------*/
	public function update_or_insert_rows($table, $data) {
	
		# Build the fields string. Ex: (person_id,first_name,email)
		# And the duplicate key update string. Ex: first_name=VALUES(first_name),email=VALUES(email)
		# We do this by using the indexes on the first row of data
		# NOTE: The index of the data array has to start at 0 in order for this to work
			$fields = ""; 
			$dup    = "";
			foreach($data[0] as $index => $value) {
				$fields .= $index.",";
				$dup    .= $index."=VALUES(".$index."),";
			}
			
			# Remove last comma
			$fields = substr($fields, 0, -1);
			$dup = substr($dup, 0, -1);
				
		# Build the data string. Ex: (1,'Ethel','ethel@aol.com'),(3,'Leroy','leroy@hotmail.com'),(3,'Francis','francis@gmail.com')
			$values = "";
			foreach($data as $row) {
				
				$values .= "(";
				foreach($row as $value) {
					$values .= "'".mysql_real_escape_string($value)."',";
				}
				$values = substr($values, 0, -1);
				$values .= "),";
			}
			# Remove last comma
			$values = substr($values, 0, -1);
					
		# Put it all together	
			$sql = "INSERT INTO ".$table." (".$fields.") 
					VALUES ".$values."
					ON DUPLICATE KEY UPDATE ".$dup;
		
		# Run it
			$run = $this->query($sql);
			return mysql_affected_rows();	
	}
		

	/*-------------------------------------------------------------------------------------------------
	Ex:
	DB::instance(DB_NAME)->delete('users', "WHERE email = 'max@gmail.com'");
	Does *not* sanitize
	
	Returns 1 if it found something to delete
	-------------------------------------------------------------------------------------------------*/
	public function delete($table, $where_condition) {

		$sql = 'DELETE FROM '.$table.' '.$where_condition; 

		return $this->query($sql);

	}
	
	
	/*-------------------------------------------------------------------------------------------------
	Accepts an array or string of data
	Returns escaped data
	
	Ex:
	$_POST = DB::instance(DB_NAME)->sanitize($_POST);
	-------------------------------------------------------------------------------------------------*/
	public function sanitize($data) {
	
		if(is_array($data)){
		
			foreach($data as $k => $v){
				if(is_array($v)){
					$data[$k] = self::sanitize($v);
				} else {
					$data[$k] = mysql_real_escape_string($v, $this->connection);
				}
			}
			
		} else {
			$data = mysql_real_escape_string($data, $this->connection);
		}

		return $data;
	
	}
	
	
}
