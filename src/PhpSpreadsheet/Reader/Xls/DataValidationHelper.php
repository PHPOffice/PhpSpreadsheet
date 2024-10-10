<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class DataValidationHelper extends Xls
{
    /**
     * @var array<int, string>
     */
    private static array $types = [
        0x00 => DataValidation::TYPE_NONE,
        0x01 => DataValidation::TYPE_WHOLE,
        0x02 => DataValidation::TYPE_DECIMAL,
        0x03 => DataValidation::TYPE_LIST,
        0x04 => DataValidation::TYPE_DATE,
        0x05 => DataValidation::TYPE_TIME,
        0x06 => DataValidation::TYPE_TEXTLENGTH,
        0x07 => DataValidation::TYPE_CUSTOM,
    ];

    /**
     * @var array<int, string>
     */
    private static array $errorStyles = [
        0x00 => DataValidation::STYLE_STOP,
        0x01 => DataValidation::STYLE_WARNING,
        0x02 => DataValidation::STYLE_INFORMATION,
    ];

    /**
     * @var array<int, string>
     */
    private static array $operators = [
        0x00 => DataValidation::OPERATOR_BETWEEN,
        0x01 => DataValidation::OPERATOR_NOTBETWEEN,
        0x02 => DataValidation::OPERATOR_EQUAL,
        0x03 => DataValidation::OPERATOR_NOTEQUAL,
        0x04 => DataValidation::OPERATOR_GREATERTHAN,
        0x05 => DataValidation::OPERATOR_LESSTHAN,
        0x06 => DataValidation::OPERATOR_GREATERTHANOREQUAL,
        0x07 => DataValidation::OPERATOR_LESSTHANOREQUAL,
    ];

    public static function type(int $type): ?string
    {
        if (isset(self::$types[$type])) {
            return self::$types[$type];
        }

        return null;
    }

    public static function errorStyle(int $errorStyle): ?string
    {
        if (isset(self::$errorStyles[$errorStyle])) {
            return self::$errorStyles[$errorStyle];
        }

        return null;
    }

    public static function operator(int $operator): ?string
    {
        if (isset(self::$operators[$operator])) {
            return self::$operators[$operator];
        }

        return null;
    }

    /**
     * Read DATAVALIDATION record.
     */
    protected function readDataValidation2(Xls $xls): void
    {
        $length = self::getUInt2d($xls->data, $xls->pos + 2);
        $recordData = $xls->readRecordData($xls->data, $xls->pos + 4, $length);

        // move stream pointer forward to next record
        $xls->pos += 4 + $length;

        if ($xls->readDataOnly) {
            return;
        }

        // offset: 0; size: 4; Options
        $options = self::getInt4d($recordData, 0);

        // bit: 0-3; mask: 0x0000000F; type
        $type = (0x0000000F & $options) >> 0;
        $type = self::type($type);

        // bit: 4-6; mask: 0x00000070; error type
        $errorStyle = (0x00000070 & $options) >> 4;
        $errorStyle = self::errorStyle($errorStyle);

        // bit: 7; mask: 0x00000080; 1= formula is explicit (only applies to list)
        // I have only seen cases where this is 1
        //$explicitFormula = (0x00000080 & $options) >> 7;

        // bit: 8; mask: 0x00000100; 1= empty cells allowed
        $allowBlank = (0x00000100 & $options) >> 8;

        // bit: 9; mask: 0x00000200; 1= suppress drop down arrow in list type validity
        $suppressDropDown = (0x00000200 & $options) >> 9;

        // bit: 18; mask: 0x00040000; 1= show prompt box if cell selected
        $showInputMessage = (0x00040000 & $options) >> 18;

        // bit: 19; mask: 0x00080000; 1= show error box if invalid values entered
        $showErrorMessage = (0x00080000 & $options) >> 19;

        // bit: 20-23; mask: 0x00F00000; condition operator
        $operator = (0x00F00000 & $options) >> 20;
        $operator = self::operator($operator);

        if ($type === null || $errorStyle === null || $operator === null) {
            return;
        }

        // offset: 4; size: var; title of the prompt box
        $offset = 4;
        $string = self::readUnicodeStringLong(substr($recordData, $offset));
        $promptTitle = $string['value'] !== chr(0) ? $string['value'] : '';
        $offset += $string['size'];

        // offset: var; size: var; title of the error box
        $string = self::readUnicodeStringLong(substr($recordData, $offset));
        $errorTitle = $string['value'] !== chr(0) ? $string['value'] : '';
        $offset += $string['size'];

        // offset: var; size: var; text of the prompt box
        $string = self::readUnicodeStringLong(substr($recordData, $offset));
        $prompt = $string['value'] !== chr(0) ? $string['value'] : '';
        $offset += $string['size'];

        // offset: var; size: var; text of the error box
        $string = self::readUnicodeStringLong(substr($recordData, $offset));
        $error = $string['value'] !== chr(0) ? $string['value'] : '';
        $offset += $string['size'];

        // offset: var; size: 2; size of the formula data for the first condition
        $sz1 = self::getUInt2d($recordData, $offset);
        $offset += 2;

        // offset: var; size: 2; not used
        $offset += 2;

        // offset: var; size: $sz1; formula data for first condition (without size field)
        $formula1 = substr($recordData, $offset, $sz1);
        $formula1 = pack('v', $sz1) . $formula1; // prepend the length

        try {
            $formula1 = $xls->getFormulaFromStructure($formula1);

            // in list type validity, null characters are used as item separators
            if ($type == DataValidation::TYPE_LIST) {
                $formula1 = str_replace(chr(0), ',', $formula1);
            }
        } catch (PhpSpreadsheetException $e) {
            return;
        }
        $offset += $sz1;

        // offset: var; size: 2; size of the formula data for the first condition
        $sz2 = self::getUInt2d($recordData, $offset);
        $offset += 2;

        // offset: var; size: 2; not used
        $offset += 2;

        // offset: var; size: $sz2; formula data for second condition (without size field)
        $formula2 = substr($recordData, $offset, $sz2);
        $formula2 = pack('v', $sz2) . $formula2; // prepend the length

        try {
            $formula2 = $xls->getFormulaFromStructure($formula2);
        } catch (PhpSpreadsheetException) {
            return;
        }
        $offset += $sz2;

        // offset: var; size: var; cell range address list with
        $cellRangeAddressList = Biff8::readBIFF8CellRangeAddressList(substr($recordData, $offset));
        $cellRangeAddresses = $cellRangeAddressList['cellRangeAddresses'];

        foreach ($cellRangeAddresses as $cellRange) {
            $stRange = $xls->phpSheet->shrinkRangeToFit($cellRange);
            foreach (Coordinate::extractAllCellReferencesInRange($stRange) as $coordinate) {
                $objValidation = $xls->phpSheet->getCell($coordinate)->getDataValidation();
                $objValidation->setType($type);
                $objValidation->setErrorStyle($errorStyle);
                $objValidation->setAllowBlank((bool) $allowBlank);
                $objValidation->setShowInputMessage((bool) $showInputMessage);
                $objValidation->setShowErrorMessage((bool) $showErrorMessage);
                $objValidation->setShowDropDown(!$suppressDropDown);
                $objValidation->setOperator($operator);
                $objValidation->setErrorTitle($errorTitle);
                $objValidation->setError($error);
                $objValidation->setPromptTitle($promptTitle);
                $objValidation->setPrompt($prompt);
                $objValidation->setFormula1($formula1);
                $objValidation->setFormula2($formula2);
            }
        }
    }
}
