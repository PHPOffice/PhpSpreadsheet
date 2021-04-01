<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class FuncDocsTest extends TestCase
{
    public function testDocByName(): void
    {
        $outString = FuncDocs::makeDocByName();
        $sample = new Sample();
        $outFile = $sample->getFilename('function-list-by-name.md', 'md');
        self::assertNotFalse(file_put_contents($outFile, $outString));
    }

    public function testDocByCategory(): void
    {
        $outString = FuncDocs::makeDocByCategory();
        $sample = new Sample();
        $outFile = $sample->getFilename('function-list-by-category.md', 'md');
        self::assertNotFalse(file_put_contents($outFile, $outString));
    }
}
