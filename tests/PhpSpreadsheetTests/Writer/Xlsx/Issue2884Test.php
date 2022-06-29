<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Writer;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class Issue2884Test extends TestCase
{
    public function testCellCharsLimit(): void
    {
        // Problem where text exceed limit in Excel single cell
        $outputFilename = File::temporaryFilename();
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $stringHelper = new ReflectionClass(new StringHelper());
        $cellCharsLimit = $stringHelper->getConstant('CELL_CHARS_LIMIT');
        $text = str_repeat('a', $cellCharsLimit);
        $worksheet->setCellValueByColumnAndRow(1, 1, $text . $text);

        $writer = new Writer($spreadsheet);
        $writer->save($outputFilename);
        $zipfile = "zip://$outputFilename#xl/sharedStrings.xml";
        $contents = file_get_contents($zipfile);
        unlink($outputFilename);
        if ($contents === false) {
            self::fail('Unable to open file');
        } else {
            self::assertStringContainsString($text, $contents);
        }
    }
}
