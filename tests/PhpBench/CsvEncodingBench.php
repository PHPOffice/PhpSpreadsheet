<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetPhpBench;

use PhpBench\Attributes as Bench;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheetBenchmarks\CsvChunk;
use PhpOffice\PhpSpreadsheetTests\Reader\Csv\CsvIconv2;

/**
 * Reading non-UTF-8 Csv files with the stock reader and the experimental chunked and iconv readers.
 */
#[Bench\BeforeClassMethods('createFixtures')]
#[Bench\ParamProviders('provideReaders')]
#[Bench\Groups(['reader'])]
class CsvEncodingBench
{
    private const ROWS = 5000;

    private const ENCODINGS = ['ISO-8859-1', 'UTF-16LE'];

    public static function createFixtures(): void
    {
        foreach (self::ENCODINGS as $encoding) {
            $file = self::fixture($encoding);
            if (is_file($file)) {
                continue;
            }
            $accents = ['é', 'ö', 'ü', 'ñ', 'à', 'ç'];
            $content = $encoding === 'UTF-16LE' ? "\xFF\xFE" : '';
            for ($row = 0; $row < self::ROWS; ++$row) {
                $fields = [];
                for ($col = 0; $col < 10; ++$col) {
                    $accent = $accents[($row + $col) % count($accents)];
                    $fields[] = "Cell_{$row}_{$col}_{$accent}_data_with_special_chars_{$accent}";
                }
                $content .= mb_convert_encoding(implode(',', $fields) . "\n", $encoding, 'UTF-8');
            }
            file_put_contents($file, $content);
        }
    }

    private static function fixture(string $encoding): string
    {
        return SheetFactory::fixtureDirectory() . '/csv-' . strtolower($encoding) . '.csv';
    }

    /**
     * @return array<string, array{reader: class-string<Csv>, encoding: string}>
     */
    public function provideReaders(): array
    {
        $sets = [];
        foreach (self::ENCODINGS as $encoding) {
            $sets["Csv {$encoding}"] = ['reader' => Csv::class, 'encoding' => $encoding];
            $sets["CsvChunk {$encoding}"] = ['reader' => CsvChunk::class, 'encoding' => $encoding];
            $sets["CsvIconv2 {$encoding}"] = ['reader' => CsvIconv2::class, 'encoding' => $encoding];
        }

        return $sets;
    }

    /**
     * @param array{reader: class-string<Csv>, encoding: string} $params
     */
    public function benchLoad(array $params): void
    {
        $reader = new $params['reader']();
        $reader->setInputEncoding($params['encoding']);
        $reader->load(self::fixture($params['encoding']));
    }
}
