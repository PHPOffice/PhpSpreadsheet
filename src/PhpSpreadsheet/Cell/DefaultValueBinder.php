<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class DefaultValueBinder implements IValueBinder
{
    /**
     * Bind value to a cell.
     *
     * @param Cell $cell Cell to bind value to
     * @param mixed $value Value to bind in cell
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     *
     * @return bool
     */
    public function bindValue(Cell $cell, $value)
    {
        // sanitize UTF-8 strings
        if (is_string($value)) {
            $value = StringHelper::sanitizeUTF8($value);
        } elseif (is_object($value)) {
            // Handle any objects that might be injected
            if ($value instanceof DateTimeInterface) {
                $value = $value->format('Y-m-d H:i:s');
            } elseif (!($value instanceof RichText)) {
                $value = (string) $value;
            }
        }

        // Set value explicit
        $cell->setValueExplicit($value, static::dataTypeForValue($value));

        // Done!
        return true;
    }

    /**
     * DataType for value.
     *
     * @param mixed $pValue
     *
     * @return string
     */
    public static function dataTypeForValue($pValue)
    {
        // Match the value against a few data types
        if ($pValue === null) {
            return DataType::TYPE_NULL;
        } elseif ($pValue === '') {
            return DataType::TYPE_STRING;
        } elseif ($pValue instanceof RichText) {
            return DataType::TYPE_INLINE;
        } elseif ($pValue[0] === '=' && strlen($pValue) > 1) {
            return DataType::TYPE_FORMULA;
        } elseif (is_bool($pValue)) {
            return DataType::TYPE_BOOL;
        } elseif (is_float($pValue) || is_int($pValue)) {
            return DataType::TYPE_NUMERIC;
        } elseif (preg_match('/^[\+\-]?(\d+\\.?\d*|\d*\\.?\d+)([Ee][\-\+]?[0-2]?\d{1,3})?$/', $pValue)) {
            $tValue = ltrim($pValue, '+-');
            if (is_string($pValue) && $tValue[0] === '0' && strlen($tValue) > 1 && $tValue[1] !== '.') {
                return DataType::TYPE_STRING;
            } elseif ((strpos($pValue, '.') === false) && ($pValue > PHP_INT_MAX)) {
                return DataType::TYPE_STRING;
            }

            return DataType::TYPE_NUMERIC;
        } elseif (is_string($pValue)) {
            $errorCodes = DataType::getErrorCodes();
            if (isset($errorCodes[$pValue])) {
                return DataType::TYPE_ERROR;
            }
        }

        return DataType::TYPE_STRING;
    }
}
