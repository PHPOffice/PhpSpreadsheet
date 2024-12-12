<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PHPUnit\Framework\TestCase;

// not sure why succeeding now
class HtmlImage2Test extends TestCase
{
    public function testCanInsertImageGoodProtocolxx(): void
    {
        if (getenv('SKIP_URL_IMAGE_TEST') === '1') {
            self::markTestSkipped('Skipped due to setting of environment variable');
        }
        $imagePath = 'https://phpspreadsheet.readthedocs.io/en/latest/topics/images/01-03-filter-icon-1.png';
        $html = '<table>
                    <tr>
                        <td><img src="' . $imagePath . '" alt="test image voilà"></td>
                    </tr>
                </table>';
        $filename = HtmlHelper::createHtml($html);
        $spreadsheet = HtmlHelper::loadHtmlIntoSpreadsheet($filename, true);
        $firstSheet = $spreadsheet->getSheet(0);

        /** @var Drawing $drawing */
        $drawing = $firstSheet->getDrawingCollection()[0];
        self::assertEquals($imagePath, $drawing->getPath());
        self::assertEquals('A1', $drawing->getCoordinates());
    }

    public function testCantInsertImageNotFound(): void
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
        $spreadsheet = HtmlHelper::loadHtmlIntoSpreadsheet($filename, true);
        $firstSheet = $spreadsheet->getSheet(0);
        $drawingCollection = $firstSheet->getDrawingCollection();
        self::assertCount(0, $drawingCollection);
    }

    public function testCannotInsertImageBadProtocol(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Invalid protocol for linked drawing');
        $imagePath = 'httpx://phpspreadsheet.readthedocs.io/en/latest/topics/images/01-03-filter-icon-1.png';
        $html = '<table>
                    <tr>
                        <td><img src="' . $imagePath . '" alt="test image voilà"></td>
                    </tr>
                </table>';
        $filename = HtmlHelper::createHtml($html);
        HtmlHelper::loadHtmlIntoSpreadsheet($filename, true);
    }
}
