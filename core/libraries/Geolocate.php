<?php

// library for re-usable utility functions
// All methods should be static, accessed like: Utils::method(...);
class Geolocate {
	

	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public static function locate() {
		
		# first try to use geoIP library for IP address, since it uses it's own IP detection
		$ip = @$_SERVER['GEOIP_ADDR'];

		if (empty($ip))
			$ip = self::ip_address();
			
		# if we're on local inject an IP address
		if (!IN_PRODUCTION)
			 $ip = "76.109.14.196"; // Miami, FL
			// $ip = "24.44.58.79";    # Connecticut
			// $ip = "78.86.225.25";   # Great Britain

		# If we want to mimick being a foreign country we can create an IP cookie
		$ip = ! empty($_COOKIE["IP"]) ? $_COOKIE["IP"] : $ip;

		# Default values (if nothing is found)
		$geo 				 = array();
		$geo['ip'] 			 = $ip;
		$geo['country_code'] = 'US';
		$geo['state'] 		 = NULL;

		if(ENABLE_GEOLOCATION)
			$geo = self::geoplugin($ip);
		
		# Debug info
		//echo '<!-- User Geolocation: '.print_r($geo, TRUE).' -->';
		
		return $geo;
		
	}

	
	
	
	/*-------------------------------------------------------------------------------------------------
	// get the user's IP Address
	-------------------------------------------------------------------------------------------------*/
	public static function ip_address() {

		// server keys that could contain the client IP address
		$keys = array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR');

		foreach ($keys as $key) {
			if (isset($_SERVER[$key]) && ! empty($_SERVER[$key])) {
				// IP address has been found
				$ip = $_SERVER[$key];
				break;
			}
		}

		if ($comma = strrpos($ip, ',') !== FALSE) {
			$ip = substr($ip, $comma + 1);
		}

		// check for valid IP
		if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			// use an empty IP
			$ip = '0.0.0.0';
		}

		return $ip;
	}
	
	
	/*-------------------------------------------------------------------------------------------------
	http://www.geoplugin.net/
	-------------------------------------------------------------------------------------------------*/
	public static function geoplugin($ip) {
	
		$url  = 'http://www.geoplugin.net/php.gp?ip='.$ip;
		$data = Utils::curl($url, 3, false, '', '', false);
		$data = unserialize($data);
			
		if(!$data) 
			Utils::alert_admin("Geolocation exceeded time limit for IP ".$ip, "");
			
		$geo 				 = array();
		$geo['ip'] 			 = $ip;
		$geo['country_code'] = $data['geoplugin_countryCode'];
		$geo['state'] 		 = $data['geoplugin_region'];
		$geo['city'] 		 = $data['geoplugin_city'];
		
		return $geo;
	
	}
	
	
} # end class
