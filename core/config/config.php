<?php

# Http protocol
	define('PROTOCOL', (isset($_SERVER['HTTPS'])) ? 'https://' : 'http://');

# App url
	define('APP_URL', PROTOCOL.$_SERVER['HTTP_HOST'].'/');
		
# All routes go through index.php	
	define('ROUTER', 'index.php');

# All of the following constants only get set if they're not already defined by app level constants

# Set locale
	if(!defined('LOCALE')) define('LOCALE', 'en_US');
    setlocale(LC_ALL, LOCALE);
    
# Set timezone
	if(!defined('TIMEZONE')) define('TIMEZONE', 'UTC'); 
    date_default_timezone_set(TIMEZONE);
    
# Error reporting - default to off. Set in environment.php to turn on
	if(!defined('DISPLAY_ERRORS')) define('DISPLAY_ERRORS', FALSE);
	
	if(DISPLAY_ERRORS) error_reporting(-1); // Report all PHP errors
	else error_reporting(0); // Turn off all error reporting
	
# Default log location
	if(!defined('LOG_PATH')) define('LOG_PATH', APP_PATH.'logs/');

# Default time format
	if(!defined('TIME_FORMAT')) define('TIME_FORMAT', 'F j, Y g:ia'); 
	if(!defined('ENABLE_GEOLOCATION')) define('ENABLE_GEOLOCATION', TRUE);
	
# Default encrypting salts	
	if(!defined('PASSWORD_SALT')) define('PASSWORD_SALT', 'commodore64'); 
	if(!defined('TOKEN_SALT')) define('TOKEN_SALT', 'fluxcapacitor'); 

# Default Image / Avatar settings
	if(!defined('AVATAR_PATH')) define('AVATAR_PATH', "/uploads/avatars/");
	if(!defined('SMALL_W')) define('SMALL_W', 200);
	if(!defined('SMALL_H')) define('SMALL_H', 200);
	if(!defined('PLACE_HOLDER_IMAGE')) define('PLACE_HOLDER_IMAGE', "/core/images/placeholder.png");

# Default app settings
	if(!defined('APP_EMAIL')) define('APP_EMAIL', 'webmaster@myapp.com'); # Should match domain name to avoid hitting the spam box
	if(!defined('APP_NAME')) define('APP_NAME', 'My APp'); # Should match domain name to avoid hitting the spam box
	if(!defined('SYSTEM_EMAIL')) define('SYSTEM_EMAIL', 'webmaster@myapp.com'); 

# Whether or not to send outgoing emails - default to off. Set in environment.php to turn on.
	if(!defined('ENABLE_OUTGOING_EMAIL')) define('ENABLE_OUTGOING_EMAIL', FALSE);
