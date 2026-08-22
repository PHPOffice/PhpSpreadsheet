<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class BorderStyleTest extends AbstractFunctional
{
    public function testReadAlignmentStyles(): void
    {
        $spreadsheetOld = new Spreadsheet();
        $sheet = $spreadsheetOld->getActiveSheet();
        $sheet->getCell('A1')->setValue('noStyle');
        $sheet->getCell('A2')->setValue('left');
        $sheet->getStyle('A2')->getBorders()
            ->getLeft()
            ->setBorderStyle(Border::BORDER_THICK);
        $sheet->getCell('A3')->setValue('right');
        $sheet->getStyle('A3')->getBorders()
            ->getRight()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setRgb('00ff00');
        $sheet->getCell('A4')->setValue('top/bottom');
        $sheet->getStyle('A4')->getBorders()
            ->getTop()
            ->setBorderStyle(Border::BORDER_DASHDOT)
            ->getColor()->setRgb('ff0000');
        $sheet->getStyle('A4')->getBorders()
            ->getBottom()
            ->setBorderStyle(Border::BORDER_MEDIUMDASHDOT)
            ->getColor()->setRgb('0000ff');
        $sheet->getCell('A5')->setValue('diagUp');
        $sheet->getStyle('A5')->getBorders()
            ->setDiagonalDirection(Borders::DIAGONAL_UP)
            ->getDiagonal()
            ->setBorderStyle(Border::BORDER_DASHED)
            ->getColor()->setRgb('555555');
        $sheet->getCell('A6')->setValue('diagDown');
        $sheet->getStyle('A6')->getBorders()
            ->setDiagonalDirection(Borders::DIAGONAL_DOWN)
            ->getDiagonal()
            ->setBorderStyle(Border::BORDER_DOTTED)
            ->getColor()->setRgb('666666');
        $sheet->getCell('A7')->setValue('diagBoth');
        $sheet->getStyle('A7')->getBorders()
            ->setDiagonalDirection(Borders::DIAGONAL_BOTH)
            ->getDiagonal()
            ->setBorderStyle(Border::BORDER_DOUBLE)
            ->getColor()->setRgb('777777');

        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Ods');
        $spreadsheetOld->disconnectWorksheets();

        $newSheet = $spreadsheet->getActiveSheet();
        self::assertSame(Border::BORDER_NONE, $newSheet->getStyle('A1')->getBorders()->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $newSheet->getStyle('A1')->getBorders()->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $newSheet->getStyle('A1')->getBorders()->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $newSheet->getStyle('A1')->getBorders()->getBottom()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $newSheet->getStyle('A1')->getBorders()->getTop()->getBorderStyle());

        self::assertSame(Border::BORDER_NONE, $newSheet->getStyle('A2')->getBorders()->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_THICK, $newSheet->getStyle('A2')->getBorders()->getLeft()->getBorderStyle());
        self::assertSame('000000', $newSheet->getStyle('A2')->getBorders()->getLeft()->getColor()->getRgb());

        self::assertSame(Border::BORDER_THIN, $newSheet->getStyle('A3')->getBorders()->getRight()->getBorderStyle());
        self::assertSame('00ff00', $newSheet->getStyle('A3')->getBorders()->getRight()->getColor()->getRgb());

        self::assertSame(Border::BORDER_DASHDOT, $newSheet->getStyle('A4')->getBorders()->getTop()->getBorderStyle());
        self::assertSame('ff0000', $newSheet->getStyle('A4')->getBorders()->getTop()->getColor()->getRgb());
        self::assertSame(Border::BORDER_MEDIUMDASHDOT, $newSheet->getStyle('A4')->getBorders()->getBottom()->getBorderStyle());
        self::assertSame('0000ff', $newSheet->getStyle('A4')->getBorders()->getBottom()->getColor()->getRgb());

        self::assertSame(Border::BORDER_DASHED, $newSheet->getStyle('A5')->getBorders()->getDiagonal()->getBorderStyle());
        self::assertSame('555555', $newSheet->getStyle('A5')->getBorders()->getDiagonal()->getColor()->getRgb());
        self::assertSame(Borders::DIAGONAL_UP, $newSheet->getStyle('A5')->getBorders()->getDiagonalDirection());

        self::assertSame(Border::BORDER_DOTTED, $newSheet->getStyle('A6')->getBorders()->getDiagonal()->getBorderStyle());
        self::assertSame('666666', $newSheet->getStyle('A6')->getBorders()->getDiagonal()->getColor()->getRgb());
        self::assertSame(Borders::DIAGONAL_DOWN, $newSheet->getStyle('A6')->getBorders()->getDiagonalDirection());

        self::assertSame(Border::BORDER_DOUBLE, $newSheet->getStyle('A7')->getBorders()->getDiagonal()->getBorderStyle());
        self::assertSame('777777', $newSheet->getStyle('A7')->getBorders()->getDiagonal()->getColor()->getRgb());
        self::assertSame(Borders::DIAGONAL_BOTH, $newSheet->getStyle('A7')->getBorders()->getDiagonalDirection());

        $spreadsheet->disconnectWorksheets();
    }
}
