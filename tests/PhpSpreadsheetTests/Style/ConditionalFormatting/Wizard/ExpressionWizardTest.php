<?php

namespace PhpOffice\PhpSpreadsheetTests\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PHPUnit\Framework\TestCase;

class ExpressionWizardTest extends TestCase
{
    /**
     * @var Wizard
     */
    protected $wizardFactory;

    protected function setUp(): void
    {
        $range = '$C$3:$E$5';
        $this->wizardFactory = new Wizard($range);
    }

    /**
     * @dataProvider expressionDataProvider
     */
    public function testExpressionWizard(string $expression, string $expectedExpression): void
    {
        $ruleType = Wizard::EXPRESSION;
        /** @var Wizard\Expression $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);
        $wizard->expression($expression);

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_EXPRESSION, $conditional->getConditionType());
        $conditions = $conditional->getConditions();
        self::assertSame([$expectedExpression], $conditions, 'fromConditional() Failure');
    }

    public function expressionDataProvider(): array
    {
        return [
            ['ISODD(A1)', 'ISODD(C3)'],
            ['AND($A1="USA",$B1="Q4")', 'AND($A3="USA",$B3="Q4")'],
        ];
    }
}
