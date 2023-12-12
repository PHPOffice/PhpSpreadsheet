<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class Worksheet3Test extends TestCase
{
    // All these tests involve setting one property to a
    //    non-default value. They should be reviewed if defaults change.
    public function testPageSetup(): void
    {
        $worksheet1 = new Worksheet();
        $worksheet1->getPageSetup()->setOrientation('landscape');
        $pageSetup = clone $worksheet1->getPageSetup();
        $worksheet2 = new Worksheet();
        $worksheet2->setPageSetup($pageSetup);
        self::assertSame('landscape', $worksheet2->getPageSetup()->getOrientation());
    }

    public function testPageMargins(): void
    {
        $worksheet1 = new Worksheet();
        $worksheet1->getPageMargins()->setLeft(0.75);
        $pageMargins = clone $worksheet1->getPageMargins();
        $worksheet2 = new Worksheet();
        $worksheet2->setPageMargins($pageMargins);
        self::assertSame(0.75, $worksheet2->getPageMargins()->getLeft());
    }

    public function testHeaderFooter(): void
    {
        $worksheet1 = new Worksheet();
        $worksheet1->getHeaderFooter()->setDifferentOddEven(true);
        $headerFooter = clone $worksheet1->getHeaderFooter();
        $worksheet2 = new Worksheet();
        $worksheet2->setHeaderFooter($headerFooter);
        self::assertTrue($worksheet2->getHeaderFooter()->getDifferentOddEven());
    }

    public function testSheetView(): void
    {
        $worksheet1 = new Worksheet();
        $worksheet1->getSheetView()->setView('pageLayout');
        $sheetView = clone $worksheet1->getSheetView();
        $worksheet2 = new Worksheet();
        $worksheet2->setSheetView($sheetView);
        self::assertSame('pageLayout', $worksheet2->getSheetView()->getView());
    }

    public function testProtection(): void
    {
        $worksheet1 = new Worksheet();
        $worksheet1->getProtection()->setSpinCount(4321);
        $protection = clone $worksheet1->getProtection();
        $worksheet2 = new Worksheet();
        $worksheet2->setProtection($protection);
        self::assertSame(4321, $worksheet2->getProtection()->getSpinCount());
    }
}
