<?php

namespace PhpOffice\PhpSpreadsheet\Helper\NumberFormat;

class CurrencyLookup
{
    protected static $countryCurrencies = [
        'AF' => 'AFN', // Afghanistan => Afghani
        'AL' => 'ALL', // Albania => Lek
        'DZ' => 'DZD', // Algeria => Algerian Dinar
        'AS' => 'USD', // American Samoa => US Dollar
        'AD' => 'EUR', // Andorra => Euro
        'AO' => 'AOA', // Angola => Kwanza
        'AI' => 'XCD', // Anguilla => East Caribbean Dollar?
        'AG' => 'XCD', // Antigua and Barbuda => East Caribbean Dollar?
        'AR' => 'ARS', // Argentina => Argentine Peso
        'AM' => 'AMD', // Armenia => Armenian Dram
        'AW' => 'AWG', // Aruba => Aruban Florin
        'AU' => 'AUD', // Australia => Australian Dollar
        'AT' => 'EUR', // Austria => Euro
        'AZ' => 'AZN', // Azerbaijan => Azerbaijanian Manat
        'BS' => 'BSD', // Bahamas => Bahamian Dollar
        'BH' => 'BHD', // Bahrain => Bahraini Dinar
        'BD' => 'BDT', // Bangladesh => Taka
        'BB' => 'BBD', // Barbados => Barbados Dollar
        'BY' => 'BYR', // Belarus => Belarusian Ruble
        'BE' => 'EUR', // Belgium => Euro
        'BZ' => 'BZD', // Belize => Belize Dollar
        'BJ' => 'XOF', // Benin => CFA Franc BCEAO
        'BM' => 'BMD', // Bermuda => Bermudian Dollar
        'BT' => 'BTN', // Bhutan => Ngultrum
        'BO' => 'BOB', // Bolivia => Boliviano
        'BA' => 'BAM', // Bosnia and Herzegovina => Convertible Marks
        'BW' => 'BWP', // Botswana => Pula
        'BV' => 'NOK', // Bouvet Island => Norwegian Krone
        'BR' => 'BRL', // Brazil => Brazilian Real
        'IO' => 'USD', // British Indian Ocean Territory => US Dollar
        'BN' => 'BND', // Brunei Darussalam => Brunei Dollar
        'BG' => 'BGN', // Brunei Darussalam => Bulgarian Lev
        'BF' => 'XOF', // Burkina Faso => CFA Franc BCEAO
        'BI' => 'BIF', // Burundi => Burundi Franc
        'KH' => 'KHR', // Cambodia => Real
        'CM' => 'XAF', // Cameroon => CFA Franc BEAC
        'CA' => 'CAD', // Canada => Canadian Dollar
        'CV' => 'CVE', // Cape Verde => Cabo Verde Escudo
        'KY' => 'KYD', // Cayman Islands => Cayman Islands Dollar
        'CF' => 'XAF', // Central African Republic => CFA Franc BEAC
        'TD' => 'XAF', // Chad => CFA Franc BEAC
        'CL' => 'CLP', // Chile => Chilean Peso
        'CN' => 'CNY', // China => Yuan Renminbi
        'HK' => 'HKD', // Hong Kong => Hong Kong Dollar
        'CX' => 'AUD', // Christmas Island => Australian Dollar
        'CC' => 'AUD', // Cocos (Keeling) Islands => Australian Dollar
        'CO' => 'COP', // Colombia => Colombian Peso
        'KM' => 'KMF', // Comoros => Comoro Franc
        'CG' => 'XAF', // Congo => CFA Franc BEAC
        'CD' => 'CDF', // Democratic Republic of the Congo => Franc Congolais
        'CK' => 'NZD', // Cook Islands => New Zealand Dollar
        'CR' => 'CRC', // Costa Rica => Costa Rican Colon
        'HR' => 'HRK', // Croatia => Croatian Kuna
        'CU' => 'CUP', // Cuba => Cuba
        'CY' => 'CYP', // Cyprus => Cyprus Pound
        'CZ' => 'CZK', // Czech Republic => Czech Koruna
        'DK' => 'DKK', // Denmark => Danish Krone
        'DJ' => 'DJF', // Djibouti => Djibouti Franc
        'DM' => 'XCD', // Dominica => East Caribbean Dollar
        'DO' => 'DOP', // Dominican Republic => Dominican Peso
        'EC' => 'USD', // Ecuador => US Dollar
        'EG' => 'EGP', // Egypt => Egyptian Pound
        'SV' => 'SVC', // El Salvador => El Salvador Colon
        'GQ' => 'XAF', // Equatorial Guinea => CFA Franc BEAC
        'ER' => 'ERN', // Eritrea => Nakfa
        'EE' => 'EUR', // Estonia => Euro
        'ET' => 'ETB', // Ethiopia => Ethiopian Birr
        'FK' => 'FKP', // Falkland Islands => Falkland Islands Pound
        'FO' => 'DKK', // Faroe Islands => Danish Krone
        'FJ' => 'FJD', // Fiji => Fiji Dollar
        'FI' => 'EUR', // Finland => Euro
        'FR' => 'EUR', // France => Euro
        'GF' => 'EUR', // French Guiana => Euro
        'TF' => 'EUR', // French Southern Territories => Euro
        'GA' => 'XAF', // Gabon => CFA Franc BEAC
        'GM' => 'GMD', // Gambia => Dalasi
        'GE' => 'GEL', // Georgia => Lari
        'DE' => 'EUR', // Germany => Euro
        'GH' => 'GHS', // Ghana => Ghana Cedi
        'GI' => 'GIP', // Gibraltar => Gibraltar Pound
        'GR' => 'EUR', // Greece => Euro
        'GL' => 'DKK', // Greece => Danish Krone
        'GD' => 'XCD', // Grenada => East Caribbean Dollar
        'GP' => 'EUR', // Guadeloupe => Euro
        'GU' => 'USD', // Guam => US Dollar
        'GT' => 'QTQ', // Guatemala => Quetzal
        'GG' => 'GBP', // Guernsey => Pound Sterling
        'GN' => 'GNF', // Guinea => Guinea Franc
        'GW' => 'GWP', // Guinea-Bissau => Guinea-Bissau Peso
        'GY' => 'GYD', // Guyana => Guyana Dollar
        'HT' => 'HTG', // Haiti => Gourde
        'HM' => 'AUD', // Heard Island and McDonald Islands => Australian Dollar
        'HN' => 'HNL', // Honduras => Lempira
        'HU' => 'HUF', // Hungary => Forint
        'IS' => 'ISK', // Iceland => Iceland Krona
        'IN' => 'INR', // India => Rupee
        'ID' => 'IDR', // Indonesia => Rupiah
        'IR' => 'IRR', // Iran => Iranian Rial
        'IQ' => 'IQD', // Iraq => Iraqi Dinar
        'IE' => 'EUR', // Ireland => Euro
        'IM' => 'GBP', // Isle of Man => Pound Sterling
        'IL' => 'ILS', // Israel => New Israeli Sheqel
        'IT' => 'EUR', // Italy => Euro
        'JM' => 'JMD', // Jamaica => Jamaica
        'JP' => 'JPY', // Japan => Yen
        'JE' => 'GBP', // Jersey => Pound Sterling
        'JO' => 'JOD', // Jordan => Jordanian Dinar
        'KZ' => 'KZT', // Kazakhstan => Tenge
        'KE' => 'KES', // Kenya => Kenyan Shilling
        'KI' => 'AUD', // Kiribati => Kiribati
        'KP' => 'KPW', // Democratic People's Republic of Korea => North Korean Won
        'KR' => 'KRW', // Republic of Korea => Won
        'KW' => 'KWD', // Kuwait => Kuwait
        'KG' => 'KGS', // Kyrgyzstan => Som
        'LA' => 'LAK', // Lao People's Democratic Republic => Kip
        'LV' => 'EUR', // Latvia => Euro
        'LB' => 'LBP', // Lebanon => Lebanese Pound
        'LS' => 'LSL', // Lesotho => Loti
        'LR' => 'LRD', // Liberia => Liberian Dollar
        'LY' => 'LYD', // Libya => Libyan Dinar
        'LI' => 'CHF', // Liechtenstein => Swiss Franc
        'LT' => 'EUR', // Lithuania => Euro
        'LU' => 'EUR', // Luxembourg => Euro
        'MK' => 'MKD', // Macedonia => Denar
        'MG' => 'MGA', // Madagascar => Malagasy Ariary
        'MW' => 'MWK', // Malawi => Kwacha
        'MY' => 'MYR', // Malaysia => Malaysian Ringgit
        'MV' => 'MVR', // Maldives => Rufiyaa
        'ML' => 'XOF', // Mali => CFA Franc BCEAO
        'MT' => 'EUR', // Malta => Euro
        'MH' => 'USD', // Marshall Islands => US Dollar
        'MQ' => 'EUR', // Martinique => Euro
        'MR' => 'MRO', // Mauritania => Ouguiya
        'MU' => 'MUR', // Mauritius => Mauritius Rupee
        'YT' => 'EUR', // Mayotte => Euro
        'MX' => 'MXN', // Mexico => Peso
        'FM' => 'USD', // Federated States of Micronesia => US Dollar
        'MD' => 'MDL', // Moldova => Moldovan Leu
        'MC' => 'EUR', // Monaco => Euro
        'MN' => 'MNT', // Mongolia => Mongolia
        'ME' => 'EUR', // Montenegro => Euro
        'MS' => 'XCD', // Montserrat => East Caribbean Dollar
        'MA' => 'MAD', // Morocco => Moroccan Dirham
        'MZ' => 'MZN', // Mozambique => Mozambique Metical
        'MM' => 'MMK', // Myanmar => Kyat
        'NA' => 'NAD', // Namibia => Namibia Dollar
        'NR' => 'AUD', // Nauru => Australian Dollar
        'NP' => 'NPR', // Nepal => Nepalese Rupee
        'NL' => 'EUR', // Netherlands => Euro
        'NC' => 'XPF', // New Caledonia => CFP Franc
        'NZ' => 'NZD', // New Zealand => New Zealand Dollar
        'NI' => 'NIO', // Nicaragua => Cordoba Oro
        'NE' => 'XOF', // Niger => CFA Franc BCEAO
        'NG' => 'NGN', // Nigeria => Naira
        'NU' => 'NZD', // Niue => New Zealand Dollar
        'NF' => 'AUD', // Norfolk Island => Australian Dollar
        'MP' => 'USD', // Northern Mariana Islands => US Dollar
        'NO' => 'NOK', // Norway => Norwegian Krone
        'OM' => 'OMR', // Oman => Omani Rial
        'PK' => 'PKR', // Pakistan => Pakistan Rupee
        'PW' => 'USD', // Palau => US Dollar
        'PA' => 'PAB', // Panama => Balboa
        'PG' => 'PGK', // Papua New Guinea => Kina
        'PY' => 'PYG', // Paraguay => Guarani
        'PE' => 'PEN', // Peru => Nuevo Sol
        'PH' => 'PHP', // Philippines => Philippine Peso
        'PN' => 'NZD', // Pitcairn => New Zealand Dollar
        'PL' => 'PLN', // Poland => Zloty
        'PT' => 'EUR', // Portugal => Euro
        'PR' => 'USD', // Puerto Rico => US Dollar
        'QA' => 'QAR', // Qatar => Qatari Rial
        'RE' => 'EUR', // Réunion => Euro
        'RO' => 'RON', // Romania => New Romanian Leu
        'RU' => 'RUB', // Russian Federation => Russian Ruble
        'RW' => 'RWF', // Rwanda => Rwanda Franc
        'SH' => 'SHP', // Saint Helena, Ascension and Tristan Da Cunha => Saint Helena Pound
        'KN' => 'XCD', // Saint Kitts and Nevis => East Caribbean Dollar
        'LC' => 'XCD', // Saint Lucia => East Caribbean Dollar
        'PM' => 'EUR', // Saint Pierre and Miquelon => Euro
        'VC' => 'XCD', // Saint Vincent and The Grenadines => East Caribbean Dollar
        'WS' => 'WST', // Samoa => Tala
        'SM' => 'EUR', // San Marino => Euro
        'ST' => 'STD', // Sao Tome and Principe => Dobra
        'SA' => 'SAR', // Saudi Arabia => Saudi Riyal
        'SN' => 'XOF', // Senegal => CFA Franc BCEAO
        'RS' => 'RSD', // Serbia => Serbian Dinar
        'SC' => 'SCR', // Seychelles => Seychelles Rupee
        'SL' => 'SLL', // Sierra Leone => Leone
        'SG' => 'SGD', // Singapore => Singapore Dollar
        'SK' => 'EUR', // Slovakia => Euro
        'SI' => 'EUR', // Slovenia => Euro
        'SB' => 'SBD', // Solomon Islands => Solomon Islands Dollar
        'SO' => 'SOS', // Somalia => Somali Shilling
        'ZA' => 'ZAR', // South Africa => Rand
        'SS' => 'SSP', // South Sudan => South Sudanese Pound
        'ES' => 'EUR', // Spin => Euro
        'LK' => 'LKR', // Sri Lanka => Sri Lanka Rupee
        'SD' => 'SDG', // Sudan => Sudanese Pound
        'SR' => 'SRD', // Suriname => Surinam Dollar
        'SJ' => 'NOK', // Svalbard and Jan Mayen => Norwegian Krone
        'SZ' => 'SZL', // Swaziland => Lilangeni
        'SE' => 'SEK', // Sweden => Swedish Krona
        'CH' => 'CHF', // Switzerland => Swiss Franc
        'SY' => 'SYP', // Syria => Syrian Pound
        'TW' => 'TWD', // Taiwan => New Taiwan Dollar
        'TJ' => 'TJS', // Tajikistan => Somoni
        'TZ' => 'TZS', // Tanzania => Tanzanian Shilling
        'TH' => 'THB', // Thailand => Baht
        'TG' => 'XOF', // Togo => CFA Franc BCEAO
        'TK' => 'NZD', // Tokelau => New Zealand Dollar
        'TO' => 'TOP', // Tonga => Pa'anga
        'TT' => 'TTD', // Trinidad and Tobago => Trinidad and Tobago Dollar
        'TN' => 'TND', // Tunisia => Tunisian Dinar
        'TR' => 'TRY', // Turkey => New Turkish Lira
        'TM' => 'TMT', // Turkmenistan => Turkmenistan New Manat
        'TC' => 'USD', // Turks and Caicos Islands => US Dollar
        'TV' => 'AUD', // Tuvalu => Australian Dollar
        'UG' => 'UGX', // Uganda => Ugandan Shilling
        'UA' => 'UAH', // Ukraine => Hryvnia
        'AE' => 'AED', // United Arab Emirates => UAE Dirham
        'GB' => 'GBP', // Great Britain => Pound Sterling
        'UK' => 'GBP', // United Kingdom => Pound Sterling
        'US' => 'USD', // United States of America => US Dollar
        'UM' => 'USD', // United States Minor Outlying Islands => US Dollar
        'UY' => 'UYU', // Uruguay => Peso Uruguayo
        'UZ' => 'UZS', // Uzbekistan => Uzbekistan Sum
        'VU' => 'VUV', // Vanuatu => Vatu
        'VE' => 'VEF', // Venezuela => Bolivar
        'VN' => 'VND', // Viet Nam => Dong
        'VI' => 'USD', // Virgin Islands => US Dollar
        'WF' => 'XPF', // Wallis And Futuna => CFP Franc
        'EH' => 'MAD', // Western Sahara => Moroccan Dirham
        'YE' => 'YER', // Yemen => Yemeni Rial
        'ZM' => 'ZMW', // Zambia => Kwacha
        'ZW' => 'ZWD', // Zimbabwe => Zimbabwe Dollar
    ];

    public static function lookup(string $countryCode): ?string
    {
        $countryCode = strtoupper($countryCode);

        return array_key_exists($countryCode, self::$countryCurrencies) ? self::$countryCurrencies[$countryCode] : null;
    }

    public static function symbolLookup(string $countryCode): ?string
    {
        $currencyCode = self::lookup($countryCode);

        return ($currencyCode !== null) ? CurrencySymbolLookup::lookup($currencyCode) : null;
    }
}
