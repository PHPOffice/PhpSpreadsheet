<?php

namespace PhpOffice\PhpSpreadsheet\Helper;

class TextGrid
{
    /**
     * @var bool
     */
    private $isCli = true;

    /**
     * @var array
     */
    protected $matrix;

    /**
     * @var array
     */
    protected $rows;

    /**
     * @var array
     */
    protected $columns;

    /**
     * @var string
     */
    private $gridDisplay;

    public function __construct(array $matrix, bool $isCli = true)
    {
        $this->rows = array_keys($matrix);
        $this->columns = array_keys($matrix[$this->rows[0]]);

        $matrix = array_values($matrix);
        array_walk(
            $matrix,
            function (&$row): void {
                $row = array_values($row);
            }
        );

        $this->matrix = $matrix;
        $this->isCli = $isCli;
    }

    public function render(): string
    {
        $this->gridDisplay = $this->isCli ? '' : '<code>';

        $maxRow = max($this->rows);
        $maxRowLength = strlen((string) $maxRow) + 1;
        $columnWidths = $this->getColumnWidths();

        $this->renderColumnHeader($maxRowLength, $columnWidths);
        $this->renderRows($maxRowLength, $columnWidths);
        $this->renderFooter($maxRowLength, $columnWidths);

        $this->gridDisplay .= $this->isCli ? '' : '</code>';

        return $this->gridDisplay;
    }

    private function renderRows(int $maxRowLength, array $columnWidths): void
    {
        foreach ($this->matrix as $row => $rowData) {
            $this->gridDisplay .= '|' . str_pad((string) $this->rows[$row], $maxRowLength, ' ', STR_PAD_LEFT) . ' ';
            $this->renderCells($rowData, $columnWidths);
            $this->gridDisplay .= '|' . PHP_EOL;
        }
    }

    private function renderCells(array $rowData, array $columnWidths): void
    {
        foreach ($rowData as $column => $cell) {
            $cell = ($this->isCli) ? (string) $cell : htmlentities((string) $cell);
            $this->gridDisplay .= '| ';
            $this->gridDisplay .= str_pad($cell, $columnWidths[$column] + 1, ' ');
        }
    }

    private function renderColumnHeader(int $maxRowLength, array $columnWidths): void
    {
        $this->gridDisplay .= str_repeat(' ', $maxRowLength + 2);
        foreach ($this->columns as $column => $reference) {
            $this->gridDisplay .= '+-' . str_repeat('-', $columnWidths[$column] + 1);
        }
        $this->gridDisplay .= '+' . PHP_EOL;

        $this->gridDisplay .= str_repeat(' ', $maxRowLength + 2);
        foreach ($this->columns as $column => $reference) {
            $this->gridDisplay .= '| ' . str_pad((string) $reference, $columnWidths[$column] + 1, ' ');
        }
        $this->gridDisplay .= '|' . PHP_EOL;

        $this->renderFooter($maxRowLength, $columnWidths);
    }

    private function renderFooter(int $maxRowLength, array $columnWidths): void
    {
        $this->gridDisplay .= '+' . str_repeat('-', $maxRowLength + 1);
        foreach ($this->columns as $column => $reference) {
            $this->gridDisplay .= '+-';
            $this->gridDisplay .= str_pad((string) '', $columnWidths[$column] + 1, '-');
        }
        $this->gridDisplay .= '+' . PHP_EOL;
    }

    private function getColumnWidths(): array
    {
        $columnCount = count($this->matrix, COUNT_RECURSIVE) / count($this->matrix);
        $columnWidths = [];
        for ($column = 0; $column < $columnCount; ++$column) {
            $columnWidths[] = $this->getColumnWidth(array_column($this->matrix, $column));
        }

        return $columnWidths;
    }

    private function getColumnWidth(array $columnData): int
    {
        $columnWidth = 0;
        $columnData = array_values($columnData);

        foreach ($columnData as $columnValue) {
            if (is_string($columnValue)) {
                $columnWidth = max($columnWidth, strlen($columnValue));
            } elseif (is_bool($columnValue)) {
                $columnWidth = max($columnWidth, strlen($columnValue ? 'TRUE' : 'FALSE'));
            }

            $columnWidth = max($columnWidth, strlen((string) $columnWidth));
        }

        return $columnWidth;
    }
}
