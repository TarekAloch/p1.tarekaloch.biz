<?php

class User {
	
	# Cache user in this class
	private $_user;
	
	# Can't use the email_template defined in base_controller
	public $email_template;
	
	private $token;
	
	public function __construct() {
		
		# Look for the token cookie
		$this->token = @$_COOKIE['token'];
	
		$this->email_template = View::instance('_v_email');		
	}
	
	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public function authenticate() {
		
		# If we have cookie token, load that user
		if(!empty($this->token)) {
			return $this->__load_user(); 
		}
		
		# Otherwise, return false, they're not logged in
		return false;

	}

		
	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public function __load_user() {

		# Retreive from cache, reduce DB calls
		if (! isset($this->_user)) {
						
			# Load user from DB
				$q = "SELECT *
					FROM users
					WHERE token = '".$this->token."'
					LIMIT 1";	
					
				$this->_user = DB::instance(DB_NAME)->select_row($q, "object");
																			
			# Configure user's avatar (if they're logged in)
				if($this->_user) {
					if(@!$this->_user->avatar) 
						$this->_user->avatar = PLACE_HOLDER_IMAGE;
					else 
						$this->_user->avatar = AVATAR_PATH.$this->_user->avatar;	
												
					$this->_user->avatar_small  = Utils::postfix("_200_200", $this->_user->avatar);
					$this->_user->avatar_medium = Utils::postfix("_600_400", $this->_user->avatar);
				}

		}
				
		# Done
		return $this->_user;

	}
	

