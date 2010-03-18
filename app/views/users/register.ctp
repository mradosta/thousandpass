<div class="inner_container_border">

	<div class="inner_container">

	<div class="left"></div>

	<div class="right">

		<div class="text_and_logo">
			<?php
				echo $html->tag('span', __('Sign Up at', true));
				echo $html->image('logo_black.png');
			?>
			<span class="subtitle"><?php __('We order your home page') ?></span>
		</div>


			<?php
				echo $form->create('User', array('action' => 'register'));
				echo $form->input('name', array(
					'label' => __('Name', true),
					'after'	=> '(*)',
					'error' => array(
						'not_empty'	=> __('Must enter the Username.', true),
					)
				));
				echo $form->input('lastname', array(
					'label' => __('Lastname', true),
					'after'	=> '(*)',
					'error' => array(
						'not_empty'	=> __('Must enter the Lastname.', true),
					)
				));
				echo $form->input('birthdate', array(
					'label' 		=> __('Birth Date', true),
					'type'			=> 'date',
					'separator'		=> '',
					'dateFormat' 	=> 'D/M/Y',
					'after'			=> '(*)',
					'error' => array(
						'valid'	=> __('Must select the Birth Date.', true),
					)
				));
				echo $form->input('sex', array(
					'label' => __('Sex', true),
					'options' 	=> array(
						'm' => __('Male', true),
						'f' => __('Female', true))));
				echo $form->input('country', array(
					'label' => __('Country', true),
					'options'	=> array(
						'North America' => array(
							'Anguilla' => 'Anguilla',
							'Antigua and Barbuda' => 'Antigua and Barbuda',
							'Aruba' => 'Aruba',
							'Bahamas' => 'Bahamas',
							'Barbados' => 'Barbados',
							'Belize' => 'Belize',
							'Bermuda' => 'Bermuda',
							'Virgin Islands, British' => 'Virgin Islands, British',
							'Canada' => 'Canada',
							'Cayman Islands' => 'Cayman Islands',
							'Costa Rica' => 'Costa Rica',
							'Cuba' => 'Cuba',
							'Dominica' => 'Dominica',
							'Dominican Republic' => 'Dominican Republic',
							'El Salvador' => 'El Salvador',
							'Falkland Islands (Malvinas)' => 'Falkland Islands (Malvinas)',
							'Greenland' => 'Greenland',
							'Grenada' => 'Grenada',
							'Guadeloupe' => 'Guadeloupe',
							'Guatemala' => 'Guatemala',
							'Haiti' => 'Haiti',
							'Honduras' => 'Honduras',
							'Jamaica' => 'Jamaica',
							'Martinique' => 'Martinique',
							'Mexico' => 'Mexico',
							'Montserrat' => 'Montserrat',
							'Netherlands Antilles' => 'Netherlands Antilles',
							'Nicaragua' => 'Nicaragua',
							'Panama' => 'Panama',
							'Puerto Rico' => 'Puerto Rico',
							'Saint Kitts and Nevis' => 'Saint Kitts and Nevis',
							'Saint Lucia' => 'Saint Lucia',
							'Saint Pierre and Miquelon' => 'Saint Pierre and Miquelon',
							'Saint Vincent and The Grenadines' => 'Saint Vincent and The Grenadines',
							'Trinidad and Tobago' => 'Trinidad and Tobago',
							'Turks and Caicos Islands' => 'Turks and Caicos Islands',
							'United States' => 'United States',
							'United States Minor Outlying Islands' => 'United States Minor Outlying Islands',
							'Virgin Islands, U.S.' => 'Virgin Islands, U.S.'),
						'South America' => array(
							'Argentina' => 'Argentina',
							'Bolivia' => 'Bolivia',
							'Brazil' => 'Brazil',
							'Chile' => 'Chile',
							'Colombia' => 'Colombia',
							'Ecuador' => 'Ecuador',
							'French Guiana' => 'French Guiana',
							'Guyana' => 'Guyana',
							'Paraguay' => 'Paraguay',
							'Peru' => 'Peru',
							'Suriname' => 'Suriname',
							'Uruguay' => 'Uruguay',
							'Venezuela' => 'Venezuela'),
						'Africa' => array(
							'Algeria' => 'Algeria',
							'Angola' => 'Angola',
							'Benin' => 'Benin',
							'Botswana' => 'Botswana',
							'Burkina' => 'Burkina',
							'Burundi' => 'Burundi',
							'Cameroon' => 'Cameroon',
							'Cape Verde' => 'Cape Verde',
							'Central African' => 'Central African',
							'dot Republic' => 'dot Republic',
							'Chad' => 'Chad',
							'Comoros' => 'Comoros',
							'Congo' => 'Congo',
							'Congo' => 'Congo',
							'dot (Dem. Rep.)' => 'dot (Dem. Rep.)',
							'Djibouti' => 'Djibouti',
							'Egypt' => 'Egypt',
							'Equatorial Guinea' => 'Equatorial Guinea',
							'Eritrea' => 'Eritrea',
							'Ethiopia' => 'Ethiopia',
							'Gabon' => 'Gabon',
							'Gambia' => 'Gambia',
							'Ghana' => 'Ghana',
							'Guinea' => 'Guinea',
							'Guinea-Bissau' => 'Guinea-Bissau',
							'Ivory Coast' => 'Ivory Coast',
							'Kenya' => 'Kenya',
							'Lesotho' => 'Lesotho',
							'Liberia' => 'Liberia',
							'Libya' => 'Libya',
							'Madagascar' => 'Madagascar',
							'Malawi' => 'Malawi',
							'Mali' => 'Mali',
							'Mauritania' => 'Mauritania',
							'Mauritius' => 'Mauritius',
							'Morocco' => 'Morocco',
							'Mozambique' => 'Mozambique',
							'Namibia' => 'Namibia',
							'Niger' => 'Niger',
							'Nigeria' => 'Nigeria',
							'Rwanda' => 'Rwanda',
							'Sao Tome' => 'Sao Tome',
							'dot and Principe' => 'dot and Principe',
							'Senegal' => 'Senegal',
							'Seychelles' => 'Seychelles',
							'Sierra Leone' => 'Sierra Leone',
							'Somalia' => 'Somalia',
							'South Africa' => 'South Africa',
							'Sudan' => 'Sudan',
							'Swaziland' => 'Swaziland',
							'Tanzania' => 'Tanzania',
							'Togo' => 'Togo',
							'Tunisia' => 'Tunisia',
							'Uganda' => 'Uganda',
							'Zambia' => 'Zambia',
							'Zimbabwe' => 'Zimbabwe'),
						'Asia' => array(
							'Afghanistan' => 'Afghanistan',
							'Bahrain' => 'Bahrain',
							'Bangladesh' => 'Bangladesh',
							'Bhutan' => 'Bhutan',
							'Brunei' => 'Brunei',
							'Burma (Myanmar)' => 'Burma (Myanmar)',
							'Cambodia' => 'Cambodia',
							'China' => 'China',
							'East Timor' => 'East Timor',
							'India' => 'India',
							'Indonesia' => 'Indonesia',
							'Iran' => 'Iran',
							'Iraq' => 'Iraq',
							'Israel' => 'Israel',
							'Japan' => 'Japan',
							'Jordan' => 'Jordan',
							'Kazakhstan' => 'Kazakhstan',
							'Korea (north)' => 'Korea (north)',
							'Korea (south)' => 'Korea (south)',
							'Kuwait' => 'Kuwait',
							'Kyrgyzstan' => 'Kyrgyzstan',
							'Laos' => 'Laos',
							'Lebanon' => 'Lebanon',
							'Malaysia' => 'Malaysia',
							'Maldives' => 'Maldives',
							'Mongolia' => 'Mongolia',
							'Nepal' => 'Nepal',
							'Oman' => 'Oman',
							'Pakistan' => 'Pakistan',
							'Philippines' => 'Philippines',
							'Qatar' => 'Qatar',
							'Russian' => 'Russian',
							'dotFederation' => 'dotFederation',
							'Saudi Arabia' => 'Saudi Arabia',
							'Singapore' => 'Singapore',
							'Sri Lanka' => 'Sri Lanka',
							'Syria' => 'Syria',
							'Tajikistan' => 'Tajikistan',
							'Thailand' => 'Thailand',
							'Turkey' => 'Turkey',
							'Turkmenistan' => 'Turkmenistan',
							'United Arab' => 'United Arab',
							'dot Emirates' => 'dot Emirates',
							'Uzbekistan' => 'Uzbekistan',
							'Vietnam' => 'Vietnam',
							'Yemen' => 'Yemen'),
						'Europe' => array(
							'Albania' => 'Albania',
							'Andorra' => 'Andorra',
							'Armenia' => 'Armenia',
							'Austria' => 'Austria',
							'Azerbaijan' => 'Azerbaijan',
							'Belarus' => 'Belarus',
							'Belgium' => 'Belgium',
							'Bosnia' => 'Bosnia',
							'dotand Herzegovina' => 'dotand Herzegovina',
							'Bulgaria' => 'Bulgaria',
							'Croatia' => 'Croatia',
							'Cyprus' => 'Cyprus',
							'Czech Republic' => 'Czech Republic',
							'Denmark' => 'Denmark',
							'Estonia' => 'Estonia',
							'Finland' => 'Finland',
							'France' => 'France',
							'Georgia' => 'Georgia',
							'Germany' => 'Germany',
							'Greece' => 'Greece',
							'Hungary' => 'Hungary',
							'Iceland' => 'Iceland',
							'Ireland' => 'Ireland',
							'Italy' => 'Italy',
							'Latvia' => 'Latvia',
							'Liechtenstein' => 'Liechtenstein',
							'Lithuania' => 'Lithuania',
							'Luxembourg' => 'Luxembourg',
							'Macedonia' => 'Macedonia',
							'Malta' => 'Malta',
							'Moldova' => 'Moldova',
							'Monaco' => 'Monaco',
							'Montenegro' => 'Montenegro',
							'Netherlands' => 'Netherlands',
							'Norway' => 'Norway',
							'Poland' => 'Poland',
							'Portugal' => 'Portugal',
							'Romania' => 'Romania',
							'San Marino' => 'San Marino',
							'Serbia' => 'Serbia',
							'Slovakia' => 'Slovakia',
							'Slovenia' => 'Slovenia',
							'Spain' => 'Spain',
							'Sweden' => 'Sweden',
							'Switzerland' => 'Switzerland',
							'Ukraine' => 'Ukraine',
							'United Kingdom' => 'United Kingdom',
							'Vatican City' => 'Vatican City'),
						'Oceania' => array(
							'American Samoa' => 'American Samoa',
							'Australia' => 'Australia',
							'Christmas Island' => 'Christmas Island',
							'Cocos (Keeling) Islands' => 'Cocos (Keeling) Islands',
							'Cook Islands' => 'Cook Islands',
							'Easter Island' => 'Easter Island',
							'Fiji' => 'Fiji',
							'Guam' => 'Guam',
							'Indonesia' => 'Indonesia',
							'Kiribati' => 'Kiribati',
							'Marshall Islands' => 'Marshall Islands',
							'Micronesia, Federated States of' => 'Micronesia, Federated States of',
							'Nauru' => 'Nauru',
							'New Caledonia' => 'New Caledonia',
							'New Zealand' => 'New Zealand',
							'Niue' => 'Niue',
							'Norfolk Island' => 'Norfolk Island',
							'Northern Mariana Islands' => 'Northern Mariana Islands',
							'Palau' => 'Palau',
							'Papua New Guinea' => 'Papua New Guinea',
							'Pitcairn' => 'Pitcairn',
							'French Polynesia' => 'French Polynesia',
							'Samoa' => 'Samoa',
							'Solomon Islands' => 'Solomon Islands',
							'Tokelau' => 'Tokelau',
							'Tonga' => 'Tonga',
							'Tuvalu' => 'Tuvalu',
							'Vanuatu' => 'Vanuatu'),
						'Antartica' => array(
							'Antarctica' => 'Antarctica',
							'Bouvet Island' => 'Bouvet Island',
							'French Southern Territories' => 'French Southern Territories',
							'Heard Island and Mcdonald Islands' => 'Heard Island and Mcdonald Islands',
							'South Georgia and The South Sandwich Islands' => 'South Georgia and The South Sandwich Islands'))));

				echo $form->input('username', array(
					'label' => __('Username', true),
					'error' => array(
						'unique' 		=> __('This username has already been taken.', true),
						'alphanumeric'	=> __('Only the letters A-z and digits 0-9 are allowed', true),
						'length'		=> __('Your username must be between 4 and 20 characters long', true),
					)
				));
				echo $form->input('password', array(
					'label' => __('Password', true),
					'type' => 'password',
					'error' => array(
						'alphanumeric'	=> __('Only the letters A-z and digits 0-9 are allowed', true),
						'length'		=> __('Your password must be at least 6 characters long', true),
					)
				));
				echo $form->input('repassword', array(
					'label' => __('Retype Password', true),
					'type' 	=> 'password',
					'error' => array(
						'repeated'	=> __('Passwords do not match', true),
					)
				));
				echo $form->input('email', array(
					'label' => __('Email', true),
					'error' => array(
						'valid'		=> __('Your email is not valid', true),
					)
				));

				echo $html->link(__('Term of Service', true), array('controller' => 'pages', 'action' => 'contract'), array('target' => '_BLANK'));
				echo $form->input('policy', array('label' => __('I understand and accept the term of service', true), 'type' => 'checkbox'));

?>
		<?php echo $form->end(__('Sign Up', true));?>
		</div>
	</div> <!--right-->
</div> <!--inner_container_border-->