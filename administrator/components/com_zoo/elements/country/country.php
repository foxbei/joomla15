<?php
/**
* @package   ZOO Component
* @file      country.php
* @version   2.3.6 March 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
   Class: ElementCountry
       The country element class
*/
class ElementCountry extends Element implements iSubmittable {

	/*
		Function: getSearchData
			Get elements search data.
					
		Returns:
			String - Search data
	*/	
	public function getSearchData() {
		$countries = $this->_data->get('country', array());
		$keys = array_flip($countries);
		$countries = array_intersect_key(self::getCountryArray(), $keys);
		
		return implode (' ', $countries);
	}

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/		
	public function hasValue($params = array()) {
		foreach ($this->_data->get('country', array()) as $country) {
            if (!empty($country)) {
                return true;
            }
        }
        return false;
	}
	
	/*
		Function: render
			Renders the element.

	   Parameters:
            $params - render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {

		$countries = $this->_data->get('country', array());
		$keys = array_flip($countries);
		$countries = array_intersect_key(self::getCountryArray(), $keys);

		$countries = array_map(create_function('$a', 'return JText::_($a);'), $countries);

		return ElementHelper::applySeparators($params['separated_by'], $countries);
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit(){
		
		//init vars
		$selectable_countries = $this->_config->get('selectable_countries', array());
		
		if (count($selectable_countries)) {
			
			$multiselect = $this->_config->get('multiselect', array());
	
			$countries = self::getCountryArray();
			$keys = array_flip($selectable_countries);
			$countries = array_intersect_key($countries, $keys);
	
			return JHTML::_('element.countryselectlist', $countries, 'elements[' . $this->identifier . '][country][]', $this->_data->get('country', array()), $multiselect);
		}
		
		return JText::_("There are no countries to choose from.");
	}

	/*
		Function: renderSubmission
			Renders the element in submission.

	   Parameters:
            $params - submission parameters

		Returns:
			String - html
	*/
	public function renderSubmission($params = array()) {
        return $this->edit();
	}

	/*
		Function: validateSubmission
			Validates the submitted element

	   Parameters:
            $value  - YArray value
            $params - YArray submission parameters

		Returns:
			Array - cleaned value
	*/
	public function validateSubmission($value, $params) {

        $options     = array('required' => $params->get('required'));
		$multiselect = $this->_config->get('multiselect');
		$messages    = ($multiselect) ? array('required' => 'Please select at least one country.') : array('required' => 'Please select a country.');
		
        $validator = new YValidatorForeach(new YValidatorString($options, $messages), $options, $messages);
        $clean = $validator->clean($value->get('country'));

        foreach ($clean as $country) {
            if (!empty($country) && !in_array($country, $this->_config->get('selectable_countries', array()))) {
                throw new YValidatorException('Please choose a correct country.');
            }
        }

		return array('country' => $clean);
	}
		
