<?php

declare(strict_types=1);

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
     */
    protected function writeAndReload(Spreadsheet $spreadsheet, string $format, ?callable $readerCustomizer = null, ?callable $writerCustomizer = null): Spreadsheet
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
