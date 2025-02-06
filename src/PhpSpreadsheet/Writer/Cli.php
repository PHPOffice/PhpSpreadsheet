<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Cli extends BaseWriter
{
    /**
     * Spreadsheet object.
     *
     * @var Spreadsheet
     */
    protected $spreadsheet;

    /**
     * Sheet index to write.
     *
     * @var null|int
     */
    private $sheetIndex = 0;

    /**
     * Create a new CLI Writer.
     */
    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
    }

    /**
     * Save Spreadsheet to file.
     *
     * @param resource|string $filename
     */
    public function save($filename, int $flags = 0): void
    {
        $this->processFlags($flags);

        // Open file
        $this->openFileHandle($filename);

        // Write CLI table
        fwrite($this->fileHandle, $this->generateCliTable());

        // Close file
        $this->maybeCloseFileHandle();
    }

    /**
     * Generate CLI table output
     */
    private function generateCliTable(): string
    {
        // Get sheet
        $sheet = ($this->sheetIndex === null) 
            ? $this->spreadsheet->getActiveSheet()
            : $this->spreadsheet->getSheet($this->sheetIndex);

        // Get worksheet dimension
        [$min, $max] = explode(':', $sheet->calculateWorksheetDataDimension());
        [$minCol, $minRow] = Coordinate::indexesFromString($min);
        [$maxCol, $maxRow] = Coordinate::indexesFromString($max);

        // Get column widths
        $colWidths = $this->calculateColumnWidths($sheet, $minCol, $maxCol, $minRow, $maxRow);

        // Generate the output
        $output = $this->generateTableHeader($colWidths);
        $output .= $this->generateTableRows($sheet, $minCol, $maxCol, $minRow, $maxRow, $colWidths);
        
        return $output;
    }

    /**
     * Calculate the width of each column
     */
    private function calculateColumnWidths(Worksheet $sheet, int $minCol, int $maxCol, int $minRow, int $maxRow): array
    {
        $colWidths = [];

        // Initialize with column header widths
        for ($col = $minCol; $col <= $maxCol; ++$col) {
            $cell = $sheet->getCellByColumnAndRow($col, $minRow);
            $colWidths[$col] = strlen($cell->getValue() ?? '');
        }

        // Check cell values
        for ($row = $minRow; $row <= $maxRow; ++$row) {
            for ($col = $minCol; $col <= $maxCol; ++$col) {
                $cell = $sheet->getCellByColumnAndRow($col, $row);
                $value = $cell->getValue();
                if ($value !== null) {
                    $length = strlen($value);
                    if ($length > ($colWidths[$col] ?? 0)) {
                        $colWidths[$col] = $length;
                    }
                }
            }
        }

        return $colWidths;
    }

    /**
     * Generate the table header with correct column widths
     */
    private function generateTableHeader(array $colWidths): string
    {
        $separator = '+';
        foreach ($colWidths as $width) {
            $separator .= str_repeat('-', $width + 2) . '+';
        }
        return $separator . "\n";
    }

    /**
     * Generate the table rows
     */
    private function generateTableRows(Worksheet $sheet, int $minCol, int $maxCol, int $minRow, int $maxRow, array $colWidths): string
    {
        $output = '';

        for ($row = $minRow; $row <= $maxRow; ++$row) {
            $output .= '|';
            for ($col = $minCol; $col <= $maxCol; ++$col) {
                $cell = $sheet->getCellByColumnAndRow($col, $row);
                $value = (string) $cell->getValue();
                $output .= ' ' . str_pad($value, $colWidths[$col]) . ' |';
            }
            $output .= "\n";

            // Add separator after each row
            $output .= $this->generateTableHeader($colWidths);
        }

        return $output;
    }

    /**
     * Get sheet index.
     */
    public function getSheetIndex(): ?int
    {
        return $this->sheetIndex;
    }

    /**
     * Set sheet index.
     *
     * @param int $sheetIndex Sheet index
     *
     * @return $this
     */
    public function setSheetIndex($sheetIndex)
    {
        $this->sheetIndex = $sheetIndex;
        return $this;
    }

    /**
     * Write all sheets (resets sheetIndex to NULL).
     *
     * @return $this
     */
    public function writeAllSheets()
    {
        $this->sheetIndex = null;
        return $this;
    }
}
?>
