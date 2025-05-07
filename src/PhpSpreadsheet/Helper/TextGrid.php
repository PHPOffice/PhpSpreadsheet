<?php

namespace PhpOffice\PhpSpreadsheet\Helper;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class TextGrid
{
    private bool $isCli;

    /** @var mixed[][] */
    protected array $matrix;

    /** @var int[] */
    protected array $rows;

    /** @var string[] */
    protected array $columns;

    private string $gridDisplay;

    private bool $rowDividers = false;

    private bool $rowHeaders = true;

    private bool $columnHeaders = true;

    /** @param mixed[][] $matrix */
    public function __construct(array $matrix, bool $isCli = true, bool $rowDividers = false, bool $rowHeaders = true, bool $columnHeaders = true)
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
        $this->rowDividers = $rowDividers;
        $this->rowHeaders = $rowHeaders;
        $this->columnHeaders = $columnHeaders;
    }

    public function render(): string
    {
        $this->gridDisplay = $this->isCli ? '' : ('<pre>' . PHP_EOL);

        if (!empty($this->rows)) {
            $maxRow = max($this->rows);
            $maxRowLength = strlen((string) $maxRow) + 1;
            $columnWidths = $this->getColumnWidths();

            $this->renderColumnHeader($maxRowLength, $columnWidths);
            $this->renderRows($maxRowLength, $columnWidths);
            if (!$this->rowDividers) {
                $this->renderFooter($maxRowLength, $columnWidths);
            }
        }

        $this->gridDisplay .= $this->isCli ? '' : '</pre>';

        return $this->gridDisplay;
    }

    /** @param int[] $columnWidths */
    private function renderRows(int $maxRowLength, array $columnWidths): void
    {
        foreach ($this->matrix as $row => $rowData) {
            if ($this->rowHeaders) {
                $this->gridDisplay .= '|' . str_pad((string) $this->rows[$row], $maxRowLength, ' ', STR_PAD_LEFT) . ' ';
            }
            $this->renderCells($rowData, $columnWidths);
            $this->gridDisplay .= '|' . PHP_EOL;
            if ($this->rowDividers) {
                $this->renderFooter($maxRowLength, $columnWidths);
            }
        }
    }

    /**
     * @param mixed[] $rowData
     * @param int[] $columnWidths
     */
    private function renderCells(array $rowData, array $columnWidths): void
    {
        foreach ($rowData as $column => $cell) {
            $valueForLength = $this->getString($cell);
            $displayCell = $this->isCli ? $valueForLength : htmlentities($valueForLength);
            $this->gridDisplay .= '| ';
            $this->gridDisplay .= $displayCell . str_repeat(' ', $columnWidths[$column] - $this->strlen($valueForLength) + 1);
        }
    }

    /** @param int[] $columnWidths */
    private function renderColumnHeader(int $maxRowLength, array &$columnWidths): void
    {
        if (!$this->columnHeaders) {
            $this->renderFooter($maxRowLength, $columnWidths);

            return;
        }
        foreach ($this->columns as $column => $reference) {
            /** @var string $reference */
            $columnWidths[$column] = max($columnWidths[$column], $this->strlen($reference));
        }
        if ($this->rowHeaders) {
            $this->gridDisplay .= str_repeat(' ', $maxRowLength + 2);
        }
        foreach ($this->columns as $column => $reference) {
            $this->gridDisplay .= '+-' . str_repeat('-', $columnWidths[$column] + 1);
        }
        $this->gridDisplay .= '+' . PHP_EOL;

        if ($this->rowHeaders) {
            $this->gridDisplay .= str_repeat(' ', $maxRowLength + 2);
        }
        foreach ($this->columns as $column => $reference) {
            /** @var scalar $reference */
            $this->gridDisplay .= '| ' . str_pad((string) $reference, $columnWidths[$column] + 1, ' ');
        }
        $this->gridDisplay .= '|' . PHP_EOL;

        $this->renderFooter($maxRowLength, $columnWidths);
    }

    /** @param int[] $columnWidths */
    private function renderFooter(int $maxRowLength, array $columnWidths): void
    {
        if ($this->rowHeaders) {
            $this->gridDisplay .= '+' . str_repeat('-', $maxRowLength + 1);
        }
        foreach ($this->columns as $column => $reference) {
            $this->gridDisplay .= '+-';
            $this->gridDisplay .= str_pad((string) '', $columnWidths[$column] + 1, '-');
        }
        $this->gridDisplay .= '+' . PHP_EOL;
    }

    /** @return int[] */
    private function getColumnWidths(): array
    {
        $columnCount = count($this->matrix, COUNT_RECURSIVE) / count($this->matrix);
        $columnWidths = [];
        for ($column = 0; $column < $columnCount; ++$column) {
            $columnWidths[] = $this->getColumnWidth(array_column($this->matrix, $column));
        }

        return $columnWidths;
    }

    /** @param mixed[] $columnData */
    private function getColumnWidth(array $columnData): int
    {
        $columnWidth = 0;
        $columnData = array_values($columnData);

        foreach ($columnData as $columnValue) {
            $columnWidth = max($columnWidth, $this->strlen($this->getString($columnValue)));
        }

        return $columnWidth;
    }

    protected function getString(mixed $value): string
    {
        return StringHelper::convertToString($value, convertBool: true);
    }

    protected function strlen(string $value): int
    {
        return mb_strlen($value);
    }
}
