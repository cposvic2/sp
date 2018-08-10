<?php
/*
 * Global variables
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Google API Key
$google_api_key = 'AIzaSyAgxj7dXKhfxjZTlyfX8Vyba1_JG0m4SN0';
$convertkit_api_key = 'HdFnwGXqlsOwD__Y-7jW_Q';

/*
// Stripe Test keys
$stripe_publishable_api_key = 'pk_test_da6WDhE5rlEPCmKKTE1nTLYm';
$stripe_private_api_key = 'sk_test_MTvlHcCbnunmWjB82AmKaNtm';
*/

// Stripe Live keys
$stripe_publishable_api_key = 'pk_live_idWyrGzgTNOo6UC5jilw7NSJ';
$stripe_private_api_key = 'sk_live_ORdWihqafXanXJXLg3o4ppoZ';

// Custom post types array
$custom_post_types = array( 
	'uc_activity', 
	'uc_class', 
	'uc_group', 
	'uc_league_challenge', 
	'uc_school', 
	'uc_student', 
	'uc_teacher', 
	'uc_signup' 
);

// States array
$us_states = array(
	'AL'=>'Alabama',
	'AK'=>'Alaska',
	'AZ'=>'Arizona',
	'AR'=>'Arkansas',
	'CA'=>'California',
	'CO'=>'Colorado',
	'CT'=>'Connecticut',
	'DE'=>'Delaware',
	'DC'=>'District of Columbia',
	'FL'=>'Florida',
	'GA'=>'Georgia',
	'HI'=>'Hawaii',
	'ID'=>'Idaho',
	'IL'=>'Illinois',
	'IN'=>'Indiana',
	'IA'=>'Iowa',
	'KS'=>'Kansas',
	'KY'=>'Kentucky',
	'LA'=>'Louisiana',
	'ME'=>'Maine',
	'MD'=>'Maryland',
	'MA'=>'Massachusetts',
	'MI'=>'Michigan',
	'MN'=>'Minnesota',
	'MS'=>'Mississippi',
	'MO'=>'Missouri',
	'MT'=>'Montana',
	'NE'=>'Nebraska',
	'NV'=>'Nevada',
	'NH'=>'New Hampshire',
	'NJ'=>'New Jersey',
	'NM'=>'New Mexico',
	'NY'=>'New York',
	'NC'=>'North Carolina',
	'ND'=>'North Dakota',
	'OH'=>'Ohio',
	'OK'=>'Oklahoma',
	'OR'=>'Oregon',
	'PA'=>'Pennsylvania',
	'RI'=>'Rhode Island',
	'SC'=>'South Carolina',
	'SD'=>'South Dakota',
	'TN'=>'Tennessee',
	'TX'=>'Texas',
	'UT'=>'Utah',
	'VT'=>'Vermont',
	'VA'=>'Virginia',
	'WA'=>'Washington',
	'WV'=>'West Virginia',
	'WI'=>'Wisconsin',
	'WY'=>'Wyoming',
);

// Returns US States
function get_us_states() {
	global $us_states;
	$return_states = array();
	foreach ( $us_states as $abbr => $label ) {
		$return_states[] = array(
			'value' => $abbr,
			'label' => $label
		);
	}
	return $return_states;
}

