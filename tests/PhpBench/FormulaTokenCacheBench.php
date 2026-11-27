<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetPhpBench;

use PhpBench\Attributes as Bench;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

#[Bench\BeforeMethods('setUp')]
#[Bench\ParamProviders('provideCacheSizes')]
#[Bench\Groups(['calculation'])]
class FormulaTokenCacheBench
{
    private const PATTERNS = [
        '=A%1$d+B%1$d',
        '=SUM(A%1$d:B%1$d)',
        '=IF(A%1$d>0,B%1$d,C%1$d)',
        '=AVERAGE(A%1$d:D%1$d)',
        '=A%1$d*B%1$d+C%1$d',
        '=MAX(A%1$d,B%1$d,C%1$d)',
        '=MIN(A%1$d:C%1$d)/D%1$d',
        '=CONCATENATE(A%1$d,"-",B%1$d)',
        '=ROUND(A%1$d/B%1$d,2)',
        '=IFERROR(A%1$d/B%1$d,0)',
    ];

    private Calculation $calculation;

    /** @var list<string> */
    private array $formulas = [];

    /**
     * @param array{cacheSize: int} $params
     */
    public function setUp(array $params): void
    {
        $this->calculation = Calculation::getInstance(new Spreadsheet());
        $this->calculation->setFormulaTokenCacheMaxSize($params['cacheSize']);
        for ($i = 0; $i < 10_000; ++$i) {
            $distinct = $i % 200 + 1;
            $this->formulas[] = sprintf(self::PATTERNS[$distinct % count(self::PATTERNS)], $distinct);
        }
    }

    /**
     * @return array<string, array{cacheSize: int}>
     */
    public function provideCacheSizes(): array
    {
        return [
            'cache disabled' => ['cacheSize' => 0],
            'cache enabled' => ['cacheSize' => 1000],
        ];
    }

    /**
     * 10,000 parses of 200 distinct formulas.
     *
     * @param array{cacheSize: int} $params
     */
    public function benchParseRepeatedFormulas(array $params): void
    {
        foreach ($this->formulas as $formula) {
            $this->calculation->parseFormula($formula);
        }
    }
}
