<?php

namespace PhpOffice\PhpSpreadsheetTests\Style\ConditionalFormatting;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\CellMatcher;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\Framework\TestCase;

class CellMatcherTest extends TestCase
{
    /**
     * @var Spreadsheet
     */
    protected $spreadsheet;

    protected function setUp(): void
    {
        $this->spreadsheet = $this->buildTestSpreadsheet();
        $writer = new Xlsx($this->spreadsheet);
        $writer->save(getcwd() . '/testSpreadsheet.xlsx');
//        $filename = 'tests/data/Style/ConditionalFormatting/CellMatcher.xlsx';
//        $reader = IOFactory::createReader('Xlsx');
//        $this->spreadsheet = $reader->load($filename);
    }

    /**
     * @dataProvider basicCellIsComparisonDataProvider
     */
    public function testBasicCellIsComparison(string $sheetname, string $cellAddress, array $expectedMatches): void
    {
        $worksheet = $this->spreadsheet->getSheetByName($sheetname);
        $cell = $worksheet->getCell($cellAddress);

        $cfRange = $worksheet->getConditionalRange($cell->getCoordinate());
        $cfStyles = $worksheet->getConditionalStyles($cell->getCoordinate());

        $matcher = new CellMatcher($cell, $cfRange);

        foreach ($cfStyles as $cfIndex => $cfStyle) {
            $match = $matcher->evaluateConditional($cfStyle);
            self::assertSame($expectedMatches[$cfIndex], $match);
        }
    }

    public function basicCellIsComparisonDataProvider(): array
    {
        return [
            ['cellIs Comparison', 'A1', [false, false, true]],
            ['cellIs Comparison', 'C2', [false, true, false]],
            ['cellIs Comparison', 'E5', [true, false, false]],
            ['cellIs Comparison', 'A12', [false, false, true]],
            ['cellIs Comparison', 'C12', [false, true, false]],
            ['cellIs Comparison', 'E12', [true, false, false]],
            ['cellIs Comparison', 'A20', [true]],
            ['cellIs Comparison', 'B20', [false]],
            ['cellIs Comparison', 'C20', [true]],
        ];
    }

    /**
     * @dataProvider rangeCellIsComparisonDataProvider
     */
    public function testRangeCellIsComparison(string $sheetname, string $cellAddress, bool $expectedMatch): void
    {
        $worksheet = $this->spreadsheet->getSheetByName($sheetname);
        $cell = $worksheet->getCell($cellAddress);

        $cfRange = $worksheet->getConditionalRange($cell->getCoordinate());
        $cfStyle = $worksheet->getConditionalStyles($cell->getCoordinate());

        $matcher = new CellMatcher($cell, $cfRange);

        $match = $matcher->evaluateConditional($cfStyle[0]);
        self::assertSame($expectedMatch, $match);
    }

    public function rangeCellIsComparisonDataProvider(): array
    {
        return [
            ['cellIs Range Comparison', 'A1', false],
            ['cellIs Range Comparison', 'A2', true],
            ['cellIs Range Comparison', 'A3', true],
            ['cellIs Range Comparison', 'A4', true],
            ['cellIs Range Comparison', 'A5', false],
            ['cellIs Range Comparison', 'A10', false],
            ['cellIs Range Comparison', 'A11', false],
            ['cellIs Range Comparison', 'A12', true],
            ['cellIs Range Comparison', 'A16', true],
            ['cellIs Range Comparison', 'A17', true],
        ];
    }

    /**
     * @dataProvider cellIsExpressionMultipleDataProvider
     */
    public function testCellIsMultipleExpression(string $sheetname, string $cellAddress, array $expectedMatches): void
    {
        $worksheet = $this->spreadsheet->getSheetByName($sheetname);
        $cell = $worksheet->getCell($cellAddress);

        $cfRange = $worksheet->getConditionalRange($cell->getCoordinate());
        $cfStyles = $worksheet->getConditionalStyles($cell->getCoordinate());

        $matcher = new CellMatcher($cell, $cfRange);

        foreach ($cfStyles as $cfIndex => $cfStyle) {
            $match = $matcher->evaluateConditional($cfStyle);
            self::assertSame($expectedMatches[$cfIndex], $match);
        }
    }

    public function cellIsExpressionMultipleDataProvider(): array
    {
        return [
            ['cellIs Expression', 'A1', [false, true]],
            ['cellIs Expression', 'A2', [true, false]],
            ['cellIs Expression', 'C2', [true, false]],
            ['cellIs Expression', 'E3', [false, true]],
        ];
    }

