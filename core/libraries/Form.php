<?php

// Forms library to quickly build forms
class Form {

	// show form contents
	public $visible = TRUE;
	
	// strip slashes on form data (for escaped DB fields)
	public $strip_slashes = FALSE;
	
	// strip html (strip_tags) from input data
	public $strip_html = FALSE;

	// data to (re)populate form fields (field => data) -- e.g. $_GET or $_POST
	public $data = array();

	// *optional: pretty field labels (field => label)
	public $labels = array();

	// *optional: error messages (field => error message)
	public $error_msgs = array();
	
	// list of fields that have validation errors
	public $errors = array();
	
	public $country_list = array(
		'AF'=>'Afghanistan',
		'AL'=>'Albania',
		'DZ'=>'Algeria',
		'AS'=>'American Samoa',
		'AD'=>'Andorra',
		'AO'=>'Angola',
		'AI'=>'Anguilla',
		'AQ'=>'Antarctica',
		'AG'=>'Antigua And Barbuda',
		'AR'=>'Argentina',
		'AM'=>'Armenia',
		'AW'=>'Aruba',
		'AU'=>'Australia',
		'AT'=>'Austria',
		'AZ'=>'Azerbaijan',
		'BS'=>'Bahamas',
		'BH'=>'Bahrain',
		'BD'=>'Bangladesh',
		'BB'=>'Barbados',
		'BY'=>'Belarus',
		'BE'=>'Belgium',
		'BZ'=>'Belize',
		'BJ'=>'Benin',
		'BM'=>'Bermuda',
		'BT'=>'Bhutan',
		'BO'=>'Bolivia',
		'BA'=>'Bosnia And Herzegovina',
		'BW'=>'Botswana',
		'BV'=>'Bouvet Island',
		'BR'=>'Brazil',
		'IO'=>'British Indian Ocean Territory',
		'BN'=>'Brunei',
		'BG'=>'Bulgaria',
		'BF'=>'Burkina Faso',
		'BI'=>'Burundi',
		'KH'=>'Cambodia',
		'CM'=>'Cameroon',
		'CA'=>'Canada',
		'CV'=>'Cape Verde',
		'KY'=>'Cayman Islands',
		'CF'=>'Central African Republic',
		'TD'=>'Chad',
		'CL'=>'Chile',
		'CN'=>'China',
		'CX'=>'Christmas Island',
		'CC'=>'Cocos (Keeling) Islands',
		'CO'=>'Columbia',
		'KM'=>'Comoros',
		'CG'=>'Congo',
		'CK'=>'Cook Islands',
		'CR'=>'Costa Rica',
		'CI'=>'Cote D\'Ivorie (Ivory Coast)',
		'HR'=>'Croatia (Hrvatska)',
		'CU'=>'Cuba',
		'CY'=>'Cyprus',
		'CZ'=>'Czech Republic',
		'CD'=>'Democratic Republic Of Congo (Zaire)',
		'DK'=>'Denmark',
		'DJ'=>'Djibouti',
		'DM'=>'Dominica',
		'DO'=>'Dominican Republic',
		'TP'=>'East Timor',
		'EC'=>'Ecuador',
		'EG'=>'Egypt',
		'SV'=>'El Salvador',
		'GQ'=>'Equatorial Guinea',
		'ER'=>'Eritrea',
		'EE'=>'Estonia',
		'ET'=>'Ethiopia',
		'FK'=>'Falkland Islands (Malvinas)',
		'FO'=>'Faroe Islands',
		'FJ'=>'Fiji',
		'FI'=>'Finland',
		'FR'=>'France',
		'FX'=>'France, Metropolitan',
		'GF'=>'French Guinea',
		'PF'=>'French Polynesia',
		'TF'=>'French Southern Territories',
		'GA'=>'Gabon',
		'GM'=>'Gambia',
		'GE'=>'Georgia',
		'DE'=>'Germany',
		'GH'=>'Ghana',
		'GI'=>'Gibraltar',
		'GR'=>'Greece',
		'GL'=>'Greenland',
		'GD'=>'Grenada',
		'GP'=>'Guadeloupe',
		'GU'=>'Guam',
		'GT'=>'Guatemala',
		'GN'=>'Guinea',
		'GW'=>'Guinea-Bissau',
		'GY'=>'Guyana',
		'HT'=>'Haiti',
		'HM'=>'Heard And McDonald Islands',
		'HN'=>'Honduras',
		'HK'=>'Hong Kong',
		'HU'=>'Hungary',
		'IS'=>'Iceland',
		'IN'=>'India',
		'ID'=>'Indonesia',
		'IR'=>'Iran',
		'IQ'=>'Iraq',
		'IE'=>'Ireland',
		'IL'=>'Israel',
		'IT'=>'Italy',
		'JM'=>'Jamaica',
		'JP'=>'Japan',
		'JO'=>'Jordan',
		'KZ'=>'Kazakhstan',
		'KE'=>'Kenya',
		'KI'=>'Kiribati',
		'KW'=>'Kuwait',
		'KG'=>'Kyrgyzstan',
		'LA'=>'Laos',
		'LV'=>'Latvia',
		'LB'=>'Lebanon',
		'LS'=>'Lesotho',
		'LR'=>'Liberia',
		'LY'=>'Libya',
		'LI'=>'Liechtenstein',
		'LT'=>'Lithuania',
		'LU'=>'Luxembourg',
		'MO'=>'Macau',
		'MK'=>'Macedonia',
		'MG'=>'Madagascar',
		'MW'=>'Malawi',
		'MY'=>'Malaysia',
		'MV'=>'Maldives',
		'ML'=>'Mali',
		'MT'=>'Malta',
		'MH'=>'Marshall Islands',
		'MQ'=>'Martinique',
		'MR'=>'Mauritania',
		'MU'=>'Mauritius',
		'YT'=>'Mayotte',
		'MX'=>'Mexico',
		'FM'=>'Micronesia',
		'MD'=>'Moldova',
		'MC'=>'Monaco',
		'MN'=>'Mongolia',
		'MS'=>'Montserrat',
		'MA'=>'Morocco',
		'MZ'=>'Mozambique',
		'MM'=>'Myanmar (Burma)',
		'NA'=>'Namibia',
		'NR'=>'Nauru',
		'NP'=>'Nepal',
		'NL'=>'Netherlands',
		'AN'=>'Netherlands Antilles',
		'NC'=>'New Caledonia',
		'NZ'=>'New Zealand',
		'NI'=>'Nicaragua',
		'NE'=>'Niger',
		'NG'=>'Nigeria',
		'NU'=>'Niue',
		'NF'=>'Norfolk Island',
		'KP'=>'North Korea',
		'MP'=>'Northern Mariana Islands',
		'NO'=>'Norway',
		'OM'=>'Oman',
		'PK'=>'Pakistan',
		'PW'=>'Palau',
		'PA'=>'Panama',
		'PG'=>'Papua New Guinea',
		'PY'=>'Paraguay',
		'PE'=>'Peru',
		'PH'=>'Philippines',
		'PN'=>'Pitcairn',
		'PL'=>'Poland',
		'PT'=>'Portugal',
		'PR'=>'Puerto Rico',
		'QA'=>'Qatar',
		'RE'=>'Reunion',
		'RO'=>'Romania',
		'RU'=>'Russia',
		'RW'=>'Rwanda',
		'SH'=>'Saint Helena',
		'KN'=>'Saint Kitts And Nevis',
		'LC'=>'Saint Lucia',
		'PM'=>'Saint Pierre And Miquelon',
		'VC'=>'Saint Vincent And The Grenadines',
		'SM'=>'San Marino',
		'ST'=>'Sao Tome And Principe',
		'SA'=>'Saudi Arabia',
		'SN'=>'Senegal',
		'SC'=>'Seychelles',
		'SL'=>'Sierra Leone',
		'SG'=>'Singapore',
		'SK'=>'Slovak Republic',
		'SI'=>'Slovenia',
		'SB'=>'Solomon Islands',
		'SO'=>'Somalia',
		'ZA'=>'South Africa',
		'GS'=>'South Georgia And South Sandwich Islands',
		'KR'=>'South Korea',
		'ES'=>'Spain',
		'LK'=>'Sri Lanka',
		'SD'=>'Sudan',
		'SR'=>'Suriname',
		'SJ'=>'Svalbard And Jan Mayen',
		'SZ'=>'Swaziland',
		'SE'=>'Sweden',
		'CH'=>'Switzerland',
		'SY'=>'Syria',
		'TW'=>'Taiwan',
		'TJ'=>'Tajikistan',
		'TZ'=>'Tanzania',
		'TH'=>'Thailand',
		'TG'=>'Togo',
		'TK'=>'Tokelau',
		'TO'=>'Tonga',
		'TT'=>'Trinidad And Tobago',
		'TN'=>'Tunisia',
		'TR'=>'Turkey',
		'TM'=>'Turkmenistan',
		'TC'=>'Turks And Caicos Islands',
		'TV'=>'Tuvalu',
		'UG'=>'Uganda',
		'UA'=>'Ukraine',
		'AE'=>'United Arab Emirates',
		'UK'=>'United Kingdom',
		'US'=>'United States',
		'UM'=>'United States Minor Outlying Islands',
		'UY'=>'Uruguay',
		'UZ'=>'Uzbekistan',
		'VU'=>'Vanuatu',
		'VA'=>'Vatican City (Holy See)',
		'VE'=>'Venezuela',
		'VN'=>'Vietnam',
		'VG'=>'Virgin Islands (British)',
		'VI'=>'Virgin Islands (US)',
		'WF'=>'Wallis And Futuna Islands',
		'EH'=>'Western Sahara',
		'WS'=>'Western Samoa',
		'YE'=>'Yemen',
		'YU'=>'Yugoslavia',
		'ZM'=>'Zambia',
		'ZW'=>'Zimbabwe'
		);
	
