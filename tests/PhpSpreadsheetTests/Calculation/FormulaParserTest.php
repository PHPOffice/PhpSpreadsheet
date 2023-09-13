<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcException;
use PhpOffice\PhpSpreadsheet\Calculation\FormulaParser;
use PHPUnit\Framework\TestCase;

class FormulaParserTest extends TestCase
{
    public function testNullFormula(): void
    {
        $this->expectException(CalcException::class);
        $this->expectExceptionMessage('Invalid parameter passed: formula');
        new FormulaParser(null);
    }

    public function testInvalidTokenId(): void
    {
        $this->expectException(CalcException::class);
        $this->expectExceptionMessage('Token with id 1 does not exist.');
        $result = new FormulaParser('=2');
        $result->getToken(1);
    }

    public function testNoFormula(): void
    {
        $result = new FormulaParser('');
        self::assertSame(0, $result->getTokenCount());
    }

    /**
     * @dataProvider providerFormulaParser
     */
    public function testFormulaParser(string $formula, array $expectedResult): void
    {
        $formula = "=$formula";
        $result = new FormulaParser($formula);
        self::assertSame($formula, $result->getFormula());
        self::assertSame(count($expectedResult), $result->getTokenCount());
        $tokens = $result->getTokens();
        $token0 = $result->getToken(0);
        self::assertSame($tokens[0], $token0);
        $idx = -1;
        foreach ($expectedResult as $resultArray) {
            ++$idx;
            self::assertSame($resultArray[0], $tokens[$idx]->getValue());
            self::assertSame($resultArray[1], $tokens[$idx]->getTokenType());
            self::assertSame($resultArray[2], $tokens[$idx]->getTokenSubType());
        }
    }

    public static function providerFormulaParser(): array
    {
        return [
            ['5%*(2+(-3))+A3',
                [
                    ['5', 'Operand', 'Number'],
                    ['%', 'OperatorPostfix', 'Nothing'],
                    ['*', 'OperatorInfix', 'Math'],
                    ['', 'Subexpression', 'Start'],
                    ['2', 'Operand', 'Number'],
                    ['+', 'OperatorInfix', 'Math'],
                    ['', 'Subexpression', 'Start'],
                    ['-', 'OperatorPrefix', 'Nothing'],
                    ['3', 'Operand', 'Number'],
                    ['', 'Subexpression', 'Stop'],
                    ['', 'Subexpression', 'Stop'],
                    ['+', 'OperatorInfix', 'Math'],
                    ['A3', 'Operand', 'Range'],
                ],
            ],
            ['"hello"  & "goodbye"',
                [
                    ['hello', 'Operand', 'Text'],
                    ['&', 'OperatorInfix', 'Concatenation'],
                    ['goodbye', 'Operand', 'Text'],
                ],
            ],
            ['+1.23E5',
                [
                    ['1.23E5', 'Operand', 'Number'],
                ],
            ],
            ['#DIV/0!',
                [
                    ['#DIV/0!', 'Operand', 'Error'],
                ],
            ],
            ['"HE""LLO"',
                [
                    ['HE"LLO', 'Operand', 'Text'],
                ],
            ],
            ['MINVERSE({3,1;4,2})',
                [
                    ['MINVERSE', 'Function', 'Start'],
                    ['ARRAY', 'Function', 'Start'],
                    ['ARRAYROW', 'Function', 'Start'],
                    ['3', 'Operand', 'Number'],
                    [',', 'OperatorInfix', 'Union'],
                    ['1', 'Operand', 'Number'],
                    ['', 'Function', 'Stop'],
                    [',', 'Argument', 'Nothing'],
                    ['ARRAYROW', 'Function', 'Start'],
                    ['4', 'Operand', 'Number'],
                    [',', 'OperatorInfix', 'Union'],
                    ['2', 'Operand', 'Number'],
                    ['', 'Function', 'Stop'],
                    ['', 'Function', 'Stop'],
                    ['', 'Function', 'Stop'],
                ],
            ],
            ['[1,1]*5',
                [
                    ['[1,1]', 'Operand', 'Range'],
                    ['*', 'OperatorInfix', 'Math'],
                    ['5', 'Operand', 'Number'],
                ],
            ],
            ['IF(A1>=0,2,3)',
                [
                    ['IF', 'Function', 'Start'],
                    ['A1', 'Operand', 'Range'],
                    ['>=', 'OperatorInfix', 'Logical'],
                    ['0', 'Operand', 'Number'],
                    [',', 'OperatorInfix', 'Union'],
                    ['2', 'Operand', 'Number'],
                    [',', 'OperatorInfix', 'Union'],
                    ['3', 'Operand', 'Number'],
                    ['', 'Function', 'Stop'],
                ],
            ],
            ["'Worksheet'!A1:A3",
                [
                    ['Worksheet!A1:A3', 'Operand', 'Range'],
                ],
            ],
            ["'Worksh''eet'!A1:A3",
                [
                    ['Worksh\'eet!A1:A3', 'Operand', 'Range'],
                ],
            ],
            ['true',
                [
                    ['true', 'Operand', 'Logical'],
                ],
            ],
        ];
    }
}
