<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\IconSetValues;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ConditionalIconSetTest extends AbstractFunctional
{
    public function testIconSet(): void
    {
        $filename = 'tests/data/Reader/XLSX/conditionalFormattingIconSet.xlsx';
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $worksheet = $reloadedSpreadsheet->getActiveSheet();

        $columnIndex = 'A';
        foreach (IconSetValues::cases() as $iconSetValue) {
            // styles
            $styles = $worksheet->getConditionalStyles("{$columnIndex}2:{$columnIndex}11");
            self::assertCount(1, $styles);

            // icon set
            $iconSet = $styles[0]->getIconSet();
            self::assertNotNull($iconSet);
            self::assertSame($iconSetValue, $iconSet->getIconSetType() ?? IconSetValues::ThreeTrafficLights1);

            StringHelper::stringIncrement($columnIndex);
        }

        // icon set attributes
        $columnIndex = 'A';
        foreach (
            [
                ['reverse' => false, 'showValue' => false],
                ['reverse' => true, 'showValue' => false],
                ['reverse' => false, 'showValue' => true],
            ] as $expected
        ) {
            $styles = $worksheet->getConditionalStyles("{$columnIndex}2:{$columnIndex}11");
            $iconSet = $styles[0]->getIconSet();
            self::assertNotNull($iconSet);
            self::assertSame($expected['reverse'], $iconSet->getReverse() ?? false);
            self::assertSame($expected['showValue'], $iconSet->getShowValue() ?? true);
            self::assertFalse($iconSet->getCustom() ?? false);

            StringHelper::stringIncrement($columnIndex);
        }

        // cfvos
        $columnIndex = 'A';
        foreach (
            [
                [['percent', '0', true], ['percent', '33', false], ['percent', '67', true]],
                [['percent', '0', true], ['num', '3', false], ['num', '7', true]],
                [['percent', '0', true], ['formula', '10/3', false], ['formula', '10/2', true]],
                [['percent', '0', true], ['percentile', '33', false], ['percentile', '67', true]],
            ] as $expected
        ) {
            $styles = $worksheet->getConditionalStyles("{$columnIndex}2:{$columnIndex}11");
            $iconSet = $styles[0]->getIconSet();
            self::assertNotNull($iconSet);
            $cfvos = $iconSet->getCfvos();
            self::assertCount(count($expected), $cfvos);
            foreach ($expected as $i => [$type, $value, $gte]) {
                $cfvo = $cfvos[$i];
                self::assertSame($type, $cfvo->getType());
                self::assertSame($value, $cfvo->getValue());
                self::assertSame($gte, $cfvo->getGreaterThanOrEqual() ?? true);
                self::assertNull($cfvo->getCellFormula());
            }

            StringHelper::stringIncrement($columnIndex);
        }

        // unsupported icon sets
        for ($columnIndex = 'R'; $columnIndex <= 'U'; StringHelper::stringIncrement($columnIndex)) {
            $styles = $worksheet->getConditionalStyles("{$columnIndex}2:{$columnIndex}11");
            $iconSet = $styles[0]->getIconSet();
            self::assertNull($iconSet);
        }
    }
}
