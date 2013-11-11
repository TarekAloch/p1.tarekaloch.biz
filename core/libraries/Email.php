<?
/*-------------------------------------------------------------------------------------------------
Uses PHPMailer
http://code.google.com/a/apache-extras.org/p/phpmailer/wiki/ExamplesPage
-------------------------------------------------------------------------------------------------*/
class Email {

	public static function send($to = Array(), $from, $subject, $body, $html = FALSE, $cc = Array(), $bcc = Array()) {
	
		# Instantiate log to track all outgoing email
			$log = Log::instance(LOG_PATH."Email", Log::DEBUG, true);
			$log->logInfo("Controller/Method: ".Router::$controller."/".Router::$method);
			
		# Instantiate PHPMailer class
			$mail = new PHPMailer(false); # Defaults to PHP
			
		# SMTP settings are in app/config/config.php
				
			if(defined('SMTP_HOST') && defined('SMTP_USERNAME') && defined('SMTP_PASSWORD')) {
			
				$mail->IsSMTP(); 
				
				# Don't show error's in production
					$mail->SMTPDebug = (IN_PRODUCTION) ? 0 : 1;
												
				# SMTP settings
					$mail->Host       = SMTP_HOST; 
					$mail->SMTPAuth   = true; 
					$mail->SMTPSecure = 'ssl';
					$mail->Port       = 465; 
					$mail->Username   = SMTP_USERNAME; 
					$mail->Password   = SMTP_PASSWORD; 						
			}

		# Sender 		
			$mail->SetFrom($from['email'], $from['name']);

		# Recipient(s)
			
			# Tabula rasa
			$to_string = ""; $cc_string = ""; $bcc_string = "";
		
			# To
			foreach($to as $recipient) {
				if(ENABLE_OUTGOING_EMAIL) $mail->AddAddress($recipient['email'], $recipient['name']);
				$to_string .= $recipient['name']." (".$recipient['email']."), ";
			}
			
			# CC(s) and BCC(s) (if we have them)
			if($cc) {
				foreach($cc as $recipient) {
					if(ENABLE_OUTGOING_EMAIL) $mail->AddCC($recipient['email'], $recipient['name']);
					$cc_string .= $recipient['name']." (".$recipient['email']."), ";
				}
			}
			if($bcc) {
				foreach($bcc as $recipient) {
					if(ENABLE_OUTGOING_EMAIL) $mail->AddBCC($recipient['email'], $recipient['name']);
					$bcc_string  .= $recipient['name']." (".$recipient['email']."), ";
				}
			}
					
		# Note if we've disabled outgoing emails
			if(!ENABLE_OUTGOING_EMAIL) { 
				$log->logInfo("OUTGOING EMAILS ARE DISABLED. THIS EMAIL WAS NOT SENT TO THE RECIPIENTS, ONLY ".SYSTEM_EMAIL);
				$mail->AddAddress(SYSTEM_EMAIL, APP_NAME);
			}
		
		# Logging
			$log->logInfo("From: ".$from['name']." (".$from['email'].")");
			$log->logInfo("To: ".$to_string);
			$log->logInfo("CC: ".$cc_string);
			$log->logInfo("BCC: ".$bcc_string);
			$log->logInfo("Subject: ".$subject);
			$log->logInfo("Body: ".substr($body, 0, 50));
	
		# Content
			$mail->Subject    = $subject;
			
			# Plain text
			if(!$html) {
				$mail->IsHTML(false);
				$mail->Body = $body;
				$mail->ContentType = 'text/plain';
				$mail->AltBody = $body;
			}
			# HTML email
			else {
				$mail->IsHTML(true);
				$mail->MsgHTML($body);
				$mail->AltBody = "To view the message, please use an HTML compatible email viewer."; # Optional, comment out and test
			}
		
		# Send email	
			if(!$mail->Send()) {
				$log->logInfo("FAILED TO SEND EMAIL");
				return false;
			}
			else {
				return true;
			}
			
	} # eof
	
} # eoc