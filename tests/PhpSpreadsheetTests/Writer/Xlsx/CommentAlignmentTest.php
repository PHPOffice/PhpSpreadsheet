<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class CommentAlignmentTest extends AbstractFunctional
{
    public function testIssue4004(): void
    {
        $type = 'Xlsx';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getComment('A3')->getText()->createText('Comment');
        $sheet->getComment('A4')->getText()->createText('שלום');
        $sheet->getComment('A4')->setAlignment(Alignment::HORIZONTAL_RIGHT);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $type);
        $spreadsheet->disconnectWorksheets();

        self::assertCount(1, $reloadedSpreadsheet->getAllSheets());

        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        $comment1 = $rsheet->getComment('A3');
        self::assertSame('Comment', $comment1->getText()->getPlainText());
        self::assertSame('general', $comment1->getAlignment());
        $comment2 = $rsheet->getComment('A4');
        self::assertSame('שלום', $comment2->getText()->getPlainText());
        self::assertSame('Right', $comment2->getAlignment());

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
