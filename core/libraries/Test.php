<?php
# http://www.simpletest.org/

require_once(DOC_ROOT.'/core/vendors/simpletest/autorun.php');

class Test {
		
	# Paths to search for tests	
	protected static $paths;
	
	# How we identify the test files
	protected static $test_postfix = "_Test.php";
	
	
	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public function __construct() {	
	
	}
	
	
	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public static function run() {
	
		# All the directories we'll look for tests
		$dirs = array(
			DOC_ROOT.'core/libraries/',
			APP_PATH.'controllers/',
			APP_PATH.'libraries/',
		);	
		
		# Loop through the directories
		foreach($dirs as $dir) {	
					
			foreach(glob($dir."*".self::$test_postfix) as $file) {
	
				# Run the test file
				require_once($file);
				
			}
		}
	}
	
} 