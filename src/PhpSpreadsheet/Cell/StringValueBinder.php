<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use Stringable;

class StringValueBinder implements IValueBinder
{
    /**
     * @var bool
     */
    protected $convertNull = true;

    /**
     * @var bool
     */
    protected $convertBoolean = true;

    /**
     * @var bool
     */
    protected $convertNumeric = true;

    /**
     * @var bool
     */
    protected $convertFormula = true;

    public function setNullConversion(bool $suppressConversion = false): self
    {
        $this->convertNull = $suppressConversion;

        return $this;
    }

    public function setBooleanConversion(bool $suppressConversion = false): self
    {
        $this->convertBoolean = $suppressConversion;

        return $this;
    }

    public function getBooleanConversion(): bool
    {
        return $this->convertBoolean;
    }

    public function setNumericConversion(bool $suppressConversion = false): self
    {
        $this->convertNumeric = $suppressConversion;

        return $this;
    }

    public function setFormulaConversion(bool $suppressConversion = false): self
    {
        $this->convertFormula = $suppressConversion;

        return $this;
    }

    public function setConversionForAllValueTypes(bool $suppressConversion = false): self
    {
        $this->convertNull = $suppressConversion;
        $this->convertBoolean = $suppressConversion;
        $this->convertNumeric = $suppressConversion;
        $this->convertFormula = $suppressConversion;

        return $this;
    }

    /**
     * Bind value to a cell.
     *
     * @param Cell $cell Cell to bind value to
     * @param mixed $value Value to bind in cell
     */
    public function bindValue(Cell $cell, $value): bool
    {
        if (is_object($value)) {
            return $this->bindObjectValue($cell, $value);
        }
        if ($value !== null && !is_scalar($value)) {
            throw new SpreadsheetException('Unable to bind unstringable ' . gettype($value));
        }

        // sanitize UTF-8 strings
        if (is_string($value)) {
            $value = StringHelper::sanitizeUTF8($value);
        }

        if ($value === null && $this->convertNull === false) {
            $cell->setValueExplicit($value, DataType::TYPE_NULL);
        } elseif (is_bool($value) && $this->convertBoolean === false) {
            $cell->setValueExplicit($value, DataType::TYPE_BOOL);
        } elseif ((is_int($value) || is_float($value)) && $this->convertNumeric === false) {
            $cell->setValueExplicit($value, DataType::TYPE_NUMERIC);
        } elseif (is_string($value) && strlen($value) > 1 && $value[0] === '=' && $this->convertFormula === false) {
            $cell->setValueExplicit($value, DataType::TYPE_FORMULA);
        } else {
            if (is_string($value) && strlen($value) > 1 && $value[0] === '=') {
                $cell->getStyle()->setQuotePrefix(true);
            }
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
        }

        return true;
    }

    protected function bindObjectValue(Cell $cell, object $value): bool
    {
        // Handle any objects that might be injected
        if ($value instanceof DateTimeInterface) {
            $value = $value->format('Y-m-d H:i:s');
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
        } elseif ($value instanceof RichText) {
            $cell->setValueExplicit($value, DataType::TYPE_INLINE);
        } elseif ($value instanceof Stringable) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
        } else {
            throw new SpreadsheetException('Unable to bind unstringable object of type ' . get_class($value));
        }

        return true;
    }
}
