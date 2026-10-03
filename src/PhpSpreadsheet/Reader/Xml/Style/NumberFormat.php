<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xml\Style;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat as Format;
use SimpleXMLElement;

class NumberFormat extends StyleBase
{
    /** @internal */
    public const FORMAT_MAPPINGS = [
        'Euro Currency' => '€#,##0.00;[Red](€#,##0.00)',
        'Fixed' => Format::FORMAT_NUMBER_00,
        'General' => '',
        'General Number' => '',
        'General Date' => Format::FORMAT_DATE_DATETIME_BETTER,
        'Long Date' => Format::FORMAT_DATE_LONG_DATE,
        'Long Time' => Format::FORMAT_DATE_TIME2,
        'Medium Date' => Format::FORMAT_DATE_XLSX15_YYYY,
        'Medium Time' => Format::FORMAT_DATE_TIME1,
        'Percent' => Format::FORMAT_PERCENTAGE_00,
        'Scientific' => '0.00E+00',
        'Short Date' => Format::FORMAT_DATE_YYYYMMDD,
        'Short Time' => Format::FORMAT_DATE_TIME1,
        'Standard' => Format::FORMAT_NUMBER_COMMA_SEPARATED1,
        // N.B. - following formats apply to numbers, not booleans
        'True/False' => '"True";"True";"False"',
        'Yes/No' => '"Yes";"Yes";"No"',
        'On/Off' => '"On";"On";"Off"',
    ];

    /**
     * @param string[] $numberFormatMappings expected to be FORMAT_MAPPINGS, but individual entries can be overridden
     *
     * @return mixed[]
     */
    public function parseStyle(SimpleXMLElement $styleAttributes, array $numberFormatMappings = self::FORMAT_MAPPINGS): array
    {
        $style = [];

        $fromFormats = ['\-', '\ '];
        $toFormats = ['-', ' '];

        foreach ($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
            $styleAttributeValue = (string) $styleAttributeValue;
            $styleAttributeValue = str_replace($fromFormats, $toFormats, $styleAttributeValue);
            if (array_key_exists($styleAttributeValue, $numberFormatMappings)) {
                $styleAttributeValue = $numberFormatMappings[$styleAttributeValue];
            } elseif ($styleAttributeValue === 'Currency') {
                $currencyCode = StringHelper::getCurrencyCode();
                $styleAttributeValue = "{$currencyCode}#,##0.00;[Red]({$currencyCode}#,##0.00)";
            }

            if ($styleAttributeValue !== '') {
                $style['numberFormat']['formatCode'] = $styleAttributeValue;
            }
        }

        return $style;
    }
}
