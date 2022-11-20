<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PHPUnit\Framework\TestCase;

class HtmlImageTest extends TestCase
{
    public function testCanInsertImage(): void
    {
        $imagePath = realpath(__DIR__ . '/../../../data/Reader/HTML/image.jpg');

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
        self::assertEquals('test image voilà', $drawing->getName());
        self::assertEquals('100', $drawing->getWidth());
        self::assertEquals('100', $drawing->getHeight());
    }

    public function testCanInsertImageWidth(): void
    {
        $imagePath = realpath(__DIR__ . '/../../../data/Reader/HTML/image.jpg');

        $html = '<table>
                    <tr>
                        <td><img src="' . $imagePath . '" alt="test image" width="50"></td>
                    </tr>
                </table>';
        $filename = HtmlHelper::createHtml($html);
        $spreadsheet = HtmlHelper::loadHtmlIntoSpreadsheet($filename, true);
        $firstSheet = $spreadsheet->getSheet(0);

        /** @var Drawing $drawing */
        $drawing = $firstSheet->getDrawingCollection()[0];
        self::assertEquals('50', $drawing->getWidth());
        self::assertEquals('50', $drawing->getHeight());
    }

    public function testCanInsertImageHeight(): void
    {
        $imagePath = realpath(__DIR__ . '/../../../data/Reader/HTML/image.jpg');

        $html = '<table>
                    <tr>
                        <td><img src="' . $imagePath . '" height="75"></td>
                    </tr>
                </table>';
        $filename = HtmlHelper::createHtml($html);
        $spreadsheet = HtmlHelper::loadHtmlIntoSpreadsheet($filename, true);
        $firstSheet = $spreadsheet->getSheet(0);

        /** @var Drawing $drawing */
        $drawing = $firstSheet->getDrawingCollection()[0];
        self::assertEquals('', $drawing->getName());
        self::assertEquals('75', $drawing->getWidth());
        self::assertEquals('75', $drawing->getHeight());
    }

    public function testImageWithourSrc(): void
    {
        $html = '<table>
                    <tr>
                        <td><img></td>
                    </tr>
                </table>';
        $filename = HtmlHelper::createHtml($html);
        $spreadsheet = HtmlHelper::loadHtmlIntoSpreadsheet($filename, true);
        $firstSheet = $spreadsheet->getSheet(0);

        self::assertCount(0, $firstSheet->getDrawingCollection());
    }
}