$administrative_area_level_1_array = array(
	'united-states' => array(
		'name'=>'state',
		'plural_name'=>'states',
		'values'=> array(
			'AL'=>'Alabama',
			'AK'=>'Alaska',
			'AZ'=>'Arizona',
			'AR'=>'Arkansas',
			'CA'=>'California',
			'CO'=>'Colorado',
			'CT'=>'Connecticut',
			'DE'=>'Delaware',
			'DC'=>'District of Columbia',
			'FL'=>'Florida',
			'GA'=>'Georgia',
			'HI'=>'Hawaii',
			'ID'=>'Idaho',
			'IL'=>'Illinois',
			'IN'=>'Indiana',
			'IA'=>'Iowa',
			'KS'=>'Kansas',
			'KY'=>'Kentucky',
			'LA'=>'Louisiana',
			'ME'=>'Maine',
			'MD'=>'Maryland',
			'MA'=>'Massachusetts',
			'MI'=>'Michigan',
			'MN'=>'Minnesota',
			'MS'=>'Mississippi',
			'MO'=>'Missouri',
			'MT'=>'Montana',
			'NE'=>'Nebraska',
			'NV'=>'Nevada',
			'NH'=>'New Hampshire',
			'NJ'=>'New Jersey',
			'NM'=>'New Mexico',
			'NY'=>'New York',
			'NC'=>'North Carolina',
			'ND'=>'North Dakota',
			'OH'=>'Ohio',
			'OK'=>'Oklahoma',
			'OR'=>'Oregon',
			'PA'=>'Pennsylvania',
			'RI'=>'Rhode Island',
			'SC'=>'South Carolina',
			'SD'=>'South Dakota',
			'TN'=>'Tennessee',
			'TX'=>'Texas',
			'UT'=>'Utah',
			'VT'=>'Vermont',
			'VA'=>'Virginia',
			'WA'=>'Washington',
			'WV'=>'West Virginia',
			'WI'=>'Wisconsin',
			'WY'=>'Wyoming',
		),
	),
	'mexico' => array(
		'name'=>'state',
		'plural_name'=>'states',
		'values'=> array(
			'AG' => 'Aguascalientes',
			'BN' => 'Baja California Norte',
			'BS' => 'Baja California Sur',
			'CH' => 'Coahuila',
			'CI' => 'Chihuahua',
			'CL' => 'Colima',
			'CP' => 'Campeche',
			'CS' => 'Chiapas',
			'DF' => 'Districto Federal',
			'DG' => 'Durango',
			'GE' => 'Guerrero',
			'GJ' => 'Guanajuato',
			'HD' => 'Hidalgo',
			'JA' => 'Jalisco',
			'MC' => 'Michoacan',
			'MR' => 'Morelos',
			'MX' => 'Mexico',
			'NA' => 'Nayarit',
			'NL' => 'Nuevo Leon',
			'OA' => 'Oaxaca',
			'PU' => 'Puebla',
			'QE' => 'Queretaro',
			'QI' => 'Quintana Roo',
			'SI' => 'Sinaloa',
			'SL' => 'San Luis Potosi',
			'SO' => 'Sonora',
			'TA' => 'Tamaulipas',
			'TB' => 'Tabasco',
			'TL' => 'Tlaxcala',
			'VC' => 'Veracruz',
			'YU' => 'Yucatan',
			'ZA' => 'Zacatecas',
		),
	),
	'canada' => array(
		'name'=>'province',
		'plural_name'=>'provinces',
		'values'=> array(
			"BC" => "British Columbia", 
			"ON" => "Ontario", 
			"NL" => "Newfoundland and Labrador", 
			"NS" => "Nova Scotia", 
			"PE" => "Prince Edward Island", 
			"NB" => "New Brunswick", 
			"QC" => "Quebec", 
			"MB" => "Manitoba", 
			"SK" => "Saskatchewan", 
			"AB" => "Alberta", 
			"NT" => "Northwest Territories", 
			"NU" => "Nunavut",
			"YT" => "Yukon Territory"
		),
	),
);

// Returns Administrative Areas Level 1 for a country 
function get_administrative_areas_level_1( $country ) {
	global $administrative_area_level_1_array;
	$return_administrative_areas_level_1 = array();

	if ( isset($administrative_area_level_1_array[$country]['values']) ) {
		foreach ( $administrative_area_level_1_array[$country]['values'] as $abbr => $label ) {
			$return_administrative_areas_level_1[] = array(
				'value' => $abbr,
				'label' => $label
			);
		}
	}
	return $return_administrative_areas_level_1;
}

