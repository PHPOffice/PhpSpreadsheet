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

    public function testCanInsertImageGoodProtocolAllowed(): void
    {
        if (getenv('SKIP_URL_IMAGE_TEST') === '1') {
            self::markTestSkipped('Skipped due to setting of environment variable');
        }
        $imagePath = 'https://phpspreadsheet.readthedocs.io/en/stable/topics/images/01-03-filter-icon-1.png';
        $html = '<table>
                    <tr>
                        <td><img src="' . $imagePath . '" alt="test image voilà"></td>
                    </tr>
                </table>';
        $filename = HtmlHelper::createHtml($html);
        $spreadsheet = HtmlHelper::loadHtmlIntoSpreadsheet($filename, true, true);
        $firstSheet = $spreadsheet->getSheet(0);

        /** @var Drawing $drawing */
        $drawing = $firstSheet->getDrawingCollection()[0];
        self::assertEquals($imagePath, $drawing->getPath());
        self::assertEquals('A1', $drawing->getCoordinates());
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
        $filename = HtmlHelper::createHtml($html);
        $spreadsheet = HtmlHelper::loadHtmlIntoSpreadsheet($filename, true, false);
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
        $filename = HtmlHelper::createHtml($html);
        $spreadsheet = HtmlHelper::loadHtmlIntoSpreadsheet($filename, true, true);
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
        $filename = HtmlHelper::createHtml($html);
        $spreadsheet = HtmlHelper::loadHtmlIntoSpreadsheet($filename, true, false);
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
        $filename = HtmlHelper::createHtml($html);
        HtmlHelper::loadHtmlIntoSpreadsheet($filename, true);
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
