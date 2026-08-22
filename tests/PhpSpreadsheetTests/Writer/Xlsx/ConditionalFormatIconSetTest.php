<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use Generator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalFormatValueObject;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalIconSet;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\IconSetValues;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;
use PHPUnit\Framework\Attributes\DataProvider;

class ConditionalFormatIconSetTest extends AbstractFunctional
{
    private const COORDINATE = 'A1:A3';

    #[DataProvider('iconSetsProvider')]
    public function testWriteIconSets(
        string $expected,
        ?IconSetValues $type,
        ?bool $reverse = null,
        ?bool $showValue = null,
        ?bool $custom = null,
    ): void {
        $condition = new Conditional();
        $condition->setConditionType(Conditional::CONDITION_ICONSET);
        $iconSet = $condition->setIconSet(new ConditionalIconSet())
            ->getIconSet();
        self::assertNotNull($iconSet);
        if ($type !== null) {
            $iconSet->setIconSetType($type);
        }
        $iconSet->setCfvos([
            new ConditionalFormatValueObject('percent', 0),
            new ConditionalFormatValueObject('percent', 33),
            new ConditionalFormatValueObject('percent', 67),
        ]);
        if ($reverse !== null) {
            $iconSet->setReverse($reverse);
        }
        if ($showValue !== null) {
            $iconSet->setShowValue($showValue);
        }
        if ($custom !== null) {
            $iconSet->setCustom($custom);
        }

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setConditionalStyles(self::COORDINATE, [$condition]);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($worksheet, []);

        $expected = preg_replace(['/^\s+/m', "/\n/"], '', $expected);
        self::assertIsString($expected);
        self::assertStringContainsString($expected, $data);
    }

    public static function iconSetsProvider(): Generator
    {
        $coordinate = self::COORDINATE;
        $cfvos = <<<XML
            <cfvo type="percent" val="0"/>
            <cfvo type="percent" val="33"/>
            <cfvo type="percent" val="67"/>
            XML;
        foreach (IconSetValues::cases() as $type) {
            yield $type->name => [
                <<<XML
                    <conditionalFormatting sqref="{$coordinate}">
                        <cfRule type="iconSet" priority="1">
                            <iconSet iconSet="{$type->value}">
                                {$cfvos}
                            </iconSet>
                        </cfRule>
                    </conditionalFormatting>
                    XML,
                $type,
            ];
        }

        yield 'null' => [
            <<<XML
                <conditionalFormatting sqref="{$coordinate}">
                    <cfRule type="iconSet" priority="1">
                        <iconSet>
                            {$cfvos}
                        </iconSet>
                    </cfRule>
                </conditionalFormatting>
                XML,
            null,
        ];

        foreach ([1, 0] as $reverse) {
            yield "null/reverse=$reverse" => [
                <<<XML
                    <conditionalFormatting sqref="{$coordinate}">
                        <cfRule type="iconSet" priority="1">
                            <iconSet reverse="$reverse">
                                {$cfvos}
                            </iconSet>
                        </cfRule>
                    </conditionalFormatting>
                    XML,
                null,
                $reverse === 1,
            ];
        }

        foreach ([1, 0] as $showValue) {
            yield "null/showValue=$showValue" => [
                <<<XML
                    <conditionalFormatting sqref="{$coordinate}">
                        <cfRule type="iconSet" priority="1">
                            <iconSet showValue="$showValue">
                                {$cfvos}
                            </iconSet>
                        </cfRule>
                    </conditionalFormatting>
                    XML,
                null,
                null,
                $showValue === 1,
            ];
        }

        foreach ([1, 0] as $custom) {
            yield "null/custom=$custom" => [
                <<<XML
                    <conditionalFormatting sqref="{$coordinate}">
                        <cfRule type="iconSet" priority="1">
                            <iconSet custom="$custom">
                                {$cfvos}
                            </iconSet>
                        </cfRule>
                    </conditionalFormatting>
                    XML,
                null,
                null,
                null,
                $custom === 1,
            ];
        }
    }
}
