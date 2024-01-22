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
        } elseif (is_float($value) || is_int($value)) {
            return DataType::TYPE_NUMERIC;
        } elseif (is_bool($value)) {
            return DataType::TYPE_BOOL;
        } elseif ($value === '') {
            return DataType::TYPE_STRING;
        } elseif ($value instanceof RichText) {
            return DataType::TYPE_INLINE;
        } elseif (is_string($value) && strlen($value) > 1 && $value[0] === '=') {
            return DataType::TYPE_FORMULA;
        } elseif (preg_match('/^[\+\-]?(\d+\\.?\d*|\d*\\.?\d+)([Ee][\-\+]?[0-2]?\d{1,3})?$/', $value)) {
            $tValue = ltrim($value, '+-');
            if (is_string($value) && strlen($tValue) > 1 && $tValue[0] === '0' && $tValue[1] !== '.') {
                return DataType::TYPE_STRING;
            } elseif ((!str_contains($value, '.')) && ($value > PHP_INT_MAX)) {
                return DataType::TYPE_STRING;
            } elseif (!is_numeric($value)) {
                return DataType::TYPE_STRING;
            }

            return DataType::TYPE_NUMERIC;
        } elseif (is_string($value)) {
            $errorCodes = DataType::getErrorCodes();
            if (isset($errorCodes[$value])) {
                return DataType::TYPE_ERROR;
            }
        }

        return DataType::TYPE_STRING;
    }
}
