<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    public function testIssue2316(): void
    {
        $filename = 'tests/data/Reader/XLSX/issue.2316.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $sheet = $spreadsheet->getActiveSheet();
        $comment = $sheet->getComment('A1');
        $commentString = (string) $comment;
        self::assertStringContainsString('編號長度限制：', $commentString);
        self::assertSame('jill.chen', $comment->getAuthor());
        $comment = $sheet->getComment('E1');
        $commentString = (string) $comment;
        self::assertStringContainsString('若為宅配物流僅能選「純配送」', $commentString);
        self::assertSame('Anderson Chen 陳宗棠', $comment->getAuthor());
    }

    public function testIssue3654(): void
    {
        // Reader was ignoring comments.
        $filename = 'tests/data/Reader/XLSX/issue.3654.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);

        $sheet = $spreadsheet->getActiveSheet();
        $expectedComments = [
            'X4', 'AD4', 'AN4',
            'X5', 'AD5', 'AN5',
            'AN6', // X6 and AD6 are uncommented on original
            'X7', 'AD7', 'AN7',
            'X8', 'AD8', 'AN8',
            'X9', 'AD9', 'AN9',
            'X10', 'AD10', 'AN10',
            'X11', 'AD11', 'AN11',
            'X12', 'AD12', 'AN12',
        ];
        self::assertEquals($expectedComments, array_keys($sheet->getComments()));
        self::assertNotEmpty($sheet->getComment('X4')->getBackgroundImage()->getPath());
        self::assertNotEmpty($sheet->getComment('AN12')->getBackgroundImage()->getPath());
        $spreadsheet->disconnectWorksheets();
    }
}
