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
}
