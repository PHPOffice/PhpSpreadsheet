<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

class HyperlinkTest extends AllSetupTeardown
{
    /** @var bool */
    protected $issue2464 = true;

    /**
     * @dataProvider providerHYPERLINK
     *
     * @param array|string $expectedResult
     */
    public function testHYPERLINK($expectedResult, ?string $linkUrl, ?string $description): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $formula = '=HYPERLINK(';
        if ($linkUrl !== null) {
            $formula .= '"' . $linkUrl . '"';
            if ($description !== null) {
                $formula .= ',"' . $description . '"';
            }
        }
        $formula .= ')';
        $sheet->getCell('A1')->setValue($formula);
        $result = $sheet->getCell('A1')->getCalculatedValue();

        if (!is_array($expectedResult)) {
            self::assertSame($expectedResult, $result);
        } else {
            self::assertSame($expectedResult[1], $result);
            $hyperlink = $sheet->getCell('A1')->getHyperlink();
            self::assertSame($expectedResult[0], $hyperlink->getUrl());
            self::assertSame($expectedResult[1], $hyperlink->getTooltip());
        }
    }

    /**
     * @dataProvider providerHYPERLINK
     *
     * @param array|string $expectedResult
     */
    public function testHYPERLINKcellRef($expectedResult, ?string $linkUrl, ?string $description): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $formula = '=HYPERLINK(';
        if ($linkUrl !== null) {
            $sheet->getCell('A2')->setValue($linkUrl);
            $formula .= 'A2';
            if ($description !== null) {
                $sheet->getCell('A3')->setValue($description);
                $formula .= ',A3';
            }
        }
        $formula .= ')';
        $sheet->getCell('A1')->setValue($formula);
        $result = $sheet->getCell('A1')->getCalculatedValue();

        if (!is_array($expectedResult)) {
            self::assertSame($expectedResult, $result);
        } else {
            self::assertSame($expectedResult[1], $result);
            if (!$this->issue2464) {
                $hyperlink = $sheet->getCell('A1')->getHyperlink();
                self::assertSame($expectedResult[0], $hyperlink->getUrl());
                self::assertSame($expectedResult[1], $hyperlink->getTooltip());
            }
        }
    }

    public function testLen(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('=LEN(HYPERLINK("http://www.example.com","Example"))');
        $value = $sheet->getCell('A1')->getCalculatedValue();
        self::assertSame(7, $value);
        if ($this->issue2464) {
            self::markTestIncomplete('testLen and testHYPERLINKcellRef incomplete due to issue 2464');
        } else {
            $hyperlink = $sheet->getCell('A1')->getHyperlink();
            self::assertSame('', $hyperlink->getUrl());
            self::assertSame('', $hyperlink->getTooltip());
        }
    }

    public static function providerHYPERLINK(): array
    {
        return require 'tests/data/Calculation/LookupRef/HYPERLINK.php';
    }

    public function testHYPERLINKwithoutCell(): void
    {
        $result = LookupRef\Hyperlink::set('https://phpspreadsheet.readthedocs.io/en/latest/', 'Read the Docs');
        self::assertSame(ExcelError::REF(), $result);
    }
}