	public $state_list = array(
			'' => "Choose...",
			'AL'=>"Alabama",  
			'AK'=>"Alaska",  
			'AZ'=>"Arizona",  
			'AR'=>"Arkansas",  
			'CA'=>"California",  
			'CO'=>"Colorado",  
			'CT'=>"Connecticut",  
			'DE'=>"Delaware",  
			'DC'=>"District Of Columbia",  
			'FL'=>"Florida",  
			'GA'=>"Georgia",  
			'HI'=>"Hawaii",  
			'ID'=>"Idaho",  
			'IL'=>"Illinois",  
			'IN'=>"Indiana",  
			'IA'=>"Iowa",  
			'KS'=>"Kansas",  
			'KY'=>"Kentucky",  
			'LA'=>"Louisiana",  
			'ME'=>"Maine",  
			'MD'=>"Maryland",  
			'MA'=>"Massachusetts",  
			'MI'=>"Michigan",  
			'MN'=>"Minnesota",  
			'MS'=>"Mississippi",  
			'MO'=>"Missouri",  
			'MT'=>"Montana",
			'NE'=>"Nebraska",
			'NV'=>"Nevada",
			'NH'=>"New Hampshire",
			'NJ'=>"New Jersey",
			'NM'=>"New Mexico",
			'NY'=>"New York",
			'NC'=>"North Carolina",
			'ND'=>"North Dakota",
			'OH'=>"Ohio",  
			'OK'=>"Oklahoma",  
			'OR'=>"Oregon",  
			'PA'=>"Pennsylvania",  
			'RI'=>"Rhode Island",  
			'SC'=>"South Carolina",  
			'SD'=>"South Dakota",
			'TN'=>"Tennessee",  
			'TX'=>"Texas",  
			'UT'=>"Utah",  
			'VT'=>"Vermont",  
			'VA'=>"Virginia",  
			'WA'=>"Washington",  
			'WV'=>"West Virginia",  
			'WI'=>"Wisconsin",  
			'WY'=>"Wyoming");
	
