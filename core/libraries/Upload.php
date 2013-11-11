<?php

class Upload {

	public function __construct() {	
	}
		
	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public static function newupload($file_obj, $upload_dir, $allowed_files, $new_file_name = NULL) {
		
		$original_file_name = $file_obj['file']['name'];
		$temp_file          = $file_obj['file']['tmp_name'];
		$upload_dir         = $upload_dir;
		
		if($new_file_name == NULL) $new_file_name = $original_file_name;
		
		$file_parts  = pathinfo($original_file_name);
		
		$target_file = getcwd().$upload_dir . $new_file_name . "." . $file_parts['extension'];
								
		# Validate the filetype
		if (in_array($file_parts['extension'], $allowed_files)) {
	
			# Save the file
				move_uploaded_file($temp_file,$target_file);
				return $new_file_name . "." . $file_parts['extension'];
	
		} else {
			echo 'Invalid file type.';
		}
	
	}
		

} # end class

?>