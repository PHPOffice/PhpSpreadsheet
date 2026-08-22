<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Slk;

use PhpOffice\PhpSpreadsheet\Reader\Slk;

class SlkCommentsTest extends \PHPUnit\Framework\TestCase
{
    public function testComments(): void
    {
        $testbook = 'tests/data/Reader/Slk/issue.2276.slk';
        $reader = new Slk();
        $spreadsheet = $reader->load($testbook);
        $sheet = $spreadsheet->getActiveSheet();
        $comments = $sheet->getComments();
        self::assertCount(2, $comments);
        self::assertArrayHasKey('A1', $comments);
        self::assertArrayHasKey('B2', $comments);
        self::assertSame("Zeratul:\nEn Taro Adun!", $sheet->getComment('A1')->getText()->getPlainText());
        self::assertSame("Arthas:\nFrostmourne Hungers.", $sheet->getComment('B2')->getText()->getPlainText());
        $spreadsheet->disconnectWorksheets();
    }
}
