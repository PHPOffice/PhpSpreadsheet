<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use Stringable;

class DefaultValueBinder implements IValueBinder
{
    /**
     * Bind value to a cell.
     *
     * @param Cell $cell Cell to bind value to
     * @param mixed $value Value to bind in cell
     */
    public function bindValue(Cell $cell, mixed $value): bool
    {
        // sanitize UTF-8 strings
        if (is_string($value)) {
            $value = StringHelper::sanitizeUTF8($value);
        } elseif ($value === null || is_scalar($value) || $value instanceof RichText) {
            // No need to do anything
        } elseif ($value instanceof DateTimeInterface) {
            $value = $value->format('Y-m-d H:i:s');
        } elseif ($value instanceof Stringable) {
            $value = (string) $value;
        } else {
            throw new SpreadsheetException('Unable to bind unstringable ' . gettype($value));
        }

        // Set value explicit
        $cell->setValueExplicit($value, static::dataTypeForValue($value));

        // Done!
        return true;
    }

    /**
     * DataType for value.
     */
    public static function dataTypeForValue(mixed $value): string
    {
        // Match the value against a few data types
        if ($value === null) {
            return DataType::TYPE_NULL;
        }
        if (is_float($value) || is_int($value)) {
            return DataType::TYPE_NUMERIC;
        }
        if (is_bool($value)) {
            return DataType::TYPE_BOOL;
        }
        if ($value === '') {
            return DataType::TYPE_STRING;
        }
        if ($value instanceof RichText) {
            return DataType::TYPE_INLINE;
        }
        if ($value instanceof Stringable) {
            $value = (string) $value;
        }
        if (!is_string($value)) {
            $gettype = is_object($value) ? get_class($value) : gettype($value);

            throw new SpreadsheetException("unusable type $gettype");
        }
        if (strlen($value) > 1 && $value[0] === '=') {
            return DataType::TYPE_FORMULA;
        }
        if (preg_match('/^[\+\-]?(\d+\\.?\d*|\d*\\.?\d+)([Ee][\-\+]?[0-2]?\d{1,3})?$/', $value)) {
            $tValue = ltrim($value, '+-');
            if (strlen($tValue) > 1 && $tValue[0] === '0' && $tValue[1] !== '.') {
                return DataType::TYPE_STRING;
            } elseif ((!str_contains($value, '.')) && ($value > PHP_INT_MAX)) {
                return DataType::TYPE_STRING;
            } elseif (!is_numeric($value)) {
                return DataType::TYPE_STRING;
            }

            return DataType::TYPE_NUMERIC;
        }
        $errorCodes = DataType::getErrorCodes();
        if (isset($errorCodes[$value])) {
            return DataType::TYPE_ERROR;
        }

        return DataType::TYPE_STRING;
    }
}
