<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Cell\AddressRange;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class MetadataBoundsTest extends TestCase
{
    private const STYLE_LOCATIONS = ['styles', 'styles-automatic', 'content'];

    /** @var list<string> */
    private array $files = [];

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            unlink($file);
        }
    }

    /** @return array<string, array{string}> */
    public static function invalidDigitCounts(): array
    {
        $cases = [];
        foreach (['101', '255', '256', '2000000000', '999999999999999999999999999999', '-1', '', 'abc', '1.5', '2e9'] as $digits) {
            $cases['digits ' . $digits] = [$digits];
        }

        return $cases;
    }

    #[DataProvider('invalidDigitCounts')]
    public function testNumberStylesRejectInvalidCounts(string $digits): void
    {
        // Exercise all three style-processing call sites through the public reader.
        foreach (self::STYLE_LOCATIONS as $location) {
            $file = $this->numberFixture($digits, $location);

            try {
                (new Ods())->load($file);
                self::fail('Expected invalid number-style count rejection');
            } catch (ReaderException $exception) {
                self::assertSame('Invalid ODS min-integer-digits value.', $exception->getMessage());
            }
        }
    }

    /** @return array<string, array{null|string, null|int}> */
    public static function validDigitCounts(): array
    {
        return [
            'absent' => [null, null],
            'zero' => ['0', null],
            'one' => ['1', null],
            'leading zeroes' => ['0003', 3],
            'plus and whitespace' => [' +3 ', 3],
            'exact limit' => ['100', 100],
        ];
    }

    #[DataProvider('validDigitCounts')]
    public function testNumberStylesPreserveValidFormats(?string $digits, ?int $length): void
    {
        foreach (self::STYLE_LOCATIONS as $location) {
            $spreadsheet = (new Ods())->load($this->numberFixture($digits, $location));

            try {
                self::assertSame($length === null ? 'General' : str_repeat('0', $length), $spreadsheet->getActiveSheet()->getStyle('A1')->getNumberFormat()->getFormatCode());
                self::assertSame(7, $spreadsheet->getActiveSheet()->getCell('A1')->getValue());
            } finally {
                $spreadsheet->disconnectWorksheets();
            }
        }
    }

    public function testReadDataOnlySkipsNumberStyles(): void
    {
        $reader = (new Ods())->setReadDataOnly(true);
        foreach (self::STYLE_LOCATIONS as $location) {
            $spreadsheet = $reader->load($this->numberFixture('2000000000', $location));

            try {
                self::assertSame(7, $spreadsheet->getActiveSheet()->getCell('A1')->getValue());
                self::assertSame('General', $spreadsheet->getActiveSheet()->getStyle('A1')->getNumberFormat()->getFormatCode());
            } finally {
                $spreadsheet->disconnectWorksheets();
            }
        }
    }

    public function testReaderRecoversAfterRejectedMatrixSpan(): void
    {
        $reader = new Ods();

        try {
            $reader->load($this->matrixFixture('2', '1', AddressRange::MAX_COLUMN_INT, 1));
            self::fail('Expected invalid matrix span rejection');
        } catch (ReaderException $exception) {
            self::assertSame('Invalid ODS number-matrix-columns-spanned value.', $exception->getMessage());
        }
        $spreadsheet = $reader->load($this->matrixFixture('3', '2', 2, 2));

        try {
            $cell = $spreadsheet->getActiveSheet()->getCell('B2');
            self::assertSame('=1', $cell->getValue());
            self::assertSame(['t' => 'array', 'ref' => 'B2:D3'], $cell->getFormulaAttributes());
            self::assertSame(1, $cell->getOldCalculatedValue());
        } finally {
            $spreadsheet->disconnectWorksheets();
        }
    }

    /** @return array<string, array{string, string, int, int, string}> */
    public static function invalidMatrices(): array
    {
        $cases = [];
        foreach (['0', '-1', '', '1.5', 'abc', '2e9', '2000000000', '999999999999999999999999999999'] as $count) {
            $cases['columns ' . $count] = [$count, '1', 1, 1, 'number-matrix-columns-spanned'];
            $cases['rows ' . $count] = ['1', $count, 1, 1, 'number-matrix-rows-spanned'];
        }
        $cases['column past maximum'] = [(string) (AddressRange::MAX_COLUMN_INT + 1), '1', 1, 1, 'number-matrix-columns-spanned'];
        $cases['row past maximum'] = ['1', (string) (AddressRange::MAX_ROW + 1), 1, 1, 'number-matrix-rows-spanned'];
        $cases['columns from B exceed remaining width'] = [(string) AddressRange::MAX_COLUMN_INT, '1', 2, 1, 'number-matrix-columns-spanned'];
        $cases['rows from row 2 exceed remaining height'] = ['1', (string) AddressRange::MAX_ROW, 1, 2, 'number-matrix-rows-spanned'];
        $cases['columns from XFD exceed remaining width'] = ['2', '1', AddressRange::MAX_COLUMN_INT, 1, 'number-matrix-columns-spanned'];
        $cases['rows from final row exceed remaining height'] = ['1', '2', 1, AddressRange::MAX_ROW, 'number-matrix-rows-spanned'];

        return $cases;
    }

    #[DataProvider('invalidMatrices')]
    public function testMatrixSpansAreRejectedBeforeIteration(string $columns, string $rows, int $column, int $row, string $attribute): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Invalid ODS ' . $attribute . ' value.');
        (new Ods())->load($this->matrixFixture($columns, $rows, $column, $row));
    }

    /** @return array<string, array{string, string, int, int, string}> */
    public static function validMatrices(): array
    {
        return [
            'single cell' => ['1', '1', 1, 1, 'A1:A1'],
            'small matrix' => ['3', '2', 2, 2, 'B2:D3'],
            'integer lexical forms' => [' +003 ', '0002', 2, 2, 'B2:D3'],
            'full column width' => [(string) AddressRange::MAX_COLUMN_INT, '1', 1, 1, 'A1:XFD1'],
            'full row height' => ['1', (string) AddressRange::MAX_ROW, 1, 1, 'A1:A1048576'],
            'remaining width from B' => [(string) (AddressRange::MAX_COLUMN_INT - 1), '1', 2, 1, 'B1:XFD1'],
            'remaining height from row 2' => ['1', (string) (AddressRange::MAX_ROW - 1), 1, 2, 'A2:A1048576'],
            'bottom right cell' => ['1', '1', AddressRange::MAX_COLUMN_INT, AddressRange::MAX_ROW, 'XFD1048576:XFD1048576'],
        ];
    }

    #[DataProvider('validMatrices')]
    public function testValidMatrixRangesArePreserved(string $columns, string $rows, int $column, int $row, string $expected): void
    {
        $spreadsheet = (new Ods())->load($this->matrixFixture($columns, $rows, $column, $row));

        try {
            $start = explode(':', $expected)[0];
            $cell = $spreadsheet->getActiveSheet()->getCell($start);
            self::assertSame(['t' => 'array', 'ref' => $expected], $cell->getFormulaAttributes());
            self::assertSame('=1', $cell->getValue());
        } finally {
            $spreadsheet->disconnectWorksheets();
        }
    }

    private function numberFixture(?string $digits, string $location): string
    {
        $attribute = $digits === null ? '' : ' number:min-integer-digits="' . $digits . '"';
        $numberStyle = '<number:number-style style:name="N1"><number:number number:decimal-places="0"' . $attribute . '/></number:number-style>';
        $cellStyle = '<style:style style:name="ce1" style:family="table-cell" style:data-style-name="N1"/>';
        $styles = '';
        $automaticStyles = '';
        if ($location === 'styles') {
            $styles = '<office:styles>' . $numberStyle . '</office:styles>';
            $automaticStyles = '<office:automatic-styles>' . $cellStyle . '</office:automatic-styles>';
        } elseif ($location === 'styles-automatic') {
            $styles = '<office:automatic-styles>' . $numberStyle . '</office:automatic-styles>';
            $automaticStyles = '<office:automatic-styles>' . $cellStyle . '</office:automatic-styles>';
        } else {
            $automaticStyles = '<office:automatic-styles>' . $numberStyle . $cellStyle . '</office:automatic-styles>';
        }
        $body = '<table:table table:name="S"><table:table-row><table:table-cell table:style-name="ce1" office:value-type="float" office:value="7"><text:p>7</text:p></table:table-cell></table:table-row></table:table>';

        return $this->fixture($body, $styles, $automaticStyles);
    }

    private function matrixFixture(string $columns, string $rows, int $column, int $row): string
    {
        $body = '<table:table table:name="S">';
        if ($row > 1) {
            $body .= '<table:table-row table:number-rows-repeated="' . ($row - 1) . '"><table:table-cell/></table:table-row>';
        }
        $body .= '<table:table-row>';
        if ($column > 1) {
            $body .= '<table:table-cell table:number-columns-repeated="' . ($column - 1) . '"/>';
        }
        $body .= '<table:table-cell table:formula="of:=1" table:number-matrix-columns-spanned="' . $columns . '" table:number-matrix-rows-spanned="' . $rows . '" office:value-type="float" office:value="1"><text:p>1</text:p></table:table-cell></table:table-row></table:table>';

        return $this->fixture($body);
    }

    private function fixture(string $body, string $styles = '', string $automaticStyles = ''): string
    {
        $file = tempnam(sys_get_temp_dir(), 'ods-bounds-');
        self::assertNotFalse($file);
        $this->files[] = $file;
        $zip = new ZipArchive();
        self::assertTrue($zip->open($file, ZipArchive::OVERWRITE));
        $ns = 'xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" '
            . 'xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" '
            . 'xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" '
            . 'xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" '
            . 'xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" '
            . 'xmlns:of="urn:oasis:names:tc:opendocument:xmlns:of:1.2" '
            . 'xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" '
            . 'xmlns:xlink="http://www.w3.org/1999/xlink"';
        $zip->addFromString('mimetype', 'application/vnd.oasis.opendocument.spreadsheet');
        $zip->addFromString('meta.xml', '<office:document-meta ' . $ns . '><office:meta/></office:document-meta>');
        $zip->addFromString('styles.xml', '<office:document-styles ' . $ns . '>' . $styles . '</office:document-styles>');
        $zip->addFromString('content.xml', '<office:document-content ' . $ns . '>' . $automaticStyles . '<office:body><office:spreadsheet>' . $body . '</office:spreadsheet></office:body></office:document-content>');
        self::assertTrue($zip->close());

        return $file;
    }
}
