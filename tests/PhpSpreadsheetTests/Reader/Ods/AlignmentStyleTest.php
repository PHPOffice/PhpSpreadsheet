<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class AlignmentStyleTest extends AbstractFunctional
{
    public function testReadAlignmentStyles(): void
    {
        $spreadsheetOld = new Spreadsheet();
        $sheet = $spreadsheetOld->getActiveSheet();
        $spreadsheetOld->getDefaultStyle()
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRgb('ffff00');
        $sheet->getCell('A1')->setValue('noStyle');
        $sheet->getCell('A2')->setValue('left');
        $sheet->getStyle('A2')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getCell('A3')->setValue('right');
        $sheet->getStyle('A3')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getCell('A4')->setValue('center');
        $sheet->getStyle('A4')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getCell('A5')->setValue('just ify');
        $sheet->getStyle('A5')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
        $sheet->getCell('A6')->setValue('verttop');
        $sheet->getStyle('A6')->getAlignment()
            ->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getCell('A7')->setValue('vertcen');
        $sheet->getStyle('A7')->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getCell('A8')->setValue('vertcen');
        $sheet->getStyle('A8')->getAlignment()
            ->setWrapText(true);
        $sheet->getCell('A9')->setValue('shrinkshrinkshrink');
        $sheet->getStyle('A9')->getAlignment()
            ->setShrinkToFit(true);
        $sheet->getCell('A10')->setValue('leftindent');
        $sheet->getStyle('A10')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setIndent(1);
        $sheet->getCell('A11')->setValue('readorder1');
        $sheet->getStyle('A11')->getAlignment()
            ->setReadOrder(Alignment::READORDER_RTL);
        $sheet->getCell('A12')->setValue('readorder2');
        $sheet->getStyle('A12')->getAlignment()
            ->setReadOrder(Alignment::READORDER_LTR);
        $sheet->getCell('A13')->setValue('vertjust');
        $sheet->getStyle('A13')->getAlignment()
            ->setVertical(Alignment::VERTICAL_JUSTIFY);

        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Ods');
        $spreadsheetOld->disconnectWorksheets();

        $newSheet = $spreadsheet->getActiveSheet();
        self::assertSame(Fill::FILL_SOLID, $spreadsheet->getDefaultStyle()->getFill()->getFillType());
        self::assertSame('ffff00', $spreadsheet->getDefaultStyle()->getFill()->getStartColor()->getRgb());
        self::assertSame(
            Alignment::HORIZONTAL_GENERAL,
            $newSheet->getStyle('A1')->getAlignment()
                ->getHorizontal()
        );
        self::assertSame(
            Alignment::HORIZONTAL_LEFT,
            $newSheet->getStyle('A2')->getAlignment()
                ->getHorizontal()
        );
        self::assertSame(
            Alignment::HORIZONTAL_RIGHT,
            $newSheet->getStyle('A3')->getAlignment()
                ->getHorizontal()
        );
        self::assertSame(
            Alignment::HORIZONTAL_CENTER,
            $newSheet->getStyle('A4')->getAlignment()
                ->getHorizontal()
        );
        self::assertSame(
            Alignment::HORIZONTAL_FILL,
            $newSheet->getStyle('A5')->getAlignment()
                ->getHorizontal(),
            'Ods Reader treats justify and fill the same'
        );
        self::assertSame(
            Alignment::VERTICAL_TOP,
            $newSheet->getStyle('A6')->getAlignment()
                ->getVertical()
        );
        self::assertSame(
            Alignment::VERTICAL_CENTER,
            $newSheet->getStyle('A7')->getAlignment()
                ->getVertical()
        );
        self::assertTrue(
            $newSheet->getStyle('A8')->getAlignment()
                ->getWrapText()
        );
        self::assertTrue(
            $newSheet->getStyle('A9')->getAlignment()
                ->getShrinkToFit()
        );
        self::assertSame(
            Alignment::HORIZONTAL_LEFT,
            $newSheet->getStyle('A10')->getAlignment()
                ->getHorizontal()
        );
        self::assertSame(
            1,
            $newSheet->getStyle('A10')->getAlignment()
                ->getIndent()
        );
        self::assertSame(
            Alignment::READORDER_RTL,
            $newSheet->getStyle('A11')->getAlignment()
                ->getReadOrder()
        );
        self::assertSame(
            Alignment::READORDER_LTR,
            $newSheet->getStyle('A12')->getAlignment()
                ->getReadOrder()
        );
        self::assertSame(
            Alignment::VERTICAL_JUSTIFY,
            $newSheet->getStyle('A13')->getAlignment()
                ->getVertical()
        );

        $spreadsheet->disconnectWorksheets();
    }
}