	// construct with form data (pass in $_POST or $_GET)
	public function __construct($data = NULL, $strip_slashes = FALSE, $strip_html = FALSE) {
		
		// stripslashes on input data
		$this->strip_slashes = $strip_slashes;

		// stripslashes on input data
		$this->strip_html = $strip_html;
		
		// clean input data and optionally stripslashes/strip_tags
		if ($data !== NULL)
			$this->data = self::clean_data($data, $strip_slashes, $strip_html);
			
	}
	
	// set form defaults
	public function defaults(array $defaults) {
		
		// first clean defaults
		$defaults = self::clean_data($defaults);
		
		// merge defaults with data, not overwriting any existing fields
		$this->data = array_merge($defaults, $this->data);
		
	}
	
	public function open($name = 'form', $action = NULL, $html = NULL, $method = 'post') {

		// init HTML form
		echo '<form method="'.$method.'" action="'.(!empty($action) ? $action : '#result').'" name="'.$name.'" id="'.$name.'" '.$html.(($this->visible) ? '' : ' style="display: none;"').">\n";

	}	
	
	// cleans data and forces all newline characters to "\n".
	public static function clean_data($str, $strip_slashes = FALSE, $strip_html = FALSE) {

		if (is_array($str)) {

			$new_array = array();
			foreach ($str as $key => $val) {
				// Recursion!
				$new_array[trim($key)] = self::clean_data($val, $strip_slashes, $strip_html);
			}
			return $new_array;
		}

		// strip escaping slashes
		if ($strip_slashes === TRUE)
			$str = stripslashes($str);

		// strip tags
		if ($strip_html === TRUE)
			$str = strip_tags($str);

		// Standardize newlines
		if (strpos($str, "\r") !== FALSE)
			$str = str_replace(array("\r\n", "\r"), "\n", $str);

		return $str;
	}
	

