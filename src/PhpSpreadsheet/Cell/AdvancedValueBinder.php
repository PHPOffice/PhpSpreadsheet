<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

/**
 * Copyright (c) 2006 - 2016 PhpSpreadsheet.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category   PhpSpreadsheet
 *
 * @copyright  Copyright (c) 2006 - 2016 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */
class AdvancedValueBinder extends DefaultValueBinder implements IValueBinder
{
    /**
     * Bind value to a cell.
     *
     * @param \PhpOffice\PhpSpreadsheet\Cell $cell Cell to bind value to
     * @param mixed $value Value to bind in cell
     *
     * @return bool
     */
    public function bindValue(\PhpOffice\PhpSpreadsheet\Cell $cell, $value = null)
    {
        // sanitize UTF-8 strings
        if (is_string($value)) {
            $value = \PhpOffice\PhpSpreadsheet\Shared\StringHelper::sanitizeUTF8($value);
        }

        // Find out data type
        $dataType = parent::dataTypeForValue($value);

        // Style logic - strings
        if ($dataType === DataType::TYPE_STRING && !$value instanceof \PhpOffice\PhpSpreadsheet\RichText) {
            //    Test for booleans using locale-setting
            if ($value == \PhpOffice\PhpSpreadsheet\Calculation::getTRUE()) {
                $cell->setValueExplicit(true, DataType::TYPE_BOOL);

                return true;
            } elseif ($value == \PhpOffice\PhpSpreadsheet\Calculation::getFALSE()) {
                $cell->setValueExplicit(false, DataType::TYPE_BOOL);

                return true;
            }

            // Check for number in scientific format
            if (preg_match('/^' . \PhpOffice\PhpSpreadsheet\Calculation::CALCULATION_REGEXP_NUMBER . '$/', $value)) {
                $cell->setValueExplicit((float) $value, DataType::TYPE_NUMERIC);

                return true;
            }

            // Check for fraction
            if (preg_match('/^([+-]?)\s*([0-9]+)\s?\/\s*([0-9]+)$/', $value, $matches)) {
                // Convert value to number
                $value = $matches[2] / $matches[3];
                if ($matches[1] == '-') {
                    $value = 0 - $value;
                }
                $cell->setValueExplicit((float) $value, DataType::TYPE_NUMERIC);
                // Set style
                $cell->getWorksheet()->getStyle($cell->getCoordinate())
                    ->getNumberFormat()->setFormatCode('??/??');

                return true;
            } elseif (preg_match('/^([+-]?)([0-9]*) +([0-9]*)\s?\/\s*([0-9]*)$/', $value, $matches)) {
                // Convert value to number
                $value = $matches[2] + ($matches[3] / $matches[4]);
                if ($matches[1] == '-') {
                    $value = 0 - $value;
                }
                $cell->setValueExplicit((float) $value, DataType::TYPE_NUMERIC);
                // Set style
                $cell->getWorksheet()->getStyle($cell->getCoordinate())
                    ->getNumberFormat()->setFormatCode('# ??/??');

                return true;
            }

            // Check for percentage
            if (preg_match('/^\-?[0-9]*\.?[0-9]*\s?\%$/', $value)) {
                // Convert value to number
                $value = (float) str_replace('%', '', $value) / 100;
                $cell->setValueExplicit($value, DataType::TYPE_NUMERIC);
                // Set style
                $cell->getWorksheet()->getStyle($cell->getCoordinate())
                    ->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);

                return true;
            }

            // Check for currency
            $currencyCode = \PhpOffice\PhpSpreadsheet\Shared\StringHelper::getCurrencyCode();
            $decimalSeparator = \PhpOffice\PhpSpreadsheet\Shared\StringHelper::getDecimalSeparator();
            $thousandsSeparator = \PhpOffice\PhpSpreadsheet\Shared\StringHelper::getThousandsSeparator();
            if (preg_match('/^' . preg_quote($currencyCode) . ' *(\d{1,3}(' . preg_quote($thousandsSeparator) . '\d{3})*|(\d+))(' . preg_quote($decimalSeparator) . '\d{2})?$/', $value)) {
                // Convert value to number
                $value = (float) trim(str_replace([$currencyCode, $thousandsSeparator, $decimalSeparator], ['', '', '.'], $value));
                $cell->setValueExplicit($value, DataType::TYPE_NUMERIC);
                // Set style
                $cell->getWorksheet()->getStyle($cell->getCoordinate())
                    ->getNumberFormat()->setFormatCode(
                        str_replace('$', $currencyCode, \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE)
                    );

                return true;
            } elseif (preg_match('/^\$ *(\d{1,3}(\,\d{3})*|(\d+))(\.\d{2})?$/', $value)) {
                // Convert value to number
                $value = (float) trim(str_replace(['$', ','], '', $value));
                $cell->setValueExplicit($value, DataType::TYPE_NUMERIC);
                // Set style
                $cell->getWorksheet()->getStyle($cell->getCoordinate())
                    ->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

                return true;
            }

            // Check for time without seconds e.g. '9:45', '09:45'
            if (preg_match('/^(\d|[0-1]\d|2[0-3]):[0-5]\d$/', $value)) {
                // Convert value to number
                list($h, $m) = explode(':', $value);
                $days = $h / 24 + $m / 1440;
                $cell->setValueExplicit($days, DataType::TYPE_NUMERIC);
                // Set style
                $cell->getWorksheet()->getStyle($cell->getCoordinate())
                    ->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME3);

                return true;
            }

            // Check for time with seconds '9:45:59', '09:45:59'
            if (preg_match('/^(\d|[0-1]\d|2[0-3]):[0-5]\d:[0-5]\d$/', $value)) {
                // Convert value to number
                list($h, $m, $s) = explode(':', $value);
                $days = $h / 24 + $m / 1440 + $s / 86400;
                // Convert value to number
                $cell->setValueExplicit($days, DataType::TYPE_NUMERIC);
                // Set style
                $cell->getWorksheet()->getStyle($cell->getCoordinate())
                    ->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME4);

                return true;
            }

            // Check for datetime, e.g. '2008-12-31', '2008-12-31 15:59', '2008-12-31 15:59:10'
            if (($d = \PhpOffice\PhpSpreadsheet\Shared\Date::stringToExcel($value)) !== false) {
                // Convert value to number
                $cell->setValueExplicit($d, DataType::TYPE_NUMERIC);
                // Determine style. Either there is a time part or not. Look for ':'
                if (strpos($value, ':') !== false) {
                    $formatCode = 'yyyy-mm-dd h:mm';
                } else {
                    $formatCode = 'yyyy-mm-dd';
                }
                $cell->getWorksheet()->getStyle($cell->getCoordinate())
                    ->getNumberFormat()->setFormatCode($formatCode);

                return true;
            }

            // Check for newline character "\n"
            if (strpos($value, "\n") !== false) {
                $value = \PhpOffice\PhpSpreadsheet\Shared\StringHelper::sanitizeUTF8($value);
                $cell->setValueExplicit($value, DataType::TYPE_STRING);
                // Set style
                $cell->getWorksheet()->getStyle($cell->getCoordinate())
                    ->getAlignment()->setWrapText(true);

                return true;
            }
        }

        // Not bound yet? Use parent...
        return parent::bindValue($cell, $value);
    }
}
