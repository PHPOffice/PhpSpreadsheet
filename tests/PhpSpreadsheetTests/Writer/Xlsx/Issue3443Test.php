<?php

declare(strict_types=1);

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
            'justifyLastLine' => null,
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
            'justifyLastLine' => null,
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

    public function testReadOrder(): void
    {
        $mixedString = 'xאבy'; // both LTR and RTL characters
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Following Cells will display differently depending on read order.
        // PhpSpreadsheet will not show any differences, but Excel will.
        $sheet->getCell('A1')->setValue($mixedString);
        $sheet->getCell('A2')->setValue($mixedString);
        $sheet->getCell('A3')->setValue($mixedString);
        $sheet->getStyle('A2')->getAlignment()
            ->setReadOrder(Alignment::READORDER_LTR);
        $sheet->getStyle('A3')->getAlignment()
            ->setReadOrder(Alignment::READORDER_RTL);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertSame($mixedString, $rsheet->getCell('A1')->getFormattedValue());
        self::assertSame($mixedString, $rsheet->getCell('A2')->getFormattedValue());
        self::assertSame($mixedString, $rsheet->getCell('A3')->getFormattedValue());
        self::assertSame(
            Alignment::READORDER_CONTEXT,
            $rsheet->getStyle('A1')->getAlignment()->getReadOrder()
        );
        self::assertSame(
            Alignment::READORDER_LTR,
            $rsheet->getStyle('A2')->getAlignment()->getReadOrder()
        );
        self::assertSame(
            Alignment::READORDER_RTL,
            $rsheet->getStyle('A3')->getAlignment()->getReadOrder()
        );

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
