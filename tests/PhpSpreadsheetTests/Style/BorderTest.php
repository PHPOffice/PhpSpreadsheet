<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PHPUnit\Framework\TestCase;

class BorderTest extends TestCase
{
    public function testAllBorders(): void
    {
        $spreadsheet = new Spreadsheet();
        $borders = $spreadsheet->getActiveSheet()->getStyle('A1')->getBorders();
        $allBorders = $borders->getAllBorders();
        $bottom = $borders->getBottom();

        $actual = $bottom->getBorderStyle();
        self::assertSame(Border::BORDER_NONE, $actual, 'should default to none');

        $allBorders->setBorderStyle(Border::BORDER_THIN);

        $actual = $bottom->getBorderStyle();
        self::assertSame(Border::BORDER_THIN, $actual, 'should have been set via allBorders');
        self::assertSame(Border::BORDER_THIN, $borders->getTop()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $borders->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $borders->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $borders->getDiagonal()->getBorderStyle());
    }

    public function testAllBordersArray(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle('A1')->getBorders()->applyFromArray(['allBorders' => ['borderStyle' => Border::BORDER_THIN]]);
        $borders = $sheet->getCell('A1')->getStyle()->getBorders();

        self::assertSame(Border::BORDER_THIN, $borders->getBottom()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $borders->getTop()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $borders->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $borders->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $borders->getDiagonal()->getBorderStyle());
    }

    public function testAllBordersArrayNotSupervisor(): void
    {
        $borders = new Borders();
        $borders->applyFromArray(['allBorders' => ['borderStyle' => Border::BORDER_THIN]]);

        self::assertSame(Border::BORDER_THIN, $borders->getBottom()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $borders->getTop()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $borders->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $borders->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $borders->getDiagonal()->getBorderStyle());
    }

    public function testOutline(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $borders = $sheet->getStyle('A1:B2')->getBorders();
        $outline = $borders->getOutline();
        $outline->setBorderStyle(Border::BORDER_THIN);

        self::assertSame(Border::BORDER_THIN, $sheet->getCell('A1')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $sheet->getCell('A1')->getStyle()->getBorders()->getTop()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('A1')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('A1')->getStyle()->getBorders()->getBottom()->getBorderStyle());

        self::assertSame(Border::BORDER_THIN, $sheet->getCell('A2')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $sheet->getCell('A2')->getStyle()->getBorders()->getBottom()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('A2')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('A2')->getStyle()->getBorders()->getTop()->getBorderStyle());

        self::assertSame(Border::BORDER_THIN, $sheet->getCell('B1')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $sheet->getCell('B1')->getStyle()->getBorders()->getTop()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('B1')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('B1')->getStyle()->getBorders()->getBottom()->getBorderStyle());

        self::assertSame(Border::BORDER_THIN, $sheet->getCell('B2')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $sheet->getCell('B2')->getStyle()->getBorders()->getBottom()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('B2')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('B2')->getStyle()->getBorders()->getTop()->getBorderStyle());
    }

    public function testInside(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $borders = $sheet->getStyle('A1:B2')->getBorders();
        $inside = $borders->getInside();
        $inside->setBorderStyle(Border::BORDER_THIN);

        self::assertSame(Border::BORDER_NONE, $sheet->getCell('A1')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('A1')->getStyle()->getBorders()->getTop()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $sheet->getCell('A1')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $sheet->getCell('A1')->getStyle()->getBorders()->getBottom()->getBorderStyle());

        self::assertSame(Border::BORDER_NONE, $sheet->getCell('A2')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('A2')->getStyle()->getBorders()->getBottom()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $sheet->getCell('A2')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $sheet->getCell('A2')->getStyle()->getBorders()->getTop()->getBorderStyle());

        self::assertSame(Border::BORDER_NONE, $sheet->getCell('B1')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('B1')->getStyle()->getBorders()->getTop()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $sheet->getCell('B1')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $sheet->getCell('B1')->getStyle()->getBorders()->getBottom()->getBorderStyle());

        self::assertSame(Border::BORDER_NONE, $sheet->getCell('B2')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('B2')->getStyle()->getBorders()->getBottom()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $sheet->getCell('B2')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $sheet->getCell('B2')->getStyle()->getBorders()->getTop()->getBorderStyle());
    }

