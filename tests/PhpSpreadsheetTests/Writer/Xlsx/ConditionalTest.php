<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ConditionalTest extends AbstractFunctional
{
    /**
     * @var string
     */
    protected $cellRange;

    /**
     * @var Style
     */
    protected $style;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cellRange = 'C3:E5';
        $this->style = new Style();
        $this->style->applyFromArray([
            'fill' => [
                'color' => ['argb' => 'FFFFC000'],
                'fillType' => Fill::FILL_SOLID,
            ],
        ]);
    }

    public function testWriteSimpleCellConditionalFromWizard(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $wizard = new Wizard\CellValue($this->cellRange);
        $wizard->greaterThan(5);
        $condition = $wizard->getConditional();
        $condition->setStyle($this->style);
        $worksheet->setConditionalStyles($this->cellRange, [$condition]);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($worksheet, []);

        $expected = <<<XML
            <conditionalFormatting sqref="C3:E5"><cfRule type="cellIs" dxfId="" priority="1" operator="greaterThan"><formula>5</formula></cfRule></conditionalFormatting>
            XML;
        self::assertStringContainsString($expected, $data);
    }

    public function testWriteBetweenCellConditionalFromWizard(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $wizard = new Wizard\CellValue($this->cellRange);
        $wizard->between(-5)->and(5);
        $condition = $wizard->getConditional();
        $condition->setStyle($this->style);
        $worksheet->setConditionalStyles($this->cellRange, [$condition]);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($worksheet, []);

        $expected = <<<XML
            <conditionalFormatting sqref="C3:E5"><cfRule type="cellIs" dxfId="" priority="1" operator="between"><formula>-5</formula><formula>5</formula></cfRule></conditionalFormatting>
            XML;
        self::assertStringContainsString($expected, $data);
    }

    public function testWriteTextConditionalFromWizard(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $wizard = new Wizard\TextValue($this->cellRange);
        $wizard->contains('PHP');
        $condition = $wizard->getConditional();
        $condition->setStyle($this->style);
        $worksheet->setConditionalStyles($this->cellRange, [$condition]);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($worksheet, []);

        $expected = <<<XML
            <conditionalFormatting sqref="C3:E5"><cfRule type="containsText" dxfId="" priority="1" operator="containsText" text="PHP"><formula>NOT(ISERROR(SEARCH(&quot;PHP&quot;,C3)))</formula></cfRule></conditionalFormatting>
            XML;
        self::assertStringContainsString($expected, $data);
    }

    /**
     * @dataProvider textConditionalsProvider
     */
    public function testWriteTextConditionals(string $conditionType, string $operatorType, string $expected): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $condition = new Conditional();
        $condition->setConditionType($conditionType);
        $condition->setOperatorType($operatorType);
        $condition->setText('PHP');
        $condition->setStyle($this->style);
        $worksheet->setConditionalStyles($this->cellRange, [$condition]);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($worksheet, []);

        self::assertStringContainsString($expected, $data);
    }

    public static function textConditionalsProvider(): array
    {
        return [
            'Contains' => [
                Conditional::CONDITION_CONTAINSTEXT,
                Conditional::OPERATOR_CONTAINSTEXT,
                <<<XML
                    <conditionalFormatting sqref="C3:E5"><cfRule type="containsText" dxfId="" priority="1" operator="containsText" text="PHP"><formula>NOT(ISERROR(SEARCH(&quot;PHP&quot;,C3)))</formula></cfRule></conditionalFormatting>
                    XML
            ],
            'Not Contains' => [
                Conditional::CONDITION_NOTCONTAINSTEXT,
                Conditional::OPERATOR_NOTCONTAINS,
                <<<XML
                    <conditionalFormatting sqref="C3:E5"><cfRule type="notContainsText" dxfId="" priority="1" operator="notContains" text="PHP"><formula>ISERROR(SEARCH(&quot;PHP&quot;,C3))</formula></cfRule></conditionalFormatting>
                    XML
            ],
            'Begins With' => [
                Conditional::CONDITION_BEGINSWITH,
                Conditional::OPERATOR_BEGINSWITH,
                <<<XML
                    <conditionalFormatting sqref="C3:E5"><cfRule type="beginsWith" dxfId="" priority="1" operator="beginsWith" text="PHP"><formula>LEFT(C3,LEN(&quot;PHP&quot;))=&quot;PHP&quot;</formula></cfRule></conditionalFormatting>
                    XML
            ],
            'Ends With' => [
                Conditional::CONDITION_ENDSWITH,
                Conditional::OPERATOR_ENDSWITH,
                <<<XML
                    <conditionalFormatting sqref="C3:E5"><cfRule type="endsWith" dxfId="" priority="1" operator="endsWith" text="PHP"><formula>RIGHT(C3,LEN(&quot;PHP&quot;))=&quot;PHP&quot;</formula></cfRule></conditionalFormatting>
                    XML
            ],
        ];
    }

    public function testWriteDateConditionalFromWizard(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $wizard = new Wizard\DateValue($this->cellRange);
        $wizard->today();
        $condition = $wizard->getConditional();
        $condition->setStyle($this->style);
        $worksheet->setConditionalStyles($this->cellRange, [$condition]);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($worksheet, []);

        $expected = <<<XML
            <conditionalFormatting sqref="C3:E5"><cfRule type="timePeriod" dxfId="" priority="1" timePeriod="today"><formula>FLOOR(C3,1)=TODAY()</formula></cfRule></conditionalFormatting>
            XML;
        self::assertStringContainsString($expected, $data);
    }

    /**
     * @dataProvider dateConditionalsProvider
     */
    public function testWriteDateConditionals(string $timePeriod, string $expected): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $condition = new Conditional();
        $condition->setConditionType(Conditional::CONDITION_TIMEPERIOD);
        $condition->setOperatorType($timePeriod);
        $condition->setText($timePeriod);
        $condition->setStyle($this->style);
        $worksheet->setConditionalStyles($this->cellRange, [$condition]);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($worksheet, []);

        self::assertStringContainsString($expected, $data);
    }

    public static function dateConditionalsProvider(): array
    {
        return [
            'Yesterday' => [
                Conditional::TIMEPERIOD_YESTERDAY,
                <<<XML
                    <conditionalFormatting sqref="C3:E5"><cfRule type="timePeriod" dxfId="" priority="1" timePeriod="yesterday"><formula>FLOOR(C3)=TODAY()-1</formula></cfRule></conditionalFormatting>
                    XML
            ],
            'Today' => [
                Conditional::TIMEPERIOD_TODAY,
                <<<XML
                    <conditionalFormatting sqref="C3:E5"><cfRule type="timePeriod" dxfId="" priority="1" timePeriod="today"><formula>FLOOR(C3)=TODAY()</formula></cfRule></conditionalFormatting>
                    XML
            ],
            'Tomorrow' => [
                Conditional::TIMEPERIOD_TOMORROW,
                <<<XML
                    <conditionalFormatting sqref="C3:E5"><cfRule type="timePeriod" dxfId="" priority="1" timePeriod="tomorrow"><formula>FLOOR(C3)=TODAY()+1</formula></cfRule></conditionalFormatting>
                    XML
            ],
            'Last 7 Days' => [
                Conditional::TIMEPERIOD_LAST_7_DAYS,
                <<<XML
                    <conditionalFormatting sqref="C3:E5"><cfRule type="timePeriod" dxfId="" priority="1" timePeriod="last7Days"><formula>AND(TODAY()-FLOOR(C3,1)&lt;=6,FLOOR(C3,1)&lt;=TODAY())</formula></cfRule></conditionalFormatting>
                    XML
            ],
            'Last Week' => [
                Conditional::TIMEPERIOD_LAST_WEEK,
                <<<XML
                    <conditionalFormatting sqref="C3:E5"><cfRule type="timePeriod" dxfId="" priority="1" timePeriod="lastWeek"><formula>AND(TODAY()-ROUNDDOWN(C3,0)&gt;=(WEEKDAY(TODAY())),TODAY()-ROUNDDOWN(C3,0)&lt;(WEEKDAY(TODAY())+7))</formula></cfRule></conditionalFormatting>
                    XML
            ],
            'This Week' => [
                Conditional::TIMEPERIOD_THIS_WEEK,
                <<<XML
                    <conditionalFormatting sqref="C3:E5"><cfRule type="timePeriod" dxfId="" priority="1" timePeriod="thisWeek"><formula>AND(TODAY()-ROUNDDOWN(C3,0)&lt;=WEEKDAY(TODAY())-1,ROUNDDOWN(C3,0)-TODAY()&lt;=7-WEEKDAY(TODAY()))</formula></cfRule></conditionalFormatting>
                    XML
            ],
            'Next Week' => [
                Conditional::TIMEPERIOD_NEXT_WEEK,
                <<<XML
                    <conditionalFormatting sqref="C3:E5"><cfRule type="timePeriod" dxfId="" priority="1" timePeriod="nextWeek"><formula>AND(ROUNDDOWN(C3,0)-TODAY()&gt;(7-WEEKDAY(TODAY())),ROUNDDOWN(C3,0)-TODAY()&lt;(15-WEEKDAY(TODAY())))</formula></cfRule></conditionalFormatting>
                    XML
            ],
            'Last Month' => [
                Conditional::TIMEPERIOD_LAST_MONTH,
                <<<XML
                    <conditionalFormatting sqref="C3:E5"><cfRule type="timePeriod" dxfId="" priority="1" timePeriod="lastMonth"><formula>AND(MONTH(C3)=MONTH(EDATE(TODAY(),0-1)),YEAR(C3)=YEAR(EDATE(TODAY(),0-1)))</formula></cfRule></conditionalFormatting>
                    XML
            ],
            'This Month' => [
                Conditional::TIMEPERIOD_THIS_MONTH,
                <<<XML
                    <conditionalFormatting sqref="C3:E5"><cfRule type="timePeriod" dxfId="" priority="1" timePeriod="thisMonth"><formula>AND(MONTH(C3)=MONTH(TODAY()),YEAR(C3)=YEAR(TODAY()))</formula></cfRule></conditionalFormatting>
                    XML
            ],
            'Next Month' => [
                Conditional::TIMEPERIOD_NEXT_MONTH,
                <<<XML
                    ><conditionalFormatting sqref="C3:E5"><cfRule type="timePeriod" dxfId="" priority="1" timePeriod="nextMonth"><formula>AND(MONTH(C3)=MONTH(EDATE(TODAY(),0+1)),YEAR(C3)=YEAR(EDATE(TODAY(),0+1)))</formula></cfRule></conditionalFormatting>
                    XML
            ],
        ];
    }

    public function testWriteBlankConditionalFromWizard(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $wizard = new Wizard\Blanks($this->cellRange);
        $wizard->isBlank();
        $condition = $wizard->getConditional();
        $condition->setStyle($this->style);
        $worksheet->setConditionalStyles($this->cellRange, [$condition]);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($worksheet, []);

        $expected = <<<XML
            <conditionalFormatting sqref="C3:E5"><cfRule type="containsBlanks" dxfId="" priority="1"><formula>LEN(TRIM(C3))=0</formula></cfRule></conditionalFormatting>
            XML;
        self::assertStringContainsString($expected, $data);
    }

    public function testWriteNonBlankConditionalFromWizard(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $wizard = new Wizard\Blanks($this->cellRange);
        $wizard->notBlank();
        $condition = $wizard->getConditional();
        $condition->setStyle($this->style);
        $worksheet->setConditionalStyles($this->cellRange, [$condition]);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($worksheet, []);

        $expected = <<<XML
            <conditionalFormatting sqref="C3:E5"><cfRule type="notContainsBlanks" dxfId="" priority="1"><formula>LEN(TRIM(C3))&gt;0</formula></cfRule></conditionalFormatting>
            XML;
        self::assertStringContainsString($expected, $data);
    }

    /**
     * @dataProvider blanksConditionalsProvider
     */
    public function testWriteBlanksConditionals(string $conditionalType, string $expected): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $condition = new Conditional();
        $condition->setConditionType($conditionalType);
        $condition->setStyle($this->style);
        $worksheet->setConditionalStyles($this->cellRange, [$condition]);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($worksheet, []);

        self::assertStringContainsString($expected, $data);
    }

    public static function blanksConditionalsProvider(): array
    {
        return [
            'Blanks' => [
                Conditional::CONDITION_CONTAINSBLANKS,
                <<<XML
                    <conditionalFormatting sqref="C3:E5"><cfRule type="containsBlanks" dxfId="" priority="1"><formula>LEN(TRIM(C3))=0</formula></cfRule></conditionalFormatting>
                    XML
            ],
            'Not Blanks' => [
                Conditional::CONDITION_NOTCONTAINSBLANKS,
                <<<XML
                    <conditionalFormatting sqref="C3:E5"><cfRule type="notContainsBlanks" dxfId="" priority="1"><formula>LEN(TRIM(C3))&gt;0</formula></cfRule></conditionalFormatting>
                    XML
            ],
        ];
    }

    public function testWriteNonErrorConditionalFromWizard(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $wizard = new Wizard\Errors($this->cellRange);
        $wizard->notError();
        $condition = $wizard->getConditional();
        $condition->setStyle($this->style);
        $worksheet->setConditionalStyles($this->cellRange, [$condition]);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($worksheet, []);

        $expected = <<<XML
            <conditionalFormatting sqref="C3:E5"><cfRule type="notContainsErrors" dxfId="" priority="1"><formula>NOT(ISERROR(C3))</formula></cfRule></conditionalFormatting>
            XML;
        self::assertStringContainsString($expected, $data);
    }

    public function testWriteErrorConditionalFromWizard(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $wizard = new Wizard\Errors($this->cellRange);
        $wizard->isError();
        $condition = $wizard->getConditional();
        $condition->setStyle($this->style);
        $worksheet->setConditionalStyles($this->cellRange, [$condition]);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($worksheet, []);

        $expected = <<<XML
            <conditionalFormatting sqref="C3:E5"><cfRule type="containsErrors" dxfId="" priority="1"><formula>ISERROR(C3)</formula></cfRule></conditionalFormatting>
            XML;
        self::assertStringContainsString($expected, $data);
    }

    /**
     * @dataProvider errorsConditionalsProvider
     */
    public function testWriteErrorsConditionals(string $conditionalType, string $expected): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $condition = new Conditional();
        $condition->setConditionType($conditionalType);
        $condition->setStyle($this->style);
        $worksheet->setConditionalStyles($this->cellRange, [$condition]);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($worksheet, []);

        self::assertStringContainsString($expected, $data);
    }

    public static function errorsConditionalsProvider(): array
    {
        return [
            'Errors' => [
                Conditional::CONDITION_CONTAINSERRORS,
                <<<XML
                    <conditionalFormatting sqref="C3:E5"><cfRule type="containsErrors" dxfId="" priority="1"><formula>ISERROR(C3)</formula></cfRule></conditionalFormatting>
                    XML
            ],
            'Not Errors' => [
                Conditional::CONDITION_NOTCONTAINSERRORS,
                <<<XML
                    <conditionalFormatting sqref="C3:E5"><cfRule type="notContainsErrors" dxfId="" priority="1"><formula>NOT(ISERROR(C3))</formula></cfRule></conditionalFormatting>
                    XML
            ],
        ];
    }

    public function testWriteUniqueConditionalFromWizard(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $wizard = new Wizard\Duplicates($this->cellRange);
        $wizard->unique();
        $condition = $wizard->getConditional();
        $condition->setStyle($this->style);
        $worksheet->setConditionalStyles($this->cellRange, [$condition]);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($worksheet, []);

        $expected = <<<XML
            <conditionalFormatting sqref="C3:E5"><cfRule type="uniqueValues" dxfId="" priority="1"/></conditionalFormatting>
            XML;
        self::assertStringContainsString($expected, $data);
    }

    public function testWriteDuplicateConditionalFromWizard(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $wizard = new Wizard\Duplicates($this->cellRange);
        $wizard->duplicates();
        $condition = $wizard->getConditional();
        $condition->setStyle($this->style);
        $worksheet->setConditionalStyles($this->cellRange, [$condition]);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($worksheet, []);

        $expected = <<<XML
            <conditionalFormatting sqref="C3:E5"><cfRule type="duplicateValues" dxfId="" priority="1"/></conditionalFormatting>
            XML;
        self::assertStringContainsString($expected, $data);
    }

    /**
     * @dataProvider duplicatesConditionalsProvider
     */
    public function testWriteDuplicatesConditionals(string $conditionalType, string $expected): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $condition = new Conditional();
        $condition->setConditionType($conditionalType);
        $condition->setStyle($this->style);
        $worksheet->setConditionalStyles($this->cellRange, [$condition]);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($worksheet, []);

        self::assertStringContainsString($expected, $data);
    }

    public static function duplicatesConditionalsProvider(): array
    {
        return [
            'Duplicates' => [
                Conditional::CONDITION_DUPLICATES,
                <<<XML
                    <conditionalFormatting sqref="C3:E5"><cfRule type="duplicateValues" dxfId="" priority="1"/></conditionalFormatting>
                    XML
            ],
            'Unique' => [
                Conditional::CONDITION_UNIQUE,
                <<<XML
                    <conditionalFormatting sqref="C3:E5"><cfRule type="uniqueValues" dxfId="" priority="1"/></conditionalFormatting>
                    XML
            ],
        ];
    }

    public function testWriteExpressionConditionalFromWizard(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $wizard = new Wizard\Expression($this->cellRange);
        $wizard->expression('=ISODD(A1)');
        $condition = $wizard->getConditional();
        $condition->setStyle($this->style);
        $worksheet->setConditionalStyles($this->cellRange, [$condition]);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($worksheet, []);

        $expected = <<<XML
            <conditionalFormatting sqref="C3:E5"><cfRule type="expression" dxfId="" priority="1"><formula>ISODD(C3)</formula></cfRule></conditionalFormatting>
            XML;
        self::assertStringContainsString($expected, $data);
    }

    /**
     * @dataProvider expressionsConditionalsProvider
     */
    public function testWriteExpressionConditionals(string $expression, string $expected): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $condition = new Conditional();
        $condition->setConditionType(Conditional::CONDITION_EXPRESSION);
        $condition->setStyle($this->style);
        $condition->setConditions([$expression]);
        $worksheet->setConditionalStyles($this->cellRange, [$condition]);

        $writer = new Xlsx($spreadsheet);
        $writerWorksheet = new Xlsx\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($worksheet, []);

        self::assertStringContainsString($expected, $data);
    }

    public static function expressionsConditionalsProvider(): array
    {
        return [
            'Odd' => [
                'ISODD(C3)',
                <<<XML
                    <conditionalFormatting sqref="C3:E5"><cfRule type="expression" dxfId="" priority="1"><formula>ISODD(C3)</formula></cfRule></conditionalFormatting>
                    XML
            ],
            'Even' => [
                'ISEVEN(C3)',
                <<<XML
                    <conditionalFormatting sqref="C3:E5"><cfRule type="expression" dxfId="" priority="1"><formula>ISEVEN(C3)</formula></cfRule></conditionalFormatting>
                    XML
            ],
        ];
    }
}
