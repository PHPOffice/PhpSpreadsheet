<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue3443Test extends AbstractFunctional
{
    public function testNonDefaultAlign(): void
    {
        // Issue 3443 - default alignment not honored.
        $spreadsheet = new Spreadsheet();
        $styleArray = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'font' => [
                'name' => 'Courier New',
            ],
        ];
        $spreadsheet->getDefaultStyle()->applyFromArray($styleArray);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(1);
        $sheet->getCell('A2')->setValue(2);
        $sheet->getCell('A3')->setValue(3);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('A3')->getFont()->setName('Tahoma');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        $expected1 = [
            'horizontal' => 'center',
            'indent' => 0,
            'readOrder' => 0,
            'shrinkToFit' => false,
            'textRotation' => 0,
            'vertical' => 'center',
            'wrapText' => true,
        ];
        self::assertSame($expected1, $rsheet->getStyle('A1')->getAlignment()->exportArray());
        $expected2 = $expected1;
        $expected2['horizontal'] = 'left';
        self::assertSame($expected2, $rsheet->getStyle('A2')->getAlignment()->exportArray());
        self::assertSame($expected1, $rsheet->getStyle('A3')->getAlignment()->exportArray());
        self::assertSame('Courier New', $rsheet->getStyle('A1')->getFont()->getName());
        self::assertSame('Courier New', $rsheet->getStyle('A2')->getFont()->getName());
        self::assertSame('Tahoma', $rsheet->getStyle('A3')->getFont()->getName());

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testDefaultAlign(): void
    {
        // Issue 3443 - default alignment not honored.
        $spreadsheet = new Spreadsheet();
        $styleArray = [
            'font' => [
                'name' => 'Courier New',
            ],
        ];
        $spreadsheet->getDefaultStyle()->applyFromArray($styleArray);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(1);
        $sheet->getCell('A2')->setValue(2);
        $sheet->getCell('A3')->setValue(3);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('A3')->getFont()->setName('Tahoma');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        $expected1 = [
            'horizontal' => 'general',
            'indent' => 0,
            'readOrder' => 0,
            'shrinkToFit' => false,
            'textRotation' => 0,
            'vertical' => 'bottom',
            'wrapText' => false,
        ];
        self::assertSame($expected1, $rsheet->getStyle('A1')->getAlignment()->exportArray());
        $expected2 = $expected1;
        $expected2['horizontal'] = 'left';
        self::assertSame($expected2, $rsheet->getStyle('A2')->getAlignment()->exportArray());
        self::assertSame($expected1, $rsheet->getStyle('A3')->getAlignment()->exportArray());
        self::assertSame('Courier New', $rsheet->getStyle('A1')->getFont()->getName());
        self::assertSame('Courier New', $rsheet->getStyle('A2')->getFont()->getName());
        self::assertSame('Tahoma', $rsheet->getStyle('A3')->getFont()->getName());

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