	public static function getCountryArray() {
		$countries = array();
			$countries["AF"] = "Afghanistan";
			$countries["AX"] = "Aland Islands";
			$countries["AL"] = "Albania";
			$countries["DZ"] = "Algeria";
			$countries["AS"] = "American Samoa";
			$countries["AD"] = "Andorra";
			$countries["AO"] = "Angola";
			$countries["AI"] = "Anguilla";
			$countries["AQ"] = "Antarctica";
			$countries["AG"] = "Antigua and Barbuda";
			$countries["AR"] = "Argentina";
			$countries["AM"] = "Armenia";
			$countries["AW"] = "Aruba";
			$countries["AU"] = "Australia";
			$countries["AT"] = "Austria";
			$countries["AZ"] = "Azerbaijan";
			$countries["BS"] = "Bahamas";
			$countries["BH"] = "Bahrain";
			$countries["BD"] = "Bangladesh";
			$countries["BB"] = "Barbados";
			$countries["BY"] = "Belarus";
			$countries["BE"] = "Belgium";
			$countries["BZ"] = "Belize";
			$countries["BJ"] = "Benin";
			$countries["BM"] = "Bermuda";
			$countries["BT"] = "Bhutan";
			$countries["BO"] = "Bolivia, Plurinational State of";
			$countries["BA"] = "Bosnia and Herzegovina";
			$countries["BW"] = "Botswana";
			$countries["BV"] = "Bouvet Island";
			$countries["BR"] = "Brazil";
			$countries["IO"] = "British Indian Ocean Territory";
			$countries["BN"] = "Brunei Darussalam";
			$countries["BG"] = "Bulgaria";
			$countries["BF"] = "Burkina Faso";
			$countries["BI"] = "Burundi";
			$countries["KH"] = "Cambodia";
			$countries["CM"] = "Cameroon";
			$countries["CA"] = "Canada";
			$countries["CV"] = "Cape Verde";
			$countries["KY"] = "Cayman Islands";
			$countries["CF"] = "Central African Republic";
			$countries["TD"] = "Chad";
			$countries["CL"] = "Chile";
			$countries["CN"] = "China";
			$countries["CX"] = "Christmas Island";
			$countries["CC"] = "Cocos (Keeling) Islands";
			$countries["CO"] = "Colombia";
			$countries["KM"] = "Comoros";
			$countries["CG"] = "Congo";
			$countries["CD"] = "Congo, the Democratic Republic of the";
			$countries["CK"] = "Cook Islands";
			$countries["CR"] = "Costa Rica";
			$countries["CI"] = "Cote d'Ivoire";
			$countries["HR"] = "Croatia";
			$countries["CU"] = "Cuba";
			$countries["CY"] = "Cyprus";
			$countries["CZ"] = "Czech Republic";
			$countries["DK"] = "Denmark";
			$countries["DJ"] = "Djibouti";
			$countries["DM"] = "Dominica";
			$countries["DO"] = "Dominican Republic";
			$countries["EC"] = "Ecuador";
			$countries["EG"] = "Egypt";
			$countries["SV"] = "El Salvador";
			$countries["GQ"] = "Equatorial Guinea";
			$countries["ER"] = "Eritrea";
			$countries["EE"] = "Estonia";
			$countries["ET"] = "Ethiopia";
			$countries["FK"] = "Falkland Islands (Malvinas)";
			$countries["FO"] = "Faroe Islands";
			$countries["FJ"] = "Fiji";
			$countries["FI"] = "Finland";
			$countries["FR"] = "France";
			$countries["GF"] = "French Guiana";
			$countries["PF"] = "French Polynesia";
			$countries["TF"] = "French Southern Territories";
			$countries["GA"] = "Gabon";
			$countries["GM"] = "Gambia";
			$countries["GE"] = "Georgia";
			$countries["DE"] = "Germany";
			$countries["GH"] = "Ghana";
			$countries["GI"] = "Gibraltar";
			$countries["GR"] = "Greece";
			$countries["GL"] = "Greenland";
			$countries["GD"] = "Grenada";
			$countries["GP"] = "Guadeloupe";
			$countries["GU"] = "Guam";
			$countries["GT"] = "Guatemala";
			$countries["GG"] = "Guernsey";
			$countries["GN"] = "Guinea";
			$countries["GW"] = "Guinea-Bissau";
			$countries["GY"] = "Guyana";
			$countries["HT"] = "Haiti";
			$countries["HM"] = "Heard Island and McDonald Islands";
			$countries["VA"] = "Holy See (Vatican City State)";
			$countries["HN"] = "Honduras";
			$countries["HK"] = "Hong Kong";
			$countries["HU"] = "Hungary";
			$countries["IS"] = "Iceland";
			$countries["IN"] = "India";
			$countries["ID"] = "Indonesia";
			$countries["IR"] = "Iran, Islamic Republic of";
			$countries["IQ"] = "Iraq";
			$countries["IE"] = "Ireland";
			$countries["IM"] = "Isle of Man";
			$countries["IL"] = "Israel";
			$countries["IT"] = "Italy";
			$countries["JM"] = "Jamaica";
			$countries["JP"] = "Japan";
			$countries["JE"] = "Jersey";
			$countries["JO"] = "Jordan";
			$countries["KZ"] = "Kazakhstan";
			$countries["KE"] = "Kenya";
			$countries["KI"] = "Kiribati";
			$countries["KP"] = "Korea, Democratic People's Republic of";
			$countries["KR"] = "Korea, Republic of";
			$countries["KW"] = "Kuwait";
			$countries["KG"] = "Kyrgyzstan";
			$countries["LA"] = "Lao People's Democratic Republic";
			$countries["LV"] = "Latvia";
			$countries["LB"] = "Lebanon";
			$countries["LS"] = "Lesotho";
			$countries["LR"] = "Liberia";
			$countries["LY"] = "Libyan Arab Jamahiriya";
			$countries["LI"] = "Liechtenstein";
			$countries["LT"] = "Lithuania";
			$countries["LU"] = "Luxembourg";
			$countries["MO"] = "Macao";
			$countries["MK"] = "Macedonia, the former Yugoslav Republic of";
			$countries["MG"] = "Madagascar";
			$countries["MW"] = "Malawi";
			$countries["MY"] = "Malaysia";
			$countries["MV"] = "Maldives";
			$countries["ML"] = "Mali";
			$countries["MT"] = "Malta";
			$countries["MH"] = "Marshall Islands";
			$countries["MQ"] = "Martinique";
			$countries["MR"] = "Mauritania";
			$countries["MU"] = "Mauritius";
			$countries["YT"] = "Mayotte";
			$countries["MX"] = "Mexico";
			$countries["FM"] = "Micronesia, Federated States of";
			$countries["MD"] = "Moldova, Republic of";
			$countries["MC"] = "Monaco";
			$countries["MN"] = "Mongolia";
			$countries["ME"] = "Montenegro";
			$countries["MS"] = "Montserrat";
			$countries["MA"] = "Morocco";
			$countries["MZ"] = "Mozambique";
			$countries["MM"] = "Myanmar";
			$countries["NA"] = "Namibia";
			$countries["NR"] = "Nauru";
			$countries["NP"] = "Nepal";
			$countries["NL"] = "Netherlands";
			$countries["AN"] = "Netherlands Antilles";
			$countries["NC"] = "New Caledonia";
			$countries["NZ"] = "New Zealand";
			$countries["NI"] = "Nicaragua";
			$countries["NE"] = "Niger";
			$countries["NG"] = "Nigeria";
			$countries["NU"] = "Niue";
			$countries["NF"] = "Norfolk Island";
			$countries["MP"] = "Northern Mariana Islands";
			$countries["NO"] = "Norway";
			$countries["OM"] = "Oman";
			$countries["PK"] = "Pakistan";
			$countries["PW"] = "Palau";
			$countries["PS"] = "Palestinian Territory, Occupied";
			$countries["PA"] = "Panama";
			$countries["PG"] = "Papua New Guinea";
			$countries["PY"] = "Paraguay";
			$countries["PE"] = "Peru";
			$countries["PH"] = "Philippines";
			$countries["PN"] = "Pitcairn";
			$countries["PL"] = "Poland";
			$countries["PT"] = "Portugal";
			$countries["PR"] = "Puerto Rico";
			$countries["QA"] = "Qatar";
			$countries["RE"] = "Reunion";
			$countries["RO"] = "Romania";
			$countries["RU"] = "Russian Federation";
			$countries["RW"] = "Rwanda";
			$countries["BL"] = "Saint Barthélemy";
			$countries["SH"] = "Saint Helena";
			$countries["KN"] = "Saint Kitts and Nevis";
			$countries["LC"] = "Saint Lucia";
			$countries["MF"] = "Saint Martin (French part)";
			$countries["PM"] = "Saint Pierre and Miquelon";
			$countries["VC"] = "Saint Vincent and the Grenadines";
			$countries["WS"] = "Samoa";
			$countries["SM"] = "San Marino";
			$countries["ST"] = "Sao Tome and Principe";
			$countries["SA"] = "Saudi Arabia";
			$countries["SN"] = "Senegal";
			$countries["RS"] = "Serbia";
			$countries["SC"] = "Seychelles";
			$countries["SL"] = "Sierra Leone";
			$countries["SG"] = "Singapore";
			$countries["SK"] = "Slovakia";
			$countries["SI"] = "Slovenia";
			$countries["SB"] = "Solomon Islands";
			$countries["SO"] = "Somalia";
			$countries["ZA"] = "South Africa";
			$countries["GS"] = "South Georgia and the South Sandwich Islands";
			$countries["ES"] = "Spain";
			$countries["LK"] = "Sri Lanka";
			$countries["SD"] = "Sudan";
			$countries["SR"] = "Suriname";
			$countries["SJ"] = "Svalbard and Jan Mayen";
			$countries["SZ"] = "Swaziland";
			$countries["SE"] = "Sweden";
			$countries["CH"] = "Switzerland";
			$countries["SY"] = "Syrian Arab Republic";
			$countries["TW"] = "Taiwan, Province of China";
			$countries["TJ"] = "Tajikistan";
			$countries["TZ"] = "Tanzania, United Republic of";
			$countries["TH"] = "Thailand";
			$countries["TL"] = "Timor-Leste";
			$countries["TG"] = "Togo";
			$countries["TK"] = "Tokelau";
			$countries["TO"] = "Tonga";
			$countries["TT"] = "Trinidad and Tobago";
			$countries["TN"] = "Tunisia";
			$countries["TR"] = "Turkey";
			$countries["TM"] = "Turkmenistan";
			$countries["TC"] = "Turks and Caicos Islands";
			$countries["TV"] = "Tuvalu";
			$countries["UG"] = "Uganda";
			$countries["UA"] = "Ukraine";
			$countries["AE"] = "United Arab Emirates";
			$countries["GB"] = "United Kingdom";
			$countries["US"] = "United States";
			$countries["UM"] = "United States Minor Outlying Islands";
			$countries["UY"] = "Uruguay";
			$countries["UZ"] = "Uzbekistan";
			$countries["VU"] = "Vanuatu";
			$countries["VE"] = "Venezuela, Bolivarian Republic of";
			$countries["VN"] = "Viet Nam";
			$countries["VG"] = "Virgin Islands, British";
			$countries["VI"] = "Virgin Islands, U.S.";
			$countries["WF"] = "Wallis and Futuna";
			$countries["EH"] = "Western Sahara";
			$countries["YE"] = "Yemen";
			$countries["ZM"] = "Zambia";
			$countries["ZW"] = "Zimbabwe";
			return $countries;
	}	

