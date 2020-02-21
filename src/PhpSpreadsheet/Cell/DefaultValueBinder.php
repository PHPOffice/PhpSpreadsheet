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
        }

        if (is_float($pValue) || is_int($pValue)) {
            return DataType::TYPE_NUMERIC;
        }

        if (is_bool($pValue)) {
            return DataType::TYPE_BOOL;
        }

        if ($pValue === '') {
            return DataType::TYPE_STRING;
        }

        if ($pValue instanceof RichText) {
            return DataType::TYPE_INLINE;
        }

        if ($pValue[0] === '=' && strlen($pValue) > 1) {
            return DataType::TYPE_FORMULA;
        }

        if ($pValue > PHP_INT_MAX) {
            if (strpos($pValue, '.') === false) {
                return DataType::TYPE_STRING;
            }
        } elseif (preg_match('/\.\d*0$/', $pValue)) {
            return DataType::TYPE_STRING;
        }

        if (preg_match('/^[-+]?(?!0\d)(\d+\.?\d*|\d*\.\d+)([Ee][-+]?[0-2]?\d{1,3})?$/', $pValue)) {
            return DataType::TYPE_NUMERIC;
        }

        if (DataType::isErrorCode($pValue)) {
            return DataType::TYPE_ERROR;
        }

        return DataType::TYPE_STRING;
    }
}
