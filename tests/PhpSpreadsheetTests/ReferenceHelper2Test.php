<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\ReferenceHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class ReferenceHelper2Test extends TestCase
{
    public function testNoClone(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Cloning a Singleton');
        $referenceHelper = ReferenceHelper::getInstance();
        clone $referenceHelper;
    }

    public function testRenamedWorksheetInFormula(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $referenceHelper = ReferenceHelper::getInstance();
        $referenceHelper->updateNamedFormulae($spreadsheet); // no-op
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Sheet2');
        $title2 = $sheet2->getTitle();
        $sheet2->getCell('A1')->setValue(10);
        $sheet2->getCell('A2')->setValue(20);
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Sheet3');
        $title3 = $sheet3->getTitle();
        $sheet3->getCell('A1')->setValue(30);
        $sheet3->getCell('A2')->setValue(40);
        $sheet1->getCell('A1')->setValue("=$title2!A1");
        $sheet1->getCell('A2')->setValue("='$title2'!A2");
        $sheet1->getCell('B1')->setValue("=$title3!A1");
        $sheet1->getCell('B2')->setValue("='$title3'!A2");
        $newTitle2 = 'renamedSheet2';
        $sheet2->setTitle($newTitle2);
        self::assertSame("=$newTitle2!A1", $sheet1->getCell('A1')->getValue());
        self::assertSame("='$newTitle2'!A2", $sheet1->getCell('A2')->getValue());
        self::assertSame("=$title3!A1", $sheet1->getCell('B1')->getValue());
        self::assertSame("='$title3'!A2", $sheet1->getCell('B2')->getValue());
        self::assertSame([[10, 30], [20, 40]], $sheet1->toArray(null, true, false));
        $spreadsheet->disconnectWorksheets();
    }
}
