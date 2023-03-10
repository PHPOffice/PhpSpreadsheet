<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Worksheet\SheetView;
use PHPUnit\Framework\TestCase;

class SheetViewTest extends TestCase
{
    public function testView(): void
    {
        $sheetView = new SheetView();
        self::assertSame(SheetView::SHEETVIEW_NORMAL, $sheetView->getView());
        $sheetView->setView(SheetView::SHEETVIEW_PAGE_LAYOUT);
        self::assertSame(SheetView::SHEETVIEW_PAGE_LAYOUT, $sheetView->getView());
        $sheetView->setView(null);
        self::assertSame(SheetView::SHEETVIEW_NORMAL, $sheetView->getView());
    }

    public function testBadView(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $this->expectExceptionMessage('Invalid sheetview layout type.');
        $sheetView = new SheetView();
        $sheetView->setView('unknown');
    }

    public function testBadZoomScaleNormal(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $this->expectExceptionMessage('Scale must be greater than or equal to 1.');
        $sheetView = new SheetView();
        $sheetView->setZoomScaleNormal(0);
    }

    public function testBadZoomScale(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $this->expectExceptionMessage('Scale must be greater than or equal to 1.');
        $sheetView = new SheetView();
        $sheetView->setZoomScale(0);
    }
}