$countries = array(
	"united-states" => "United States",
	"afghanistan" => "Afghanistan",
	"albania" => "Albania",
	"algeria" => "Algeria",
	"andorra" => "Andorra",
	"angola" => "Angola",
	"antigua-and-barbuda" => "Antigua and Barbuda",
	"argentina" => "Argentina",
	"armenia" => "Armenia",
	"australia" => "Australia",
	"austria" => "Austria",
	"azerbaijan" => "Azerbaijan",
	"bahamas" => "Bahamas",
	"bahrain" => "Bahrain",
	"bangladesh" => "Bangladesh",
	"barbados" => "Barbados",
	"belarus" => "Belarus",
	"belgium" => "Belgium",
	"belize" => "Belize",
	"benin" => "Benin",
	"bhutan" => "Bhutan",
	"bolivia" => "Bolivia",
	"bosnia-and-herzegovina" => "Bosnia and Herzegovina",
	"botswana" => "Botswana",
	"brazil" => "Brazil",
	"brunei" => "Brunei",
	"bulgaria" => "Bulgaria",
	"burkina-faso" => "Burkina Faso",
	"burundi" => "Burundi",
	"cambodia" => "Cambodia",
	"cameroon" => "Cameroon",
	"canada" => "Canada",
	"cape-verde" => "Cape Verde",
	"central-african-republic" => "Central African Republic",
	"chad" => "Chad",
	"chile" => "Chile",
	"china" => "China",
	"colombi" => "Colombi",
	"comoros" => "Comoros",
	"congo-(brazzaville)" => "Congo (Brazzaville)",
	"congo" => "Congo",
	"costa-rica" => "Costa Rica",
	"cote-d'ivoire" => "Cote d'Ivoire",
	"croatia" => "Croatia",
	"cuba" => "Cuba",
	"cyprus" => "Cyprus",
	"czech-republic" => "Czech Republic",
	"denmark" => "Denmark",
	"djibouti" => "Djibouti",
	"dominica" => "Dominica",
	"dominican-republic" => "Dominican Republic",
	"east-timor-(timor-timur)" => "East Timor (Timor Timur)",
	"ecuador" => "Ecuador",
	"egypt" => "Egypt",
	"el-salvador" => "El Salvador",
	"equatorial-guinea" => "Equatorial Guinea",
	"eritrea" => "Eritrea",
	"estonia" => "Estonia",
	"ethiopia" => "Ethiopia",
	"fiji" => "Fiji",
	"finland" => "Finland",
	"france" => "France",
	"gabon" => "Gabon",
	"gambia,-the" => "Gambia, The",
	"georgia" => "Georgia",
	"germany" => "Germany",
	"ghana" => "Ghana",
	"greece" => "Greece",
	"grenada" => "Grenada",
	"guatemala" => "Guatemala",
	"guinea" => "Guinea",
	"guinea-bissau" => "Guinea-Bissau",
	"guyana" => "Guyana",
	"haiti" => "Haiti",
	"honduras" => "Honduras",
	"hungary" => "Hungary",
	"iceland" => "Iceland",
	"india" => "India",
	"indonesia" => "Indonesia",
	"iran" => "Iran",
	"iraq" => "Iraq",
	"ireland" => "Ireland",
	"israel" => "Israel",
	"italy" => "Italy",
	"jamaica" => "Jamaica",
	"japan" => "Japan",
	"jordan" => "Jordan",
	"kazakhstan" => "Kazakhstan",
	"kenya" => "Kenya",
	"kiribati" => "Kiribati",
	"korea,-north" => "Korea, North",
	"korea,-south" => "Korea, South",
	"kuwait" => "Kuwait",
	"kyrgyzstan" => "Kyrgyzstan",
	"laos" => "Laos",
	"latvia" => "Latvia",
	"lebanon" => "Lebanon",
	"lesotho" => "Lesotho",
	"liberia" => "Liberia",
	"libya" => "Libya",
	"liechtenstein" => "Liechtenstein",
	"lithuania" => "Lithuania",
	"luxembourg" => "Luxembourg",
	"macedonia" => "Macedonia",
	"madagascar" => "Madagascar",
	"malawi" => "Malawi",
	"malaysia" => "Malaysia",
	"maldives" => "Maldives",
	"mali" => "Mali",
	"malta" => "Malta",
	"marshall-islands" => "Marshall Islands",
	"mauritania" => "Mauritania",
	"mauritius" => "Mauritius",
	"mexico" => "Mexico",
	"micronesia" => "Micronesia",
	"moldova" => "Moldova",
	"monaco" => "Monaco",
	"mongolia" => "Mongolia",
	"morocco" => "Morocco",
	"mozambique" => "Mozambique",
	"myanmar" => "Myanmar",
	"namibia" => "Namibia",
	"nauru" => "Nauru",
	"nepal" => "Nepal",
	"netherlands" => "Netherlands",
	"new-zealand" => "New Zealand",
	"nicaragua" => "Nicaragua",
	"niger" => "Niger",
	"nigeria" => "Nigeria",
	"norway" => "Norway",
	"oman" => "Oman",
	"pakistan" => "Pakistan",
	"palau" => "Palau",
	"panama" => "Panama",
	"papua-new-guinea" => "Papua New Guinea",
	"paraguay" => "Paraguay",
	"peru" => "Peru",
	"philippines" => "Philippines",
	"poland" => "Poland",
	"portugal" => "Portugal",
	"qatar" => "Qatar",
	"romania" => "Romania",
	"russia" => "Russia",
	"rwanda" => "Rwanda",
	"saint-kitts-and-nevis" => "Saint Kitts and Nevis",
	"saint-lucia" => "Saint Lucia",
	"saint-vincent" => "Saint Vincent",
	"samoa" => "Samoa",
	"san-marino" => "San Marino",
	"sao-tome-and-principe" => "Sao Tome and Principe",
	"saudi-arabia" => "Saudi Arabia",
	"senegal" => "Senegal",
	"serbia-and-montenegro" => "Serbia and Montenegro",
	"seychelles" => "Seychelles",
	"sierra-leone" => "Sierra Leone",
	"singapore" => "Singapore",
	"slovakia" => "Slovakia",
	"slovenia" => "Slovenia",
	"solomon-islands" => "Solomon Islands",
	"somalia" => "Somalia",
	"south-africa" => "South Africa",
	"spain" => "Spain",
	"sri-lanka" => "Sri Lanka",
	"sudan" => "Sudan",
	"suriname" => "Suriname",
	"swaziland" => "Swaziland",
	"sweden" => "Sweden",
	"switzerland" => "Switzerland",
	"syria" => "Syria",
	"taiwan" => "Taiwan",
	"tajikistan" => "Tajikistan",
	"tanzania" => "Tanzania",
	"thailand" => "Thailand",
	"togo" => "Togo",
	"tonga" => "Tonga",
	"trinidad-and-tobago" => "Trinidad and Tobago",
	"tunisia" => "Tunisia",
	"turkey" => "Turkey",
	"turkmenistan" => "Turkmenistan",
	"tuvalu" => "Tuvalu",
	"uganda" => "Uganda",
	"ukraine" => "Ukraine",
	"united-arab-emirates" => "United Arab Emirates",
	"united-kingdom" => "United Kingdom",
	"uruguay" => "Uruguay",
	"uzbekistan" => "Uzbekistan",
	"vanuatu" => "Vanuatu",
	"vatican-city" => "Vatican City",
	"venezuela" => "Venezuela",
	"vietnam" => "Vietnam",
	"yemen" => "Yemen",
	"zambia" => "Zambia",
	"zimbabwe" => "Zimbabwe",
);

