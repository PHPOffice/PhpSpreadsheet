<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\RichText\Run;
use PhpOffice\PhpSpreadsheet\Shared\Drawing as SharedDrawing;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Shared\Font as SharedFont;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;

class Html extends BaseWriter
{
    /**
     * Spreadsheet object.
     *
     * @var Spreadsheet
     */
    protected $spreadsheet;

    /**
     * Sheet index to write.
     *
     * @var int
     */
    private $sheetIndex = 0;

    /**
     * Images root.
     *
     * @var string
     */
    private $imagesRoot = '';

    /**
     * embed images, or link to images.
     *
     * @var bool
     */
    private $embedImages = false;

    /**
     * Use inline CSS?
     *
     * @var bool
     */
    private $useInlineCss = false;

    /**
     * Use embedded CSS?
     *
     * @var bool
     */
    private $useEmbeddedCSS = true;

    /**
     * Array of CSS styles.
     *
     * @var array
     */
    private $cssStyles;

    /**
     * Array of column widths in points.
     *
     * @var array
     */
    private $columnWidths;

    /**
     * Default font.
     *
     * @var Font
     */
    private $defaultFont;

    /**
     * Flag whether spans have been calculated.
     *
     * @var bool
     */
    private $spansAreCalculated = false;

    /**
     * Excel cells that should not be written as HTML cells.
     *
     * @var array
     */
    private $isSpannedCell = [];

    /**
     * Excel cells that are upper-left corner in a cell merge.
     *
     * @var array
     */
    private $isBaseCell = [];

    /**
     * Excel rows that should not be written as HTML rows.
     *
     * @var array
     */
    private $isSpannedRow = [];

    /**
     * Is the current writer creating PDF?
     *
     * @var bool
     */
    protected $isPdf = false;

    /**
     * Generate the Navigation block.
     *
     * @var bool
     */
    private $generateSheetNavigationBlock = true;

