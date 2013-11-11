<?php

session_start();

# Start benchmark
    $benchmark_start = microtime(TRUE);
     
# We need the File library for autoloader
    require_once CORE_PATH.'/libraries/File.php';

# Register class autoloader
	spl_autoload_register(array('File', 'autoloader'));
	    	
# Load core configs
	require CORE_PATH."/config/config.php";

# Stop benchmark, get execution time
    define('EXECUTION_TIME', number_format(microtime(TRUE) - $benchmark_start, 4));

# Optionally show debugging info if not in production. to enable add ?debug to URI
    Debug::info();