	/*
	   Function: loadConfig
	       Converts the XML to a data array and calls the bind method.

	   Parameters:
	      XML - The XML for this Element
	*/
	public function loadConfig($xml) {

		parent::loadConfig($xml);
		
		if (isset($xml->selectable_country)) {
			$countries = array();
			
			foreach ($xml->selectable_country as $selectable_country) {
				$countries[] = (string) $selectable_country->attributes()->value;
			}
			
			$this->_config->set('selectable_countries', $countries);
		}
	}

	/*
		Function: getConfigForm
			Get parameter form object to render input form.

		Returns:
			Parameter Object
	*/
	public function getConfigForm() {
		
		$form = parent::getConfigForm();
		$form->addElementPath(dirname(__FILE__));

		return $form;
	}
			
	/*
	   Function: getConfigXML
   	      Get elements XML.

	   Returns:
	      Object - YXMLElement
	*/
	public function getConfigXML($ignore = array()) {

		$xml = parent::getConfigXML(array('selectable_countries'));
		
		foreach ($this->_config->get('selectable_countries', array()) as $selectable_country) {		
			if ($selectable_country['value'] != '') {
				$xml->addChild('selectable_country')->addAttribute('value', $selectable_country);	
			}
		}
		
		return $xml;
	}

}

class ElementCountryData extends ElementData{

	public function encodeData() {		
		$xml = YXMLElement::create($this->_element->getElementType());
		$xml->addAttribute('identifier', $this->_element->identifier);
		foreach($this->_params->get('country', array()) as $country) {
			$xml->addChild('country', $country, null, true);
		}

		return $xml;			
	}

	public function decodeXML(YXMLElement $element_xml) {
		$data = array();
		if (isset($element_xml->country)) {
			$countries = array();
			foreach ($element_xml->country as $country) {
				$countries[] = (string) $country;
			}
			$this->set('country', $countries);
		}
		return $data;
	}	
	
}