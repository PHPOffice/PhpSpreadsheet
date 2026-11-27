<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetPhpBench;

use PhpBench\Attributes as Bench;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetBenchmarks\XlsxStreamingReadClass;

/**
 * Compares the SimpleXML based Xlsx reader with the experimental XMLReader based one.
 */
#[Bench\BeforeClassMethods('createFixture')]
#[Bench\ParamProviders('provideReaders')]
#[Bench\Groups(['reader'])]
class XlsxStreamingReaderBench
{
    public static function createFixture(): void
    {
        if (is_file(self::fixture())) {
            return;
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        for ($row = 1; $row <= 2000; ++$row) {
            $sheet->fromArray([
                $row,
                $row * 1.5,
                $row + 100,
                $row * -0.25,
                0,
                "Name_{$row}",
                "Description for row {$row}",
                'Category_' . ($row % 50),
                "Ref-{$row}-" . str_pad((string) $row, 6, '0', STR_PAD_LEFT),
                $row % 2 === 0 ? 'Even' : 'Odd',
                "=A{$row}+C{$row}",
                "=B{$row}*2",
            ], null, "A{$row}");
            $sheet->getCell("M{$row}")->setValueExplicit(true, DataType::TYPE_BOOL);
            $sheet->getCell("N{$row}")->setValueExplicit(false, DataType::TYPE_BOOL);
            $sheet->getCell("O{$row}")->setValueExplicit("Inline_{$row}", DataType::TYPE_INLINE);
        }
        (new XlsxWriter($spreadsheet))->save(self::fixture());
    }

    private static function fixture(): string
    {
        return SheetFactory::fixtureDirectory() . '/xlsx-streaming.xlsx';
    }

    /**
     * @return array<string, array{reader: class-string<Xlsx>}>
     */
    public function provideReaders(): array
    {
        return [
            'SimpleXML' => ['reader' => Xlsx::class],
            'XMLReader' => ['reader' => XlsxStreamingReadClass::class],
        ];
    }

    /**
     * @param array{reader: class-string<Xlsx>} $params
     */
    public function benchLoadDataOnly(array $params): void
    {
        $reader = new $params['reader']();
        $reader->setReadDataOnly(true);
        $reader->load(self::fixture());
    }
}
