<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Exception;

/**
 * Validate a cell value according to its validation rules.
 */
class DataValidator
{
    /**
     * Does this cell contain valid value?
     *
     * @param Cell $cell Cell to check the value
     */
    public function isValid(Cell $cell): bool
    {
        if (!$cell->hasDataValidation() || $cell->getDataValidation()->getType() === DataValidation::TYPE_NONE) {
            return true;
        }

        $cellValue = $cell->getValue();
        $dataValidation = $cell->getDataValidation();

        if (!$dataValidation->getAllowBlank() && ($cellValue === null || $cellValue === '')) {
            return false;
        }

        $returnValue = false;
        $type = $dataValidation->getType();
        if ($type === DataValidation::TYPE_LIST) {
            $returnValue = $this->isValueInList($cell);
        } elseif ($type === DataValidation::TYPE_WHOLE) {
            if (!is_numeric($cellValue) || fmod((float) $cellValue, 1) != 0) {
                $returnValue = false;
            } else {
                $returnValue = $this->numericOperator($dataValidation, (int) $cellValue);
            }
        } elseif ($type === DataValidation::TYPE_DECIMAL || $type === DataValidation::TYPE_DATE || $type === DataValidation::TYPE_TIME) {
            if (!is_numeric($cellValue)) {
                $returnValue = false;
            } else {
                $returnValue = $this->numericOperator($dataValidation, (float) $cellValue);
            }
        } elseif ($type === DataValidation::TYPE_TEXTLENGTH) {
            $returnValue = $this->numericOperator($dataValidation, mb_strlen((string) $cellValue));
        }

        return $returnValue;
    }

    private function numericOperator(DataValidation $dataValidation, int|float $cellValue): bool
    {
        $operator = $dataValidation->getOperator();
        $formula1 = $dataValidation->getFormula1();
        $formula2 = $dataValidation->getFormula2();
        $returnValue = false;
        if ($operator === DataValidation::OPERATOR_BETWEEN) {
            $returnValue = $cellValue >= $formula1 && $cellValue <= $formula2;
        } elseif ($operator === DataValidation::OPERATOR_NOTBETWEEN) {
            $returnValue = $cellValue < $formula1 || $cellValue > $formula2;
        } elseif ($operator === DataValidation::OPERATOR_EQUAL) {
            $returnValue = $cellValue == $formula1;
        } elseif ($operator === DataValidation::OPERATOR_NOTEQUAL) {
            $returnValue = $cellValue != $formula1;
        } elseif ($operator === DataValidation::OPERATOR_LESSTHAN) {
            $returnValue = $cellValue < $formula1;
        } elseif ($operator === DataValidation::OPERATOR_LESSTHANOREQUAL) {
            $returnValue = $cellValue <= $formula1;
        } elseif ($operator === DataValidation::OPERATOR_GREATERTHAN) {
            $returnValue = $cellValue > $formula1;
        } elseif ($operator === DataValidation::OPERATOR_GREATERTHANOREQUAL) {
            $returnValue = $cellValue >= $formula1;
        }

        return $returnValue;
    }

    /**
     * Does this cell contain valid value, based on list?
     *
     * @param Cell $cell Cell to check the value
     */
    private function isValueInList(Cell $cell): bool
    {
        $cellValue = $cell->getValue();
        $dataValidation = $cell->getDataValidation();

        $formula1 = $dataValidation->getFormula1();
        if (!empty($formula1)) {
            // inline values list
            if ($formula1[0] === '"') {
                return in_array(strtolower($cellValue), explode(',', strtolower(trim($formula1, '"'))), true);
            } elseif (strpos($formula1, ':') > 0) {
                // values list cells
                $matchFormula = '=MATCH(' . $cell->getCoordinate() . ', ' . $formula1 . ', 0)';
                $calculation = Calculation::getInstance($cell->getWorksheet()->getParent());

                try {
                    $result = $calculation->calculateFormula($matchFormula, $cell->getCoordinate(), $cell);
                    while (is_array($result)) {
                        $result = array_pop($result);
                    }

                    return $result !== ExcelError::NA();
                } catch (Exception) {
                    return false;
                }
            }
        }

        return true;
    }
}