    /**
     * @dataProvider cellIsExpressionDataProvider
     */
    public function testCellIsExpression(string $sheetname, string $cellAddress, bool $expectedMatch): void
    {
        $worksheet = $this->spreadsheet->getSheetByName($sheetname);
        $cell = $worksheet->getCell($cellAddress);

        $cfRange = $worksheet->getConditionalRange($cell->getCoordinate());
        $cfStyle = $worksheet->getConditionalStyles($cell->getCoordinate());

        $matcher = new CellMatcher($cell, $cfRange);

        $match = $matcher->evaluateConditional($cfStyle[0]);
        self::assertSame($expectedMatch, $match);
    }

    public function cellIsExpressionDataProvider(): array
    {
        return [
            ['cellIs Expression', 'A11', false],
            ['cellIs Expression', 'B11', false],
            ['cellIs Expression', 'C11', false],
            ['cellIs Expression', 'D11', false],
            ['cellIs Expression', 'B12', true],
            ['cellIs Expression', 'C12', true],
            ['cellIs Expression', 'B14', true],
            ['cellIs Expression', 'B15', true],
            ['cellIs Expression', 'C16', false],
            ['cellIs Expression', 'A21', false],
            ['cellIs Expression', 'B21', false],
            ['cellIs Expression', 'C21', false],
            ['cellIs Expression', 'D21', false],
            ['cellIs Expression', 'B22', true],
            ['cellIs Expression', 'C22', true],
            ['cellIs Expression', 'B24', false],
            ['cellIs Expression', 'B25', true],
            ['cellIs Expression', 'C26', false],
        ];
    }

    /**
     * @dataProvider textExpressionsDataProvider
     */
    public function testTextExpressions(string $sheetname, string $cellAddress, bool $expectedMatch): void
    {
        $worksheet = $this->spreadsheet->getSheetByName($sheetname);
        var_dump(array_keys($worksheet->getConditionalStylesCollection()));
        $cell = $worksheet->getCell($cellAddress);

        $cfRange = $worksheet->getConditionalRange($cell->getCoordinate());
        $cfStyle = $worksheet->getConditionalStyles($cell->getCoordinate());

        $matcher = new CellMatcher($cell, $cfRange);

        $match = $matcher->evaluateConditional($cfStyle[0]);
        self::assertSame($expectedMatch, $match);
    }

    public function textExpressionsDataProvider(): array
    {
        return [
            // Text Begins With
            ['Text Expressions', 'A1', true],
            ['Text Expressions', 'B1', false],
            ['Text Expressions', 'A2', false],
            ['Text Expressions', 'B2', false],
            ['Text Expressions', 'A3', false],
            ['Text Expressions', 'B3', true],
            // Text Ends With
            ['Text Expressions', 'A6', false],
            ['Text Expressions', 'B6', false],
            ['Text Expressions', 'A7', true],
            ['Text Expressions', 'B7', true],
            ['Text Expressions', 'A8', false],
            ['Text Expressions', 'B8', true],
            // Text Contains
            ['Text Expressions', 'A11', true],
            ['Text Expressions', 'B11', false],
            ['Text Expressions', 'A12', true],
            ['Text Expressions', 'B12', true],
            ['Text Expressions', 'A13', false],
            ['Text Expressions', 'B13', true],
            // Text Doesn't Contain
            ['Text Expressions', 'A16', true],
            ['Text Expressions', 'B16', true],
            ['Text Expressions', 'A17', true],
            ['Text Expressions', 'B17', true],
            ['Text Expressions', 'A18', false],
            ['Text Expressions', 'B18', true],
        ];
    }

    /**
     * @dataProvider textBlanksDataProvider
     */
    public function testTextBlankExpressions(string $sheetname, string $cellAddress, array $expectedMatches): void
    {
        $worksheet = $this->spreadsheet->getSheetByName($sheetname);
        var_dump(array_keys($worksheet->getConditionalStylesCollection()));
        $cell = $worksheet->getCell($cellAddress);

        var_dump($worksheet->getTitle());

        $cfRange = $worksheet->getConditionalRange($cell->getCoordinate());
        $cfStyles = $worksheet->getConditionalStyles($cell->getCoordinate());

        $matcher = new CellMatcher($cell, $cfRange);

        foreach ($cfStyles as $cfIndex => $cfStyle) {
            $match = $matcher->evaluateConditional($cfStyle);
            self::assertSame($expectedMatches[$cfIndex], $match);
        }
    }

