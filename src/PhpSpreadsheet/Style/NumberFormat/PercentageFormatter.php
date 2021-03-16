<?php

namespace PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PercentageFormatter
{
    public static function format($value, string $format): string
    {
        if ($format === NumberFormat::FORMAT_PERCENTAGE) {
            return round((100 * $value), 0) . '%';
        }

        // Number of decimals
        if (preg_match('/\.[#0]+/', $format, $matches)) {
            $s = substr($matches[0], 0, 1) . (strlen($matches[0]) - 1);
            $format = str_replace($matches[0], $s, $format);
        }
        // Number of digits to display before the decimal
        if (preg_match('/^[#0]+/', $format, $matches)) {
            $format = str_replace($matches[0], strlen($matches[0]), $format);
        }
        $format = '%' . str_replace('%', 'f%%', $format);

        return sprintf($format, 100 * $value);
    }
}
