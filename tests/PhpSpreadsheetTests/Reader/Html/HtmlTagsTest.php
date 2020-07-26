<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PHPUnit\Framework\TestCase;

class HtmlTagsTest extends TestCase
{
    public function testTags(): void
    {
        $reader = new Html();
        $html1 = <<<EOF
<table><tbody>
<tr><td>1</td><td>2</td><td>3</td></tr>
<tr><td><a href='www.google.com'>hyperlink</a></td><td>5<hr></td><td>6</td></tr>
<tr><td>7</td><td>8</td><td>9</td></tr>
<tr><td>10</td><td>11</td><td>12</td></tr>
</tbody></table>
<hr>
<table><tbody>
<tr><td>1</td><td><i>2</i></td><td>3</td></tr>
<tr height='20'><td>4</td><td>5</td><td>6</td></tr>
<tr><td>7</td><td>8</td><td>9</td></tr>
<tr><td><ul><li>A</li><li>B</li><li>C</li></ul></td><td>11</td><td>12</td></tr>
</tbody></table>
<ul><li>D</li><li>E</li><li>F</li></ul>
<br>
<table><tbody>
<tr><td>M</td>
<td>
  <table><tbody>
  <tr><td>N</td><td>O</td></tr>
  <tr><td>P</td><td>Q</td></tr>
  </tbody></table>
</td>
<td>R</td>
</tr>
<tr><td>S</td><td>T</td><td>U</td></tr>
</tbody></table>
EOF;
        $robj = $reader->loadFromString($html1);
        $sheet = $robj->getActiveSheet();

        self::assertEquals('www.google.com', $sheet->getCell('A2')->getHyperlink()->getUrl());
        self::assertEquals('hyperlink', $sheet->getCell('A2')->getValue());
        self::assertEquals(-1, $sheet->getRowDimension(11)->getRowHeight());
        self::assertEquals(20, $sheet->getRowDimension(12)->getRowHeight());
        self::assertEquals(5, $sheet->getCell('B2')->getValue());
        self::assertEquals(Border::BORDER_THIN, $sheet->getCell('B3')->getStyle()->getBorders()->getBottom()->getBorderStyle());
        self::assertEquals(6, $sheet->getCell('C4')->getValue());
        self::assertEquals(Border::BORDER_THIN, $sheet->getCell('A9')->getStyle()->getBorders()->getBottom()->getBorderStyle());

        self::assertEquals(2, $sheet->getCell('B11')->getValue());
        self::assertTrue($sheet->getCell('B11')->getStyle()->getFont()->getItalic());

        // list within table
        self::assertEquals("A\nB\nC", $sheet->getCell('A14')->getValue());
        self::assertTrue($sheet->getCell('A14')->getStyle()->getAlignment()->getWrapText());
        // list outside of table
        self::assertEquals('D', $sheet->getCell('A17')->getValue());
        self::assertEquals('E', $sheet->getCell('A18')->getValue());
        self::assertEquals('F', $sheet->getCell('A19')->getValue());

        // embedded table
        self::assertEquals('M', $sheet->getCell('A21')->getValue());
        self::assertEquals('N', $sheet->getCell('B20')->getValue());
        self::assertEquals('O', $sheet->getCell('C20')->getValue());
        self::assertEquals('P', $sheet->getCell('B21')->getValue());
        self::assertEquals('Q', $sheet->getCell('C21')->getValue());
        self::assertEquals('R', $sheet->getCell('C23')->getValue());
        self::assertEquals('S', $sheet->getCell('A24')->getValue());
    }

    public static function testTagsRowColSpans(): void
    {
        $reader = new Html();
        $html1 = <<<EOF
<table>
  <tr>
    <th>Month</th>
    <th>Savings</th>
    <th>Expenses</th>
  </tr>
  <tr>
    <td>January</td>
    <td>$100</td>
    <td rowspan="2">$50</td>
  </tr>
  <tr>
    <td>February</td>
    <td>$80</td>
  </tr>
  <tr>
  <td rowspan="2" colspan="2" bgcolor="#00FFFF">Away in March</td>
  <td>$30</td>
  </tr>
  <tr>
  <td>$40</td>
  </tr>
</table>
EOF;
        $robj = $reader->loadFromString($html1);
        $sheet = $robj->getActiveSheet();

        self::assertEquals(['C2:C3' => 'C2:C3', 'A4:B5' => 'A4:B5'], $sheet->getMergeCells());
        self::assertEquals('Away in March', $sheet->getCell('A4')->getValue());
        self::assertEquals('00FFFF', $sheet->getCell('A4')->getStyle()->getFill()->getEndColor()->getRGB());
    }

