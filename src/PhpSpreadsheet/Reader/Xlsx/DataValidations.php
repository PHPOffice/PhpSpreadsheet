<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataValidations
{
    private $worksheet;

    private $worksheetXml;

    public function __construct(Worksheet $workSheet, \SimpleXMLElement $worksheetXml)
    {
        $this->worksheet = $workSheet;
        $this->worksheetXml = $worksheetXml;
    }

    public function load()
    {
        foreach ($this->worksheetXml->dataValidations->dataValidation as $dataValidation) {
            // Uppercase coordinate
            $range = strtoupper($dataValidation['sqref']);
            $rangeSet = explode(' ', $range);
            foreach ($rangeSet as $range) {
                // Extract all cell references in $range
                foreach (Coordinate::extractAllCellReferencesInRange($range) as $reference) {
                    // Create validation
                    $docValidation = $this->worksheet->getCell($reference)->getDataValidation();
                    $docValidation->setType((string) $dataValidation['type']);
                    $docValidation->setErrorStyle((string) $dataValidation['errorStyle']);
                    $docValidation->setOperator((string) $dataValidation['operator']);
                    $docValidation->setAllowBlank($this->isTrue($dataValidation['allowBlank']));
                    $docValidation->setShowDropDown(!$this->isTrue($dataValidation['showDropDown']));
                    $docValidation->setShowInputMessage($this->isTrue($dataValidation['showInputMessage']));
                    $docValidation->setShowErrorMessage($this->isTrue($dataValidation['showErrorMessage']));
                    $docValidation->setErrorTitle((string) $dataValidation['errorTitle']);
                    $docValidation->setError((string) $dataValidation['error']);
                    $docValidation->setPromptTitle((string) $dataValidation['promptTitle']);
                    $docValidation->setPrompt((string) $dataValidation['prompt']);
                    $docValidation->setFormula1((string) $dataValidation->formula1);
                    $docValidation->setFormula2((string) $dataValidation->formula2);
                }
            }
        }
    }

    private function isTrue(?string $value): bool
    {
        if ($value === null) {
            return false;
        }

        $value = trim($value);

        if ($value === 'false' || $value === '0' || $value === '') {
            return false;
        }

        return true;
    }
}
