<?php

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

/**
 * Base class for functional test to write and reload file on disk across different formats.
 */
abstract class AbstractFunctional extends TestCase
{
    /**
     * Write spreadsheet to disk, reload and return it.
     *
     * @param string $format
     *
     * @return Spreadsheet
     */
    protected function writeAndReload(Spreadsheet $spreadsheet, $format, ?callable $readerCustomizer = null, ?callable $writerCustomizer = null)
    {
        $filename = File::temporaryFilename();
        $writer = IOFactory::createWriter($spreadsheet, $format);
        if ($writerCustomizer) {
            $writerCustomizer($writer);
        }
        $writer->save($filename);

        $reader = IOFactory::createReader($format);
        if ($readerCustomizer) {
            $readerCustomizer($reader);
        }
        $reloadedSpreadsheet = $reader->load($filename);
        unlink($filename);

        return $reloadedSpreadsheet;
    }
}
