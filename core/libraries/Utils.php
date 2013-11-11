<?php

// library for re-usable utility functions
// All methods should be static, accessed like: Utils::method(...);
class Utils {

	/*-------------------------------------------------------------------------------------------------
	Truncates a string to a certain char length, stopping on a word if not specified otherwise.
	-------------------------------------------------------------------------------------------------*/
	public static function truncate($string, $length, $stopanywhere = FALSE) {

	    if (strlen($string) > $length) {
	    
	        # limit hit
	        $string = substr($string,0,($length -3));
	    
	        # Stop anywhere
	        if ($stopanywhere) {
	            $string .= '...';
	        # Stop on a word
	        } else {
	            $string = substr($string,0,strrpos($string,' ')).'...';
	        }
	    }
	    return $string;
	}


	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public static function generate_random_string($length = 6) {
	
		$vowels     = 'aeuy';
		$consonants = 'bdghjmnpqrstvz';
		$string     = '';
		
		$alt = time() % 2;
		for ($i = 0; $i < $length; $i++) {
			if ($alt == 1) {
				$string .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			} else {
				$string .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}
		
		return $string;

	}


	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public static function postfix($string_to_add, $file_name) {

		# Get the extension
		$extension = strrchr($file_name, '.');
		
		# Now chop off the extension
		$file_name = str_replace($extension, "", $file_name);
		
		# Now piece it all back together
		return $file_name.$string_to_add.$extension;
	   
	}


	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public static function set_visit_time($identifier = NULL) {
		
		$cookie_name = "visit_".Router::$controller."_".Router::$method."_".$identifier;
		$cookie_value = Time::now();
				
		# Suppress notice for instances when cookie does not exist		
		$last_visit = @$_COOKIE[$cookie_name];	
				
		setcookie($cookie_name, $cookie_value, strtotime('+1 year'), '/');
		
		return $last_visit;
	
	}

	/*-------------------------------------------------------------------------------------------------
	Use: array_sort_by_column($array, 'order');
	-------------------------------------------------------------------------------------------------*/
	public static function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {

	    $sort_col = array();
	    
	    if(empty($arr)) return;
	  	     	       
	    foreach ($arr as $key => $row) {

		    # If we can't find the column, return
		    if(!array_key_exists($col, $row)) 
		    	return;		    
		    	
	        $sort_col[$key] = $row[$col];
	       
	    }
	
	    array_multisort($sort_col, $dir, $arr);
	}


	/*-------------------------------------------------------------------------------------------------
	NOTE this is configured for sending XML...
	-------------------------------------------------------------------------------------------------*/
	public static function curl($url, $timeout = 0, $ssl = true, $password = NULL, $post_fields = NULL, $xml = false) {
	
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$ch 		= curl_init();
		
		
		if($xml) {
			$header[]   = "Content-type: ".$type.";charset=\"utf-8\"";
			curl_setopt($ch, CURLOPT_HEADER, $header);
		}
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		
		# Convert to miliseconds		
		$timeout = $timeout * 1000;

		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeout);
		
		
		if($ssl) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);	
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		} 
		
		if($password)
			curl_setopt($ch, CURLOPT_USERPWD, $password);
	
		if($post_fields)
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);  
		
	
		$result = curl_exec ($ch);
		curl_close ($ch);
		return $result;
			
	}


	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public static function force_https($to_https = true) {
	
		$url = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
		$url = str_replace("index.php/", "", $url);
	
        if ($to_https) {
            // force https if not already
            if (! isset($_SERVER["HTTPS"])) {            	
                Router::redirect("https://".$url);
            } 
        }
        else {
            // force http if not already
            if (isset($_SERVER["HTTPS"])) {
                Router::redirect("http://".$url);
            } 
        }
	 
	}


	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public static function alert_admin($subject = NULL, $body = NULL) {
	
		# Email app owner
		
		if(SYSTEM_EMAIL) 
			$email = SYSTEM_EMAIL;
		else 
			$email = APP_EMAIL;
		
		$to[]    = Array("name" => APP_NAME, "email" => $email);
		$from    = Array("name" => APP_NAME, "email" => APP_EMAIL);
		
		$subject = APP_NAME." ".$subject;
		
		# Add Router and execution time
		$body .= '<h2>Routed Controller/Method:</h2> '.Router::$controller.'/'.Router::$method.'<br/>'.PHP_EOL;
		
		# Add cookies
		$body .= "<h2>Cookies</h2>";
		foreach($_COOKIE as $k => $v) $body .= $k." = ".$v."<br>";

		# Add _POST
		$body .= "<h2>POST</h2>";
		foreach($_POST as $k => $v) { $body .= $k." = ".$v."<br>"; }
		
		# Add _GET
		$body .= "<h2>GET</h2>";
		foreach($_GET as $k => $v) { $body .= $k." = ".$v."<br>"; }
		
		# Fire email
		Email::send($to, $from, $subject, $body, true, '', '');
		
	}


	/*-------------------------------------------------------------------------------------------------
	types: message, error
	-------------------------------------------------------------------------------------------------*/
	public static function quick_view($template, $message, $title = NULL, $type = 'message') {
	
		# Setup view
			$template->content     		= View::instance('v_message');
			$template->title       		= $title;
			$template->content->message = $message;
			$template->content->type    = $type;
			$template->hide_menu 		= TRUE;
			$template->hide_footer 		= TRUE;
		
		# Render view 
			echo $template;
	
	}
	
	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public static function post_only($post, $destination) {
	
		if(!$post) 
			return Router::redirect($destination);
	
	}
	
	/*-------------------------------------------------------------------------------------------------
	A JS version of this exists in /core/js/code_mirror.js
	-------------------------------------------------------------------------------------------------*/
	public static function code_mirror_replace_tags($content, $insert_line_breaks = FALSE) {
	
		$content = str_replace("</textarea", "&lt;/textarea", $content);
	
		$content = str_replace("<code>", "<textarea class='code'>", $content);
		$content = str_replace("</code>", "</textarea>", $content);
	
		$content = str_replace("<cm>", "<textarea class='code'>", $content);
		$content = str_replace("</cm>", "</textarea>", $content);
	
		$content = str_replace("<cmi>", "<textarea class='code inline'>", $content);
		$content = str_replace("</cmi>", "</textarea>", $content);
		
		if($insert_line_breaks) 
			$content = str_replace("\n", "<br>", $content);
		
		return $content;
	}


	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public static function print_colors($filename) {
	
		$handle   = fopen($filename, "rb");
		$colors   = fread($handle, filesize($filename));

		$colors = explode(";", $colors);
		
		foreach($colors as $color_info) {
		
			$color = explode("#", $color_info);
			$color = $color[1];
			
			echo "<div style='font-size:12px; width:200px; font-family:arial; padding:5px; margin:6px; background-color:".$color."'>".$color_info."</div>";
		
		}
		
		fclose($handle);
		
	}


	/*-------------------------------------------------------------------------------------------------
	given an array of k/v cookies, creates cookie(s) through PHP for the session	
	-------------------------------------------------------------------------------------------------*/
	public static function set_cookies($cookies) {
		
		// set cookies for current session (if headers haven't been sent already), not accessible until next request
		if (! headers_sent()) {
			foreach ($cookies as $name => $value)
				setcookie($name, $value, strtotime('+1 year'), '/');
		}
				
	}
	


    /*-------------------------------------------------------------------------------------------------
    
    -------------------------------------------------------------------------------------------------*/
    public static function load_client_files($files) {
    
    	$contents = "";
    
        foreach($files as $file) {
            
            if(strstr($file,".css")) {
            
            	if(strstr($file,"print_")) {
            		$contents .= '<link rel="stylesheet" type="text/css" href="'.$file.'" media="print">';
            	}
            	else {
	                $contents .= '<link rel="stylesheet" type="text/css" href="'.$file.'">';
	            }
            }
            else {
            	$contents .= '<script type="text/javascript" src="'.$file.'"></script>';	
            }

        }
        
        return $contents;
        
    }
    

	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public static function glob_recursive($pattern, $flags = 0) {
	
		$files = glob($pattern, $flags);
		
		foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
		    $files = array_merge($files, self::glob_recursive($dir.'/'.basename($pattern), $flags));
		}
		
		return $files;
	
	}
	
	
	/*-------------------------------------------------------------------------------------------------
	Returns array of strings found between two target strings
	-------------------------------------------------------------------------------------------------*/
	public static function string_extractor($string,$start,$end) {
														
		# Setup
			$cursor = 0;
			$foundString             = -1; 
			$stringExtractor_results = Array();
		 			 		
		# Extract  		
		while($foundString != 0) {
			$ini = strpos($string,$start,$cursor);
					
			if($ini >= 0) {
				$ini    += strlen($start);
				$len     = strpos($string,$end,$ini) - $ini;
				$cursor  = $ini;
				$result  = substr($string,$ini,$len);
				array_push($stringExtractor_results,$result);
				$foundString = strpos($string,$start,$cursor);	
			}
			else {
				$foundString = 0;
			}
		}
		
		return $stringExtractor_results;
		
	}
		
	
	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	// tests if the current request is an AJAX request by checking the X-Requested-With HTTP 
	// request header that most popular JS frameworks now set for AJAX calls.
	public static function is_ajax() {
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
	}	

	// given valid XML, returns a nicely formatted XML string with newlines and indenting
	public static function pretty_xml($xml) {

		$dom = new DOMDocument('1.0');
	  	$dom->preserveWhiteSpace = false;
	  	$dom->formatOutput = true;
	  	$dom->loadXML($xml);

	  	return $dom->saveXML();

	}

	// just converts to simplexml, then to array via simplexml_to_array
	public static function xml_to_array($xml, $attributesKey = null, $childrenKey = null) {

		$simpleXML = simplexml_load_string($xml);
		return Utils::simplexml_to_array($simpleXML, $attributesKey, $childrenKey);

	}

	// Converts a SimpleXMLElement to an associative array, with attributes and child nodes as key => value
	// set attributesKey or childrenKey to TRUE to get child arrays like '@attributes' => array(..) and '@children' => array(..)
	// if some of your XML attributes have the same name as child nodes, set attributesKey to TRUE to get a separate '@attributes' child
	// from php.net/simplexml
	public static function simplexml_to_array(SimpleXMLElement $xml, $attributesKey = null, $childrenKey = null, $valueKey = null) {
		if ($childrenKey && !is_string($childrenKey)) {
			$childrenKey = '@children';
		}
		if ($attributesKey && !is_string($attributesKey)) {
			$attributesKey = '@attributes';
		}
		if ($valueKey && !is_string($valueKey)) {
			$valueKey = '@values';
		}
		$return = array();
		$name   = $xml->getName();
		$_value = trim((string) $xml);
		if (!strlen($_value)) {
			$_value = null;
		}
		;
		if ($_value !== null) {
			if ($valueKey) {
				$return[$valueKey] = $_value;
			} else {
				$return = $_value;
			}
		}
		$children = array();
		$first    = true;
		foreach ($xml->children() as $elementName => $child) {
			$value = self::simplexml_to_array($child, $attributesKey, $childrenKey, $valueKey);
			if (isset($children[$elementName])) {
				if (is_array($children[$elementName])) {
					if ($first) {
						$temp = $children[$elementName];
						unset($children[$elementName]);
						$children[$elementName][] = $temp;
						$first                    = false;
					}
					$children[$elementName][] = $value;
				} else {
					$children[$elementName] = array(
						$children[$elementName],
						$value
						);
				}
			} else {
				$children[$elementName] = $value;
			}
		}
		if ($children) {
			if ($childrenKey) {
				$return[$childrenKey] = $children;
			} else {
				$return = array_merge($return, $children);
			}
		}
		$attributes = array();
		foreach ($xml->attributes() as $name => $value) {
			$attributes[$name] = trim($value);
		}
		if ($attributes) {
			if ($attributesKey) {
				$return[$attributesKey] = $attributes;
			} else {
				$return = array_merge($return, $attributes);
			}
		}
		return $return;
	}


		

} # eoc
