<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PHPUnit\Framework\TestCase;

class Issue4448Test extends TestCase
{
    private static string $testbook = 'tests/data/Reader/Xml/issue.4448.xml';

    public function testIndent(): void
    {
        $reader = new Xml();
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame(
            5,
            $sheet->getStyle('A2')
                ->getAlignment()
                ->getIndent()
        );
        self::assertSame(
            0,
            $sheet->getStyle('A1')
                ->getAlignment()
                ->getIndent()
        );
        $spreadsheet->disconnectWorksheets();
    }
}
