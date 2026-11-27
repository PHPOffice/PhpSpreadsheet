<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetPhpBench;

use PhpBench\Attributes as Bench;
use PhpOffice\PhpSpreadsheet\IOFactory;

#[Bench\BeforeClassMethods('createFixtures')]
#[Bench\Groups(['reader'])]
class ReaderBench
{
    private const ROWS = 5000;

    private const COLS = 5;

    private const FORMATS = ['Xlsx', 'Xls', 'Ods', 'Csv', 'Html'];

    public static function createFixtures(): void
    {
        $spreadsheet = null;
        foreach (self::FORMATS as $format) {
            $file = self::fixture($format);
            if (!is_file($file)) {
                $spreadsheet ??= SheetFactory::create(self::ROWS, self::COLS);
                IOFactory::createWriter($spreadsheet, $format)->save($file);
            }
        }
    }

    private static function fixture(string $format): string
    {
        return sprintf('%s/read-%dx%d.%s', SheetFactory::fixtureDirectory(), self::ROWS, self::COLS, strtolower($format));
    }

    /**
     * @return array<string, array{format: string, readDataOnly: bool}>
     */
    public function provideFormats(): array
    {
        $sets = [];
        foreach (self::FORMATS as $format) {
            $sets[$format] = ['format' => $format, 'readDataOnly' => false];
        }
        $sets['Xlsx data only'] = ['format' => 'Xlsx', 'readDataOnly' => true];

        return $sets;
    }

    /**
     * @param array{format: string, readDataOnly: bool} $params
     */
    #[Bench\ParamProviders('provideFormats')]
    public function benchLoad(array $params): void
    {
        $reader = IOFactory::createReader($params['format']);
        $reader->setReadDataOnly($params['readDataOnly']);
        $reader->load(self::fixture($params['format']));
    }
}
