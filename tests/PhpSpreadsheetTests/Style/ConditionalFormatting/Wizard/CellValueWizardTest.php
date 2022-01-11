<?php

namespace PhpOffice\PhpSpreadsheetTests\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PHPUnit\Framework\TestCase;

class CellValueWizardTest extends TestCase
{
    /**
     * @var Wizard $wizardFactory
     */
    protected $wizardFactory;

    public function setUp(): void
    {
        $range = '$C$3:$E$5';
        $this->wizardFactory = new Wizard($range);
    }

    /**
     * @dataProvider basicCellValueDataProvider
     */
    public function testBasicCellValueWizard(string $operator, $operand, string $expectedOperator, $expectedCondition)
    {
        $ruleType = Wizard::CELL_VALUE;
        /** @var Wizard\CellValue $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);
        $wizard->$operator($operand);

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_CELLIS, $conditional->getConditionType());
        self::assertSame($expectedOperator, $conditional->getOperatorType());
        $conditions = $conditional->getConditions();
        self::assertSame([$expectedCondition], $conditions);
    }

    public function basicCellValueDataProvider(): array
    {
        return [
            '=5' => ['equals', 5, Conditional::OPERATOR_EQUAL, 5],
            '<>-2' => ['notEquals', -2, Conditional::OPERATOR_NOTEQUAL, -2],
            '>3' => ['greaterThan', 3, Conditional::OPERATOR_GREATERTHAN, 3],
            '>=5.5' => ['greaterThanOrEqual', 5.5, Conditional::OPERATOR_GREATERTHANOREQUAL, 5.5],
            '<-1.5' => ['lessThan', -1.5, Conditional::OPERATOR_LESSTHAN, -1.5],
            '<=22>' => ['lessThanOrEqual', 22, Conditional::OPERATOR_LESSTHANOREQUAL, 22],
        ];
    }

    /**
     * @dataProvider relativeCellValueDataProvider
     */
    public function testRelativeCellValueWizard($operand, $expectedCondition)
    {
        $ruleType = Wizard::CELL_VALUE;
        /** @var Wizard\CellValue $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);
        $wizard->equals($operand, true);

        $conditional = $wizard->getConditional();
        $conditions = $conditional->getConditions();
        self::assertSame([$expectedCondition], $conditions);
    }

    public function relativeCellValueDataProvider(): array
    {
        return [
            '= Cell value unpinned' => ['A1', 'C3'],
            '= Cell value pinned column' => ['$G1', '$G3'],
            '= Cell value pinned row' => ['A$10', 'C$10'],
            '= Cell value pinned cell' => ['$A$1', '$A$1'],
        ];
    }

    /**
     * @dataProvider rangeCellValueDataProvider
     */
    public function testRangeCellValueWizard(string $operator, array $operands, string $expectedOperator)
    {
        $ruleType = Wizard::CELL_VALUE;
        /** @var Wizard\CellValue $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);
        $wizard->$operator($operands[0])->and($operands[1]);

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_CELLIS, $conditional->getConditionType());
        self::assertSame($expectedOperator, $conditional->getOperatorType());
        $conditions = $conditional->getConditions();
        self::assertSame($operands, $conditions);
    }

    public function rangeCellValueDataProvider(): array
    {
        return [
            'between 5 and 10' => ['between', [5, 10], Conditional::OPERATOR_BETWEEN],
            'not between 0 and 1' => ['notBetween', [0, 1], Conditional::OPERATOR_NOTBETWEEN],
        ];
    }
}
