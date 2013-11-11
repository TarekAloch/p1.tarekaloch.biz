<?php

class DB_Test extends UnitTestCase {

	private $db_name = "DB_Test";
	
	function __construct() {
	
		# Create a test database
		DB::instance()->query("CREATE DATABASE ".$this->db_name.";");
		DB::instance($this->db_name)->select_db($this->db_name);
		
		# Create table
		$q = "CREATE TABLE users
			(
			user_id int NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(user_id),
			first_name varchar(255),
			last_name varchar(255)
			)";
			
		DB::instance($this->db_name)->query($q);
	
	}
	
	
    function testInsert() {
       
       $user_id = DB::instance($this->db_name)->insert('users', Array('first_name' => 'Joe', 'last_name' => 'Smith'));
       $this->assertNotNull($user_id);

       $delete = DB::instance($this->db_name)->delete('users', "WHERE first_name = 'Jo'");
       $this->assertTrue($delete);

    }
    
  
    function testLastQuery() {
    
    	$last_query = DB::instance($this->db_name)->last_query();	
    	$this->assertNotNull($last_query);
    
    }
    
     function testQueryHistory() {
    
	    // Gives us back Krumo debug. If it contains the plural "elements" it's recognizing the above queries. If it's singular, it means it isn't.
    	$query_history = DB::instance($this->db_name)->query_history();	
    	
    	$needle_found = strpos($query_history, "elements");
    	
    	$this->assertTrue($needle_found > 0);
        
    }
    
    
    function testSelectField() {
    
       self::__insert();
    
       $q = "SELECT first_name 
       	FROM users 
       	WHERE first_name = 'Joe'";
       
       $first_name = DB::instance($this->db_name)->select_field($q);
       
       $this->assertTrue($first_name == "Joe");
    
       self::__delete();
       
    
    }
    
    
    function testSelectRow() {
    
    	self::__insert();
    	
    	$user = DB::instance($this->db_name)->select_row("SELECT * FROM users WHERE first_name = 'Joe'");
    	    	     	
    	$this->assertIsA($user, 'array');
    	
    	self::__delete();
    	 
    }
    
    
    function testSelectObject() {
    
    	self::__insert();
    	
    	$user = DB::instance($this->db_name)->select_object("SELECT * FROM users WHERE first_name = 'Joe'");
        		
    	$this->assertIsA($user, 'stdClass');
    	
    	self::__delete();
    
    }
    
    
    function testSelectRows() {
    	
    	self::__insert();
    	self::__insert();
    	
    	$users = DB::instance($this->db_name)->select_rows("SELECT * FROM users");	 		    	
    	$this->assertTrue(sizeof($users) == 2);
    	
    	self::__delete();
    	
    }
    
    
    function testSelectKv() {
    
    	self::__insert();
    	self::__insert();
    	
    	$users = DB::instance($this->db_name)->select_kv("SELECT user_id, first_name FROM users", "user_id", "first_name");
    	
    	foreach($users as $k => $v) {
    		$this->assertTrue($v = "Joe");
    	}
    	    	    	
    	self::__delete();
    
    }
    
    
    function testSelectArray() {
    
    	self::__insert();
    	self::__insert();
    	
    	$users = DB::instance($this->db_name)->select_array('SELECT * FROM users', 'first_name');
    	
  
    	foreach($users as $k => $v) {
    		//$this->assertTrue($k == "Joe");
    	}
    	
    	self::__delete();
    
    }
    
    
    function testUpdateOrInsertRow() {
    	
    	# This should be an insert
    	$data    = Array("first_name" => "Joe", "last_name" => "Smith");
    	$user_id = DB::instance($this->db_name)->update_or_insert_row("users", $data, "WHERE 0");    	    	
    	$this->assertTrue($user_id > 1);
    	
    	# This should be an update
    	$data = Array("user_id" => $user_id, "first_name" => "Joe", "last_name" => "Roberts");
    	$results = DB::instance($this->db_name)->update_or_insert_row("users", $data, "WHERE user_id = ".$user_id);
    	  
    	# Test by making sure our table still only has one entry
    	$count = DB::instance($this->db_name)->select_field("SELECT COUNT(user_id) FROM users");
    	$this->assertTrue($count == 1);
    	
    	self::__delete();
    	
    	
    }
    
    
    function testUpdateOrInsertRows() {
            	
    	# This should be an insert
    	$data[]        = Array("first_name" => "Joe", "last_name" => "Smith");
    	$data[]        = Array("first_name" => "Jane", "last_name" => "Doe");
    	$affected_rows = DB::instance($this->db_name)->update_or_insert_rows("users", $data, "WHERE 0");    	
    	$this->assertTrue($affected_rows == 2);
    	
    	# Get the user_id of the field we just inserted so we can update it
    	$user_id = DB::instance($this->db_name)->select_field("SELECT user_id FROM users");
    	
    	# This should be an update on just Joe
    	$data   = "";
    	$data[] = Array("user_id" => $user_id, "first_name" => "Joe", "last_name" => "Roberts");
    	$results = DB::instance($this->db_name)->update_or_insert_rows("users", $data, "WHERE user_id = ".$user_id);
    	  
    	# Test by making sure our table still only has two entries
    	$this->assertTrue(self::__row_count() == 2);
    	
    	self::__delete();
    	
    }


    function testDelete() {
   	
   		self::__insert();
   		
   		DB::instance($this->db_name)->DELETE("users", "WHERE first_name = 'Joe'");
   		
   		$this->assertTrue(self::__row_count() == 0);
   		 
    }
    
    
    function testSanitize() {
    
    	# Sanitize a string
	    	$data = "O'Brien";
		    $data = DB::instance($this->db_name)->sanitize($data);
		    $this->assertTrue($data == "O\'Brien");
	    
	    # Santize an array
	   		$data = Array("last_name" => "O'Brien");
	   		$data = DB::instance($this->db_name)->sanitize($data);
	   		$this->assertTrue($data['last_name'] == "O\'Brien");
	    
	    # Sanitize a multi-dimensional array
		    $data = Array("user1" => Array("last_name" => "O'Brien"),
		    			  "user2" => Array("last_name" => "O'Neil"),
		    			  );
		    			  
		    $data = DB::instance($this->db_name)->sanitize($data);
		    $this->assertTrue($data['user1']['last_name'] == "O\'Brien");
	    
    }
    
    function testDrop() {
    
    	# Don't really need to test this, but do want to delete our test database
    	# Can't use __deconstruct because this class doesn't get destroyed
	    DB::instance($this->db_name)->query("DROP DATABASE ".$this->db_name.";");
	    
	}
    
   
    function __insert() {
    	return DB::instance($this->db_name)->insert('users', Array('first_name' => 'Joe', 'last_name' => 'Smith'));
    }
    
    function __delete() {
    	DB::instance($this->db_name)->query('DELETE FROM users');
    }
    
    function __row_count() {
    	return DB::instance($this->db_name)->select_field("SELECT COUNT(user_id) FROM users");
    }

    
   
    
}