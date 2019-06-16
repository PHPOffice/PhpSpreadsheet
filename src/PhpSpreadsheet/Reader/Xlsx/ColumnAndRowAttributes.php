<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ColumnAndRowAttributes
{
    private $worksheetXml;

    private $worksheet;

    public function __construct(\SimpleXMLElement $worksheetXml, Worksheet $workSheet)
    {
        $this->worksheetXml = $worksheetXml;
        $this->worksheet = $workSheet;
    }

    private static function boolean($value)
    {
        if (is_object($value)) {
            $value = (string) $value;
        }
        if (is_numeric($value)) {
            return (bool) $value;
        }

        return $value === strtolower('true');
    }

    /**
     * Set Worksheet column attributes by attributes array passed.
     *
     * @param string $columnAddress A, B, ... DX, ...
     * @param array $columnAttributes array of attributes (indexes are attribute name, values are value)
     *                               'xfIndex', 'visible', 'collapsed', 'outlineLevel', 'width', ... ?
     */
    private function setColumnAttributes($columnAddress, array $columnAttributes)
    {
        if (isset($columnAttributes['xfIndex'])) {
            $this->worksheet->getColumnDimension($columnAddress)->setXfIndex($columnAttributes['xfIndex']);
        }
        if (isset($columnAttributes['visible'])) {
            $this->worksheet->getColumnDimension($columnAddress)->setVisible($columnAttributes['visible']);
        }
        if (isset($columnAttributes['collapsed'])) {
            $this->worksheet->getColumnDimension($columnAddress)->setCollapsed($columnAttributes['collapsed']);
        }
        if (isset($columnAttributes['outlineLevel'])) {
            $this->worksheet->getColumnDimension($columnAddress)->setOutlineLevel($columnAttributes['outlineLevel']);
        }
        if (isset($columnAttributes['width'])) {
            $this->worksheet->getColumnDimension($columnAddress)->setWidth($columnAttributes['width']);
        }
    }

    /**
     * Set Worksheet row attributes by attributes array passed.
     *
     * @param int $rowNumber 1, 2, 3, ... 99, ...
     * @param array $rowAttributes array of attributes (indexes are attribute name, values are value)
     *                               'xfIndex', 'visible', 'collapsed', 'outlineLevel', 'rowHeight', ... ?
     */
    private function setRowAttributes($rowNumber, array $rowAttributes)
    {
        if (isset($rowAttributes['xfIndex'])) {
            $this->worksheet->getRowDimension($rowNumber)->setXfIndex($rowAttributes['xfIndex']);
        }
        if (isset($rowAttributes['visible'])) {
            $this->worksheet->getRowDimension($rowNumber)->setVisible($rowAttributes['visible']);
        }
        if (isset($rowAttributes['collapsed'])) {
            $this->worksheet->getRowDimension($rowNumber)->setCollapsed($rowAttributes['collapsed']);
        }
        if (isset($rowAttributes['outlineLevel'])) {
            $this->worksheet->getRowDimension($rowNumber)->setOutlineLevel($rowAttributes['outlineLevel']);
        }
        if (isset($rowAttributes['rowHeight'])) {
            $this->worksheet->getRowDimension($rowNumber)->setRowHeight($rowAttributes['rowHeight']);
        }
    }

    /**
     * @param boolean $readDataOnly
     */
    public function load(IReadFilter $readFilter = null, $readDataOnly = false)
    {
        $columnsAttributes = [];
        $rowsAttributes = [];
        if (isset($this->worksheetXml->cols) && !$readDataOnly) {
            foreach ($this->worksheetXml->cols->col as $column) {
                for ($i = (int) ($column['min']); $i <= (int) ($column['max']); ++$i) {
                    $columnAddress = Coordinate::stringFromColumnIndex($i);
                    if ($column['style'] && !$readDataOnly) {
                        $columnsAttributes[$columnAddress]['xfIndex'] = (int) $column['style'];
                    }
                    if (self::boolean($column['hidden'])) {
                        $columnsAttributes[$columnAddress]['visible'] = false;
                    }
                    if (self::boolean($column['collapsed'])) {
                        $columnsAttributes[$columnAddress]['collapsed'] = true;
                    }
                    if ($column['outlineLevel'] > 0) {
                        $columnsAttributes[$columnAddress]['outlineLevel'] = (int) $column['outlineLevel'];
                    }
                    $columnsAttributes[$columnAddress]['width'] = (float) $column['width'];

                    if ((int) ($column['max']) == 16384) {
                        break;
                    }
                }
            }
        }

        if ($this->worksheetXml && $this->worksheetXml->sheetData && $this->worksheetXml->sheetData->row) {
            foreach ($this->worksheetXml->sheetData->row as $row) {
                if ($row['ht'] && !$readDataOnly) {
                    $rowsAttributes[(int) $row['r']]['rowHeight'] = (float) $row['ht'];
                }
                if (self::boolean($row['hidden']) && !$readDataOnly) {
                    $rowsAttributes[(int) $row['r']]['visible'] = false;
                }
                if (self::boolean($row['collapsed'])) {
                    $rowsAttributes[(int) $row['r']]['collapsed'] = true;
                }
                if ($row['outlineLevel'] > 0) {
                    $rowsAttributes[(int) $row['r']]['outlineLevel'] = (int) $row['outlineLevel'];
                }
                if ($row['s'] && !$readDataOnly) {
                    $rowsAttributes[(int) $row['r']]['xfIndex'] = (int) $row['s'];
                }
            }
        }

        // set columns/rows attributes
        $columnsAttributesSet = [];
        $rowsAttributesSet = [];
        foreach ($columnsAttributes as $columnCoordinate => $columnAttributes) {
            if ($readFilter !== null) {
                foreach ($rowsAttributes as $rowCoordinate => $rowAttributes) {
                    if (!$readFilter->readCell($columnCoordinate, $rowCoordinate, $this->worksheet->getTitle())) {
                        continue 2;
                    }
                }
            }

            if (!isset($columnsAttributesSet[$columnCoordinate])) {
                $this->setColumnAttributes($columnCoordinate, $columnAttributes);
                $columnsAttributesSet[$columnCoordinate] = true;
            }
        }

        foreach ($rowsAttributes as $rowCoordinate => $rowAttributes) {
            if ($readFilter !== null) {
                foreach ($columnsAttributes as $columnCoordinate => $columnAttributes) {
                    if (!$readFilter->readCell($columnCoordinate, $rowCoordinate, $this->worksheet->getTitle())) {
                        continue 2;
                    }
                }
            }

            if (!isset($rowsAttributesSet[$rowCoordinate])) {
                $this->setRowAttributes($rowCoordinate, $rowAttributes);
                $rowsAttributesSet[$rowCoordinate] = true;
            }
        }
    }
}
