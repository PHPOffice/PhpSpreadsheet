<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PHPUnit\Framework\TestCase;

class CellValueWizardTest extends TestCase
{
    protected Style $style;

    protected string $range = '$C$3:$E$5';

    protected Wizard $wizardFactory;

    protected function setUp(): void
    {
        $this->wizardFactory = new Wizard($this->range);
        $this->style = new Style();
    }

    /**
     * @dataProvider basicCellValueDataProvider
     */
    public function testBasicCellValueWizard(string $operator, mixed $operand, string $expectedOperator, mixed $expectedCondition): void
    {
        $ruleType = Wizard::CELL_VALUE;
        /** @var Wizard\CellValue $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);

        $wizard->setStyle($this->style);
        $wizard->$operator($operand);

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_CELLIS, $conditional->getConditionType());
        self::assertSame($expectedOperator, $conditional->getOperatorType());
        $conditions = $conditional->getConditions();
        self::assertSame([$expectedCondition], $conditions);

        $newWizard = Wizard::fromConditional($conditional, $this->range);
        $newWizard->getConditional();
        self::assertEquals($newWizard, $wizard, 'fromConditional() Failure');
    }

    public static function basicCellValueDataProvider(): array
    {
        return [
            '=5' => ['equals', 5, Conditional::OPERATOR_EQUAL, 5],
            '<>-2' => ['notEquals', -2, Conditional::OPERATOR_NOTEQUAL, -2],
            '>3' => ['greaterThan', 3, Conditional::OPERATOR_GREATERTHAN, 3],
            '>=5.5' => ['greaterThanOrEqual', 5.5, Conditional::OPERATOR_GREATERTHANOREQUAL, 5.5],
            '<-1.5' => ['lessThan', -1.5, Conditional::OPERATOR_LESSTHAN, -1.5],
            '<=22>' => ['lessThanOrEqual', 22, Conditional::OPERATOR_LESSTHANOREQUAL, 22],
            '= Boolean True Value' => ['equals', true, Conditional::OPERATOR_EQUAL, 'TRUE'],
            '= Boolean False Value' => ['equals', false, Conditional::OPERATOR_EQUAL, 'FALSE'],
            '= Null Value' => ['equals', null, Conditional::OPERATOR_EQUAL, 'NULL'],
            '= String Value' => ['equals', 'Hello World', Conditional::OPERATOR_EQUAL, '"Hello World"'],
        ];
    }

    /**
     * @dataProvider relativeCellValueDataProvider
     */
    public function testRelativeCellValueWizard(mixed $operand, mixed $expectedCondition): void
    {
        $ruleType = Wizard::CELL_VALUE;
        /** @var Wizard\CellValue $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);

        $wizard->setStyle($this->style);
        $wizard->equals($operand, Wizard::VALUE_TYPE_CELL);

        $conditional = $wizard->getConditional();
        $conditions = $conditional->getConditions();
        self::assertSame([$expectedCondition], $conditions);

        $newWizard = Wizard::fromConditional($conditional, $this->range);
        $newWizard->getConditional();
        self::assertEquals($newWizard, $wizard, 'fromConditional() Failure');
    }

    public static function relativeCellValueDataProvider(): array
    {
        return [
            '= Cell value unpinned' => ['A1', 'C3'],
            '= Cell value pinned column' => ['$G1', '$G3'],
            '= Cell value pinned row' => ['A$10', 'C$10'],
            '= Cell value pinned cell' => ['$A$1', '$A$1'],
        ];
    }

    /**
     * @dataProvider formulaCellValueDataProvider
     */
    public function testCellValueWizardWithFormula(mixed $operand, mixed $expectedCondition): void
    {
        $ruleType = Wizard::CELL_VALUE;
        /** @var Wizard\CellValue $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);

        $wizard->setStyle($this->style);
        $wizard->equals($operand, Wizard::VALUE_TYPE_FORMULA);

        $conditional = $wizard->getConditional();
        $conditions = $conditional->getConditions();
        self::assertSame([$expectedCondition], $conditions);

        $newWizard = Wizard::fromConditional($conditional, $this->range);
        $newWizard->getConditional();
        self::assertEquals($newWizard, $wizard, 'fromConditional() Failure');
    }

    public static function formulaCellValueDataProvider(): array
    {
        return [
            '= Cell value unpinned in function' => ['SQRT(A1)', 'SQRT(C3)'],
            '= Cell value pinned column in function' => ['SQRT($G1)', 'SQRT($G3)'],
            '= Cell value pinned row in function' => ['SQRT(A$10)', 'SQRT(C$10)'],
            '= Cell value pinned cell in function' => ['SQRT($A$1)', 'SQRT($A$1)'],
            '= Cell value unpinned in expression' => ['A1+B2', 'C3+D4'],
            '= Cell value pinned column in expression' => ['$G1+$H2', '$G3+$H4'],
            '= Cell value pinned row in expression' => ['A$10+B$11', 'C$10+D$11'],
            '= Cell value pinned cell in expression' => ['$A$1+$B$2', '$A$1+$B$2'],
        ];
    }

    /**
     * @dataProvider rangeCellValueDataProvider
     */
    public function testRangeCellValueWizard(string $operator, array $operands, string $expectedOperator): void
    {
        $ruleType = Wizard::CELL_VALUE;
        /** @var Wizard\CellValue $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);

        $wizard->setStyle($this->style);
        $wizard->$operator($operands[0])->and($operands[1]);

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_CELLIS, $conditional->getConditionType());
        self::assertSame($expectedOperator, $conditional->getOperatorType());
        $conditions = $conditional->getConditions();
        self::assertSame($operands, $conditions);

        $newWizard = Wizard::fromConditional($conditional, $this->range);
        $newWizard->getConditional();
        self::assertEquals($newWizard, $wizard, 'fromConditional() Failure');
    }

    public static function rangeCellValueDataProvider(): array
    {
        return [
            'between 5 and 10' => ['between', [5, 10], Conditional::OPERATOR_BETWEEN],
            'between 10 and 5' => ['between', [10, 5], Conditional::OPERATOR_BETWEEN],
            'not between 0 and 1' => ['notBetween', [0, 1], Conditional::OPERATOR_NOTBETWEEN],
        ];
    }

    /**
     * @dataProvider rangeRelativeCellValueDataProvider
     */
    public function testRelativeRangeCellValueWizard(array $operands, array $expectedConditions): void
    {
        $ruleType = Wizard::CELL_VALUE;
        /** @var Wizard\CellValue $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);

        $wizard->setStyle($this->style);
        $wizard
            ->between($operands[0], is_string($operands[0]) ? Wizard::VALUE_TYPE_CELL : Wizard::VALUE_TYPE_LITERAL)
            ->and($operands[1], is_string($operands[1]) ? Wizard::VALUE_TYPE_CELL : Wizard::VALUE_TYPE_LITERAL);

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_CELLIS, $conditional->getConditionType());
        $conditions = $conditional->getConditions();
        self::assertSame($expectedConditions, $conditions);

        $newWizard = Wizard::fromConditional($conditional, $this->range);
        $newWizard->getConditional();
        self::assertEquals($newWizard, $wizard, 'fromConditional() Failure');
    }

    public static function rangeRelativeCellValueDataProvider(): array
    {
        return [
            'between A6 and 5' => [['A$6', 5], ['C$6', 5]],
            'between -5 and C6' => [[-5, '$C6'], [-5, '$C8']],
        ];
    }

    /**
     * @dataProvider rangeFormulaCellValueDataProvider
     */
    public function testFormulaRangeCellValueWizard(array $operands, array $expectedConditions): void
    {
        $ruleType = Wizard::CELL_VALUE;
        /** @var Wizard\CellValue $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);

        $wizard->setStyle($this->style);
        $wizard
            ->between($operands[0], is_string($operands[0]) ? Wizard::VALUE_TYPE_FORMULA : Wizard::VALUE_TYPE_LITERAL)
            ->and($operands[1], is_string($operands[1]) ? Wizard::VALUE_TYPE_FORMULA : Wizard::VALUE_TYPE_LITERAL);

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_CELLIS, $conditional->getConditionType());
        $conditions = $conditional->getConditions();
        self::assertSame($expectedConditions, $conditions);

        $newWizard = Wizard::fromConditional($conditional, $this->range);
        $newWizard->getConditional();
        self::assertEquals($newWizard, $wizard, 'fromConditional() Failure');
    }

    public static function rangeFormulaCellValueDataProvider(): array
    {
        return [
            'between yesterday and tomorrow' => [['TODAY()-1', 'TODAY()+1'], ['TODAY()-1', 'TODAY()+1']],
        ];
    }

    public function testInvalidFromConditional(): void
    {
        $ruleType = 'Unknown';
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Conditional is not a Cell Value CF Rule conditional');

        $conditional = new Conditional();
        $conditional->setConditionType($ruleType);
        Wizard\CellValue::fromConditional($conditional);
    }
}