// Returns countries
function get_countries() {
	global $countries;
	$return_countries = array();
	foreach ( $countries as $abbr => $country ) {
		$return_countries[] = array(
			'value' => $abbr,
			'label' => $country
		);
	}
	return $return_countries;
}

// Genders array
$uc_genders = array(
	'm'=>'Male',
	'f'=>'Female',
);

// Returns genders
function get_genders() {
	global $uc_genders;
	$return_genders = array();
	foreach ( $uc_genders as $abbr => $label ) {
		$return_genders[] = array(
			'value' => $abbr,
			'label' => $label
		);
	}
	return $return_genders;
}

// Yes/no array
$uc_yesno = array(
	'1' => 'Yes',
	'2' => 'No',
);

// Returns yes/no
function get_yesno() {
	global $uc_yesno;
	$return_yesno = array();
	foreach ( $uc_yesno as $abbr => $label ) {
		$return_yesno[] = array(
			'value' => $abbr,
			'label' => $label
		);
	}
	return $return_yesno;
}

// Returns months
function get_uc_months() {
	$months = cal_info(0)[months];
	$return_yesno = array();
	foreach ( $months as $abbr => $label ) {
		$return_yesno[] = array(
			'value' => $abbr,
			'label' => $label
		);
	}
	return $return_yesno;
}

?>
