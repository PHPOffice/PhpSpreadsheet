<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ConditionalColorScaleTest extends AbstractFunctional
{
    public function testColorScale(): void
    {
        $filename = 'tests/data/Reader/XLSX/colorscale.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $worksheet = $reloadedSpreadsheet->getActiveSheet();
        $styles = $worksheet->getConditionalStyles('A1:A10');
        self::assertCount(1, $styles);
        $colorScale = $styles[0]->getColorScale();
        self::assertNotNull($colorScale);
        self::assertNotNull($colorScale->getMinimumConditionalFormatValueObject());
        self::assertNotNull($colorScale->getMidpointConditionalFormatValueObject());
        self::assertSame('50', $colorScale->getMidpointConditionalFormatValueObject()->getValue());
        self::assertNotNull($colorScale->getMaximumConditionalFormatValueObject());
        self::assertSame('FFF8696B', $colorScale->getMinimumColor()?->getARGB());
        self::assertSame('FFFFEB84', $colorScale->getMidpointColor()?->getARGB());
        self::assertSame('FF63BE7B', $colorScale->getMaximumColor()?->getARGB());
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
