<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class SpaceExpansionTest extends TestCase
{
    /** @var list<string> */
    private array $files = [];

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            unlink($file);
        }
    }

    /** @return array<string, array{string, string}> */
    public static function invalidCounts(): array
    {
        $cases = [];
        foreach (['0', '-1', '', 'abc', '1.5', '2e9', '1 2'] as $count) {
            $cases['invalid ' . $count] = [$count, 'Invalid text:c space count.'];
        }
        foreach (['32768', '2000000000', '999999999999999999999999999999999999'] as $count) {
            $cases['excessive ' . $count] = [$count, 'ODS text space expansion exceeds the supported limit.'];
        }

        return $cases;
    }

    #[DataProvider('invalidCounts')]
    public function testInvalidCountsAreRejected(string $count, string $message): void
    {
        // Both text-reading paths must fail before constructing the repeated string.
        foreach ([false, true] as $annotation) {
            $file = $this->fixture('<text:p><text:s text:c="' . $count . '"/></text:p>', $annotation);

            try {
                (new Ods())->load($file);
                self::fail('Expected rejection of ' . $count);
            } catch (ReaderException $exception) {
                self::assertSame($message, $exception->getMessage());
            }
        }
    }

    /** @return array<string, array{string}> */
    public static function cumulativeExpansions(): array
    {
        return [
            'default space after exact limit' => ['<text:p><text:s text:c="32767"/><text:s/></text:p>'],
            'adjacent runs' => ['<text:p><text:s text:c="16384"/><text:s text:c="16384"/></text:p>'],
            'nested spans' => ['<text:p><text:s text:c="20000"/><text:span><text:span><text:s text:c="12768"/></text:span></text:span></text:p>'],
            'separate paragraphs' => ['<text:p><text:s text:c="16384"/></text:p><text:p><text:s text:c="16384"/></text:p>'],
        ];
    }

    #[DataProvider('cumulativeExpansions')]
    public function testCumulativeExpansionIsRejected(string $paragraphs): void
    {
        foreach ([false, true] as $annotation) {
            try {
                (new Ods())->load($this->fixture($paragraphs, $annotation));
                self::fail('Expected cumulative expansion rejection');
            } catch (ReaderException $exception) {
                self::assertSame('ODS text space expansion exceeds the supported limit.', $exception->getMessage());
            }
        }
    }

    /** @return array<string, array{string, string}> */
    public static function validText(): array
    {
        return [
            'default count' => ['<text:p>A<text:s/>B</text:p>', 'A B'],
            'explicit count' => ['<text:p>A<text:s text:c="2"/>B</text:p>', 'A  B'],
            'leading zeroes and plus' => ['<text:p><text:s text:c="0002"/><text:s text:c="+2"/><text:s text:c=" 2 "/></text:p>', '      '],
            'exact limit' => ['<text:p><text:s text:c="32767"/></text:p>', str_repeat(' ', DataType::MAX_STRING_LENGTH)],
            'nested exact limit' => ['<text:p><text:s text:c="20000"/><text:span><text:s text:c="12767"/></text:span></text:p>', str_repeat(' ', DataType::MAX_STRING_LENGTH)],
            'multiple paragraphs' => ['<text:p><text:s text:c="16000"/></text:p><text:p><text:s text:c="16766"/></text:p>', str_repeat(' ', 16000) . "\n" . str_repeat(' ', 16766)],
            'unicode and line breaks' => ['<text:p>日本語<text:span><text:s text:c="2"/>é</text:span><text:line-break/>fin</text:p>', "日本語  é\nfin"],
        ];
    }

    #[DataProvider('validText')]
    public function testValidTextIsPreserved(string $paragraphs, string $expected): void
    {
        foreach ([false, true] as $annotation) {
            $spreadsheet = (new Ods())->load($this->fixture($paragraphs, $annotation));

            try {
                $sheet = $spreadsheet->getActiveSheet();
                $actual = $annotation ? $sheet->getComment('A1')->getText()->getPlainText() : $sheet->getCell('A1')->getValue();
                self::assertSame($expected, $actual);
            } finally {
                $spreadsheet->disconnectWorksheets();
            }
        }
    }

    public function testCellAndAnnotationHaveIndependentBudgets(): void
    {
        $paragraphs = '<text:p><text:s text:c="32767"/></text:p>';
        $file = $this->fixture('<office:annotation>' . $paragraphs . '</office:annotation>' . $paragraphs);
        $spreadsheet = (new Ods())->load($file);

        try {
            $sheet = $spreadsheet->getActiveSheet();
            $expected = str_repeat(' ', DataType::MAX_STRING_LENGTH);
            self::assertSame($expected, $sheet->getCell('A1')->getValue());
            self::assertSame($expected, $sheet->getComment('A1')->getText()->getPlainText());
        } finally {
            $spreadsheet->disconnectWorksheets();
        }
    }

    public function testCellsHaveIndependentBudgets(): void
    {
        foreach ([false, true] as $annotation) {
            $file = $this->fixture('<text:p><text:s text:c="32767"/></text:p>', $annotation, true);
            $spreadsheet = (new Ods())->load($file);

            try {
                $expected = str_repeat(' ', DataType::MAX_STRING_LENGTH);
                $sheet = $spreadsheet->getActiveSheet();
                foreach (['A1', 'B1'] as $coordinate) {
                    $actual = $annotation ? $sheet->getComment($coordinate)->getText()->getPlainText() : $sheet->getCell($coordinate)->getValue();
                    self::assertSame($expected, $actual);
                }
            } finally {
                $spreadsheet->disconnectWorksheets();
            }
        }
    }

    public function testReaderCanRecoverAfterExpansionFailure(): void
    {
        $reader = new Ods();
        $spreadsheet = new Spreadsheet();

        try {
            try {
                $reader->loadIntoExisting($this->fixture('<text:p><text:s text:c="20000"/><text:s text:c="12768"/></text:p>'), $spreadsheet);
                self::fail('Expected expansion rejection');
            } catch (ReaderException $exception) {
                self::assertSame('ODS text space expansion exceeds the supported limit.', $exception->getMessage());
            }
        } finally {
            $spreadsheet->disconnectWorksheets();
        }
        $valid = $reader->load($this->fixture('<text:p><text:s text:c="32767"/></text:p>'));

        try {
            self::assertSame(str_repeat(' ', DataType::MAX_STRING_LENGTH), $valid->getActiveSheet()->getCell('A1')->getValue());
        } finally {
            $valid->disconnectWorksheets();
        }
    }

    /** @return array<string, array{string, string, bool, bool}> */
    public static function truncatedText(): array
    {
        $limit = DataType::MAX_STRING_LENGTH;
        $literal = str_repeat('é', $limit);

        $textCases = [
            'long literal text' => ['<text:p>' . $literal . 'é</text:p>', $literal],
            'literal followed by spaces' => ['<text:p>é<text:s text:c="32767"/></text:p>', 'é' . str_repeat(' ', $limit - 1)],
            'spaces followed by literal' => ['<text:p><text:s text:c="32767"/>é</text:p>', str_repeat(' ', $limit)],
            'nested span reaches limit' => ['<text:p>' . str_repeat('é', $limit - 1) . '<text:span>日<text:span>本</text:span></text:span></text:p>', str_repeat('é', $limit - 1) . '日'],
            'paragraph separator reaches limit' => ['<text:p>' . str_repeat('é', $limit - 1) . '</text:p><text:p>日</text:p>', str_repeat('é', $limit - 1) . "\n"],
            'paragraph separator after limit' => ['<text:p>' . $literal . '</text:p><text:p>日</text:p>', $literal],
            'spaces across paragraphs' => ['<text:p><text:s text:c="16000"/></text:p><text:p><text:s text:c="16767"/></text:p>', str_repeat(' ', 16000) . "\n" . str_repeat(' ', 16766)],
            'line break reaches limit' => ['<text:p>' . str_repeat('é', $limit - 1) . '<text:line-break/>日</text:p>', str_repeat('é', $limit - 1) . "\n"],
            'line break after limit' => ['<text:p><text:s text:c="32767"/><text:line-break/></text:p>', str_repeat(' ', $limit)],
            'spaces after literal limit' => ['<text:p>' . $literal . '<text:s text:c="32767"/></text:p>', $literal],
        ];

        $cases = [];
        foreach ($textCases as $name => [$paragraphs, $expected]) {
            foreach (['load' => false, 'loadIntoExisting' => true] as $method => $loadIntoExisting) {
                foreach (['with styles' => false, 'data only' => true] as $mode => $readDataOnly) {
                    $cases["$name, $method, $mode"] = [$paragraphs, $expected, $loadIntoExisting, $readDataOnly];
                }
            }
        }

        return $cases;
    }

    #[DataProvider('truncatedText')]
    public function testCompleteTextIsTruncated(string $paragraphs, string $expected, bool $loadIntoExisting, bool $readDataOnly): void
    {
        foreach ([false, true] as $annotation) {
            $reader = (new Ods())->setReadDataOnly($readDataOnly);
            $parsedText = null;
            $reader->setFormatCallback(static function (string $type, string $text) use (&$parsedText): string {
                $parsedText = $text;

                return '';
            });
            $file = $this->fixture($paragraphs, $annotation, true);
            $spreadsheet = null;

            try {
                if ($loadIntoExisting) {
                    $spreadsheet = new Spreadsheet();
                    $reader->loadIntoExisting($file, $spreadsheet);
                } else {
                    $spreadsheet = $reader->load($file);
                }
                $sheet = $spreadsheet->getActiveSheet();
                foreach (['A1', 'B1'] as $coordinate) {
                    $actual = $annotation ? $sheet->getComment($coordinate)->getText()->getPlainText() : $sheet->getCell($coordinate)->getValue();
                    self::assertSame($expected, $actual);
                    self::assertSame(DataType::MAX_STRING_LENGTH, mb_strlen($actual, 'UTF-8'));
                }
                if (!$annotation) {
                    // The parser must cap the text before the cell's later truncation.
                    self::assertSame($expected, $parsedText);
                }
            } finally {
                $spreadsheet?->disconnectWorksheets();
            }
        }
    }

    /** @return array<string, array{string, string}> */
    public static function invalidCountsAfterTextLimit(): array
    {
        return [
            'invalid count' => ['<text:s text:c="-1"/>', 'Invalid text:c space count.'],
            'excessive count' => ['<text:s text:c="2000000000"/>', 'ODS text space expansion exceeds the supported limit.'],
            'cumulative expansion' => ['<text:s text:c="32767"/><text:s/>', 'ODS text space expansion exceeds the supported limit.'],
        ];
    }

    #[DataProvider('invalidCountsAfterTextLimit')]
    public function testCountsAreValidatedAfterTextLimit(string $runs, string $message): void
    {
        $paragraphs = '<text:p>' . str_repeat('é', DataType::MAX_STRING_LENGTH) . '<text:span>' . $runs . '</text:span></text:p>';
        foreach ([false, true] as $annotation) {
            try {
                (new Ods())->load($this->fixture($paragraphs, $annotation));
                self::fail('Expected rejection after reaching the text limit');
            } catch (ReaderException $exception) {
                self::assertSame($message, $exception->getMessage());
            }
        }
    }

    private function fixture(string $paragraphs, bool $annotation = false, bool $repeatCell = false): string
    {
        $file = tempnam(sys_get_temp_dir(), 'ods-spaces-');
        self::assertNotFalse($file);
        $this->files[] = $file;
        $zip = new ZipArchive();
        self::assertTrue($zip->open($file, ZipArchive::OVERWRITE));
        $namespaces = 'xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" '
            . 'xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" '
            . 'xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" '
            . 'xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" '
            . 'xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" '
            . 'xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" '
            . 'xmlns:xlink="http://www.w3.org/1999/xlink"';
        $content = $annotation ? '<office:annotation>' . $paragraphs . '</office:annotation><text:p>cell</text:p>' : $paragraphs;
        $cell = '<table:table-cell office:value-type="string">' . $content . '</table:table-cell>';
        $zip->addFromString('mimetype', 'application/vnd.oasis.opendocument.spreadsheet');
        $zip->addFromString('meta.xml', '<office:document-meta ' . $namespaces . '><office:meta/></office:document-meta>');
        $zip->addFromString('styles.xml', '<office:document-styles ' . $namespaces . '><office:styles/></office:document-styles>');
        $zip->addFromString('content.xml', '<office:document-content ' . $namespaces . '><office:body><office:spreadsheet><table:table table:name="S"><table:table-row>' . $cell . ($repeatCell ? $cell : '') . '</table:table-row></table:table></office:spreadsheet></office:body></office:document-content>');
        self::assertTrue($zip->close());

        return $file;
    }
}
