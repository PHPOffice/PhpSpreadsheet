<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class ArrayFormulaTest extends TestCase
{
    /**
     * @var string
     */
    private $compatibilityMode;

    /**
     * @var string
     */
    private $locale;

    protected function setUp(): void
    {
        $this->compatibilityMode = Functions::getCompatibilityMode();
        $calculation = Calculation::getInstance();
        $this->locale = $calculation->getLocale();
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    protected function tearDown(): void
    {
        Functions::setCompatibilityMode($this->compatibilityMode);
        $calculation = Calculation::getInstance();
        $calculation->setLocale($this->locale);
    }

    /**
     * @dataProvider providerArrayFormulae
     *
     * @param mixed $expectedResult
     */
    public function testArrayFormula(string $formula, $expectedResult): void
    {
        $result = Calculation::getInstance()->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerArrayFormulae(): array
    {
        return [
            [
                '=MAX(ABS({-3, 4, -2; 6, -3, -12}))',
                12,
            ],
            [
                '=SUM(SEQUENCE(3,3,0,1))',
                36,
            ],
            [
                '=IFERROR({5/2, 5/0}, MAX(ABS({-2,4,-6})))',
                [[2.5, 6]],
            ],
            [
                '=MAX(IFERROR({5/2, 5/0}, 2.1))',
                2.5,
            ],
            [
                '=IF(FALSE,{1,2,3},{4,5,6})',
                [[4, 5, 6]],
            ],
            [
                '=IFS(FALSE, {1,2,3}, TRUE, {4,5,6})',
                [[4, 5, 6]],
            ],
        ];
    }

    /**
     * @dataProvider providerArrayArithmetic
     *
     * @param mixed $expectedResult
     */
    public function testArrayArithmetic(string $formula, $expectedResult): void
    {
        $result = Calculation::getInstance()->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerArrayArithmetic(): array
    {
        return [
            // Addition
            'Addition: row vector 2 + column vector 2' => [
                '={2,3} + {4;5}',
                [[6, 7], [7, 8]],
            ],
            'Addition: square matrix 2x2 + scalar' => [
                '={1,2;3,4} + 1',
                [[2, 3], [4, 5]],
            ],
            'Addition: square matrix 2x2 + 2x2' => [
                '={1,2;3,4} + {-2,4;-6,8}',
                [[-1, 6], [-3, 12]],
            ],
            'Addition: row vector + row vector' => [
                '={1,2,3} + {4,5,6}',
                [[5, 7, 9]],
            ],
            'Addition: column vector + column vector' => [
                '={1;2;3} + {4;5;6}',
                [[5], [7], [9]],
            ],
            'Addition: row vector + column vector' => [
                '={1,2,3} + {4;5;6}',
                [[5, 6, 7], [6, 7, 8], [7, 8, 9]],
            ],
            'Addition: column vector + row vector' => [
                '={1;2;3} + {4,5,6}',
                [[5, 6, 7], [6, 7, 8], [7, 8, 9]],
            ],
            'Addition: matrix 3x2 + 3x2' => [
                '={1,2,3;4,5,6} + {7,8,9;10,11,12}',
                [[8, 10, 12], [14, 16, 18]],
            ],
            'Addition: matrix 2x3 + 2x3' => [
                '={1,4;2,5;3,6} + {7,10;8,11;9,12}',
                [[8, 14], [10, 16], [12, 18]],
            ],
            'Addition: matrix 3x2 + 2x3' => [
                '={1,2,3;4,5,6} + {7,10;8,11;9,12}',
                [[8, 12], [12, 16]],
            ],
            'Addition: matrix 2x3 + 3x2' => [
                '={7,10;8,11;9,12} + {1,2,3;4,5,6}',
                [[8, 12], [12, 16]],
            ],
            // Subtraction
            'Subtraction: row vector 2 - column vector 2' => [
                '={2,3} - {4;5}',
                [[-2, -1], [-3, -2]],
            ],
            'Subtraction: square matrix 2x2 - scalar' => [
                '={1,2;3,4} - 1',
                [[0, 1], [2, 3]],
            ],
            'Subtraction: square matrix 2x2 - 2x2' => [
                '={1,2;3,4} - {-2,4;-6,8}',
                [[3, -2], [9, -4]],
            ],
            'Subtraction: row vector - row vector' => [
                '={1,2,3} - {4,5,6}',
                [[-3, -3, -3]],
            ],
            'Subtraction: column vector - column vector' => [
                '={1;2;3} - {4;5;6}',
                [[-3], [-3], [-3]],
            ],
            'Subtraction: row vector - column vector' => [
                '={1,2,3} - {4;5;6}',
                [[-3, -2, -1], [-4, -3, -2], [-5, -4, -3]],
            ],
            'Subtraction: column vector - row vector' => [
                '={1;2;3} - {4,5,6}',
                [[-3, -4, -5], [-2, -3, -4], [-1, -2, -3]],
            ],
            'Subtraction: matrix 3x2 - 3x2' => [
                '={1,2,3;4,5,6} - {7,8,9;10,11,12}',
                [[-6, -6, -6], [-6, -6, -6]],
            ],
            'Subtraction: matrix 2x3 - 2x3' => [
                '={1,4;2,5;3,6} - {7,10;8,11;9,12}',
                [[-6, -6], [-6, -6], [-6, -6]],
            ],
            'Subtraction: matrix 3x2 - 2x3' => [
                '={1,2,3;4,5,6} - {7,10;8,11;9,12}',
                [[-6, -8], [-4, -6]],
            ],
            'Subtraction: matrix 2x3 - 3x2' => [
                '={7,10;8,11;9,12} - {1,2,3;4,5,6}',
                [[6, 8], [4, 6]],
            ],
            // Multiplication
            'Multiplication: square matrix 2x2 * 2x2' => [
                '={1,2;3,4} * {-2,4;-6,8}',
                [[-2, 8], [-18, 32]],
            ],
            'Multiplication: square matrix 2x2 * scalar' => [
                '={1,2;3,4} * 2',
                [[2, 4], [6, 8]],
            ],
            'Multiplication: row vector * row vector' => [
                '={1,2,3} * {4,5,6}',
                [[4, 10, 18]],
            ],
            'Multiplication: column vector * column vector' => [
                '={1;2;3} * {4;5;6}',
                [[4], [10], [18]],
            ],
            'Multiplication: row vector * column vector' => [
                '={1,2,3} * {4;5;6}',
                [[4, 8, 12], [5, 10, 15], [6, 12, 18]],
            ],
            'Multiplication: column vector * row vector' => [
                '={1;2;3} * {4,5,6}',
                [[4, 5, 6], [8, 10, 12], [12, 15, 18]],
            ],
            'Multiplication: matrix 3x2 * 3x2' => [
                '={1,2,3;4,5,6} * {7,8,9;10,11,12}',
                [[7, 16, 27], [40, 55, 72]],
            ],
            'Multiplication: matrix 2x3 * 2x3' => [
                '={1,4;2,5;3,6} * {7,10;8,11;9,12}',
                [[7, 40], [16, 55], [27, 72]],
            ],
            'Multiplication: row vector 2 * column vector 2' => [
                '={2,3} * {4;5}',
                [[8, 12], [10, 15]],
            ],
            'Multiplication: matrix 3x2 * 2x3' => [
                '={1,2,3;4,5,6} * {7,10;8,11;9,12}',
                [[7, 20], [32, 55]],
            ],
            'Multiplication: matrix 2x3 * 3x2' => [
                '={7,10;8,11;9,12} * {1,2,3;4,5,6}',
                [[7, 20], [32, 55]],
            ],
            // Division
            'Division: square matrix 2x2 / 2x2' => [
                '={1,2;3,4} / {-2,4;-6,8}',
                [[-0.5, 0.5], [-0.5, 0.5]],
            ],
            'Division: square matrix 2x2 / scalar' => [
                '={1,2;3,4} / 0.5',
                [[2, 4], [6, 8]],
            ],
            'Division: row vector / row vector' => [
                '={1,2,3} / {4,5,6}',
                [[0.25, 0.4, 0.5]],
            ],
            'Division: column vector / column vector' => [
                '={1;2;3} / {4;5;6}',
                [[0.25], [0.4], [0.5]],
            ],
            'Division: row vector / column vector' => [
                '={1,2,3} / {4;5;6}',
                [[0.25, 0.5, 0.75], [0.2, 0.4, 0.6], [0.16666666666667, 0.33333333333333, 0.5]],
            ],
            'Division: column vector / row vector' => [
                '={1;2;3} / {4,5,6}',
                [[0.25, 0.2, 0.16666666666667], [0.5, 0.4, 0.33333333333333], [0.75, 0.6, 0.5]],
            ],
            'Division: matrix 3x2 / 3x2' => [
                '={1,2,3;4,5,6} / {7,8,9;10,11,12}',
                [[0.142857142857143, 0.25, 0.33333333333333], [0.4, 0.45454545454545, 0.5]],
            ],
            'Division: matrix 2x3 / 2x3' => [
                '={1,4;2,5;3,6} / {7,10;8,11;9,12}',
                [[0.142857142857143, 0.4], [0.25, 0.45454545454545], [0.33333333333333, 0.5]],
            ],
            'Division: row vector 2 / column vector 2' => [
                '={2,3} / {4;5}',
                [[0.5, 0.75], [0.4, 0.6]],
            ],
            'Division: matrix 3x2 / 2x3' => [
                '={1,2,3;4,5,6} / {7,10;8,11;9,12}',
                [[0.14285714285714, 0.2], [0.5, 0.45454545454545]],
            ],
            'Division: matrix 2x3 / 3x2' => [
                '={7,10;8,11;9,12} / {1,2,3;4,5,6}',
                [[7, 5], [2, 2.2]],
            ],
            // Power
            'Power: square matrix 2x2 ^ 2x2' => [
                '={1,2;3,4} ^ {-2,4;-6,8}',
                [[1, 16], [0.00137174211248, 65536]],
            ],
            'Power: square matrix 2x2 ^ scalar' => [
                '={1,2;3,4} ^ 2',
                [[1, 4], [9, 16]],
            ],
            'Power: row vector ^ row vector' => [
                '={1,2,3} ^ {4,5,6}',
                [[1, 32, 729]],
            ],
            'Power: column vector / column vector' => [
                '={1;2;3} ^ {4;5;6}',
                [[1], [32], [729]],
            ],
            'Power: row vector ^ column vector' => [
                '={1,2,3} ^ {4;5;6}',
                [[1, 16, 81], [1, 32, 243], [1, 64, 729]],
            ],
            'Power: column vector ^ row vector' => [
                '={1;2;3} ^ {4,5,6}',
                [[1, 1, 1], [16, 32, 64], [81, 243, 729]],
            ],
            'Power: matrix 3x2 ^ 3x2' => [
                '={1,2,3;4,5,6} ^ {7,8,9;10,11,12}',
                [[1, 256, 19683], [1048576, 48828125, 2176782336]],
            ],
            'Power: matrix 2x3 ^ 2x3' => [
                '={1,4;2,5;3,6} ^ {7,10;8,11;9,12}',
                [[1, 1048576], [256, 48828125], [19683, 2176782336]],
            ],
            'Power: row vector 2 ^ column vector 2' => [
                '={2,3} ^ {4;5}',
                [[16, 81], [32, 243]],
            ],
            'Power: matrix 3x2 ^ 2x3' => [
                '={1,2,3;4,5,6} ^ {7,10;8,11;9,12}',
                [[1, 1024], [65536, 48828125]],
            ],
            'Power: matrix 2x3 ^ 3x2' => [
                '={7,10;8,11;9,12} ^ {1,2,3;4,5,6}',
                [[7, 100], [4096, 161051]],
            ],
            // Concatenation
            'Concatenation: row vector 2 & column vector 2' => [
                '={"A",",B"} & {"C";";D"}',
                [['AC', ',BC'], ['A;D', ',B;D']],
            ],
            'Concatenation: matrix 3x2 & 3x2' => [
                '={"A","B","C";"D","E","F"} & {"G","H","I";"J","K","L"}',
                [['AG', 'BH', 'CI'], ['DJ', 'EK', 'FL']],
            ],
            'Concatenation: matrix 2x3 & 2x3' => [
                '={"A","B";"C","D";"E","F"} & {"G","H";"I","J";"K","L"}',
                [['AG', 'BH'], ['CI', 'DJ'], ['EK', 'FL']],
            ],
            'Concatenation: 2x2 matrix & scalar' => [
                '={"A","B";"C","D"} & "E"',
                [['AE', 'BE'], ['CE', 'DE']],
            ],
            'Concatenation: scalar & 2x2 matrix' => [
                '="E" & {"A","B";"C","D"}',
                [['EA', 'EB'], ['EC', 'ED']],
            ],
            'Concatenation: 2x2 & 2x1 vector' => [
                '={"A","B";"C","D"} & {"E","F"}',
                [['AE', 'BF'], ['CE', 'DF']],
            ],
            'Concatenation: 2x2 & 1x2 vector' => [
                '={"A","B";"C","D"} & {"E";"F"}',
                [['AE', 'BE'], ['CF', 'DF']],
            ],

            // Unary Negation
            'Unary Negation: square matrix - 2x2' => [
                '= - {-2,4;-6,8}',
                [[2, -4], [6, -8]],
            ],
            // Percentage
            'Percentage: square matrix % 2x2' => [
                '={-2,4;-6,8} %',
                [[-0.02, 0.04], [-0.06, 0.08]],
            ],
        ];
    }
}
