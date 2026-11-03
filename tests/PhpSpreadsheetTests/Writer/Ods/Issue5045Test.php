<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods as OdsWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue5045Test extends AbstractFunctional
{
    private function spreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('A2', 2);
        $sheet->setCellValue('A3', '=SUM(A1:A2)');
        $sheet->getStyle('A3')->getFont()->setBold(true);
        $sheet->setCellValue('A4', '=A1&"a"');
        $sheet->getCell('A4')->getHyperlink()->setUrl('https://example.com/');

        return $spreadsheet;
    }

    public function testFormulaWithoutPreCalculation(): void
    {
        $spreadsheet = $this->spreadsheet();
        $writer = new OdsWriter($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $data = (new OdsWriter\Content($writer))->write();
        self::assertStringContainsString('table:formula="of:=SUM([.A1:.A2])"/>', $data);
        self::assertStringNotContainsString('office:string-value', $data);

        $reloaded = $this->writeAndReload($spreadsheet, 'Ods', null, function (OdsWriter $writer): void {
            $writer->setPreCalculateFormulas(false);
        });
        $spreadsheet->disconnectWorksheets();
        $sheet = $reloaded->getActiveSheet();
        self::assertSame('=SUM(A1:A2)', $sheet->getCell('A3')->getValue());
        self::assertNull($sheet->getCell('A3')->getOldCalculatedValue());
        self::assertTrue($sheet->getStyle('A3')->getFont()->getBold(), 'a cell without content keeps its style');
        self::assertSame('=A1&"a"', $sheet->getCell('A4')->getValue());
        self::assertSame('https://example.com/', $sheet->getCell('A4')->getHyperlink()->getUrl());
        $reloaded->disconnectWorksheets();
    }

    public function testFormulaWithPreCalculation(): void
    {
        $spreadsheet = $this->spreadsheet();
        $data = (new OdsWriter\Content(new OdsWriter($spreadsheet)))->write();
        self::assertStringContainsString('table:formula="of:=SUM([.A1:.A2])" office:value-type="float" office:value="3"', $data);
        self::assertStringContainsString('office:string-value="1a"', $data);
        $spreadsheet->disconnectWorksheets();
    }
}
