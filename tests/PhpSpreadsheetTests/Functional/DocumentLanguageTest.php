<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use Composer\Pcre\Preg;
use PhpOffice\PhpSpreadsheet\Reader\Ods as OdsReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods as OdsWriter;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\Attributes\DataProvider;
use ZipArchive;

class DocumentLanguageTest extends AbstractFunctional
{
    // Converted by LibreOffice 26.8 from a Xlsx with no language: the default style says fo:language="en" fo:country="US"
    private const LIBREOFFICE_FILE = 'tests/data/Reader/Ods/DefaultLanguage.ods';

    #[DataProvider('providerFormats')]
    public function testLanguageSurvivesTheRoundTrip(string $format): void
    {
        $spreadsheetOld = new Spreadsheet();
        $spreadsheetOld->getProperties()->setLanguage('uk-UA');
        $spreadsheet = $this->writeAndReload($spreadsheetOld, $format);
        $spreadsheetOld->disconnectWorksheets();
        self::assertSame('uk-UA', $spreadsheet->getProperties()->getLanguage());
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerFormats(): array
    {
        return [['Xlsx'], ['Ods']];
    }

    public function testWrittenAsDcLanguage(): void
    {
        $spreadsheet = new Spreadsheet();
        self::assertStringNotContainsString('dc:language', (new XlsxWriter($spreadsheet))->getWriterPartDocProps()->writeDocPropsCore($spreadsheet));
        self::assertStringNotContainsString('dc:language', (new OdsWriter($spreadsheet))->getWriterPartMeta()->write());

        $spreadsheet->getProperties()->setLanguage('sr-latn-rs');
        self::assertStringContainsString('<dc:language>sr-latn-rs</dc:language>', (new XlsxWriter($spreadsheet))->getWriterPartDocProps()->writeDocPropsCore($spreadsheet));
        self::assertStringContainsString('<dc:language>sr-latn-rs</dc:language>', (new OdsWriter($spreadsheet))->getWriterPartMeta()->write());
        $spreadsheet->disconnectWorksheets();
    }

    #[DataProvider('providerDefaultStyle')]
    public function testWrittenToTheDefaultStyleAsLibreOfficeWritesIt(string $language, string $expected): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setLanguage($language);
        $styles = (new OdsWriter($spreadsheet))->getWriterPartStyles()->write();
        $spreadsheet->disconnectWorksheets();
        self::assertSame(1, Preg::matchAll('~<style:default-style style:family="table-cell"><style:text-properties ([^>]*)/>~', $styles, $defaultStyle));
        Preg::matchAll('~(?:fo|style):(?:rfc-language-tag|language|script|country)[-a-z]*="[^"]*"~', $defaultStyle[1][0], $attributes);
        self::assertSame($expected, implode(' ', $attributes[0]));
    }

    public static function providerDefaultStyle(): array
    {
        return [
            'none' => ['', ''],
            'not a tag' => ['en US', ''],
            'language' => ['uk', 'fo:language="uk"'],
            'language and country, any case' => ['EN-us', 'fo:language="en" fo:country="US"'],
            'script' => ['sr-latn-rs', 'style:rfc-language-tag="sr-latn-rs" fo:language="sr" fo:script="Latn" fo:country="RS"'],
            'UN M.49 region' => ['es-419', 'style:rfc-language-tag="es-419" fo:language="es"'],
            'variant' => ['de-CH-1996', 'style:rfc-language-tag="de-CH-1996" fo:language="de" fo:country="CH"'],
            'variant that starts like a script' => ['en-scotland', 'style:rfc-language-tag="en-scotland" fo:language="en"'],
            'private use' => ['x-private', 'style:rfc-language-tag="x-private"'],
            'Asian' => ['ja-JP', 'style:language-asian="ja" style:country-asian="JP"'],
            'Asian with a script' => ['zh-Hant-TW', 'style:rfc-language-tag-asian="zh-Hant-TW" style:language-asian="zh" style:script-asian="Hant" style:country-asian="TW"'],
            'complex' => ['ar-EG', 'style:language-complex="ar" style:country-complex="EG"'],
            'complex, three letters' => ['kok-IN', 'style:language-complex="kok" style:country-complex="IN"'],
        ];
    }

    /**
     * A copy of the file with one part changed.
     */
    private static function rewritten(string $file, string $part, string $from, string $to): string
    {
        $copy = File::temporaryFilename();
        copy($file, $copy);
        $zip = new ZipArchive();
        $zip->open($copy);
        $xml = (string) $zip->getFromName($part);
        self::assertStringContainsString($from, $xml);
        $zip->addFromString($part, str_replace($from, $to, $xml));
        $zip->close();

        return $copy;
    }

    #[DataProvider('providerLanguageOfLibreOffice')]
    public function testReadTheLanguageLibreOfficeKeepsInTheDefaultStyle(string $expected, string $to): void
    {
        $file = self::rewritten(self::LIBREOFFICE_FILE, 'styles.xml', 'fo:language="en" fo:country="US"', $to);
        $spreadsheet = (new OdsReader())->load($file);
        unlink($file);
        self::assertSame($expected, $spreadsheet->getProperties()->getLanguage());
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerLanguageOfLibreOffice(): array
    {
        return [
            'as LibreOffice writes it' => ['en-US', 'fo:language="en" fo:country="US"'],
            'script' => ['sr-Latn-RS', 'fo:language="sr" fo:script="Latn" fo:country="RS"'],
            'language only' => ['uk', 'fo:language="uk"'],
            'three letters and UN M.49 region' => ['kok-419', 'fo:language="kok" fo:country="419"'],
            'any case' => ['sr-Latn-RS', 'fo:language="SR" fo:script="LATN" fo:country="rs"'],
            'invalid script and country left out' => ['sr', 'fo:language="sr" fo:script="Latin" fo:country="R"'],
            'invalid language' => ['', 'fo:language="e1" fo:country="US"'],
            'tag' => ['de-CH-1996', 'fo:language="de" fo:country="CH" style:rfc-language-tag="de-CH-1996"'],
            'no linguistic content' => ['', 'fo:language="zxx" fo:country="none"'],
        ];
    }

    public function testDcLanguageWinsOverTheDefaultStyle(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setLanguage('uk-UA');
        $file = File::temporaryFilename();
        (new OdsWriter($spreadsheet))->save($file);
        $spreadsheet->disconnectWorksheets();
        $changed = self::rewritten($file, 'styles.xml', 'fo:language="uk"', 'fo:language="de"');
        unlink($file);
        $spreadsheet = (new OdsReader())->load($changed);
        unlink($changed);
        self::assertSame('uk-UA', $spreadsheet->getProperties()->getLanguage());
        $spreadsheet->disconnectWorksheets();
    }

    public function testReadWithReadDataOnly(): void
    {
        $reader = new OdsReader();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load(self::LIBREOFFICE_FILE);
        self::assertSame('en-US', $spreadsheet->getProperties()->getLanguage());
        $spreadsheet->disconnectWorksheets();
    }
}
