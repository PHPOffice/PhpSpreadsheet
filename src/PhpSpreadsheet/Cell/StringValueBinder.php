<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use Stringable;

class StringValueBinder extends DefaultValueBinder implements IValueBinder
{
    protected bool $convertNull = true;

    protected bool $convertBoolean = true;

    protected bool $convertNumeric = true;

    protected bool $convertFormula = true;

    protected bool $setIgnoredErrors = false;

    public function setSetIgnoredErrors(bool $setIgnoredErrors = false): self
    {
        $this->setIgnoredErrors = $setIgnoredErrors;

        return $this;
    }

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
    public function bindValue(Cell $cell, mixed $value): bool
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

        $ignoredErrors = false;
        if ($value === null && $this->convertNull === false) {
            $cell->setValueExplicit($value, DataType::TYPE_NULL);
        } elseif (is_bool($value) && $this->convertBoolean === false) {
            $cell->setValueExplicit($value, DataType::TYPE_BOOL);
        } elseif ((is_int($value) || is_float($value)) && $this->convertNumeric === false) {
            $cell->setValueExplicit($value, DataType::TYPE_NUMERIC);
        } elseif (is_string($value) && strlen($value) > 1 && $value[0] === '=' && $this->convertFormula === false && parent::dataTypeForValue($value) === DataType::TYPE_FORMULA) {
            $cell->setValueExplicit($value, DataType::TYPE_FORMULA);
        } else {
            $ignoredErrors = is_numeric($value);
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
        }
        if ($this->setIgnoredErrors) {
            $cell->getIgnoredErrors()->setNumberStoredAsText($ignoredErrors);
        }

        return true;
    }

    protected function bindObjectValue(Cell $cell, object $value): bool
    {
        // Handle any objects that might be injected
        $ignoredErrors = false;
        if ($value instanceof DateTimeInterface) {
            $value = $value->format('Y-m-d H:i:s');
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
        } elseif ($value instanceof RichText) {
            $cell->setValueExplicit($value, DataType::TYPE_INLINE);
            $ignoredErrors = is_numeric($value->getPlainText());
        } elseif ($value instanceof Stringable) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            $ignoredErrors = is_numeric((string) $value);
        } else {
            throw new SpreadsheetException('Unable to bind unstringable object of type ' . get_class($value));
        }
        if ($this->setIgnoredErrors) {
            $cell->getIgnoredErrors()->setNumberStoredAsText($ignoredErrors);
        }

        return true;
    }
}
