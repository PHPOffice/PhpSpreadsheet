<?php

namespace PhpOffice\PhpSpreadsheetTests\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PHPUnit\Framework\TestCase;

class ExpressionWizardTest extends TestCase
{
    /**
     * @var Style
     */
    protected $style;

    /**
     * @var string
     */
    protected $range = '$C$3:$E$5';

    /**
     * @var Wizard
     */
    protected $wizardFactory;

    protected function setUp(): void
    {
        $this->wizardFactory = new Wizard($this->range);
        $this->style = new Style();
    }

    /**
     * @dataProvider expressionDataProvider
     */
    public function testExpressionWizard(string $expression, string $expectedExpression): void
    {
        $ruleType = Wizard::EXPRESSION;
        /** @var Wizard\Expression $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);

        $wizard->setStyle($this->style);
        $wizard->expression($expression);

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_EXPRESSION, $conditional->getConditionType());
        $conditions = $conditional->getConditions();
        self::assertSame([$expectedExpression], $conditions);

        $newWizard = Wizard::fromConditional($conditional, $this->range);
        $newWizard->getConditional();
        self::assertEquals($newWizard, $wizard, 'fromConditional() Failure');
    }

    /**
     * @dataProvider expressionDataProvider
     */
    public function testExpressionWizardUsingAlias(string $expression, string $expectedExpression): void
    {
        $ruleType = Wizard::EXPRESSION;
        /** @var Wizard\Expression $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);

        $wizard->setStyle($this->style);
        $wizard->formula($expression);

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_EXPRESSION, $conditional->getConditionType());
        $conditions = $conditional->getConditions();
        self::assertSame([$expectedExpression], $conditions);
    }

    public static function expressionDataProvider(): array
    {
        return [
            ['ISODD(A1)', 'ISODD(C3)'],
            ['AND($A1="USA",$B1="Q4")', 'AND($A3="USA",$B3="Q4")'],
        ];
    }

    public function testInvalidFromConditional(): void
    {
        $ruleType = 'Unknown';
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Conditional is not an Expression CF Rule conditional');

        $conditional = new Conditional();
        $conditional->setConditionType($ruleType);
        Wizard\Expression::fromConditional($conditional);
    }
}