	public static function attributes($attrs) {

		if (empty($attrs))
			return '';

		if (is_string($attrs))
			return ' '.$attrs;

		$compiled = '';
		foreach ($attrs as $key => $val) {
			$compiled .= ' '.$key.'="'.htmlspecialchars($val, ENT_QUOTES, 'UTF-8').'"';
		}

		return $compiled;
	}

	public function error_summary() {

		if (empty($this->errors)) return;

		// this is the default error summary box. forms can override this
		$e = '<div class="error-summary"><b>Please review the following errors and try again: </b><ul>';
		foreach ($this->errors as $field => $error)
				$e .= '<li>'.$this->error_msg($field)."</li>\n";
		$e .= '</ul></div>';

		echo $e;
	}

	public function field($field, $value = NULL, $html = NULL) {
		// this gives us the form value back if there's any errors
		// also sets the class to error-field for red highlight
		$html .= (empty($this->errors[$field])) ? ' class="form-field"' : ' class="form-field error-field"';
		$value = (empty($this->data[$field])) ? $value : $this->data[$field];		
		$attr = array(
			'id' => $field,
			'name' => $field,
			'type'  => 'text',
			'value' => $value,
		);
		echo '<input'.self::attributes($attr).' '.$html.' />';
	}
	

	
	public function password($field, $value = NULL, $html = NULL) {
		$html .= (empty($this->errors[$field])) ? ' class="form-field"' : ' class="form-field error-field"';
		$value = (empty($this->data[$field])) ? $value : $this->data[$field];
		$attr = array(
			'id' => $field,
			'name' => $field,
			'type' => 'password',
			'value' => $value,
		);
		echo '<input'.self::attributes($attr).' '.$html.' />';
	}


	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public function select($field, $options, $value = NULL, $html = NULL) {
	
		# Populate with 50 States
		if($options == "state_list") $options = $this->state_list;
		if($options == "country_list") $options = $this->country_list;
		
		$value = (empty($this->data[$field])) ? $value : $this->data[$field];
		$input = '<select id="'.$field.'" name="'.$field.'" '.$html.'>'."\n";
		foreach ((array) $options as $key => $val) {
			$sel = ($key == $value) ? ' selected="selected"' : '';
			$input .= '<option value="'.$key.'"'.$sel.'>'.$val.'</option>'."\n";
		}
		$input .= '</select>';
		
		return $input;
		
	}
	
	
	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/	
	public function select_multiple($field, $options, $value = Array(), $html = NULL) {
		$value = (empty($this->data[$field])) ? $value : $this->data[$field];				
		$input = '<select multiple="multiple" id="'.$field.'" name="'.$field.'" '.$html.'>'."\n";
		foreach ((array) $options as $key => $val) {
			$sel = (in_array($key, $value)) ? ' selected="selected"' : '';
			$input .= '<option value="'.$key.'"'.$sel.'>'.$val.'</option>'."\n";
		}
		$input .= '</select>';
		echo $input;
	}
	
	
	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public function textarea($field, $value = NULL, $html = NULL) {
		$value = (empty($this->data[$field])) ? $value : $this->data[$field];		
		$attr = array('name' => $field, 'id' => $field);
		echo '<textarea'.self::attributes($attr).' '.$html.'>'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'</textarea>';
		
	}
	

