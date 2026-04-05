<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Reader\Html as HtmlReader;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class HtmlImage2Test extends TestCase
{
    public function testDefault(): void
    {
        $reader = new HtmlReader();
        self::assertFalse($reader->getAllowExternalImages());
    }

    public function testCanInsertImageGoodProtocolAllowedNoWhitelist(): void
    {
        if (getenv('SKIP_URL_IMAGE_TEST') === '1') {
            self::markTestSkipped('Skipped due to setting of environment variable');
        }
        $imagePath1 = 'https://phpspreadsheet.readthedocs.io/en/'
            . 'latest/topics/images/01-03-filter-icon-1.png';
        $imagePath2 = 'https://phpspreadsheet.readthedocs.io/en/'
            . 'latest/topics/images/01-02-autofilter.png';
        $html = '<table>
                    <tr>
                        <td><img src="' . $imagePath1 . '" alt="test image1 voilà"></td>
                    </tr>
                    <tr>
                        <td><img src="' . $imagePath2 . '" alt="test image2 voilà"></td>
                    </tr>
                </table>';
        $reader = new HtmlReader();
        $reader->setAllowExternalImages(true);
        $spreadsheet = $reader->loadFromString($html);
        $firstSheet = $spreadsheet->getSheet(0);
        $drawings = $firstSheet->getDrawingCollection();
        self::assertCount(2, $drawings);
        $drawing1 = $drawings[0];
        self::assertInstanceOf(Drawing::class, $drawing1);
        self::assertSame($imagePath1, $drawing1->getPath());
        self::assertSame('A1', $drawing1->getCoordinates());
        $drawing2 = $drawings[1];
        self::assertInstanceOf(Drawing::class, $drawing2);
        self::assertSame($imagePath2, $drawing2->getPath());
        self::assertSame('A2', $drawing2->getCoordinates());
        $spreadsheet->disconnectWorksheets();
    }

    private function suppliedWhiteList(string $path): bool
    {
        return str_ends_with($path, 'autofilter.png');
    }

    public function testCanInsertImageGoodProtocolAllowedWhitelist(): void
    {
        if (getenv('SKIP_URL_IMAGE_TEST') === '1') {
            self::markTestSkipped('Skipped due to setting of environment variable');
        }
        $imagePath1 = 'https://phpspreadsheet.readthedocs.io/en/'
            . 'latest/topics/images/01-03-filter-icon-1.png';
        $imagePath2 = 'https://phpspreadsheet.readthedocs.io/en/'
            . 'latest/topics/images/01-02-autofilter.png';
        $html = '<table>
                    <tr>
                        <td><img src="' . $imagePath1 . '" alt="test image1 voilà"></td>
                    </tr>
                    <tr>
                        <td><img src="' . $imagePath2 . '" alt="test image2 voilà"></td>
                    </tr>
                </table>';
        $reader = new HtmlReader();
        $reader->setAllowExternalImages(true)
            ->setIsWhitelisted($this->suppliedWhiteList(...));
        $spreadsheet = $reader->loadFromString($html);
        $firstSheet = $spreadsheet->getSheet(0);
        $drawings = $firstSheet->getDrawingCollection();
        self::assertCount(1, $drawings, 'one drawing whitelisted, one not');
        $drawing2 = $drawings[0];
        self::assertInstanceOf(Drawing::class, $drawing2);
        self::assertSame($imagePath2, $drawing2->getPath());
        self::assertSame('A2', $drawing2->getCoordinates());
        $spreadsheet->disconnectWorksheets();
    }

    public function testCanInsertImageGoodProtocolNotAllowed(): void
    {
        $imagePath = 'https://phpspreadsheet.readthedocs.io/en/stable/topics/images/01-03-filter-icon-1.png';
        $html = '<table>
                    <tr>
                        <td><img src="' . $imagePath . '" alt="test image voilà"></td>
                    </tr>
                </table>';
        $spreadsheet = HtmlHelper::loadHtmlStringIntoSpreadsheet($html, false);
        $firstSheet = $spreadsheet->getSheet(0);
        self::assertCount(0, $firstSheet->getDrawingCollection());
        $spreadsheet->disconnectWorksheets();
    }

    public function testCantInsertImageNotFoundAllowed(): void
    {
        if (getenv('SKIP_URL_IMAGE_TEST') === '1') {
            self::markTestSkipped('Skipped due to setting of environment variable');
        }
        $imagePath = 'https://phpspreadsheet.readthedocs.io/en/latest/topics/images/xxx01-03-filter-icon-1.png';
        $html = '<table>
                    <tr>
                        <td><img src="' . $imagePath . '" alt="test image voilà"></td>
                    </tr>
                </table>';
        $spreadsheet = HtmlHelper::loadHtmlStringIntoSpreadsheet($html, true);
        $firstSheet = $spreadsheet->getSheet(0);
        $drawingCollection = $firstSheet->getDrawingCollection();
        self::assertCount(0, $drawingCollection);
        $spreadsheet->disconnectWorksheets();
    }

    public function testCantInsertImageNotFoundNotAllowed(): void
    {
        $imagePath = 'https://phpspreadsheet.readthedocs.io/en/latest/topics/images/xxx01-03-filter-icon-1.png';
        $html = '<table>
                    <tr>
                        <td><img src="' . $imagePath . '" alt="test image voilà"></td>
                    </tr>
                </table>';
        $spreadsheet = HtmlHelper::loadHtmlStringIntoSpreadsheet($html, false);
        $firstSheet = $spreadsheet->getSheet(0);
        $drawingCollection = $firstSheet->getDrawingCollection();
        self::assertCount(0, $drawingCollection);
        $spreadsheet->disconnectWorksheets();
    }

    #[DataProvider('providerBadProtocol')]
    public function testCannotInsertImageBadProtocol(string $imagePath): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Invalid protocol for linked drawing');
        $html = '<table>
                    <tr>
                        <td><img src="' . $imagePath . '" alt="test image voilà"></td>
                    </tr>
                </table>';
        HtmlHelper::loadHtmlStringIntoSpreadsheet($html);
    }

    public static function providerBadProtocol(): array
    {
        return [
            'unknown protocol' => ['httpx://example.com/image.png'],
            'embedded whitespace' => ['ht tp://example.com/image.png'],
            'control character' => ["\x14http://example.com/image.png"],
            'mailto' => ['mailto:xyz@example.com'],
            'mailto whitespace' => ['mail to:xyz@example.com'],
            'phar' => ['phar://example.com/image.phar'],
            'phar control' => ["\x14phar://example.com/image.phar"],
        ];
    }
}