    public static function testDoublyEmbeddedTable(): void
    {
        $reader = new Html();
        $html1 = <<<EOF
<table><tbody>
<tr><td>1</td><td>2</td><td>3</td></tr>
<tr><td>4</td><td>5</td><td>6</td></tr>
<tr><td>7</td><td>8</td><td>9</td></tr>
<tr><td></td><td></td><td></td></tr>
<tr><td></td><td></td><td></td></tr>
<tr><td></td><td></td><td></td></tr>
<tr><td>M</td>
<td>
  <table><tbody>
  <tr><td>N</td>
    <td>
      <table><tbody>
      <tr><td>10</td><td>11</td></tr>
      <tr><td>12</td><td>13</td></tr>
      </tbody></table>
    </td>
  <td>Y</td>
  </tr>
  <tr><td>P</td><td>Q</td><td>X</td></tr>
  </tbody></table>
</td>
<td>R</td>
</tr>
<tr><td>S</td><td>T</td><td>U</td></tr>
</tbody></table>
EOF;
        $robj = $reader->loadFromString($html1);
        $sheet = $robj->getActiveSheet();

        self::assertEquals('1', $sheet->getCell('A1')->getValue());
        self::assertEquals('2', $sheet->getCell('B1')->getValue());
        self::assertEquals('3', $sheet->getCell('C1')->getValue());
        self::assertEquals('4', $sheet->getCell('A2')->getValue());
        self::assertEquals('5', $sheet->getCell('B2')->getValue());
        self::assertEquals('6', $sheet->getCell('C2')->getValue());
        self::assertEquals('7', $sheet->getCell('A3')->getValue());
        self::assertEquals('8', $sheet->getCell('B3')->getValue());
        self::assertEquals('9', $sheet->getCell('C3')->getValue());
        self::assertEquals('10', $sheet->getCell('C5')->getValue());
        self::assertEquals('11', $sheet->getCell('D5')->getValue());
        self::assertEquals('12', $sheet->getCell('C6')->getValue());
        self::assertEquals('13', $sheet->getCell('D6')->getValue());
        self::assertEquals('N', $sheet->getCell('B6')->getValue());
        self::assertEquals('M', $sheet->getCell('A7')->getValue());
        self::assertEquals('Y', $sheet->getCell('E7')->getValue());
        self::assertEquals('P', $sheet->getCell('B8')->getValue());
        self::assertEquals('Q', $sheet->getCell('C8')->getValue());
        self::assertEquals('X', $sheet->getCell('D8')->getValue());
        self::assertEquals('R', $sheet->getCell('C10')->getValue());
        self::assertEquals('S', $sheet->getCell('A11')->getValue());
        self::assertEquals('T', $sheet->getCell('B11')->getValue());
        self::assertEquals('U', $sheet->getCell('C11')->getValue());
    }

    public static function testTagsOutsideTable(): void
    {
        $reader = new Html();
        $html1 = <<<EOF
<h1>Here comes a list</h1>
<ol>
<li>Item 1</li>
<li>Item 2</li>
<li>Item 3</li>
<li>Item 4</li>
</ol>
And here's another
<ul>
<li>Item A</li>
<li>Item B</li>
</ul>
<ol>
Content before list
<li>Item I</li>
<li>Item II</li>
<li>This <i>is</i> <span style='color: #ff0000;'>rich</span> text</li>
</ol>

EOF;
        $robj = $reader->loadFromString($html1);
        $sheet = $robj->getActiveSheet();

        self::assertTrue($sheet->getCell('A1')->getStyle()->getFont()->getBold());
        self::assertEquals('Here comes a list', $sheet->getCell('A1')->getValue());
        self::assertEquals('Item 1', $sheet->getCell('A3')->getValue());
        self::assertEquals('Item 2', $sheet->getCell('A4')->getValue());
        self::assertEquals('Item 3', $sheet->getCell('A5')->getValue());
        self::assertEquals('Item 4', $sheet->getCell('A6')->getValue());
        self::assertEquals('And here\'s another', $sheet->getCell('A7')->getValue());
        self::assertEquals('Item A', $sheet->getCell('A9')->getValue());
        self::assertEquals('Item B', $sheet->getCell('A10')->getValue());
        self::assertEquals('Content before list', $sheet->getCell('A11')->getValue());
        self::assertEquals('Item I', $sheet->getCell('A12')->getValue());
        self::assertEquals('Item II', $sheet->getCell('A13')->getValue());
        // TODO Rich Text not yet supported
    }

    public static function testHyperlinksWithRowspan(): void
    {
        $reader = new Html();
        $html1 = <<<EOF
<table>
	<tr>
		<td rowspan="3">Title</td>
		<td><a href="https://google.com">Link 1</a></td>
	</tr>
	<tr>
		<td><a href="https://google.com">Link 2</a></td>
	</tr>
	<tr>
		<td><a href="https://google.com">Link 3</a></td>
	</tr>
</table>
EOF;
        $robj = $reader->loadFromString($html1);
        $sheet = $robj->getActiveSheet();
        self::assertEquals('https://google.com', $sheet->getCell('B1')->getHyperlink()->getUrl());
        self::assertEquals('https://google.com', $sheet->getCell('B2')->getHyperlink()->getUrl());
        self::assertEquals('https://google.com', $sheet->getCell('B3')->getHyperlink()->getUrl());
    }
}
