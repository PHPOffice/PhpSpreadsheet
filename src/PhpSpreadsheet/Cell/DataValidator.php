<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use PhpOffice\PhpSpreadsheet\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class DataValidator
{
    /**
     * Does this cell contain valid value?
     *
     * @param Cell $cell Cell to check the value
     *
     * @return bool
     */
    public function isValidCellValue(&$cell)
    {
        $cellValue = $cell->getValue();
        $dataValidation = $cell->getDataValidation();

        if (!$dataValidation->getAllowBlank() && ($cellValue === null || $cellValue == '')) {
            return false;
        }

        // TODO: write check on all cases
        switch ($dataValidation->getType()) {
            case DataValidation::TYPE_LIST:
                $formula1 = $dataValidation->getFormula1();
                if (!empty($formula1)) {
                    if ($formula1[0] == '"') {                         // inline values list
                        return in_array(strtolower($cellValue), explode(',', strtolower(trim($formula1, '"'))), true);
                    } elseif (strpos($formula1, ':') > 0) {            // values list cells
                        $match_formula = '=MATCH(' . $cell->getCoordinate() . ',' . $formula1 . ',0)';

                        try {
                            $result = Calculation::getInstance(
                                $cell->getWorksheet()->getParent()
                            )->calculateFormula($match_formula, $cell->getCoordinate(), $cell);

                            return $result !== Functions::NA();
                        } catch (Exception $ex) {
                            return false;
                        }
                    }
                }

                break;
        }

        return true;
    }
}