	/*-------------------------------------------------------------------------------------------------
	
	-------------------------------------------------------------------------------------------------*/
	public function checkbox($field, $value = NULL, $checked = FALSE, $html = NULL) {

		$checked = (empty($this->data[$field])) ? $value : $this->data[$field];
	
		$attr = array(
			'id' => $field,			
			'name' => $field,
			'type' => 'checkbox',
			'value' => $value,
		);

		if ($checked == TRUE || $checked == "1") 
			$attr['checked'] = 'checked';

		echo '<input'.self::attributes($attr).' '.$html.' />';
	}	
	

	/*-------------------------------------------------------------------------------------------------

	-------------------------------------------------------------------------------------------------*/
	public function label($field) {
		// returns the configured label for a field, or a cleaned up version of the field name if none is set
		return (isset($this->labels[$field])) ? $this->labels[$field] : self::split_camelcase($field);
	}

	public function error($field, $simple = FALSE) {
		// displays the error msg for a given field, hidden by default
		$visible = (in_array($this->errors, $field, FALSE)) ? ' style="display: none;"' : NULL;
		$msg = ($simple) ? 'Required' : $this->error_msg($field);
		echo '<strong class="field-error"'.$visible.'>'.$msg.'</strong>';
	}

	public function error_msg($field) {
		// returns the configured error message for a field, or a sensible default if none is set
		return (isset($this->error_msgs[$field])) ? $this->error_msgs[$field] : self::split_camelcase($field).' is required.';
	}

	public static function split_camelcase($field) {
		// capitlizes first word and adds spaces to a CamelCaseFieldName
		return ucfirst(preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]/', ' $0', $field));
	}

	
	public function hidden($field, $value = NULL, $html = NULL) {
		$value = (empty($this->data[$field])) ? $value : $this->data[$field];
		echo '<input type="hidden" name="'.$field.'" id="'.$field.'" '.$html.' value="'.$value.'" />';
	}
	
	
	public function radio($field, $value = NULL, $html = NULL) {
		$checked = (@$this->data[$field] == $value) ? TRUE : FALSE;		
		$attr = array(
			'id' => $field,			
			'name' => $field,
			'type' => 'radio',
			'value' => $value,
		);

		if ($checked == TRUE)
			$attr['checked'] = 'checked';
		
		echo '<input'.self::attributes($attr).' '.$html.' />';
	}
	
	/*
	// UNTESTED BELOW HERE, PROBABLY WORKS, BUT TRY IT FIRST, THEN UNCOMMENT IT!!
	
	public function checkbox_array($field, $value = NULL, $checked = FALSE, $html = NULL) {
		// only use if field is an array of values (multiple checkboxes, same field name)
		if (!empty($this->data[$field]) && is_array($this->data[$field])) {
			$checked = in_array($value, $this->data[$field]);
		}
		return $this->checkbox($field.'[]', $value, $checked, $html);
	}
	
	public function select_multiple($field, $title, $options, $value = NULL, $html = NULL, $use_keys = NULL) {
		echo '<select multiple="multiple" id="'.$field.'" name="'.$field.'[]" title="'.$title.'" '.$html.'>'."\n";
		foreach ($options as $key => $label) {
			if ($use_keys === FALSE OR (is_numeric($key) AND $use_keys !== TRUE)) $key = $label;
			$selected = ((isset($this->data[$field]) && is_array($this->data[$field]) && in_array($key, $this->data[$field])) || (! isset($this->data[$field]) && $value === $key))  ? ' selected' : '';
			echo '<option value="'.$key.'"'.$selected.' >'.$label.'</option>'."\n";
		}
		echo "</select>\n";
	}
	*/
	



}