    public function textBlanksDataProvider(): array
    {
        return [
            ['Blank Expressions', 'A1', [true, false]],
            ['Blank Expressions', 'A2', [false, true]],
            ['Blank Expressions', 'B1', [false, true]],
            ['Blank Expressions', 'B2', [true, false]],
        ];
    }

    /**
     * @dataProvider textErrorDataProvider
     */
    public function testTextErrorExpressions(string $sheetname, string $cellAddress, array $expectedMatches): void
    {
        $worksheet = $this->spreadsheet->getSheetByName($sheetname);
        var_dump(array_keys($worksheet->getConditionalStylesCollection()));
        $cell = $worksheet->getCell($cellAddress);

        $cfRange = $worksheet->getConditionalRange($cell->getCoordinate());
        $cfStyles = $worksheet->getConditionalStyles($cell->getCoordinate());

        $matcher = new CellMatcher($cell, $cfRange);

        foreach ($cfStyles as $cfIndex => $cfStyle) {
            $match = $matcher->evaluateConditional($cfStyle);
            self::assertSame($expectedMatches[$cfIndex], $match);
        }
    }

    public function textErrorDataProvider(): array
    {
        return [
            ['Error Expressions', 'C1', [false, true]],
            ['Error Expressions', 'C3', [true, false]],
        ];
    }

    protected function buildTestSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        $worksheets = $this->worksheets();
        foreach ($worksheets as $worksheet) {
            $this->buildTestWorksheet($spreadsheet, $worksheet);
        }

