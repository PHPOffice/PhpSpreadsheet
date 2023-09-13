<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\AutoFilter;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ColumnTest extends SetupTeardown
{
    protected function initSheet(): Worksheet
    {
        $sheet = $this->getSheet();
        $sheet->getCell('G1')->setValue('Heading');
        $sheet->getCell('G2')->setValue(2);
        $sheet->getCell('G3')->setValue(3);
        $sheet->getCell('G4')->setValue(4);
        $sheet->getCell('H1')->setValue('Heading2');
        $sheet->getCell('H2')->setValue(1);
        $sheet->getCell('H3')->setValue(2);
        $sheet->getCell('H4')->setValue(3);
        $this->maxRow = $maxRow = 4;
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange("G1:H$maxRow");

        return $sheet;
    }

    public function testVariousGets(): void
    {
        $sheet = $this->initSheet();
        $columnFilter = $sheet->getAutoFilter()->getColumn('H');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                3
            );
        $result = $columnFilter->getColumnIndex();
        self::assertEquals('H', $result);
    }

    public function testGetBadColumnIndex(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $this->expectExceptionMessage('Column is outside of current autofilter range.');
        $sheet = $this->initSheet();
        $sheet->getAutoFilter()->getColumn('B');
    }

    public function testSetColumnIndex(): void
    {
        $sheet = $this->initSheet();
        $columnFilter = $sheet->getAutoFilter()->getColumn('H');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                3
            );
        $expectedResult = 'G';

        $result = $columnFilter->setColumnIndex($expectedResult);
        self::assertInstanceOf(Column::class, $result);

        $result = $result->getColumnIndex();
        self::assertEquals($expectedResult, $result);
    }

    public function testSetParent(): void
    {
        $sheet = $this->initSheet();
        $columnFilter = $sheet->getAutoFilter()->getColumn('H');
        //    Setters return the instance to implement the fluent interface
        $result = $columnFilter->setParent(null);
        self::assertInstanceOf(Column::class, $result);
    }

    public function testVariousSets(): void
    {
        $sheet = $this->initSheet();
        $columnFilter = $sheet->getAutoFilter()->getColumn('H');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                3
            );

        $result = $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_DYNAMICFILTER);
        self::assertInstanceOf(Column::class, $result);

        $result = $columnFilter->getFilterType();
        self::assertEquals(Column::AUTOFILTER_FILTERTYPE_DYNAMICFILTER, $result);

        $result = $columnFilter->setJoin(Column::AUTOFILTER_COLUMN_JOIN_AND);
        self::assertInstanceOf(Column::class, $result);

        $result = $columnFilter->getJoin();
        self::assertEquals(Column::AUTOFILTER_COLUMN_JOIN_AND, $result);
    }

    public function testSetInvalidFilterTypeThrowsException(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $this->expectExceptionMessage('Invalid filter type for column AutoFilter.');
        $sheet = $this->initSheet();
        $columnFilter = $sheet->getAutoFilter()->getColumn('H');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                3
            );

        $expectedResult = 'Unfiltered';

        $columnFilter->setFilterType($expectedResult);
    }

    public function testSetInvalidJoinThrowsException(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $this->expectExceptionMessage('Invalid rule connection for column AutoFilter.');
        $sheet = $this->initSheet();
        $columnFilter = $sheet->getAutoFilter()->getColumn('H');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                3
            );

        $expectedResult = 'Neither';

        $columnFilter->setJoin($expectedResult);
    }

    public function testGetAttributes(): void
    {
        $sheet = $this->initSheet();
        $columnFilter = $sheet->getAutoFilter()->getColumn('H');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                3
            );
        $attributeSet = [
            'val' => 100,
            'maxVal' => 200,
        ];

        $result = $columnFilter->setAttributes($attributeSet);
        self::assertInstanceOf(Column::class, $result);

        $result = $columnFilter->getAttributes();
        self::assertSame($attributeSet, $result);
    }

    public function testSetAttribute(): void
    {
        $sheet = $this->initSheet();
        $columnFilter = $sheet->getAutoFilter()->getColumn('H');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                3
            );

        $attributeSet = [
            'val' => 100,
            'maxVal' => 200,
        ];

        foreach ($attributeSet as $attributeName => $attributeValue) {
            //    Setters return the instance to implement the fluent interface
            $result = $columnFilter->setAttribute($attributeName, $attributeValue);
            self::assertInstanceOf(Column::class, $result);
        }
        self::assertSame($attributeSet, $columnFilter->getAttributes());
    }

    public function testGetAttribute(): void
    {
        $sheet = $this->initSheet();
        $columnFilter = $sheet->getAutoFilter()->getColumn('H');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                3
            );

        $attributeSet = [
            'val' => 100,
            'maxVal' => 200,
        ];

        $columnFilter->setAttributes($attributeSet);

        foreach ($attributeSet as $attributeName => $attributeValue) {
            $result = $columnFilter->getAttribute($attributeName);
            self::assertSame($attributeValue, $result);
        }
        $result = $columnFilter->getAttribute('nonExistentAttribute');
        self::assertNull($result);
    }

    public function testClone(): void
    {
        $sheet = $this->initSheet();
        $columnFilter = $sheet->getAutoFilter()->getColumn('H');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                3
            );
        $originalRule = $columnFilter->getRules();
        $result = clone $columnFilter;
        self::assertSame($columnFilter->getColumnIndex(), $result->getColumnIndex());
        self::assertSame($columnFilter->getFilterType(), $result->getFilterType());
        self::assertSame($columnFilter->getJoin(), $result->getJoin());
        self::assertNull($result->getParent());
        self::assertNotNull($columnFilter->getParent());
        self::assertContainsOnlyInstancesOf(Rule::class, $result->getRules());
        $clonedRule = $result->getRules();
        self::assertCount(1, $clonedRule);
        self::assertCount(1, $originalRule);
        self::assertNotSame($originalRule[0], $clonedRule[0]);
        self::assertSame($originalRule[0]->getRuleType(), $clonedRule[0]->getRuleType());
        self::assertSame($originalRule[0]->getValue(), $clonedRule[0]->getValue());
        self::assertSame($originalRule[0]->getOperator(), $clonedRule[0]->getOperator());
        self::assertSame($originalRule[0]->getGrouping(), $clonedRule[0]->getGrouping());
        self::assertSame($result, $clonedRule[0]->getParent());
    }

    public function testRuleManipulation(): void
    {
        $sheet = $this->initSheet();
        $columnFilter = $sheet->getAutoFilter()->getColumn('H');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                3
            );
        $originalRules = $columnFilter->getRules();
        self::assertCount(1, $originalRules);
        $rule0 = $columnFilter->getRule(0);
        self::assertSame($originalRules[0], $rule0);
        $rule1 = $columnFilter->getRule(1);
        self::assertInstanceOf(Rule::class, $rule1);
        self::assertNotEquals($originalRules[0], $rule1);
        self::assertCount(2, $columnFilter->getRules());
        self::assertSame(Column::AUTOFILTER_COLUMN_JOIN_OR, $columnFilter->getJoin());
        $columnFilter->setJoin(Column::AUTOFILTER_COLUMN_JOIN_AND);
        $rule2 = new Rule();
        $columnFilter->addRule($rule2);
        self::assertCount(3, $columnFilter->getRules());
        self::assertSame(Column::AUTOFILTER_COLUMN_JOIN_AND, $columnFilter->getJoin());
        $columnFilter->deleteRule(2);
        self::assertCount(2, $columnFilter->getRules());
        self::assertSame(Column::AUTOFILTER_COLUMN_JOIN_AND, $columnFilter->getJoin());
        $columnFilter->deleteRule(1);
        self::assertCount(1, $columnFilter->getRules());
        self::assertSame(Column::AUTOFILTER_COLUMN_JOIN_OR, $columnFilter->getJoin());
        $columnFilter->addRule($rule1);
        $columnFilter->addRule($rule2);
        $columnFilter->setJoin(Column::AUTOFILTER_COLUMN_JOIN_AND);
        self::assertCount(3, $columnFilter->getRules());
        self::assertSame(Column::AUTOFILTER_COLUMN_JOIN_AND, $columnFilter->getJoin());
        $columnFilter->clearRules();
        self::assertCount(0, $columnFilter->getRules());
        self::assertSame(Column::AUTOFILTER_COLUMN_JOIN_OR, $columnFilter->getJoin());
    }
}
