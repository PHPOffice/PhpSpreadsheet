<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class Filter implements IReadFilter {
    private $start_row;
    private $end_row;
    private $columns;
    private $worksheet_name;

    public function reset() : self {
        $this->start_row = NULL;
        $this->end_row = NULL;
        $this->columns = NULL;
        $this->worksheet_name = NULL;
        return $this;
    }

    public function setStartRow(int $start_row) : self {
        $this->start_row = $start_row;
        return $this;
    }

    public function setEndRow(int $end_row) : self {
        $this->end_row = $end_row;
        return $this;
    }

    public function setColumns(array $columns) : self {
        $this->columns = $columns;
        return $this;
    }

    public function setWorksheetName(string $worksheet_name) : self {
        $this->worksheet_name = $worksheet_name;
        return $this;
    }

    public function readCell($column, $row, $worksheetName = '') {
        $start_row = $this->start_row ? $row >= $this->start_row : TRUE;
        $end_row = $this->end_row ? $row <= $this->end_row : TRUE;
        $columns = $this->columns ? in_array($column, $this->columns) : TRUE;
        $worksheet_name = $this->worksheet_name ? $this->worksheet_name ===  $worksheetName : TRUE;

        return $start_row && $end_row && $columns && $worksheet_name;
    }
}
