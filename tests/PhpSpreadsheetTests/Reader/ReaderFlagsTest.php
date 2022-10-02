<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class ReaderFlagsTest extends TestCase
{
    private const EMPTY_CELLS = 'Empty Cells';
    private const DATA_ONLY = 'Data only';
    private const WITH_CHARTS = 'with Charts';

    /**
     * @var Xlsx
     */
    private $reader;

    protected function setup(): void
    {
        $this->reader = new Xlsx();
    }

    /**
     * @dataProvider flagsProvider
     */
    public function testFlags(int $flags, array $settings): void
    {
        $this->reader->setFlags($flags);

        self::assertSame($settings[self::EMPTY_CELLS], $this->reader->getReadEmptyCells());
        self::assertSame($settings[self::DATA_ONLY], $this->reader->getReadDataOnly());
        self::assertSame($settings[self::WITH_CHARTS], $this->reader->getIncludeCharts());
    }

    public function flagsProvider(): array
    {
        return [
            [
                0,
                [
                    self::EMPTY_CELLS => true,
                    self::DATA_ONLY => false,
                    self::WITH_CHARTS => false,
                ],
            ],
            [
                IReader::IGNORE_EMPTY_CELLS,
                [
                    self::EMPTY_CELLS => false,
                    self::DATA_ONLY => false,
                    self::WITH_CHARTS => false,
                ],
            ],
            [
                IReader::READ_DATA_ONLY,
                [
                    self::EMPTY_CELLS => true,
                    self::DATA_ONLY => true,
                    self::WITH_CHARTS => false,
                ],
            ],
            [
                IReader::IGNORE_EMPTY_CELLS | IReader::READ_DATA_ONLY,
                [
                    self::EMPTY_CELLS => false,
                    self::DATA_ONLY => true,
                    self::WITH_CHARTS => false,
                ],
            ],
            [
                IReader::LOAD_WITH_CHARTS,
                [
                    self::EMPTY_CELLS => true,
                    self::DATA_ONLY => false,
                    self::WITH_CHARTS => true,
                ],
            ],
            [
                IReader::IGNORE_EMPTY_CELLS | IReader::LOAD_WITH_CHARTS,
                [
                    self::EMPTY_CELLS => false,
                    self::DATA_ONLY => false,
                    self::WITH_CHARTS => true,
                ],
            ],
            [
                IReader::READ_DATA_ONLY | IReader::LOAD_WITH_CHARTS,
                [
                    self::EMPTY_CELLS => true,
                    self::DATA_ONLY => true,
                    self::WITH_CHARTS => true,
                ],
            ],
            [
                IReader::IGNORE_EMPTY_CELLS | IReader::READ_DATA_ONLY | IReader::LOAD_WITH_CHARTS,
                [
                    self::EMPTY_CELLS => false,
                    self::DATA_ONLY => true,
                    self::WITH_CHARTS => true,
                ],
            ],
        ];
    }
}
