<?php

// Library for re-usable debugging functions
// All methods should be static, accessed like: Debug::method(...);
class Debug {
	
	
	/*-------------------------------------------------------------------------------------------------
	Dumper function, currently a wrapper for krumo::dump
	-------------------------------------------------------------------------------------------------*/
	public static function dump($data, $label = NULL, $backtrace = TRUE) {
		
		ob_start();

		krumo::dump($data, $label, $backtrace);

		return ob_get_clean();
		
	}
	
	
	/*-------------------------------------------------------------------------------------------------
	General debug info for page footer
	-------------------------------------------------------------------------------------------------*/
	public static function info() {
	
		// disable in production, except from PJ HQ ip address
		if (isset($_GET['debug']) && strtolower($_GET['debug']) != "false") {

			// debug block w/ execution time and router info
			echo PHP_EOL.'<div style="font-family: monospace; font-size: 13px; width: 80%; margin: 20px auto;"><b style="color: #008800;">DEBUG INFO</b><br/>'.PHP_EOL;
			echo '<b>Routed Controller/Method:</b> '.Router::$controller.'/'.Router::$method.'<br/>'.PHP_EOL;
			echo '<b>Execution Time:</b> '.EXECUTION_TIME.' sec'.PHP_EOL;
			
			// show mysql query history
			echo DB::instance()->query_history().PHP_EOL;
			
			// show included files
			krumo::includes(FALSE).PHP_EOL;
			
			echo '<br/></div>'.PHP_EOL;

		// disable krumo in production
		} elseif (IN_PRODUCTION) {
			
			// disable krumo output
			krumo::disable();
			
		}
		
	}
	
	
	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public static function console($message) {
		define("NL","\r\n");
	    echo '<script type="text/javascript">'.NL;
	    echo 'console.log("'.$message.'");'.NL;    
	    echo '</script>'.NL;
	
	}
	
}