	/*-------------------------------------------------------------------------------------------------
	Returns token or false
	-------------------------------------------------------------------------------------------------*/
	public function login($email, $password) {
				
		# Hash password
		$password = sha1(PASSWORD_SALT.$password);
		
		# See if we can login
		$q = "SELECT token 
			FROM users 
			WHERE email = '".$email."' 
			AND password = '".$password."'";
					
		$token = DB::instance(DB_NAME)->select_field($q);	
					
		# If we get a token back, we were successful...
		if($token) {
		
			$this->__set_login_cookie($token);
		
			# Update their timezone on login
			if(@$_POST['timezone'])
				DB::instance(DB_NAME)->update("users", Array("timezone" => $_POST['timezone']), "WHERE token = '".$token."'");
					
			return $token;
		}
		# Failed
		else {
			return false;	
		}
				
	}
	
	
	/*-------------------------------------------------------------------------------------------------
	Where do we go after logging in / attempting to login?
	-------------------------------------------------------------------------------------------------*/
	public function login_redirect($token, $email, $destination) {
		
		# Success - send them to their destination
		if($token) {
			Router::redirect($destination);
		}
		# Fail - try and figure out why
		else {
			# Do we even have a user with that email?
			$found_email = DB::instance(DB_NAME)->select_field("SELECT email FROM users WHERE email = '".$email."'");
						
			# If we found the email, then the problem must be the password
			$error = ($found_email) ? "password" : "email";
			
			# Send them back to the login page
			Router::redirect('/users/login/?error='.$error.'&email='.$email.'&ref='.$destination);
		
		}
	
	}
	
	
	/*-------------------------------------------------------------------------------------------------
	Create a user row, with geolocation info
	-------------------------------------------------------------------------------------------------*/
	public function signup($data = array()) {
			
		# Check for duplicate emails via JS / Ajax, but double check here
		if( !$this->confirm_unique_email($data['email'])) 
			return Router::redirect('/users/login/?error=signup');
		
		# Geolocate user 
		$geolocation = Geolocate::locate();		
		
		# Start our user array
		$user = array(
			'created'      => time(),
			'modified'     => time(),
			'ip' 		   => $geolocation['ip'],
			'country'      => $geolocation['country_code'],
			'state'        => $geolocation['state'],
			'city'         => $geolocation['city'],
			'registration_code' => Utils::generate_random_string(10)
		);
		
		# Load the inputted info into the user array (email, password)
		foreach($data as $key => $value) {
			$user[$key] = $value;
		}
				
		# Secure the password
		$user['password'] = $this->hash_password($user['password']);
				
		# Create them an intial token
		$user['token'] = sha1(TOKEN_SALT.$data['email'].Utils::generate_random_string());	
											
		# Add new user
		$user_id = DB::instance(DB_NAME)->insert('users', $user);
		$user['user_id'] = $user_id;

		# Create cookie with token, i.e. log them in
		$this->__set_login_cookie($user['token']);
		
		# If all went well, return the user
		if( is_numeric($user_id) && $user['token'] )
			return $user;
		else 
			return false;
				
	}
	
	
	/*-------------------------------------------------------------------------------------------------
	Pass in the user_id here because it's typically a new user we're creating the avatar for
	-------------------------------------------------------------------------------------------------*/
	public function create_initial_avatar($user_id) {
			
		# What we'll call the avatar and where it'll be saved
		$file_name = APP_PATH.AVATAR_PATH.$user_id.".png";	
					
		# Instantiate image object
		$imgObj = new Image($file_name);		
		
		# Generate random, checkered image
		$imgObj->generate_random_image(600,400, TRUE);
					
		# Name and path for $thumb
		$thumb_filename = APP_PATH.AVATAR_PATH.$user_id."_".SMALL_W."_".SMALL_H.".png";	
		
		# Now resize and save thumb
		$imgObj->resize(200,200);
		$imgObj->save_image($thumb_filename, 100);
	
		# Update the database
		DB::instance(DB_NAME)->update("users", Array("avatar" => $user_id.".png"), "WHERE user_id = ".$user_id);
	
	}
	
	
	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public function send_signup_email($user_array, $subject = "Welcome!") {
						
		# Setup confirmation email
			$to[]    = Array("name" => $user_array['first_name']." ".$user_array['last_name'], "email" => $user_array['email']);
			$from    = Array("name" => APP_NAME, "email" => APP_EMAIL);				
			
			$this->email_template->content = View::instance('e_users_signup');
			$this->email_template->content->user_array = $user_array;
		
		# Send email
			Email::send($to, $from, $subject, nl2br($this->email_template), true, '');
		
	}


	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public function reset_password($email) {
		
		# Do we have a user with that email?
		$user_id = DB::instance(DB_NAME)->select_field("SELECT user_id FROM users WHERE email = '".$email."'");
		
		# False will indicate a user was not found for this email
		if(!$user_id) return false;
	
		# Generate a new password; this is what we'll send in the email
		$new_password = Utils::generate_random_string();
		
		# Create a hashed version to store in the database
		$hashed_password = $this->hash_password($new_password);
		
		# Update database with new hashed password
		$update = DB::instance(DB_NAME)->update("users", Array("password" => $hashed_password), "WHERE user_id = ".$user_id);
	
		# Success
		if($update) 
			return $new_password;
		else 
			return false;
	
	}
	
	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public function send_new_password($new_password, $post, $subject = "Your password has been reset") {
		
		# Setup email
			$to[]    = Array("name" => $post['email'], "email" => $post['email']);
			$from    = Array("name" => APP_NAME, "email" => APP_EMAIL);
			$body    = View::instance('e_users_new_password');
			$body->password = $new_password;
		
		# Send email
			$email = Email::send($to, $from, $subject, nl2br($body), true, '');
	
	}
	
	
	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public function logout($email) {
	
		# Generate and save a new token for next login
		$new_token = sha1(TOKEN_SALT.$email.Utils::generate_random_string());
		DB::instance(DB_NAME)->update("users", Array("token" => $new_token), "WHERE token = '".$this->token."'");

		# Delete their "token" cookie
		setcookie("token", "", strtotime('-1 year'), '/');
		
		return;
	
	}
	
	
	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public function confirm_unique_email($email) {
	
		$user_id = DB::instance(DB_NAME)->select_row("SELECT user_id FROM users WHERE email = '".$email."'");
	
		# If we don't have a user_id that means this email is free to use
		if(!$user_id)
			return true;
		else 
			return false;
	}
	
	
	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public static function hash_password($password) {
		return sha1(PASSWORD_SALT.$password);
	}
	
	
	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	private static function __set_login_cookie($token) {
		@setcookie("token", $token, strtotime('+1 year'), '/');
	}


	

} # end class

?>