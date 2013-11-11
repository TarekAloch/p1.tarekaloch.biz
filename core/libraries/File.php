<?php 

// File and path utilities
class File {
	
	// cache file paths
	protected static $caching = FALSE;
	
	// include/search paths that are used to find files
	protected static $_paths = array(APP_PATH, CORE_PATH);
	
	// file path cache, used when caching is true
	protected static $_files = array();
	
	// Searches for a file in the application, and returns the path to the file that has the highest precedence.
	// Example: File::find('views/templates/v_users_login'); 
	// Returns an absolute path to /path/to/www/views/templates/v_users_login.php
	// You can specify an additional search_path for looking in non-standard dirs first (such as other APP_PATHS or outside of DOC_ROOT)
	public static function find($path, $search_path = NULL) {

		// Create a partial path of the filename
		$path = $path.'.php';
						
		// don't cache files outside of the usual search_path
		if (self::$caching === TRUE AND $search_path === NULL AND isset(self::$_files[$path])) {
			
			// This path has been cached
			return self::$_files[$path];
		}

		// The file has not been found yet
		$found = FALSE;
		
		// add custom search_path
		if ($search_path !== NULL) {
			
			// expand references and force trailing slash
			$search_path = realpath($search_path).'/';
						
			// prefix to search paths array
			array_unshift(self::$_paths, $search_path);
			
		}

		// search include paths
		foreach (self::$_paths as $dir) {
											
			if (is_file($dir.$path)) {

				// A path has been found
				$found = $dir.$path;

				// Stop searching
				break;
			}
		}
		

		if (self::$caching === TRUE) {
			
			// Add the path to the cache
			self::$_files[$path] = $found;

		}

		return $found;
	}
	
	// Library / Class autoloader
	// A simple auto loader that allows us to call classes and have them auto-included if they aren't already.
	// It checks the current app and parent "libraries" and "vendors" directories, giving precidence to the app folders.
	public static function autoloader($class) {	

		// return if class already exists
		if (class_exists($class, FALSE)) {
			return TRUE;
		}

		// first check for a controller
		if (strstr($class, 'controller')) {
			
			// format to controller filename convention "c_<controller>.php"
			$file = 'c_'.str_replace('_controller', '', $class);
						
			if ($path = File::find('controllers/'.$file)) {

				require $path;
				return TRUE;
				
			} else {
				
				return FALSE;
				
			}

		// try the libraries folders
		} elseif ($path = File::find('libraries/'.$class)) {
		
			require $path;
			return TRUE;

		// try the vendors folders
		} elseif ($path = File::find('vendors/'.$class.'/'.$class)) {

			require $path;
			return TRUE;

		}

		// couldn't find the file
		return FALSE;

	}
}