    public function testHorizontal(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $borders = $sheet->getStyle('A1:B2')->getBorders();
        $horizontal = $borders->getHorizontal();
        $horizontal->setBorderStyle(Border::BORDER_THIN);

        self::assertSame(Border::BORDER_NONE, $sheet->getCell('A1')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('A1')->getStyle()->getBorders()->getTop()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('A1')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $sheet->getCell('A1')->getStyle()->getBorders()->getBottom()->getBorderStyle());

        self::assertSame(Border::BORDER_NONE, $sheet->getCell('A2')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('A2')->getStyle()->getBorders()->getBottom()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('A2')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $sheet->getCell('A2')->getStyle()->getBorders()->getTop()->getBorderStyle());

        self::assertSame(Border::BORDER_NONE, $sheet->getCell('B1')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('B1')->getStyle()->getBorders()->getTop()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('B1')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $sheet->getCell('B1')->getStyle()->getBorders()->getBottom()->getBorderStyle());

        self::assertSame(Border::BORDER_NONE, $sheet->getCell('B2')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('B2')->getStyle()->getBorders()->getBottom()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('B2')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $sheet->getCell('B2')->getStyle()->getBorders()->getTop()->getBorderStyle());
    }

    public function testVertical(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $borders = $sheet->getStyle('A1:B2')->getBorders();
        $vertical = $borders->getVertical();
        $vertical->setBorderStyle(Border::BORDER_THIN);

        self::assertSame(Border::BORDER_NONE, $sheet->getCell('A1')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('A1')->getStyle()->getBorders()->getTop()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $sheet->getCell('A1')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('A1')->getStyle()->getBorders()->getBottom()->getBorderStyle());

        self::assertSame(Border::BORDER_NONE, $sheet->getCell('A2')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('A2')->getStyle()->getBorders()->getBottom()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $sheet->getCell('A2')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('A2')->getStyle()->getBorders()->getTop()->getBorderStyle());

        self::assertSame(Border::BORDER_NONE, $sheet->getCell('B1')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('B1')->getStyle()->getBorders()->getTop()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $sheet->getCell('B1')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('B1')->getStyle()->getBorders()->getBottom()->getBorderStyle());

        self::assertSame(Border::BORDER_NONE, $sheet->getCell('B2')->getStyle()->getBorders()->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('B2')->getStyle()->getBorders()->getBottom()->getBorderStyle());
        self::assertSame(Border::BORDER_THIN, $sheet->getCell('B2')->getStyle()->getBorders()->getLeft()->getBorderStyle());
        self::assertSame(Border::BORDER_NONE, $sheet->getCell('B2')->getStyle()->getBorders()->getTop()->getBorderStyle());
    }

    public function testNoSupervisorAllBorders(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $borders = new Borders();
        $borders->getAllBorders();
    }

    public function testNoSupervisorOutline(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $borders = new Borders();
        $borders->getOutline();
    }

    public function testNoSupervisorInside(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $borders = new Borders();
        $borders->getInside();
    }

    public function testNoSupervisorVertical(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $borders = new Borders();
        $borders->getVertical();
    }

    public function testNoSupervisorHorizontal(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $borders = new Borders();
        $borders->getHorizontal();
    }

    public function testGetSharedComponentPseudo(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle('A1')->getBorders()->getHorizontal()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle('A1')->getBorders()->getHorizontal()->getSharedComponent();
    }

    public function testBorderStyle(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle('A1')->getBorders()->getTop()->setBorderStyle(false);
        $sheet->getStyle('A2')->getBorders()->getTop()->setBorderStyle(true);
        self::assertEquals(Border::BORDER_NONE, $sheet->getStyle('A1')->getBorders()->getTop()->getBorderStyle());
        self::assertEquals(Border::BORDER_MEDIUM, $sheet->getStyle('A2')->getBorders()->getTop()->getBorderStyle());
        $sheet->getStyle('A3')->getBorders()->getTop()->applyFromArray(['borderStyle' => Border::BORDER_MEDIUM]);
        self::assertEquals(Border::BORDER_MEDIUM, $sheet->getStyle('A3')->getBorders()->getTop()->getBorderStyle());
        $border = new Border();
        $border->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('FFFF0000'));
        self::assertEquals('FFFF0000', $border->getColor()->getARGB());
        self::assertEquals(Border::BORDER_THIN, $border->getBorderStyle());
    }

    public function testDiagonalDirection(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle('A1')->getBorders()->getDiagonal()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getStyle('A1')->getBorders()->setDiagonalDirection(Borders::DIAGONAL_BOTH);
        $borders = $sheet->getStyle('A1')->getBorders();

        self::assertSame(Border::BORDER_MEDIUM, $borders->getDiagonal()->getBorderStyle());
        self::assertSame(Borders::DIAGONAL_BOTH, $borders->getDiagonalDirection());
    }
}
