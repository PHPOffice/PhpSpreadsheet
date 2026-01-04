<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use Composer\Pcre\Preg;
use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing;
use Stringable;

class DefaultValueBinder implements IValueBinder
{
    //                            123 456 789 012 345
    private const FIFTEEN_NINES = 999_999_999_999_999;

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
        } elseif ($value instanceof BaseDrawing) {
            $value->setCoordinates($cell->getCoordinate());
            $value->setResizeProportional(false);
            $value->setInCell(true);
            $value->setWorksheet($cell->getWorksheet(), true);
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
        if (is_int($value) && abs($value) > self::FIFTEEN_NINES) {
            return DataType::TYPE_STRING;
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
        if ($value instanceof BaseDrawing) {
            return DataType::TYPE_DRAWING_IN_CELL;
        }
        if ($value instanceof Stringable) {
            $value = (string) $value;
        }
        if (!is_string($value)) {
            $gettype = is_object($value) ? get_class($value) : gettype($value);

            throw new SpreadsheetException("unusable type $gettype");
        }
        if (strlen($value) > 1 && $value[0] === '=') {
            $calculation = new Calculation();
            $calculation->disableBranchPruning();

            try {
                if (empty($calculation->parseFormula($value))) {
                    return DataType::TYPE_STRING;
                }
            } catch (CalculationException $e) {
                $message = $e->getMessage();
                if (
                    $message === 'Formula Error: An unexpected error occurred'
                    || str_contains($message, 'has no operands')
                ) {
                    return DataType::TYPE_STRING;
                }
            }

            return DataType::TYPE_FORMULA;
        }
        if (Preg::isMatch('/^[\+\-]?(\d+\.?\d*|\d*\.?\d+)([Ee][\-\+]?[0-2]?\d{1,3})?$/', $value)) {
            $tValue = ltrim($value, '+-');
            if (strlen($tValue) > 1 && $tValue[0] === '0' && $tValue[1] !== '.') {
                return DataType::TYPE_STRING;
            }
            if (!Preg::isMatch('/[eE.]/', $value)) {
                $aValue = abs((float) $value);
                if ($aValue > self::FIFTEEN_NINES) {
                    return DataType::TYPE_STRING;
                }
            }
            if (!is_numeric($value) || !is_finite((float) $value)) {
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

    protected bool $preserveCr = false;

    public function getPreserveCr(): bool
    {
        return $this->preserveCr;
    }

    public function setPreserveCr(bool $preserveCr): self
    {
        $this->preserveCr = $preserveCr;

        return $this;
    }
}