        return $spreadsheet;
    }

    protected function buildTestWorksheet(Spreadsheet $spreadsheet, array $worksheet): void
    {
        $sheet = $spreadsheet->addSheet(new Worksheet($spreadsheet, $worksheet['Title']));

        $dataSets = call_user_func($worksheet['Data']);
        foreach ($dataSets as $cellReference => $dataSet) {
            $sheet->fromArray($dataSet, null, $cellReference, true);
        }

        $ruleSet = call_user_func($worksheet['Rules']);
        foreach ($ruleSet as $ruleRange => $rules) {
            $conditionalStyles = [];
            foreach ($rules as $rule) {
                $conditional = new Conditional();
                $conditional->setConditionType($rule['Condition']);
                if ($rule['Operator'] !== Conditional::OPERATOR_NONE) {
                    $conditional->setOperatorType($rule['Operator']);
                }
                if (!empty($rule['Operands'])) {
                    $conditional->setConditions($rule['Operands']);
                }
                $conditional->setStyle($rule['Style']);
                $conditionalStyles[] = $conditional;
            }

            $sheet->getStyle($ruleRange)->setConditionalStyles($conditionalStyles);
        }
    }

    protected function worksheets()
    {
        return [
            [
                'Title' => 'cellIs Comparison',
                'Data' => [$this, 'cellIsComparisonData'],
                'Rules' => [$this, 'cellIsComparisonRules'],
            ],
            [
                'Title' => 'cellIs Range Comparison',
                'Data' => [$this, 'cellIsRangeComparisonData'],
                'Rules' => [$this, 'cellIsRangeComparisonRules'],
            ],
            [
                'Title' => 'cellIs Expression',
                'Data' => [$this, 'cellExpressionData'],
                'Rules' => [$this, 'cellExpressionRules'],
            ],
            [
                'Title' => 'Text Expressions',
                'Data' => [$this, 'textExpressionData'],
                'Rules' => [$this, 'textExpressionRules'],
            ],
            [
                'Title' => 'Blank Expressions',
                'Data' => [$this, 'blankExpressionData'],
                'Rules' => [$this, 'blankExpressionRules'],
            ],
            [
                'Title' => 'Error Expressions',
                'Data' => [$this, 'errorExpressionData'],
                'Rules' => [$this, 'errorExpressionRules'],
            ],
        ];
    }

    protected function style($fontColor, $fillColor): Style
    {
        $style = new Style();
        $style->getFont()->getColor()->setARGB($fontColor);
        $style->getFill()->setFillType(Fill::FILL_SOLID);
        $style->getFill()->getEndColor()->setARGB($fillColor);

        return $style;
    }

    protected function styleGreen(): Style
    {
        return $this->style(Color::COLOR_DARKGREEN, Color::COLOR_GREEN);
    }

    protected function styleRed(): Style
    {
        return $this->style(Color::COLOR_DARKRED, Color::COLOR_RED);
    }

    protected function styleBlue(): Style
    {
        return $this->style(Color::COLOR_DARKBLUE, Color::COLOR_BLUE);
    }

    protected function styleYellow(): Style
    {
        return $this->style(Color::COLOR_DARKBLUE, Color::COLOR_YELLOW);
    }

    protected function cellIsComparisonData()
    {
        return [
            'A1' => $this->comparisonSimpleGrid(),
            'A10' => $this->comparisonSimpleGrid(),
            'G10' => [1],
            'A20' => [
                ['HELLO', 'WORLD', '="HE"&"LLO"'],
            ],
        ];
    }

    protected function cellIsComparisonRules()
    {
        return [
            'A1:E5' => [
                [
                    'Condition' => Conditional::CONDITION_CELLIS,
                    'Operator' => Conditional::OPERATOR_GREATERTHAN,
                    'Operands' => [0],
                    'Style' => $this->styleGreen(),
                ],
                [
                    'Condition' => Conditional::CONDITION_CELLIS,
                    'Operator' => Conditional::OPERATOR_EQUAL,
                    'Operands' => [0],
                    'Style' => $this->styleBlue(),
                ],
                [
                    'Condition' => Conditional::CONDITION_CELLIS,
                    'Operator' => Conditional::OPERATOR_LESSTHAN,
                    'Operands' => [0],
                    'Style' => $this->styleRed(),
                ],
            ],
            'A10:E14' => [
                [
                    'Condition' => Conditional::CONDITION_CELLIS,
                    'Operator' => Conditional::OPERATOR_GREATERTHAN,
                    'Operands' => ['$G$10'],
                    'Style' => $this->styleGreen(),
                ],
                [
                    'Condition' => Conditional::CONDITION_CELLIS,
                    'Operator' => Conditional::OPERATOR_EQUAL,
                    'Operands' => ['$G$10'],
                    'Style' => $this->styleBlue(),
                ],
                [
                    'Condition' => Conditional::CONDITION_CELLIS,
                    'Operator' => Conditional::OPERATOR_LESSTHAN,
                    'Operands' => ['$G$10'],
                    'Style' => $this->styleRed(),
                ],
            ],
            'A20:C20' => [
                [
                    'Condition' => Conditional::CONDITION_CELLIS,
                    'Operator' => Conditional::OPERATOR_EQUAL,
                    'Operands' => ['"HELLO"'],
                    'Style' => $this->styleBlue(),
                ],
            ],
        ];
    }

    protected function cellIsRangeComparisonData()
    {
        return [
            'A1' => [
                [-2],
                [-1],
                [0],
                [1],
                [2],
            ],
            'A10' => [
                [2, 7, 6],
                [9, 5, 1],
                [4, 3, 8],
            ],
            'A16' => [
                [5, 3, 7],
                [5, 7, 3],
            ],
        ];
    }

    protected function cellIsRangeComparisonRules()
    {
        return [
            'A1:A5' => [
                [
                    'Condition' => Conditional::CONDITION_CELLIS,
                    'Operator' => Conditional::OPERATOR_BETWEEN,
                    'Operands' => [-1, 1],
                    'Style' => $this->styleGreen(),
                ],
            ],
            'A10:A12' => [
                [
                    'Condition' => Conditional::CONDITION_CELLIS,
                    'Operator' => Conditional::OPERATOR_BETWEEN,
                    'Operands' => ['$B10', '$C10'],
                    'Style' => $this->styleGreen(),
                ],
            ],
            'A16:A18' => [
                [
                    'Condition' => Conditional::CONDITION_CELLIS,
                    'Operator' => Conditional::OPERATOR_BETWEEN,
                    'Operands' => ['$B16', '$C16'],
                    'Style' => $this->styleGreen(),
                ],
            ],
        ];
    }

    protected function cellExpressionData()
    {
        return [
            'A1' => $this->comparisonSimpleGrid(),
            'A10' => $this->salesGrid(),
            'A20' => $this->salesGrid(),
        ];
    }

    protected function cellExpressionRules()
    {
        return [
            'A1:E5' => [
                [
                    'Condition' => Conditional::CONDITION_EXPRESSION,
                    'Operator' => Conditional::OPERATOR_NONE,
                    'Operands' => ['ISEVEN(A1)'],
                    'Style' => $this->styleGreen(),
                ],
                [
                    'Condition' => Conditional::CONDITION_EXPRESSION,
                    'Operator' => Conditional::OPERATOR_NONE,
                    'Operands' => ['ISODD(A1)'],
                    'Style' => $this->styleBlue(),
                ],
            ],
            'A11:D16' => [
                [
                    'Condition' => Conditional::CONDITION_EXPRESSION,
                    'Operator' => Conditional::OPERATOR_NONE,
                    'Operands' => ['$C11="USA"'],
                    'Style' => $this->styleBlue(),
                ],
            ],
            'A21:D26' => [
                [
                    'Condition' => Conditional::CONDITION_EXPRESSION,
                    'Operator' => Conditional::OPERATOR_NONE,
                    'Operands' => ['AND($C21="USA",$D21="Q4")'],
                    'Style' => $this->styleBlue(),
                ],
            ],
        ];
    }

    protected function textExpressionData()
    {
        return [
            'A1' => $this->textGrid(),
            'A6' => $this->textGrid(),
            'A11' => $this->textGrid(),
            'A16' => $this->textGrid(),
        ];
    }

    protected function textExpressionRules()
    {
        return [
            'A1:B3' => [
                [
                    'Condition' => Conditional::CONDITION_BEGINSWITH,
                    'Operator' => Conditional::OPERATOR_BEGINSWITH,
                    'Operands' => ['"H"'],
                    'Style' => $this->styleYellow(),
                ],
            ],
            'A6:B8' => [
                [
                    'Condition' => Conditional::CONDITION_ENDSWITH,
                    'Operator' => Conditional::OPERATOR_ENDSWITH,
                    'Operands' => ['"OW"'],
                    'Style' => $this->styleYellow(),
                ],
            ],
            'A11:B13' => [
                [
                    'Condition' => Conditional::CONDITION_CONTAINSTEXT,
                    'Operator' => Conditional::OPERATOR_CONTAINSTEXT,
                    'Operands' => ['"LL"'],
                    'Style' => $this->styleYellow(),
                ],
            ],
            'A16:B18' => [
                [
                    'Condition' => Conditional::CONDITION_NOTCONTAINSTEXT,
                    'Operator' => Conditional::OPERATOR_NOTCONTAINS,
                    'Operands' => ['"EE"'],
                    'Style' => $this->styleYellow(),
                ],
            ],
        ];
    }

    protected function blankExpressionData()
    {
        return [
            'A1' => [
                ['HELLO', null],
                [null, 'WORLD'],
            ],
        ];
    }

    protected function blankExpressionRules()
    {
        return [
            'A1:B2' => [
                [
                    'Condition' => Conditional::CONDITION_NOTCONTAINSBLANKS,
                    'Operator' => Conditional::OPERATOR_NONE,
                    'Style' => $this->styleBlue(),
                ],
                [
                    'Condition' => Conditional::CONDITION_CONTAINSBLANKS,
                    'Operator' => Conditional::OPERATOR_NONE,
                    'Style' => $this->styleGreen(),
                ],
            ],
        ];
    }

    protected function errorExpressionData()
    {
        return [
            'A1' => [
                [5, -2, '=A1/B1'],
                [5, -1, '=A2/B2'],
                [5, 0, '=A3/B3'],
                [5, 1, '=A4/B4'],
                [5, 2, '=A5/B5'],
            ],
        ];
    }

    protected function errorExpressionRules()
    {
        return [
            'C1:C5' => [
                [
                    'Condition' => Conditional::CONDITION_CONTAINSERRORS,
                    'Operator' => Conditional::OPERATOR_NONE,
                    'Style' => $this->styleRed(),
                ],
                [
                    'Condition' => Conditional::CONDITION_NOTCONTAINSERRORS,
                    'Operator' => Conditional::OPERATOR_NONE,
                    'Style' => $this->styleGreen(),
                ],
            ],
        ];
    }

    protected function comparisonSimpleGrid(): array
    {
        return [
            [-3, -2, -1, 0, 1],
            [-2, -1, 0, 1, 2],
            [-1, 0, 1, 2, 3],
            [0, 1, 2, 3, 4],
            [1, 2, 3, 4, 5],
        ];
    }

    protected function salesGrid(): array
    {
        return [
            ['Name', 'Sales', 'Country', 'Quarter'],
            ['Smith', 16753, 'UK', 'Q3'],
            ['Johnson', 14808, 'USA', 'Q4'],
            ['Williams', 10644, 'UK', 'Q2'],
            ['Jones', 1390, 'USA', 'Q3'],
            ['Brown', 4865, 'USA', 'Q4'],
            ['Williams', 12438, 'UK', 'Q2'],
        ];
    }

    protected function textGrid(): array
    {
        return [
            ['HELLO', 'WORLD'],
            ['MELLOW', 'YELLOW'],
            ['SLEEPY', 'HOLLOW'],
        ];
    }
}