    /**
     * Create a new HTML.
     *
     * @param Spreadsheet $spreadsheet
     */
    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
        $this->defaultFont = $this->spreadsheet->getDefaultStyle()->getFont();
    }

    /**
     * Save Spreadsheet to file.
     *
     * @param string $pFilename
     *
     * @throws WriterException
     */
    public function save($pFilename)
    {
        // garbage collect
        $this->spreadsheet->garbageCollect();

        $saveDebugLog = Calculation::getInstance($this->spreadsheet)->getDebugLog()->getWriteDebugLog();
        Calculation::getInstance($this->spreadsheet)->getDebugLog()->setWriteDebugLog(false);
        $saveArrayReturnType = Calculation::getArrayReturnType();
        Calculation::setArrayReturnType(Calculation::RETURN_ARRAY_AS_VALUE);

        // Build CSS
        $this->buildCSS(!$this->useInlineCss);

        // Open file
        $fileHandle = fopen($pFilename, 'wb+');
        if ($fileHandle === false) {
            throw new WriterException("Could not open file $pFilename for writing.");
        }

        // Write headers
        fwrite($fileHandle, $this->generateHTMLHeader(!$this->useInlineCss));

        // Write navigation (tabs)
        if ((!$this->isPdf) && ($this->generateSheetNavigationBlock)) {
            fwrite($fileHandle, $this->generateNavigation());
        }

        // Write data
        fwrite($fileHandle, $this->generateSheetData());

        // Write footer
        fwrite($fileHandle, $this->generateHTMLFooter());

        // Close file
        fclose($fileHandle);

        Calculation::setArrayReturnType($saveArrayReturnType);
        Calculation::getInstance($this->spreadsheet)->getDebugLog()->setWriteDebugLog($saveDebugLog);
    }

    /**
     * Map VAlign.
     *
     * @param string $vAlign Vertical alignment
     *
     * @return string
     */
    private function mapVAlign($vAlign)
    {
        switch ($vAlign) {
            case Alignment::VERTICAL_BOTTOM:
                return 'bottom';
            case Alignment::VERTICAL_TOP:
                return 'top';
            case Alignment::VERTICAL_CENTER:
            case Alignment::VERTICAL_JUSTIFY:
                return 'middle';
            default:
                return 'baseline';
        }
    }

    /**
     * Map HAlign.
     *
     * @param string $hAlign Horizontal alignment
     *
     * @return false|string
     */
    private function mapHAlign($hAlign)
    {
        switch ($hAlign) {
            case Alignment::HORIZONTAL_GENERAL:
                return false;
            case Alignment::HORIZONTAL_LEFT:
                return 'left';
            case Alignment::HORIZONTAL_RIGHT:
                return 'right';
            case Alignment::HORIZONTAL_CENTER:
            case Alignment::HORIZONTAL_CENTER_CONTINUOUS:
                return 'center';
            case Alignment::HORIZONTAL_JUSTIFY:
                return 'justify';
            default:
                return false;
        }
    }

    /**
     * Map border style.
     *
     * @param int $borderStyle Sheet index
     *
     * @return string
     */
    private function mapBorderStyle($borderStyle)
    {
        switch ($borderStyle) {
            case Border::BORDER_NONE:
                return 'none';
            case Border::BORDER_DASHDOT:
                return '1px dashed';
            case Border::BORDER_DASHDOTDOT:
                return '1px dotted';
            case Border::BORDER_DASHED:
                return '1px dashed';
            case Border::BORDER_DOTTED:
                return '1px dotted';
            case Border::BORDER_DOUBLE:
                return '3px double';
            case Border::BORDER_HAIR:
                return '1px solid';
            case Border::BORDER_MEDIUM:
                return '2px solid';
            case Border::BORDER_MEDIUMDASHDOT:
                return '2px dashed';
            case Border::BORDER_MEDIUMDASHDOTDOT:
                return '2px dotted';
            case Border::BORDER_MEDIUMDASHED:
                return '2px dashed';
            case Border::BORDER_SLANTDASHDOT:
                return '2px dashed';
            case Border::BORDER_THICK:
                return '3px solid';
            case Border::BORDER_THIN:
                return '1px solid';
            default:
                // map others to thin
                return '1px solid';
        }
    }

    /**
     * Get sheet index.
     *
     * @return int
     */
    public function getSheetIndex()
    {
        return $this->sheetIndex;
    }

    /**
     * Set sheet index.
     *
     * @param int $pValue Sheet index
     *
     * @return $this
     */
    public function setSheetIndex($pValue)
    {
        $this->sheetIndex = $pValue;

        return $this;
    }

    /**
     * Get sheet index.
     *
     * @return bool
     */
    public function getGenerateSheetNavigationBlock()
    {
        return $this->generateSheetNavigationBlock;
    }

    /**
     * Set sheet index.
     *
     * @param bool $pValue Flag indicating whether the sheet navigation block should be generated or not
     *
     * @return $this
     */
    public function setGenerateSheetNavigationBlock($pValue)
    {
        $this->generateSheetNavigationBlock = (bool) $pValue;

        return $this;
    }

    /**
     * Write all sheets (resets sheetIndex to NULL).
     *
     * @return $this
     */
    public function writeAllSheets()
    {
        $this->sheetIndex = null;

        return $this;
    }

    /**
     * Generate HTML header.
     *
     * @param bool $pIncludeStyles Include styles?
     *
     * @throws WriterException
     *
     * @return string
     */
    public function generateHTMLHeader($pIncludeStyles = false)
    {
        // Construct HTML
        $properties = $this->spreadsheet->getProperties();
        $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">' . PHP_EOL;
        $html .= '<html>' . PHP_EOL;
        $html .= '  <head>' . PHP_EOL;
        $html .= '      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . PHP_EOL;
        $html .= '      <meta name="generator" content="PhpSpreadsheet, https://github.com/PHPOffice/PhpSpreadsheet">' . PHP_EOL;
        if ($properties->getTitle() > '') {
            $html .= '      <title>' . htmlspecialchars($properties->getTitle()) . '</title>' . PHP_EOL;
        }
        if ($properties->getCreator() > '') {
            $html .= '      <meta name="author" content="' . htmlspecialchars($properties->getCreator()) . '" />' . PHP_EOL;
        }
        if ($properties->getTitle() > '') {
            $html .= '      <meta name="title" content="' . htmlspecialchars($properties->getTitle()) . '" />' . PHP_EOL;
        }
        if ($properties->getDescription() > '') {
            $html .= '      <meta name="description" content="' . htmlspecialchars($properties->getDescription()) . '" />' . PHP_EOL;
        }
        if ($properties->getSubject() > '') {
            $html .= '      <meta name="subject" content="' . htmlspecialchars($properties->getSubject()) . '" />' . PHP_EOL;
        }
        if ($properties->getKeywords() > '') {
            $html .= '      <meta name="keywords" content="' . htmlspecialchars($properties->getKeywords()) . '" />' . PHP_EOL;
        }
        if ($properties->getCategory() > '') {
            $html .= '      <meta name="category" content="' . htmlspecialchars($properties->getCategory()) . '" />' . PHP_EOL;
        }
        if ($properties->getCompany() > '') {
            $html .= '      <meta name="company" content="' . htmlspecialchars($properties->getCompany()) . '" />' . PHP_EOL;
        }
        if ($properties->getManager() > '') {
            $html .= '      <meta name="manager" content="' . htmlspecialchars($properties->getManager()) . '" />' . PHP_EOL;
        }

        if ($pIncludeStyles) {
            $html .= $this->generateStyles(true);
        }

        $html .= '  </head>' . PHP_EOL;
        $html .= '' . PHP_EOL;
        $html .= '  <body>' . PHP_EOL;

        return $html;
    }

    /**
     * Generate sheet data.
     *
     * @throws WriterException
     *
     * @return string
     */
    public function generateSheetData()
    {
        // Ensure that Spans have been calculated?
        if ($this->sheetIndex !== null || !$this->spansAreCalculated) {
            $this->calculateSpans();
        }

        // Fetch sheets
        $sheets = [];
        if ($this->sheetIndex === null) {
            $sheets = $this->spreadsheet->getAllSheets();
        } else {
            $sheets[] = $this->spreadsheet->getSheet($this->sheetIndex);
        }

        // Construct HTML
        $html = '';

        // Loop all sheets
        $sheetId = 0;
        foreach ($sheets as $sheet) {
            // Write table header
            $html .= $this->generateTableHeader($sheet);

            // Get worksheet dimension
            $dimension = explode(':', $sheet->calculateWorksheetDimension());
            $dimension[0] = Coordinate::coordinateFromString($dimension[0]);
            $dimension[0][0] = Coordinate::columnIndexFromString($dimension[0][0]);
            $dimension[1] = Coordinate::coordinateFromString($dimension[1]);
            $dimension[1][0] = Coordinate::columnIndexFromString($dimension[1][0]);

            // row min,max
            $rowMin = $dimension[0][1];
            $rowMax = $dimension[1][1];

            // calculate start of <tbody>, <thead>
            $tbodyStart = $rowMin;
            $theadStart = $theadEnd = 0; // default: no <thead>    no </thead>
            if ($sheet->getPageSetup()->isRowsToRepeatAtTopSet()) {
                $rowsToRepeatAtTop = $sheet->getPageSetup()->getRowsToRepeatAtTop();

                // we can only support repeating rows that start at top row
                if ($rowsToRepeatAtTop[0] == 1) {
                    $theadStart = $rowsToRepeatAtTop[0];
                    $theadEnd = $rowsToRepeatAtTop[1];
                    $tbodyStart = $rowsToRepeatAtTop[1] + 1;
                }
            }

            // Loop through cells
            $row = $rowMin - 1;
            while ($row++ < $rowMax) {
                // <thead> ?
                if ($row == $theadStart) {
                    $html .= '        <thead>' . PHP_EOL;
                    $cellType = 'th';
                }

                // <tbody> ?
                if ($row == $tbodyStart) {
                    $html .= '        <tbody>' . PHP_EOL;
                    $cellType = 'td';
                }

                // Write row if there are HTML table cells in it
                if (!isset($this->isSpannedRow[$sheet->getParent()->getIndex($sheet)][$row])) {
                    // Start a new rowData
                    $rowData = [];
                    // Loop through columns
                    $column = $dimension[0][0];
                    while ($column <= $dimension[1][0]) {
                        // Cell exists?
                        if ($sheet->cellExistsByColumnAndRow($column, $row)) {
                            $rowData[$column] = Coordinate::stringFromColumnIndex($column) . $row;
                        } else {
                            $rowData[$column] = '';
                        }
                        ++$column;
                    }
                    $html .= $this->generateRow($sheet, $rowData, $row - 1, $cellType);
                }

                // </thead> ?
                if ($row == $theadEnd) {
                    $html .= '        </thead>' . PHP_EOL;
                }
            }
            $html .= $this->extendRowsForChartsAndImages($sheet, $row);

            // Close table body.
            $html .= '        </tbody>' . PHP_EOL;

            // Write table footer
            $html .= $this->generateTableFooter();

            // Writing PDF?
            if ($this->isPdf) {
                if ($this->sheetIndex === null && $sheetId + 1 < $this->spreadsheet->getSheetCount()) {
                    $html .= '<div style="page-break-before:always" />';
                }
            }

            // Next sheet
            ++$sheetId;
        }

        return $html;
    }

    /**
     * Generate sheet tabs.
     *
     * @throws WriterException
     *
     * @return string
     */
    public function generateNavigation()
    {
        // Fetch sheets
        $sheets = [];
        if ($this->sheetIndex === null) {
            $sheets = $this->spreadsheet->getAllSheets();
        } else {
            $sheets[] = $this->spreadsheet->getSheet($this->sheetIndex);
        }

        // Construct HTML
        $html = '';

        // Only if there are more than 1 sheets
        if (count($sheets) > 1) {
            // Loop all sheets
            $sheetId = 0;

            $html .= '<ul class="navigation">' . PHP_EOL;

            foreach ($sheets as $sheet) {
                $html .= '  <li class="sheet' . $sheetId . '"><a href="#sheet' . $sheetId . '">' . $sheet->getTitle() . '</a></li>' . PHP_EOL;
                ++$sheetId;
            }

            $html .= '</ul>' . PHP_EOL;
        }

        return $html;
    }

    private function extendRowsForChartsAndImages(Worksheet $pSheet, $row)
    {
        $rowMax = $row;
        $colMax = 'A';
        if ($this->includeCharts) {
            foreach ($pSheet->getChartCollection() as $chart) {
                if ($chart instanceof Chart) {
                    $chartCoordinates = $chart->getTopLeftPosition();
                    $chartTL = Coordinate::coordinateFromString($chartCoordinates['cell']);
                    $chartCol = Coordinate::columnIndexFromString($chartTL[0]);
                    if ($chartTL[1] > $rowMax) {
                        $rowMax = $chartTL[1];
                        if ($chartCol > Coordinate::columnIndexFromString($colMax)) {
                            $colMax = $chartTL[0];
                        }
                    }
                }
            }
        }

        foreach ($pSheet->getDrawingCollection() as $drawing) {
            if ($drawing instanceof Drawing) {
                $imageTL = Coordinate::coordinateFromString($drawing->getCoordinates());
                $imageCol = Coordinate::columnIndexFromString($imageTL[0]);
                if ($imageTL[1] > $rowMax) {
                    $rowMax = $imageTL[1];
                    if ($imageCol > Coordinate::columnIndexFromString($colMax)) {
                        $colMax = $imageTL[0];
                    }
                }
            }
        }

        // Don't extend rows if not needed
        if ($row === $rowMax) {
            return '';
        }

        $html = '';
        ++$colMax;

        while ($row <= $rowMax) {
            $html .= '<tr>';
            for ($col = 'A'; $col != $colMax; ++$col) {
                $html .= '<td>';
                $html .= $this->writeImageInCell($pSheet, $col . $row);
                if ($this->includeCharts) {
                    $html .= $this->writeChartInCell($pSheet, $col . $row);
                }
                $html .= '</td>';
            }
            ++$row;
            $html .= '</tr>';
        }

        return $html;
    }

    /**
     * Generate image tag in cell.
     *
     * @param Worksheet $pSheet \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     * @param string $coordinates Cell coordinates
     *
     * @return string
     */
    private function writeImageInCell(Worksheet $pSheet, $coordinates)
    {
        // Construct HTML
        $html = '';

        // Write images
        foreach ($pSheet->getDrawingCollection() as $drawing) {
            if ($drawing instanceof Drawing) {
                if ($drawing->getCoordinates() == $coordinates) {
                    $filename = $drawing->getPath();

                    // Strip off eventual '.'
                    if (substr($filename, 0, 1) == '.') {
                        $filename = substr($filename, 1);
                    }

                    // Prepend images root
                    $filename = $this->getImagesRoot() . $filename;

                    // Strip off eventual '.'
                    if (substr($filename, 0, 1) == '.' && substr($filename, 0, 2) != './') {
                        $filename = substr($filename, 1);
                    }

                    // Convert UTF8 data to PCDATA
                    $filename = htmlspecialchars($filename);

                    $html .= PHP_EOL;
                    if ((!$this->embedImages) || ($this->isPdf)) {
                        $imageData = $filename;
                    } else {
                        $imageDetails = getimagesize($filename);
                        if ($fp = fopen($filename, 'rb', 0)) {
                            $picture = '';
                            while (!feof($fp)) {
                                $picture .= fread($fp, 1024);
                            }
                            fclose($fp);
                            // base64 encode the binary data, then break it
                            // into chunks according to RFC 2045 semantics
                            $base64 = chunk_split(base64_encode($picture));
                            $imageData = 'data:' . $imageDetails['mime'] . ';base64,' . $base64;
                        } else {
                            $imageData = $filename;
                        }
                    }

                    $html .= '<div style="position: relative;">';
                    $html .= '<img style="position: absolute; z-index: 1; left: ' .
                        $drawing->getOffsetX() . 'px; top: ' . $drawing->getOffsetY() . 'px; width: ' .
                        $drawing->getWidth() . 'px; height: ' . $drawing->getHeight() . 'px;" src="' .
                        $imageData . '" border="0" />';
                    $html .= '</div>';
                }
            } elseif ($drawing instanceof MemoryDrawing) {
                if ($drawing->getCoordinates() != $coordinates) {
                    continue;
                }
                ob_start(); //  Let's start output buffering.
                imagepng($drawing->getImageResource()); //  This will normally output the image, but because of ob_start(), it won't.
                $contents = ob_get_contents(); //  Instead, output above is saved to $contents
                ob_end_clean(); //  End the output buffer.

                $dataUri = 'data:image/jpeg;base64,' . base64_encode($contents);

                //  Because of the nature of tables, width is more important than height.
                //  max-width: 100% ensures that image doesnt overflow containing cell
                //  width: X sets width of supplied image.
                //  As a result, images bigger than cell will be contained and images smaller will not get stretched
                $html .= '<img src="' . $dataUri . '" style="max-width:100%;width:' . $drawing->getWidth() . 'px;" />';
            }
        }

        return $html;
    }

    /**
     * Generate chart tag in cell.
     *
     * @param Worksheet $pSheet \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     * @param string $coordinates Cell coordinates
     *
     * @return string
     */
    private function writeChartInCell(Worksheet $pSheet, $coordinates)
    {
        // Construct HTML
        $html = '';

        // Write charts
        foreach ($pSheet->getChartCollection() as $chart) {
            if ($chart instanceof Chart) {
                $chartCoordinates = $chart->getTopLeftPosition();
                if ($chartCoordinates['cell'] == $coordinates) {
                    $chartFileName = File::sysGetTempDir() . '/' . uniqid('', true) . '.png';
                    if (!$chart->render($chartFileName)) {
                        return;
                    }

                    $html .= PHP_EOL;
                    $imageDetails = getimagesize($chartFileName);
                    if ($fp = fopen($chartFileName, 'rb', 0)) {
                        $picture = fread($fp, filesize($chartFileName));
                        fclose($fp);
                        // base64 encode the binary data, then break it
                        // into chunks according to RFC 2045 semantics
                        $base64 = chunk_split(base64_encode($picture));
                        $imageData = 'data:' . $imageDetails['mime'] . ';base64,' . $base64;

                        $html .= '<div style="position: relative;">';
                        $html .= '<img style="position: absolute; z-index: 1; left: ' . $chartCoordinates['xOffset'] . 'px; top: ' . $chartCoordinates['yOffset'] . 'px; width: ' . $imageDetails[0] . 'px; height: ' . $imageDetails[1] . 'px;" src="' . $imageData . '" border="0" />' . PHP_EOL;
                        $html .= '</div>';

                        unlink($chartFileName);
                    }
                }
            }
        }

        // Return
        return $html;
    }

    /**
     * Generate CSS styles.
     *
     * @param bool $generateSurroundingHTML Generate surrounding HTML tags? (&lt;style&gt; and &lt;/style&gt;)
     *
     * @throws WriterException
     *
     * @return string
     */
    public function generateStyles($generateSurroundingHTML = true)
    {
        // Build CSS
        $css = $this->buildCSS($generateSurroundingHTML);

        // Construct HTML
        $html = '';

        // Start styles
        if ($generateSurroundingHTML) {
            $html .= '    <style type="text/css">' . PHP_EOL;
            $html .= '      html { ' . $this->assembleCSS($css['html']) . ' }' . PHP_EOL;
        }

        // Write all other styles
        foreach ($css as $styleName => $styleDefinition) {
            if ($styleName != 'html') {
                $html .= '      ' . $styleName . ' { ' . $this->assembleCSS($styleDefinition) . ' }' . PHP_EOL;
            }
        }

        // End styles
        if ($generateSurroundingHTML) {
            $html .= '    </style>' . PHP_EOL;
        }

        // Return
        return $html;
    }

    /**
     * Build CSS styles.
     *
     * @param bool $generateSurroundingHTML Generate surrounding HTML style? (html { })
     *
     * @throws WriterException
     *
     * @return array
     */
    public function buildCSS($generateSurroundingHTML = true)
    {
        // Cached?
        if ($this->cssStyles !== null) {
            return $this->cssStyles;
        }

        // Ensure that spans have been calculated
        if (!$this->spansAreCalculated) {
            $this->calculateSpans();
        }

        // Construct CSS
        $css = [];

        // Start styles
        if ($generateSurroundingHTML) {
            // html { }
            $css['html']['font-family'] = 'Calibri, Arial, Helvetica, sans-serif';
            $css['html']['font-size'] = '11pt';
            $css['html']['background-color'] = 'white';
        }

        // CSS for comments as found in LibreOffice
        $css['a.comment-indicator:hover + div.comment'] = [
            'background' => '#ffd',
            'position' => 'absolute',
            'display' => 'block',
            'border' => '1px solid black',
            'padding' => '0.5em',
        ];

        $css['a.comment-indicator'] = [
            'background' => 'red',
            'display' => 'inline-block',
            'border' => '1px solid black',
            'width' => '0.5em',
            'height' => '0.5em',
        ];

        $css['div.comment']['display'] = 'none';

        // table { }
        $css['table']['border-collapse'] = 'collapse';
        if (!$this->isPdf) {
            $css['table']['page-break-after'] = 'always';
        }

        // .gridlines td { }
        $css['.gridlines td']['border'] = '1px dotted black';
        $css['.gridlines th']['border'] = '1px dotted black';

        // .b {}
        $css['.b']['text-align'] = 'center'; // BOOL

        // .e {}
        $css['.e']['text-align'] = 'center'; // ERROR

        // .f {}
        $css['.f']['text-align'] = 'right'; // FORMULA

        // .inlineStr {}
        $css['.inlineStr']['text-align'] = 'left'; // INLINE

        // .n {}
        $css['.n']['text-align'] = 'right'; // NUMERIC

        // .s {}
        $css['.s']['text-align'] = 'left'; // STRING

        // Calculate cell style hashes
        foreach ($this->spreadsheet->getCellXfCollection() as $index => $style) {
            $css['td.style' . $index] = $this->createCSSStyle($style);
            $css['th.style' . $index] = $this->createCSSStyle($style);
        }

        // Fetch sheets
        $sheets = [];
        if ($this->sheetIndex === null) {
            $sheets = $this->spreadsheet->getAllSheets();
        } else {
            $sheets[] = $this->spreadsheet->getSheet($this->sheetIndex);
        }

        // Build styles per sheet
        foreach ($sheets as $sheet) {
            // Calculate hash code
            $sheetIndex = $sheet->getParent()->getIndex($sheet);

            // Build styles
            // Calculate column widths
            $sheet->calculateColumnWidths();

            // col elements, initialize
            $highestColumnIndex = Coordinate::columnIndexFromString($sheet->getHighestColumn()) - 1;
            $column = -1;
            while ($column++ < $highestColumnIndex) {
                $this->columnWidths[$sheetIndex][$column] = 42; // approximation
                $css['table.sheet' . $sheetIndex . ' col.col' . $column]['width'] = '42pt';
            }

            // col elements, loop through columnDimensions and set width
            foreach ($sheet->getColumnDimensions() as $columnDimension) {
                if (($width = SharedDrawing::cellDimensionToPixels($columnDimension->getWidth(), $this->defaultFont)) >= 0) {
                    $width = SharedDrawing::pixelsToPoints($width);
                    $column = Coordinate::columnIndexFromString($columnDimension->getColumnIndex()) - 1;
                    $this->columnWidths[$sheetIndex][$column] = $width;
                    $css['table.sheet' . $sheetIndex . ' col.col' . $column]['width'] = $width . 'pt';

                    if ($columnDimension->getVisible() === false) {
                        $css['table.sheet' . $sheetIndex . ' .column' . $column]['visibility'] = 'collapse';
                        $css['table.sheet' . $sheetIndex . ' .column' . $column]['display'] = 'none'; // target IE6+7
                    }
                }
            }

            // Default row height
            $rowDimension = $sheet->getDefaultRowDimension();

            // table.sheetN tr { }
            $css['table.sheet' . $sheetIndex . ' tr'] = [];

            if ($rowDimension->getRowHeight() == -1) {
                $pt_height = SharedFont::getDefaultRowHeightByFont($this->spreadsheet->getDefaultStyle()->getFont());
            } else {
                $pt_height = $rowDimension->getRowHeight();
            }
            $css['table.sheet' . $sheetIndex . ' tr']['height'] = $pt_height . 'pt';
            if ($rowDimension->getVisible() === false) {
                $css['table.sheet' . $sheetIndex . ' tr']['display'] = 'none';
                $css['table.sheet' . $sheetIndex . ' tr']['visibility'] = 'hidden';
            }

            // Calculate row heights
            foreach ($sheet->getRowDimensions() as $rowDimension) {
                $row = $rowDimension->getRowIndex() - 1;

                // table.sheetN tr.rowYYYYYY { }
                $css['table.sheet' . $sheetIndex . ' tr.row' . $row] = [];

                if ($rowDimension->getRowHeight() == -1) {
                    $pt_height = SharedFont::getDefaultRowHeightByFont($this->spreadsheet->getDefaultStyle()->getFont());
                } else {
                    $pt_height = $rowDimension->getRowHeight();
                }
                $css['table.sheet' . $sheetIndex . ' tr.row' . $row]['height'] = $pt_height . 'pt';
                if ($rowDimension->getVisible() === false) {
                    $css['table.sheet' . $sheetIndex . ' tr.row' . $row]['display'] = 'none';
                    $css['table.sheet' . $sheetIndex . ' tr.row' . $row]['visibility'] = 'hidden';
                }
            }
        }

        // Cache
        if ($this->cssStyles === null) {
            $this->cssStyles = $css;
        }

        // Return
        return $css;
    }

    /**
     * Create CSS style.
     *
     * @param Style $pStyle
     *
     * @return array
     */
    private function createCSSStyle(Style $pStyle)
    {
        // Create CSS
        return array_merge(
            $this->createCSSStyleAlignment($pStyle->getAlignment()),
            $this->createCSSStyleBorders($pStyle->getBorders()),
            $this->createCSSStyleFont($pStyle->getFont()),
            $this->createCSSStyleFill($pStyle->getFill())
        );
    }

    /**
     * Create CSS style (\PhpOffice\PhpSpreadsheet\Style\Alignment).
     *
     * @param Alignment $pStyle \PhpOffice\PhpSpreadsheet\Style\Alignment
     *
     * @return array
     */
    private function createCSSStyleAlignment(Alignment $pStyle)
    {
        // Construct CSS
        $css = [];

        // Create CSS
        $css['vertical-align'] = $this->mapVAlign($pStyle->getVertical());
        if ($textAlign = $this->mapHAlign($pStyle->getHorizontal())) {
            $css['text-align'] = $textAlign;
            if (in_array($textAlign, ['left', 'right'])) {
                $css['padding-' . $textAlign] = (string) ((int) $pStyle->getIndent() * 9) . 'px';
            }
        }

        return $css;
    }

    /**
     * Create CSS style (\PhpOffice\PhpSpreadsheet\Style\Font).
     *
     * @param Font $pStyle
     *
     * @return array
     */
    private function createCSSStyleFont(Font $pStyle)
    {
        // Construct CSS
        $css = [];

        // Create CSS
        if ($pStyle->getBold()) {
            $css['font-weight'] = 'bold';
        }
        if ($pStyle->getUnderline() != Font::UNDERLINE_NONE && $pStyle->getStrikethrough()) {
            $css['text-decoration'] = 'underline line-through';
        } elseif ($pStyle->getUnderline() != Font::UNDERLINE_NONE) {
            $css['text-decoration'] = 'underline';
        } elseif ($pStyle->getStrikethrough()) {
            $css['text-decoration'] = 'line-through';
        }
        if ($pStyle->getItalic()) {
            $css['font-style'] = 'italic';
        }

        $css['color'] = '#' . $pStyle->getColor()->getRGB();
        $css['font-family'] = '\'' . $pStyle->getName() . '\'';
        $css['font-size'] = $pStyle->getSize() . 'pt';

        return $css;
    }

    /**
     * Create CSS style (Borders).
     *
     * @param Borders $pStyle Borders
     *
     * @return array
     */
    private function createCSSStyleBorders(Borders $pStyle)
    {
        // Construct CSS
        $css = [];

        // Create CSS
        $css['border-bottom'] = $this->createCSSStyleBorder($pStyle->getBottom());
        $css['border-top'] = $this->createCSSStyleBorder($pStyle->getTop());
        $css['border-left'] = $this->createCSSStyleBorder($pStyle->getLeft());
        $css['border-right'] = $this->createCSSStyleBorder($pStyle->getRight());

        return $css;
    }

    /**
     * Create CSS style (Border).
     *
     * @param Border $pStyle Border
     *
     * @return string
     */
    private function createCSSStyleBorder(Border $pStyle)
    {
        //    Create CSS - add !important to non-none border styles for merged cells
        $borderStyle = $this->mapBorderStyle($pStyle->getBorderStyle());

        return $borderStyle . ' #' . $pStyle->getColor()->getRGB() . (($borderStyle == 'none') ? '' : ' !important');
    }

    /**
     * Create CSS style (Fill).
     *
     * @param Fill $pStyle Fill
     *
     * @return array
     */
    private function createCSSStyleFill(Fill $pStyle)
    {
        // Construct HTML
        $css = [];

        // Create CSS
        $value = $pStyle->getFillType() == Fill::FILL_NONE ?
            'white' : '#' . $pStyle->getStartColor()->getRGB();
        $css['background-color'] = $value;

        return $css;
    }

    /**
     * Generate HTML footer.
     */
    public function generateHTMLFooter()
    {
        // Construct HTML
        $html = '';
        $html .= '  </body>' . PHP_EOL;
        $html .= '</html>' . PHP_EOL;

        return $html;
    }

    /**
     * Generate table header.
     *
     * @param Worksheet $pSheet The worksheet for the table we are writing
     *
     * @return string
     */
    private function generateTableHeader($pSheet)
    {
        $sheetIndex = $pSheet->getParent()->getIndex($pSheet);

        // Construct HTML
        $html = '';
        if ($this->useEmbeddedCSS) {
            $html .= $this->setMargins($pSheet);
        }

        if (!$this->useInlineCss) {
            $gridlines = $pSheet->getShowGridlines() ? ' gridlines' : '';
            $html .= '    <table border="0" cellpadding="0" cellspacing="0" id="sheet' . $sheetIndex . '" class="sheet' . $sheetIndex . $gridlines . '">' . PHP_EOL;
        } else {
            $style = isset($this->cssStyles['table']) ?
                $this->assembleCSS($this->cssStyles['table']) : '';

            if ($this->isPdf && $pSheet->getShowGridlines()) {
                $html .= '    <table border="1" cellpadding="1" id="sheet' . $sheetIndex . '" cellspacing="1" style="' . $style . '">' . PHP_EOL;
            } else {
                $html .= '    <table border="0" cellpadding="1" id="sheet' . $sheetIndex . '" cellspacing="0" style="' . $style . '">' . PHP_EOL;
            }
        }

        // Write <col> elements
        $highestColumnIndex = Coordinate::columnIndexFromString($pSheet->getHighestColumn()) - 1;
        $i = -1;
        while ($i++ < $highestColumnIndex) {
            if (!$this->isPdf) {
                if (!$this->useInlineCss) {
                    $html .= '        <col class="col' . $i . '">' . PHP_EOL;
                } else {
                    $style = isset($this->cssStyles['table.sheet' . $sheetIndex . ' col.col' . $i]) ?
                        $this->assembleCSS($this->cssStyles['table.sheet' . $sheetIndex . ' col.col' . $i]) : '';
                    $html .= '        <col style="' . $style . '">' . PHP_EOL;
                }
            }
        }

        return $html;
    }

    /**
     * Generate table footer.
     */
    private function generateTableFooter()
    {
        return '    </table>' . PHP_EOL;
    }

    /**
     * Generate row.
     *
     * @param Worksheet $pSheet \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     * @param array $pValues Array containing cells in a row
     * @param int $pRow Row number (0-based)
     * @param string $cellType eg: 'td'
     *
     * @throws WriterException
     *
     * @return string
     */
    private function generateRow(Worksheet $pSheet, array $pValues, $pRow, $cellType)
    {
        // Construct HTML
        $html = '';

        // Sheet index
        $sheetIndex = $pSheet->getParent()->getIndex($pSheet);

        // Dompdf and breaks
        if ($this->isPdf && count($pSheet->getBreaks()) > 0) {
            $breaks = $pSheet->getBreaks();

            // check if a break is needed before this row
            if (isset($breaks['A' . $pRow])) {
                // close table: </table>
                $html .= $this->generateTableFooter();

                // insert page break
                $html .= '<div style="page-break-before:always" />';

                // open table again: <table> + <col> etc.
                $html .= $this->generateTableHeader($pSheet);
            }
        }

        // Write row start
        if (!$this->useInlineCss) {
            $html .= '          <tr class="row' . $pRow . '">' . PHP_EOL;
        } else {
            $style = isset($this->cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $pRow])
                ? $this->assembleCSS($this->cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $pRow]) : '';

            $html .= '          <tr style="' . $style . '">' . PHP_EOL;
        }

        // Write cells
        $colNum = 0;
        foreach ($pValues as $cellAddress) {
            $cell = ($cellAddress > '') ? $pSheet->getCell($cellAddress) : '';
            $coordinate = Coordinate::stringFromColumnIndex($colNum + 1) . ($pRow + 1);
            if (!$this->useInlineCss) {
                $cssClass = 'column' . $colNum;
            } else {
                $cssClass = [];
                if ($cellType == 'th') {
                    if (isset($this->cssStyles['table.sheet' . $sheetIndex . ' th.column' . $colNum])) {
                        $this->cssStyles['table.sheet' . $sheetIndex . ' th.column' . $colNum];
                    }
                } else {
                    if (isset($this->cssStyles['table.sheet' . $sheetIndex . ' td.column' . $colNum])) {
                        $this->cssStyles['table.sheet' . $sheetIndex . ' td.column' . $colNum];
                    }
                }
            }
            $colSpan = 1;
            $rowSpan = 1;

            // initialize
            $cellData = '&nbsp;';

            // Cell
            if ($cell instanceof Cell) {
                $cellData = '';
                if ($cell->getParent() === null) {
                    $cell->attach($pSheet);
                }
                // Value
                if ($cell->getValue() instanceof RichText) {
                    // Loop through rich text elements
                    $elements = $cell->getValue()->getRichTextElements();
                    foreach ($elements as $element) {
                        // Rich text start?
                        if ($element instanceof Run) {
                            $cellData .= '<span style="' . $this->assembleCSS($this->createCSSStyleFont($element->getFont())) . '">';

                            if ($element->getFont()->getSuperscript()) {
                                $cellData .= '<sup>';
                            } elseif ($element->getFont()->getSubscript()) {
                                $cellData .= '<sub>';
                            }
                        }

                        // Convert UTF8 data to PCDATA
                        $cellText = $element->getText();
                        $cellData .= htmlspecialchars($cellText);

                        if ($element instanceof Run) {
                            if ($element->getFont()->getSuperscript()) {
                                $cellData .= '</sup>';
                            } elseif ($element->getFont()->getSubscript()) {
                                $cellData .= '</sub>';
                            }

                            $cellData .= '</span>';
                        }
                    }
                } else {
                    if ($this->preCalculateFormulas) {
                        $cellData = NumberFormat::toFormattedString(
                            $cell->getCalculatedValue(),
                            $pSheet->getParent()->getCellXfByIndex($cell->getXfIndex())->getNumberFormat()->getFormatCode(),
                            [$this, 'formatColor']
                        );
                    } else {
                        $cellData = NumberFormat::toFormattedString(
                            $cell->getValue(),
                            $pSheet->getParent()->getCellXfByIndex($cell->getXfIndex())->getNumberFormat()->getFormatCode(),
                            [$this, 'formatColor']
                        );
                    }
                    $cellData = htmlspecialchars($cellData);
                    if ($pSheet->getParent()->getCellXfByIndex($cell->getXfIndex())->getFont()->getSuperscript()) {
                        $cellData = '<sup>' . $cellData . '</sup>';
                    } elseif ($pSheet->getParent()->getCellXfByIndex($cell->getXfIndex())->getFont()->getSubscript()) {
                        $cellData = '<sub>' . $cellData . '</sub>';
                    }
                }

                // Converts the cell content so that spaces occuring at beginning of each new line are replaced by &nbsp;
                // Example: "  Hello\n to the world" is converted to "&nbsp;&nbsp;Hello\n&nbsp;to the world"
                $cellData = preg_replace('/(?m)(?:^|\\G) /', '&nbsp;', $cellData);

                // convert newline "\n" to '<br>'
                $cellData = nl2br($cellData);

                // Extend CSS class?
                if (!$this->useInlineCss) {
                    $cssClass .= ' style' . $cell->getXfIndex();
                    $cssClass .= ' ' . $cell->getDataType();
                } else {
                    if ($cellType == 'th') {
                        if (isset($this->cssStyles['th.style' . $cell->getXfIndex()])) {
                            $cssClass = array_merge($cssClass, $this->cssStyles['th.style' . $cell->getXfIndex()]);
                        }
                    } else {
                        if (isset($this->cssStyles['td.style' . $cell->getXfIndex()])) {
                            $cssClass = array_merge($cssClass, $this->cssStyles['td.style' . $cell->getXfIndex()]);
                        }
                    }

                    // General horizontal alignment: Actual horizontal alignment depends on dataType
                    $sharedStyle = $pSheet->getParent()->getCellXfByIndex($cell->getXfIndex());
                    if ($sharedStyle->getAlignment()->getHorizontal() == Alignment::HORIZONTAL_GENERAL
                        && isset($this->cssStyles['.' . $cell->getDataType()]['text-align'])
                    ) {
                        $cssClass['text-align'] = $this->cssStyles['.' . $cell->getDataType()]['text-align'];
                    }
                }
            }

            // Hyperlink?
            if ($pSheet->hyperlinkExists($coordinate) && !$pSheet->getHyperlink($coordinate)->isInternal()) {
                $cellData = '<a href="' . htmlspecialchars($pSheet->getHyperlink($coordinate)->getUrl()) . '" title="' . htmlspecialchars($pSheet->getHyperlink($coordinate)->getTooltip()) . '">' . $cellData . '</a>';
            }

            // Should the cell be written or is it swallowed by a rowspan or colspan?
            $writeCell = !(isset($this->isSpannedCell[$pSheet->getParent()->getIndex($pSheet)][$pRow + 1][$colNum])
                && $this->isSpannedCell[$pSheet->getParent()->getIndex($pSheet)][$pRow + 1][$colNum]);

            // Colspan and Rowspan
            $colspan = 1;
            $rowspan = 1;
            if (isset($this->isBaseCell[$pSheet->getParent()->getIndex($pSheet)][$pRow + 1][$colNum])) {
                $spans = $this->isBaseCell[$pSheet->getParent()->getIndex($pSheet)][$pRow + 1][$colNum];
                $rowSpan = $spans['rowspan'];
                $colSpan = $spans['colspan'];

                //    Also apply style from last cell in merge to fix borders -
                //        relies on !important for non-none border declarations in createCSSStyleBorder
                $endCellCoord = Coordinate::stringFromColumnIndex($colNum + $colSpan) . ($pRow + $rowSpan);
                if (!$this->useInlineCss) {
                    $cssClass .= ' style' . $pSheet->getCell($endCellCoord)->getXfIndex();
                }
            }

            // Write
            if ($writeCell) {
                // Column start
                $html .= '            <' . $cellType;
                if (!$this->useInlineCss) {
                    $html .= ' class="' . $cssClass . '"';
                } else {
                    //** Necessary redundant code for the sake of \PhpOffice\PhpSpreadsheet\Writer\Pdf **
                    // We must explicitly write the width of the <td> element because TCPDF
                    // does not recognize e.g. <col style="width:42pt">
                    $width = 0;
                    $i = $colNum - 1;
                    $e = $colNum + $colSpan - 1;
                    while ($i++ < $e) {
                        if (isset($this->columnWidths[$sheetIndex][$i])) {
                            $width += $this->columnWidths[$sheetIndex][$i];
                        }
                    }
                    $cssClass['width'] = $width . 'pt';

                    // We must also explicitly write the height of the <td> element because TCPDF
                    // does not recognize e.g. <tr style="height:50pt">
                    if (isset($this->cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $pRow]['height'])) {
                        $height = $this->cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $pRow]['height'];
                        $cssClass['height'] = $height;
                    }
                    //** end of redundant code **

                    $html .= ' style="' . $this->assembleCSS($cssClass) . '"';
                }
                if ($colSpan > 1) {
                    $html .= ' colspan="' . $colSpan . '"';
                }
                if ($rowSpan > 1) {
                    $html .= ' rowspan="' . $rowSpan . '"';
                }
                $html .= '>';

                $html .= $this->writeComment($pSheet, $coordinate);

                // Image?
                $html .= $this->writeImageInCell($pSheet, $coordinate);

                // Chart?
                if ($this->includeCharts) {
                    $html .= $this->writeChartInCell($pSheet, $coordinate);
                }

                // Cell data
                $html .= $cellData;

                // Column end
                $html .= '</' . $cellType . '>' . PHP_EOL;
            }

            // Next column
            ++$colNum;
        }

        // Write row end
        $html .= '          </tr>' . PHP_EOL;

        // Return
        return $html;
    }

    /**
     * Takes array where of CSS properties / values and converts to CSS string.
     *
     * @param array $pValue
     *
     * @return string
     */
    private function assembleCSS(array $pValue = [])
    {
        $pairs = [];
        foreach ($pValue as $property => $value) {
            $pairs[] = $property . ':' . $value;
        }
        $string = implode('; ', $pairs);

        return $string;
    }

    /**
     * Get images root.
     *
     * @return string
     */
    public function getImagesRoot()
    {
        return $this->imagesRoot;
    }

    /**
     * Set images root.
     *
     * @param string $pValue
     *
     * @return $this
     */
    public function setImagesRoot($pValue)
    {
        $this->imagesRoot = $pValue;

        return $this;
    }

    /**
     * Get embed images.
     *
     * @return bool
     */
    public function getEmbedImages()
    {
        return $this->embedImages;
    }

    /**
     * Set embed images.
     *
     * @param bool $pValue
     *
     * @return $this
     */
    public function setEmbedImages($pValue)
    {
        $this->embedImages = $pValue;

        return $this;
    }

    /**
     * Get use inline CSS?
     *
     * @return bool
     */
    public function getUseInlineCss()
    {
        return $this->useInlineCss;
    }

    /**
     * Set use inline CSS?
     *
     * @param bool $pValue
     *
     * @return $this
     */
    public function setUseInlineCss($pValue)
    {
        $this->useInlineCss = $pValue;

        return $this;
    }

    /**
     * Get use embedded CSS?
     *
     * @return bool
     */
    public function getUseEmbeddedCSS()
    {
        return $this->useEmbeddedCSS;
    }

    /**
     * Set use embedded CSS?
     *
     * @param bool $pValue
     *
     * @return $this
     */
    public function setUseEmbeddedCSS($pValue)
    {
        $this->useEmbeddedCSS = $pValue;

        return $this;
    }

    /**
     * Add color to formatted string as inline style.
     *
     * @param string $pValue Plain formatted value without color
     * @param string $pFormat Format code
     *
     * @return string
     */
    public function formatColor($pValue, $pFormat)
    {
        // Color information, e.g. [Red] is always at the beginning
        $color = null; // initialize
        $matches = [];

        $color_regex = '/^\\[[a-zA-Z]+\\]/';
        if (preg_match($color_regex, $pFormat, $matches)) {
            $color = str_replace(['[', ']'], '', $matches[0]);
            $color = strtolower($color);
        }

        // convert to PCDATA
        $value = htmlspecialchars($pValue);

        // color span tag
        if ($color !== null) {
            $value = '<span style="color:' . $color . '">' . $value . '</span>';
        }

        return $value;
    }

    /**
     * Calculate information about HTML colspan and rowspan which is not always the same as Excel's.
     */
    private function calculateSpans()
    {
        // Identify all cells that should be omitted in HTML due to cell merge.
        // In HTML only the upper-left cell should be written and it should have
        //   appropriate rowspan / colspan attribute
        $sheetIndexes = $this->sheetIndex !== null ?
            [$this->sheetIndex] : range(0, $this->spreadsheet->getSheetCount() - 1);

        foreach ($sheetIndexes as $sheetIndex) {
            $sheet = $this->spreadsheet->getSheet($sheetIndex);

            $candidateSpannedRow = [];

            // loop through all Excel merged cells
            foreach ($sheet->getMergeCells() as $cells) {
                [$cells] = Coordinate::splitRange($cells);
                $first = $cells[0];
                $last = $cells[1];

                [$fc, $fr] = Coordinate::coordinateFromString($first);
                $fc = Coordinate::columnIndexFromString($fc) - 1;

                [$lc, $lr] = Coordinate::coordinateFromString($last);
                $lc = Coordinate::columnIndexFromString($lc) - 1;

                // loop through the individual cells in the individual merge
                $r = $fr - 1;
                while ($r++ < $lr) {
                    // also, flag this row as a HTML row that is candidate to be omitted
                    $candidateSpannedRow[$r] = $r;

                    $c = $fc - 1;
                    while ($c++ < $lc) {
                        if (!($c == $fc && $r == $fr)) {
                            // not the upper-left cell (should not be written in HTML)
                            $this->isSpannedCell[$sheetIndex][$r][$c] = [
                                'baseCell' => [$fr, $fc],
                            ];
                        } else {
                            // upper-left is the base cell that should hold the colspan/rowspan attribute
                            $this->isBaseCell[$sheetIndex][$r][$c] = [
                                'xlrowspan' => $lr - $fr + 1, // Excel rowspan
                                'rowspan' => $lr - $fr + 1, // HTML rowspan, value may change
                                'xlcolspan' => $lc - $fc + 1, // Excel colspan
                                'colspan' => $lc - $fc + 1, // HTML colspan, value may change
                            ];
                        }
                    }
                }
            }

            // Identify which rows should be omitted in HTML. These are the rows where all the cells
            //   participate in a merge and the where base cells are somewhere above.
            $countColumns = Coordinate::columnIndexFromString($sheet->getHighestColumn());
            foreach ($candidateSpannedRow as $rowIndex) {
                if (isset($this->isSpannedCell[$sheetIndex][$rowIndex])) {
                    if (count($this->isSpannedCell[$sheetIndex][$rowIndex]) == $countColumns) {
                        $this->isSpannedRow[$sheetIndex][$rowIndex] = $rowIndex;
                    }
                }
            }

            // For each of the omitted rows we found above, the affected rowspans should be subtracted by 1
            if (isset($this->isSpannedRow[$sheetIndex])) {
                foreach ($this->isSpannedRow[$sheetIndex] as $rowIndex) {
                    $adjustedBaseCells = [];
                    $c = -1;
                    $e = $countColumns - 1;
                    while ($c++ < $e) {
                        $baseCell = $this->isSpannedCell[$sheetIndex][$rowIndex][$c]['baseCell'];

                        if (!in_array($baseCell, $adjustedBaseCells)) {
                            // subtract rowspan by 1
                            --$this->isBaseCell[$sheetIndex][$baseCell[0]][$baseCell[1]]['rowspan'];
                            $adjustedBaseCells[] = $baseCell;
                        }
                    }
                }
            }

            // TODO: Same for columns
        }

        // We have calculated the spans
        $this->spansAreCalculated = true;
    }

    private function setMargins(Worksheet $pSheet)
    {
        $htmlPage = '@page { ';
        $htmlBody = 'body { ';

        $left = StringHelper::formatNumber($pSheet->getPageMargins()->getLeft()) . 'in; ';
        $htmlPage .= 'margin-left: ' . $left;
        $htmlBody .= 'margin-left: ' . $left;
        $right = StringHelper::formatNumber($pSheet->getPageMargins()->getRight()) . 'in; ';
        $htmlPage .= 'margin-right: ' . $right;
        $htmlBody .= 'margin-right: ' . $right;
        $top = StringHelper::formatNumber($pSheet->getPageMargins()->getTop()) . 'in; ';
        $htmlPage .= 'margin-top: ' . $top;
        $htmlBody .= 'margin-top: ' . $top;
        $bottom = StringHelper::formatNumber($pSheet->getPageMargins()->getBottom()) . 'in; ';
        $htmlPage .= 'margin-bottom: ' . $bottom;
        $htmlBody .= 'margin-bottom: ' . $bottom;

        $htmlPage .= "}\n";
        $htmlBody .= "}\n";

        return "<style>\n" . $htmlPage . $htmlBody . "</style>\n";
    }

    /**
     * Write a comment in the same format as LibreOffice.
     *
     * @see https://github.com/LibreOffice/core/blob/9fc9bf3240f8c62ad7859947ab8a033ac1fe93fa/sc/source/filter/html/htmlexp.cxx#L1073-L1092
     *
     * @param Worksheet $pSheet
     * @param string $coordinate
     *
     * @return string
     */
    private function writeComment(Worksheet $pSheet, $coordinate)
    {
        $result = '';
        if (!$this->isPdf && isset($pSheet->getComments()[$coordinate])) {
            $result .= '<a class="comment-indicator"></a>';
            $result .= '<div class="comment">' . nl2br($pSheet->getComment($coordinate)->getText()->getPlainText()) . '</div>';
            $result .= PHP_EOL;
        }

        return $result;
    }
}
