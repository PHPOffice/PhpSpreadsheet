<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetPhpBench;

use PhpBench\Attributes as Bench;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

#[Bench\BeforeMethods('setUp')]
#[Bench\AfterMethods('tearDown')]
#[Bench\Groups(['writer'])]
class WriterBench
{
    private Spreadsheet $spreadsheet;

    private string $file;

    /**
     * @param array{format: string, formulas: bool} $params
     */
    public function setUp(array $params): void
    {
        $this->spreadsheet = SheetFactory::create(200, 40, $params['formulas']);
        $this->file = SheetFactory::tempFile(strtolower($params['format']));
    }

    public function tearDown(): void
    {
        if (is_file($this->file)) {
            unlink($this->file);
        }
    }

    /**
     * @return array<string, array{format: string, formulas: bool}>
     */
    public function provideFormats(): array
    {
        $sets = [];
        foreach (['Xlsx', 'Xls', 'Ods', 'Csv', 'Html'] as $format) {
            $sets[$format] = ['format' => $format, 'formulas' => false];
            $sets["{$format} with formulas"] = ['format' => $format, 'formulas' => true];
        }

        return $sets;
    }

    /**
     * @param array{format: string, formulas: bool} $params
     */
    #[Bench\ParamProviders('provideFormats')]
    public function benchSave(array $params): void
    {
        IOFactory::createWriter($this->spreadsheet, $params['format'])->save($this->file);
    }
}
