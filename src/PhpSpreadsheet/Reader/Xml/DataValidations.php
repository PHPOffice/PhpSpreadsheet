<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Cell\AddressHelper;
use PhpOffice\PhpSpreadsheet\Cell\AddressRange;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Namespaces;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use SimpleXMLElement;

class DataValidations
{
    private const OPERATOR_MAPPINGS = [
        'between' => DataValidation::OPERATOR_BETWEEN,
        'equal' => DataValidation::OPERATOR_EQUAL,
        'greater' => DataValidation::OPERATOR_GREATERTHAN,
        'greaterorequal' => DataValidation::OPERATOR_GREATERTHANOREQUAL,
        'less' => DataValidation::OPERATOR_LESSTHAN,
        'lessorequal' => DataValidation::OPERATOR_LESSTHANOREQUAL,
        'notbetween' => DataValidation::OPERATOR_NOTBETWEEN,
        'notequal' => DataValidation::OPERATOR_NOTEQUAL,
    ];

    private const TYPE_MAPPINGS = [
        'textlength' => DataValidation::TYPE_TEXTLENGTH,
    ];

    private int $thisRow = 0;

    private int $thisColumn = 0;

    private function replaceR1C1(array $matches): string
    {
        return AddressHelper::convertToA1($matches[0], $this->thisRow, $this->thisColumn, false);
    }

    public function loadDataValidations(SimpleXMLElement $worksheet, Spreadsheet $spreadsheet): void
    {
        $xmlX = $worksheet->children(Namespaces::URN_EXCEL);
        $sheet = $spreadsheet->getActiveSheet();
        /** @var callable $pregCallback */
        $pregCallback = [$this, 'replaceR1C1'];
        foreach ($xmlX->DataValidation as $dataValidation) {
            $cells = [];
            $validation = new DataValidation();

            // set defaults
            $validation->setShowDropDown(true);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $this->thisRow = 1;
            $this->thisColumn = 1;

            foreach ($dataValidation as $tagName => $tagValue) {
                $tagValue = (string) $tagValue;
                $tagValueLower = strtolower($tagValue);
                switch ($tagName) {
                    case 'Range':
                        foreach (explode(',', $tagValue) as $range) {
                            $cell = '';
                            if (preg_match('/^R(\d+)C(\d+):R(\d+)C(\d+)$/', (string) $range, $selectionMatches) === 1) {
                                // range
                                $firstCell = Coordinate::stringFromColumnIndex((int) $selectionMatches[2])
                                    . $selectionMatches[1];
                                $cell = $firstCell
                                    . ':'
                                    . Coordinate::stringFromColumnIndex((int) $selectionMatches[4])
                                    . $selectionMatches[3];
                                $this->thisRow = (int) $selectionMatches[1];
                                $this->thisColumn = (int) $selectionMatches[2];
                                $sheet->getCell($firstCell);
                            } elseif (preg_match('/^R(\d+)C(\d+)$/', (string) $range, $selectionMatches) === 1) {
                                // cell
                                $cell = Coordinate::stringFromColumnIndex((int) $selectionMatches[2])
                                    . $selectionMatches[1];
                                $sheet->getCell($cell);
                                $this->thisRow = (int) $selectionMatches[1];
                                $this->thisColumn = (int) $selectionMatches[2];
                            } elseif (preg_match('/^C(\d+)$/', (string) $range, $selectionMatches) === 1) {
                                // column
                                $firstCell = Coordinate::stringFromColumnIndex((int) $selectionMatches[1])
                                    . '1';
                                $cell = $firstCell
                                    . ':'
                                    . Coordinate::stringFromColumnIndex((int) $selectionMatches[1])
                                    . ((string) AddressRange::MAX_ROW);
                                $this->thisColumn = (int) $selectionMatches[1];
                                $sheet->getCell($firstCell);
                            } elseif (preg_match('/^R(\d+)$/', (string) $range, $selectionMatches)) {
                                // row
                                $firstCell = 'A'
                                    . $selectionMatches[1];
                                $cell = $firstCell
                                    . ':'
                                    . AddressRange::MAX_COLUMN
                                    . $selectionMatches[1];
                                $this->thisRow = (int) $selectionMatches[1];
                                $sheet->getCell($firstCell);
                            }

                            $validation->setSqref($cell);
                            $stRange = $sheet->shrinkRangeToFit($cell);
                            $cells = array_merge($cells, Coordinate::extractAllCellReferencesInRange($stRange));
                        }

                        break;
                    case 'Type':
                        $validation->setType(self::TYPE_MAPPINGS[$tagValueLower] ?? $tagValueLower);

                        break;
                    case 'Qualifier':
                        $validation->setOperator(self::OPERATOR_MAPPINGS[$tagValueLower] ?? $tagValueLower);

                        break;
                    case 'InputTitle':
                        $validation->setPromptTitle($tagValue);

                        break;
                    case 'InputMessage':
                        $validation->setPrompt($tagValue);

                        break;
                    case 'InputHide':
                        $validation->setShowInputMessage(false);

                        break;
                    case 'ErrorStyle':
                        $validation->setErrorStyle($tagValueLower);

                        break;
                    case 'ErrorTitle':
                        $validation->setErrorTitle($tagValue);

                        break;
                    case 'ErrorMessage':
                        $validation->setError($tagValue);

                        break;
                    case 'ErrorHide':
                        $validation->setShowErrorMessage(false);

                        break;
                    case 'ComboHide':
                        $validation->setShowDropDown(false);

                        break;
                    case 'UseBlank':
                        $validation->setAllowBlank(true);

                        break;
                    case 'CellRangeList':
                        // FIXME missing FIXME

                        break;
                    case 'Min':
                    case 'Value':
                        $tagValue = (string) preg_replace_callback(AddressHelper::R1C1_COORDINATE_REGEX, $pregCallback, $tagValue);
                        $validation->setFormula1($tagValue);

                        break;
                    case 'Max':
                        $tagValue = (string) preg_replace_callback(AddressHelper::R1C1_COORDINATE_REGEX, $pregCallback, $tagValue);
                        $validation->setFormula2($tagValue);

                        break;
                }
            }

            foreach ($cells as $cell) {
                $sheet->getCell($cell)->setDataValidation(clone $validation);
            }
        }
    }
}
