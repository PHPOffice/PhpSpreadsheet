<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

use Composer\Pcre\Preg;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Comment;
use PhpOffice\PhpSpreadsheet\Document\Properties;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\RichText\Run;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
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
use PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Html extends BaseWriter
{
    private const DEFAULT_CELL_WIDTH_POINTS = 42;

    private const DEFAULT_CELL_WIDTH_PIXELS = 56;

    /**
     * Migration aid to tell if html tags will be treated as plaintext in comments.
     *     if (
     *         defined(
     *             \PhpOffice\PhpSpreadsheet\Writer\Html::class
     *             . '::COMMENT_HTML_TAGS_PLAINTEXT'
     *         )
     *     ) {
     *         new logic with styling in TextRun elements
     *     } else {
     *         old logic with styling via Html tags
     *     }.
     */
    public const COMMENT_HTML_TAGS_PLAINTEXT = true;

    /**
     * Spreadsheet object.
     */
    protected Spreadsheet $spreadsheet;

    /**
     * Sheet index to write.
     */
    private ?int $sheetIndex = 0;

    /**
     * Images root.
     */
    private string $imagesRoot = '';

    /**
     * embed images, or link to images.
     */
    protected bool $embedImages = false;

    /**
     * Use inline CSS?
     */
    private bool $useInlineCss = false;

    /**
     * Array of CSS styles.
     */
    private ?array $cssStyles = null;

    /**
     * Array of column widths in points.
     */
    private array $columnWidths;

    /**
     * Default font.
     */
    private Font $defaultFont;

    /**
     * Flag whether spans have been calculated.
     */
    private bool $spansAreCalculated = false;

    /**
     * Excel cells that should not be written as HTML cells.
     */
    private array $isSpannedCell = [];

    /**
     * Excel cells that are upper-left corner in a cell merge.
     */
    private array $isBaseCell = [];

    /**
     * Excel rows that should not be written as HTML rows.
     */
    private array $isSpannedRow = [];

    /**
     * Is the current writer creating PDF?
     */
    protected bool $isPdf = false;

    /**
     * Generate the Navigation block.
     */
    private bool $generateSheetNavigationBlock = true;

    /**
     * Callback for editing generated html.
     *
     * @var null|callable
     */
    private $editHtmlCallback;

    /** @var BaseDrawing[] */
    private $sheetDrawings;

    /** @var Chart[] */
    private $sheetCharts;

    private bool $betterBoolean = true;

    private string $getTrue = 'TRUE';

    private string $getFalse = 'FALSE';

    /**
     * Create a new HTML.
     */
    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
        $this->defaultFont = $this->spreadsheet->getDefaultStyle()->getFont();
        $calc = Calculation::getInstance($this->spreadsheet);
        $this->getTrue = $calc->getTRUE();
        $this->getFalse = $calc->getFALSE();
    }

    /**
     * Save Spreadsheet to file.
     *
     * @param resource|string $filename
     */
    public function save($filename, int $flags = 0): void
    {
        $this->processFlags($flags);

        // Open file
        $this->openFileHandle($filename);

        // Write html
        fwrite($this->fileHandle, $this->generateHTMLAll());

        // Close file
        $this->maybeCloseFileHandle();
    }

    /**
     * Save Spreadsheet as html to variable.
     */
    public function generateHtmlAll(): string
    {
        $sheets = $this->generateSheetPrep();
        foreach ($sheets as $sheet) {
            $sheet->calculateArrays($this->preCalculateFormulas);
        }
        // garbage collect
        $this->spreadsheet->garbageCollect();

        $saveDebugLog = Calculation::getInstance($this->spreadsheet)->getDebugLog()->getWriteDebugLog();
        Calculation::getInstance($this->spreadsheet)->getDebugLog()->setWriteDebugLog(false);

        // Build CSS
        $this->buildCSS(!$this->useInlineCss);

        $html = '';

        // Write headers
        $html .= $this->generateHTMLHeader(!$this->useInlineCss);

        // Write navigation (tabs)
        if ((!$this->isPdf) && ($this->generateSheetNavigationBlock)) {
            $html .= $this->generateNavigation();
        }

        // Write data
        $html .= $this->generateSheetData();

        // Write footer
        $html .= $this->generateHTMLFooter();
        $callback = $this->editHtmlCallback;
        if ($callback) {
            $html = $callback($html);
        }

        Calculation::getInstance($this->spreadsheet)->getDebugLog()->setWriteDebugLog($saveDebugLog);

        return $html;
    }

    /**
     * Set a callback to edit the entire HTML.
     *
     * The callback must accept the HTML as string as first parameter,
     * and it must return the edited HTML as string.
     */
    public function setEditHtmlCallback(?callable $callback): void
    {
        $this->editHtmlCallback = $callback;
    }

    /**
     * Map VAlign.
     *
     * @param string $vAlign Vertical alignment
     */
    private function mapVAlign(string $vAlign): string
    {
        return Alignment::VERTICAL_ALIGNMENT_FOR_HTML[$vAlign] ?? '';
    }

    /**
     * Map HAlign.
     *
     * @param string $hAlign Horizontal alignment
     */
    private function mapHAlign(string $hAlign): string
    {
        return Alignment::HORIZONTAL_ALIGNMENT_FOR_HTML[$hAlign] ?? '';
    }

    const BORDER_NONE = 'none';
    const BORDER_ARR = [
        Border::BORDER_NONE => self::BORDER_NONE,
        Border::BORDER_DASHDOT => '1px dashed',
        Border::BORDER_DASHDOTDOT => '1px dotted',
        Border::BORDER_DASHED => '1px dashed',
        Border::BORDER_DOTTED => '1px dotted',
        Border::BORDER_DOUBLE => '3px double',
        Border::BORDER_HAIR => '1px solid',
        Border::BORDER_MEDIUM => '2px solid',
        Border::BORDER_MEDIUMDASHDOT => '2px dashed',
        Border::BORDER_MEDIUMDASHDOTDOT => '2px dotted',
        Border::BORDER_SLANTDASHDOT => '2px dashed',
        Border::BORDER_THICK => '3px solid',
    ];

    /**
     * Map border style.
     *
     * @param int|string $borderStyle Sheet index
     */
    private function mapBorderStyle($borderStyle): string
    {
        return self::BORDER_ARR[$borderStyle] ?? '1px solid';
    }

    /**
     * Get sheet index.
     */
    public function getSheetIndex(): ?int
    {
        return $this->sheetIndex;
    }

    /**
     * Set sheet index.
     *
     * @param int $sheetIndex Sheet index
     *
     * @return $this
     */
    public function setSheetIndex(int $sheetIndex): static
    {
        $this->sheetIndex = $sheetIndex;

        return $this;
    }

    /**
     * Get sheet index.
     */
    public function getGenerateSheetNavigationBlock(): bool
    {
        return $this->generateSheetNavigationBlock;
    }

    /**
     * Set sheet index.
     *
     * @param bool $generateSheetNavigationBlock Flag indicating whether the sheet navigation block should be generated or not
     *
     * @return $this
     */
    public function setGenerateSheetNavigationBlock(bool $generateSheetNavigationBlock): static
    {
        $this->generateSheetNavigationBlock = (bool) $generateSheetNavigationBlock;

        return $this;
    }

    /**
     * Write all sheets (resets sheetIndex to NULL).
     *
     * @return $this
     */
    public function writeAllSheets(): static
    {
        $this->sheetIndex = null;

        return $this;
    }

    private static function generateMeta(?string $val, string $desc): string
    {
        return ($val || $val === '0')
            ? ('      <meta name="' . $desc . '" content="' . htmlspecialchars($val, Settings::htmlEntityFlags()) . '" />' . PHP_EOL)
            : '';
    }

    public const BODY_LINE = '  <body>' . PHP_EOL;

    private const CUSTOM_TO_META = [
        Properties::PROPERTY_TYPE_BOOLEAN => 'bool',
        Properties::PROPERTY_TYPE_DATE => 'date',
        Properties::PROPERTY_TYPE_FLOAT => 'float',
        Properties::PROPERTY_TYPE_INTEGER => 'int',
        Properties::PROPERTY_TYPE_STRING => 'string',
    ];

    /**
     * Generate HTML header.
     *
     * @param bool $includeStyles Include styles?
     */
    public function generateHTMLHeader(bool $includeStyles = false): string
    {
        // Construct HTML
        $properties = $this->spreadsheet->getProperties();
        $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . PHP_EOL;
        $html .= '<html xmlns="http://www.w3.org/1999/xhtml">' . PHP_EOL;
        $html .= '  <head>' . PHP_EOL;
        $html .= '      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . PHP_EOL;
        $html .= '      <meta name="generator" content="PhpSpreadsheet, https://github.com/PHPOffice/PhpSpreadsheet" />' . PHP_EOL;
        $title = $properties->getTitle();
        if ($title === '') {
            $title = $this->spreadsheet->getActiveSheet()->getTitle();
        }
        $html .= '      <title>' . htmlspecialchars($title, Settings::htmlEntityFlags()) . '</title>' . PHP_EOL;
        $html .= self::generateMeta($properties->getCreator(), 'author');
        $html .= self::generateMeta($properties->getTitle(), 'title');
        $html .= self::generateMeta($properties->getDescription(), 'description');
        $html .= self::generateMeta($properties->getSubject(), 'subject');
        $html .= self::generateMeta($properties->getKeywords(), 'keywords');
        $html .= self::generateMeta($properties->getCategory(), 'category');
        $html .= self::generateMeta($properties->getCompany(), 'company');
        $html .= self::generateMeta($properties->getManager(), 'manager');
        $html .= self::generateMeta($properties->getLastModifiedBy(), 'lastModifiedBy');
        $html .= self::generateMeta($properties->getViewport(), 'viewport');
        $date = Date::dateTimeFromTimestamp((string) $properties->getCreated());
        $date->setTimeZone(Date::getDefaultOrLocalTimeZone());
        $html .= self::generateMeta($date->format(DATE_W3C), 'created');
        $date = Date::dateTimeFromTimestamp((string) $properties->getModified());
        $date->setTimeZone(Date::getDefaultOrLocalTimeZone());
        $html .= self::generateMeta($date->format(DATE_W3C), 'modified');

        $customProperties = $properties->getCustomProperties();
        foreach ($customProperties as $customProperty) {
            $propertyValue = $properties->getCustomPropertyValue($customProperty);
            $propertyType = $properties->getCustomPropertyType($customProperty);
            $propertyQualifier = self::CUSTOM_TO_META[$propertyType] ?? null;
            if ($propertyQualifier !== null) {
                if ($propertyType === Properties::PROPERTY_TYPE_BOOLEAN) {
                    $propertyValue = $propertyValue ? '1' : '0';
                } elseif ($propertyType === Properties::PROPERTY_TYPE_DATE) {
                    $date = Date::dateTimeFromTimestamp((string) $propertyValue);
                    $date->setTimeZone(Date::getDefaultOrLocalTimeZone());
                    $propertyValue = $date->format(DATE_W3C);
                } else {
                    $propertyValue = (string) $propertyValue;
                }
                $html .= self::generateMeta($propertyValue, htmlspecialchars("custom.$propertyQualifier.$customProperty"));
            }
        }

        if (!empty($properties->getHyperlinkBase())) {
            $html .= '      <base href="' . htmlspecialchars($properties->getHyperlinkBase()) . '" />' . PHP_EOL;
        }

        $html .= $includeStyles ? $this->generateStyles(true) : $this->generatePageDeclarations(true);

        $html .= '  </head>' . PHP_EOL;
        $html .= '' . PHP_EOL;
        $html .= self::BODY_LINE;

        return $html;
    }

    /** @return Worksheet[] */
    private function generateSheetPrep(): array
    {
        // Fetch sheets
        if ($this->sheetIndex === null) {
            $sheets = $this->spreadsheet->getAllSheets();
        } else {
            $sheets = [$this->spreadsheet->getSheet($this->sheetIndex)];
        }

        return $sheets;
    }

    private function generateSheetStarts(Worksheet $sheet, int $rowMin): array
    {
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

        return [$theadStart, $theadEnd, $tbodyStart];
    }

    private function generateSheetTags(int $row, int $theadStart, int $theadEnd, int $tbodyStart): array
    {
        // <thead> ?
        $startTag = ($row == $theadStart) ? ('        <thead>' . PHP_EOL) : '';
        if (!$startTag) {
            $startTag = ($row == $tbodyStart) ? ('        <tbody>' . PHP_EOL) : '';
        }
        $endTag = ($row == $theadEnd) ? ('        </thead>' . PHP_EOL) : '';
        $cellType = ($row >= $tbodyStart) ? 'td' : 'th';

        return [$cellType, $startTag, $endTag];
    }

    /**
     * Generate sheet data.
     */
    public function generateSheetData(): string
    {
        // Ensure that Spans have been calculated?
        $this->calculateSpans();
        $sheets = $this->generateSheetPrep();

        // Construct HTML
        $html = '';

        // Loop all sheets
        $sheetId = 0;
        foreach ($sheets as $sheet) {
            // Write table header
            $html .= $this->generateTableHeader($sheet);
            $this->sheetCharts = [];
            $this->sheetDrawings = [];

            // Get worksheet dimension
            [$min, $max] = explode(':', $sheet->calculateWorksheetDataDimension());
            [$minCol, $minRow, $minColString] = Coordinate::indexesFromString($min);
            [$maxCol, $maxRow] = Coordinate::indexesFromString($max);
            $this->extendRowsAndColumns($sheet, $maxCol, $maxRow);

            [$theadStart, $theadEnd, $tbodyStart] = $this->generateSheetStarts($sheet, $minRow);

            // Loop through cells
            $row = $minRow - 1;
            while ($row++ < $maxRow) {
                [$cellType, $startTag, $endTag] = $this->generateSheetTags($row, $theadStart, $theadEnd, $tbodyStart);
                $html .= $startTag;

                // Write row if there are HTML table cells in it
                if ($this->shouldGenerateRow($sheet, $row) && !isset($this->isSpannedRow[$sheet->getParentOrThrow()->getIndex($sheet)][$row])) {
                    // Start a new rowData
                    $rowData = [];
                    // Loop through columns
                    $column = $minCol;
                    $colStr = $minColString;
                    while ($column <= $maxCol) {
                        // Cell exists?
                        $cellAddress = Coordinate::stringFromColumnIndex($column) . $row;
                        if ($this->shouldGenerateColumn($sheet, $colStr)) {
                            $rowData[$column] = ($sheet->getCellCollection()->has($cellAddress)) ? $cellAddress : '';
                        }
                        ++$column;
                        ++$colStr;
                    }
                    $html .= $this->generateRow($sheet, $rowData, $row - 1, $cellType);
                }

                $html .= $endTag;
            }

            // Write table footer
            $html .= $this->generateTableFooter();
            // Writing PDF?
            if ($this->isPdf && $this->useInlineCss) {
                if ($this->sheetIndex === null && $sheetId + 1 < $this->spreadsheet->getSheetCount()) {
                    $html .= '<div style="page-break-before:always" ></div>';
                }
            }

            // Next sheet
            ++$sheetId;
        }

        return $html;
    }

    /**
     * Generate sheet tabs.
     */
    public function generateNavigation(): string
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
                $html .= '  <li class="sheet' . $sheetId . '"><a href="#sheet' . $sheetId . '">' . htmlspecialchars($sheet->getTitle()) . '</a></li>' . PHP_EOL;
                ++$sheetId;
            }

            $html .= '</ul>' . PHP_EOL;
        }

        return $html;
    }

    private function extendRowsAndColumns(Worksheet $worksheet, int &$colMax, int &$rowMax): void
    {
        if ($this->includeCharts) {
            foreach ($worksheet->getChartCollection() as $chart) {
                $chartCoordinates = $chart->getTopLeftPosition();
                $this->sheetCharts[$chartCoordinates['cell']] = $chart;
                $chartTL = Coordinate::indexesFromString($chartCoordinates['cell']);
                if ($chartTL[1] > $rowMax) {
                    $rowMax = $chartTL[1];
                }
                if ($chartTL[0] > $colMax) {
                    $colMax = $chartTL[0];
                }
            }
        }
        foreach ($worksheet->getDrawingCollection() as $drawing) {
            if ($drawing instanceof Drawing && $drawing->getPath() === '') {
                continue;
            }
            $imageTL = Coordinate::indexesFromString($drawing->getCoordinates());
            $this->sheetDrawings[$drawing->getCoordinates()] = $drawing;
            if ($imageTL[1] > $rowMax) {
                $rowMax = $imageTL[1];
            }
            if ($imageTL[0] > $colMax) {
                $colMax = $imageTL[0];
            }
        }
    }

    /**
     * Convert Windows file name to file protocol URL.
     *
     * @param string $filename file name on local system
     */
    public static function winFileToUrl(string $filename, bool $mpdf = false): string
    {
        // Windows filename
        if (substr($filename, 1, 2) === ':\\') {
            $protocol = $mpdf ? '' : 'file:///';
            $filename = $protocol . str_replace('\\', '/', $filename);
        }

        return $filename;
    }

    /**
     * Generate image tag in cell.
     *
     * @param string $coordinates Cell coordinates
     */
    private function writeImageInCell(string $coordinates): string
    {
        // Construct HTML
        $html = '';

        // Write images
        $drawing = $this->sheetDrawings[$coordinates] ?? null;
        if ($drawing !== null) {
            $opacity = '';
            $opacityValue = $drawing->getOpacity();
            if ($opacityValue !== null) {
                $opacityValue = $opacityValue / 100000;
                if ($opacityValue >= 0.0 && $opacityValue <= 1.0) {
                    $opacity = "opacity:$opacityValue; ";
                }
            }
            $filedesc = $drawing->getDescription();
            $filedesc = $filedesc ? htmlspecialchars($filedesc, ENT_QUOTES) : 'Embedded image';
            if ($drawing instanceof Drawing && $drawing->getPath() !== '') {
                $filename = $drawing->getPath();

                // Strip off eventual '.'
                $filename = Preg::replace('/^[.]/', '', $filename);

                // Prepend images root
                $filename = $this->getImagesRoot() . $filename;

                // Strip off eventual '.' if followed by non-/
                $filename = Preg::replace('@^[.]([^/])@', '$1', $filename);

                // Convert UTF8 data to PCDATA
                $filename = htmlspecialchars($filename, Settings::htmlEntityFlags());

                $html .= PHP_EOL;
                $imageData = self::winFileToUrl($filename, $this instanceof Pdf\Mpdf);

                if ($this->embedImages || str_starts_with($imageData, 'zip://')) {
                    $imageData = 'data:,';
                    $picture = @file_get_contents($filename);
                    if ($picture !== false) {
                        $mimeContentType = (string) @mime_content_type($filename);
                        if (str_starts_with($mimeContentType, 'image/')) {
                            // base64 encode the binary data
                            $base64 = base64_encode($picture);
                            $imageData = 'data:' . $mimeContentType . ';base64,' . $base64;
                        }
                    }
                }

                $html .= '<img style="' . $opacity . 'position: absolute; z-index: 1; left: '
                    . $drawing->getOffsetX() . 'px; top: ' . $drawing->getOffsetY() . 'px; width: '
                    . $drawing->getWidth() . 'px; height: ' . $drawing->getHeight() . 'px;" src="'
                    . $imageData . '" alt="' . $filedesc . '" />';
            } elseif ($drawing instanceof MemoryDrawing) {
                $imageResource = $drawing->getImageResource();
                if ($imageResource) {
                    ob_start(); //  Let's start output buffering.
                    imagepng($imageResource); //  This will normally output the image, but because of ob_start(), it won't.
                    $contents = (string) ob_get_contents(); //  Instead, output above is saved to $contents
                    ob_end_clean(); //  End the output buffer.

                    $dataUri = 'data:image/png;base64,' . base64_encode($contents);

                    //  Because of the nature of tables, width is more important than height.
                    //  max-width: 100% ensures that image doesnt overflow containing cell
                    //    However, PR #3535 broke test
                    //    25_In_memory_image, apparently because
                    //    of the use of max-with. In addition,
                    //    non-memory-drawings don't use max-width.
                    //    Its use here is suspect and is being eliminated.
                    //  width: X sets width of supplied image.
                    //  As a result, images bigger than cell will be contained and images smaller will not get stretched
                    $html .= '<img alt="' . $filedesc . '" src="' . $dataUri . '" style="' . $opacity . 'width:' . $drawing->getWidth() . 'px;left: '
                        . $drawing->getOffsetX() . 'px; top: ' . $drawing->getOffsetY() . 'px;position: absolute; z-index: 1;" />';
                }
            }
        }

        return $html;
    }

    /**
     * Generate chart tag in cell.
     * This code should be exercised by sample:
     * Chart/32_Chart_read_write_PDF.php.
     */
    private function writeChartInCell(Worksheet $worksheet, string $coordinates): string
    {
        // Construct HTML
        $html = '';

        // Write charts
        $chart = $this->sheetCharts[$coordinates] ?? null;
        if ($chart !== null) {
            $chartCoordinates = $chart->getTopLeftPosition();
            $chartFileName = File::sysGetTempDir() . '/' . uniqid('', true) . '.png';
            $renderedWidth = $chart->getRenderedWidth();
            $renderedHeight = $chart->getRenderedHeight();
            if ($renderedWidth === null || $renderedHeight === null) {
                $this->adjustRendererPositions($chart, $worksheet);
            }
            $title = $chart->getTitle();
            $caption = null;
            $filedesc = '';
            if ($title !== null) {
                $calculatedTitle = $title->getCalculatedTitle($worksheet->getParent());
                if ($calculatedTitle !== null) {
                    $caption = $title->getCaption();
                    $title->setCaption($calculatedTitle);
                }
                $filedesc = $title->getCaptionText($worksheet->getParent());
            }
            $renderSuccessful = $chart->render($chartFileName);
            $chart->setRenderedWidth($renderedWidth);
            $chart->setRenderedHeight($renderedHeight);
            if (isset($title, $caption)) {
                $title->setCaption($caption);
            }
            if (!$renderSuccessful) {
                return '';
            }

            $html .= PHP_EOL;
            $imageDetails = getimagesize($chartFileName) ?: ['', '', 'mime' => ''];

            $filedesc = $filedesc ? htmlspecialchars($filedesc, ENT_QUOTES) : 'Embedded chart';
            $picture = file_get_contents($chartFileName);
            unlink($chartFileName);
            if ($picture !== false) {
                $base64 = base64_encode($picture);
                $imageData = 'data:' . $imageDetails['mime'] . ';base64,' . $base64;

                $html .= '<img style="position: absolute; z-index: 1; left: ' . $chartCoordinates['xOffset'] . 'px; top: ' . $chartCoordinates['yOffset'] . 'px; width: ' . $imageDetails[0] . 'px; height: ' . $imageDetails[1] . 'px;" src="' . $imageData . '" alt="' . $filedesc . '" />' . PHP_EOL;
            }
        }

        // Return
        return $html;
    }

    private function adjustRendererPositions(Chart $chart, Worksheet $sheet): void
    {
        $topLeft = $chart->getTopLeftPosition();
        $bottomRight = $chart->getBottomRightPosition();
        $tlCell = $topLeft['cell'];
        $brCell = $bottomRight['cell'];
        if ($tlCell !== '' && $brCell !== '') {
            $tlCoordinate = Coordinate::indexesFromString($tlCell);
            $brCoordinate = Coordinate::indexesFromString($brCell);
            $totalHeight = 0.0;
            $totalWidth = 0.0;
            $defaultRowHeight = $sheet->getDefaultRowDimension()->getRowHeight();
            $defaultRowHeight = SharedDrawing::pointsToPixels(($defaultRowHeight >= 0) ? $defaultRowHeight : SharedFont::getDefaultRowHeightByFont($this->defaultFont));
            if ($tlCoordinate[1] <= $brCoordinate[1] && $tlCoordinate[0] <= $brCoordinate[0]) {
                for ($row = $tlCoordinate[1]; $row <= $brCoordinate[1]; ++$row) {
                    $height = $sheet->getRowDimension($row)->getRowHeight('pt');
                    $totalHeight += ($height >= 0) ? $height : $defaultRowHeight;
                }
                $rightEdge = $brCoordinate[2];
                ++$rightEdge;
                for ($column = $tlCoordinate[2]; $column !== $rightEdge; ++$column) {
                    $width = $sheet->getColumnDimension($column)->getWidth();
                    $width = ($width < 0) ? self::DEFAULT_CELL_WIDTH_PIXELS : SharedDrawing::cellDimensionToPixels($sheet->getColumnDimension($column)->getWidth(), $this->defaultFont);
                    $totalWidth += $width;
                }
                $chart->setRenderedWidth($totalWidth);
                $chart->setRenderedHeight($totalHeight);
            }
        }
    }

    /**
     * Generate CSS styles.
     *
     * @param bool $generateSurroundingHTML Generate surrounding HTML tags? (&lt;style&gt; and &lt;/style&gt;)
     */
    public function generateStyles(bool $generateSurroundingHTML = true): string
    {
        // Build CSS
        $css = $this->buildCSS($generateSurroundingHTML);

        // Construct HTML
        $html = '';

        // Start styles
        if ($generateSurroundingHTML) {
            $html .= '    <style type="text/css">' . PHP_EOL;
            $html .= (array_key_exists('html', $css)) ? ('      html { ' . $this->assembleCSS($css['html']) . ' }' . PHP_EOL) : '';
        }

        // Write all other styles
        foreach ($css as $styleName => $styleDefinition) {
            if ($styleName != 'html') {
                $html .= '      ' . $styleName . ' { ' . $this->assembleCSS($styleDefinition) . ' }' . PHP_EOL;
            }
        }
        $html .= $this->generatePageDeclarations(false);

        // End styles
        if ($generateSurroundingHTML) {
            $html .= '    </style>' . PHP_EOL;
        }

        // Return
        return $html;
    }

    private function buildCssRowHeights(Worksheet $sheet, array &$css, int $sheetIndex): void
    {
        // Calculate row heights
        foreach ($sheet->getRowDimensions() as $rowDimension) {
            $row = $rowDimension->getRowIndex() - 1;

            // table.sheetN tr.rowYYYYYY { }
            $css['table.sheet' . $sheetIndex . ' tr.row' . $row] = [];

            if ($rowDimension->getRowHeight() != -1) {
                $pt_height = $rowDimension->getRowHeight();
                $css['table.sheet' . $sheetIndex . ' tr.row' . $row]['height'] = $pt_height . 'pt';
            }
            if ($rowDimension->getVisible() === false) {
                $css['table.sheet' . $sheetIndex . ' tr.row' . $row]['display'] = 'none';
                $css['table.sheet' . $sheetIndex . ' tr.row' . $row]['visibility'] = 'hidden';
            }
        }
    }

    private function buildCssPerSheet(Worksheet $sheet, array &$css): void
    {
        // Calculate hash code
        $sheetIndex = $sheet->getParentOrThrow()->getIndex($sheet);
        $setup = $sheet->getPageSetup();
        if ($setup->getFitToPage() && $setup->getFitToHeight() === 1) {
            $css["table.sheet$sheetIndex"]['page-break-inside'] = 'avoid';
            $css["table.sheet$sheetIndex"]['break-inside'] = 'avoid';
        }
        $picture = $sheet->getBackgroundImage();
        if ($picture !== '') {
            $base64 = base64_encode($picture);
            $css["table.sheet$sheetIndex"]['background-image'] = 'url(data:' . $sheet->getBackgroundMime() . ';base64,' . $base64 . ')';
        }

        // Build styles
        // Calculate column widths
        $sheet->calculateColumnWidths();

        // col elements, initialize
        $highestColumnIndex = Coordinate::columnIndexFromString($sheet->getHighestColumn()) - 1;
        $column = -1;
        $colStr = 'A';
        while ($column++ < $highestColumnIndex) {
            $this->columnWidths[$sheetIndex][$column] = self::DEFAULT_CELL_WIDTH_POINTS; // approximation
            if ($this->shouldGenerateColumn($sheet, $colStr)) {
                $css['table.sheet' . $sheetIndex . ' col.col' . $column]['width'] = self::DEFAULT_CELL_WIDTH_POINTS . 'pt';
            }
            ++$colStr;
        }

        // col elements, loop through columnDimensions and set width
        foreach ($sheet->getColumnDimensions() as $columnDimension) {
            $column = Coordinate::columnIndexFromString($columnDimension->getColumnIndex()) - 1;
            $width = SharedDrawing::cellDimensionToPixels($columnDimension->getWidth(), $this->defaultFont);
            $width = SharedDrawing::pixelsToPoints($width);
            if ($columnDimension->getVisible() === false) {
                $css['table.sheet' . $sheetIndex . ' .column' . $column]['display'] = 'none';
                // This would be better but Firefox has an 11-year-old bug.
                // https://bugzilla.mozilla.org/show_bug.cgi?id=819045
                //$css['table.sheet' . $sheetIndex . ' col.col' . $column]['visibility'] = 'collapse';
            }
            if ($width >= 0) {
                $this->columnWidths[$sheetIndex][$column] = $width;
                $css['table.sheet' . $sheetIndex . ' col.col' . $column]['width'] = $width . 'pt';
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

        $this->buildCssRowHeights($sheet, $css, $sheetIndex);
    }

    /**
     * Build CSS styles.
     *
     * @param bool $generateSurroundingHTML Generate surrounding HTML style? (html { })
     */
    public function buildCSS(bool $generateSurroundingHTML = true): array
    {
        // Cached?
        if ($this->cssStyles !== null) {
            return $this->cssStyles;
        }

        // Ensure that spans have been calculated
        $this->calculateSpans();

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
            $css['td.style' . $index . ', th.style' . $index] = $this->createCSSStyle($style);
            //$css['th.style' . $index] = $this->createCSSStyle($style);
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
            $this->buildCssPerSheet($sheet, $css);
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
     */
    private function createCSSStyle(Style $style): array
    {
        // Create CSS
        return array_merge(
            $this->createCSSStyleAlignment($style->getAlignment()),
            $this->createCSSStyleBorders($style->getBorders()),
            $this->createCSSStyleFont($style->getFont()),
            $this->createCSSStyleFill($style->getFill())
        );
    }

    /**
     * Create CSS style.
     */
    private function createCSSStyleAlignment(Alignment $alignment): array
    {
        // Construct CSS
        $css = [];

        // Create CSS
        $verticalAlign = $this->mapVAlign($alignment->getVertical() ?? '');
        if ($verticalAlign) {
            $css['vertical-align'] = $verticalAlign;
        }
        $textAlign = $this->mapHAlign($alignment->getHorizontal() ?? '');
        if ($textAlign) {
            $css['text-align'] = $textAlign;
            if (in_array($textAlign, ['left', 'right'])) {
                $css['padding-' . $textAlign] = (string) ((int) $alignment->getIndent() * 9) . 'px';
            }
        }
        $rotation = $alignment->getTextRotation();
        if ($rotation !== 0 && $rotation !== Alignment::TEXTROTATION_STACK_PHPSPREADSHEET) {
            if ($this instanceof Pdf\Mpdf) {
                $css['text-rotate'] = "$rotation";
            } else {
                $css['transform'] = "rotate({$rotation}deg)";
            }
        }

        return $css;
    }

    /**
     * Create CSS style.
     */
    private function createCSSStyleFont(Font $font): array
    {
        // Construct CSS
        $css = [];

        // Create CSS
        if ($font->getBold()) {
            $css['font-weight'] = 'bold';
        }
        if ($font->getUnderline() != Font::UNDERLINE_NONE && $font->getStrikethrough()) {
            $css['text-decoration'] = 'underline line-through';
        } elseif ($font->getUnderline() != Font::UNDERLINE_NONE) {
            $css['text-decoration'] = 'underline';
        } elseif ($font->getStrikethrough()) {
            $css['text-decoration'] = 'line-through';
        }
        if ($font->getItalic()) {
            $css['font-style'] = 'italic';
        }

        $css['color'] = '#' . $font->getColor()->getRGB();
        $css['font-family'] = '\'' . htmlspecialchars((string) $font->getName(), ENT_QUOTES) . '\'';
        $css['font-size'] = $font->getSize() . 'pt';

        return $css;
    }

    /**
     * Create CSS style.
     *
     * @param Borders $borders Borders
     */
    private function createCSSStyleBorders(Borders $borders): array
    {
        // Construct CSS
        $css = [];

        // Create CSS
        if (!($this instanceof Pdf\Mpdf)) {
            $css['border-bottom'] = $this->createCSSStyleBorder($borders->getBottom());
            $css['border-top'] = $this->createCSSStyleBorder($borders->getTop());
            $css['border-left'] = $this->createCSSStyleBorder($borders->getLeft());
            $css['border-right'] = $this->createCSSStyleBorder($borders->getRight());
        } else {
            // Mpdf doesn't process !important, so omit unimportant border none
            if ($borders->getBottom()->getBorderStyle() !== Border::BORDER_NONE) {
                $css['border-bottom'] = $this->createCSSStyleBorder($borders->getBottom());
            }
            if ($borders->getTop()->getBorderStyle() !== Border::BORDER_NONE) {
                $css['border-top'] = $this->createCSSStyleBorder($borders->getTop());
            }
            if ($borders->getLeft()->getBorderStyle() !== Border::BORDER_NONE) {
                $css['border-left'] = $this->createCSSStyleBorder($borders->getLeft());
            }
            if ($borders->getRight()->getBorderStyle() !== Border::BORDER_NONE) {
                $css['border-right'] = $this->createCSSStyleBorder($borders->getRight());
            }
        }

        return $css;
    }

    /**
     * Create CSS style.
     *
     * @param Border $border Border
     */
    private function createCSSStyleBorder(Border $border): string
    {
        //    Create CSS - add !important to non-none border styles for merged cells
        $borderStyle = $this->mapBorderStyle($border->getBorderStyle());

        return $borderStyle . ' #' . $border->getColor()->getRGB() . (($borderStyle === self::BORDER_NONE) ? '' : ' !important');
    }

    /**
     * Create CSS style (Fill).
     *
     * @param Fill $fill Fill
     */
    private function createCSSStyleFill(Fill $fill): array
    {
        // Construct HTML
        $css = [];

        // Create CSS
        if ($fill->getFillType() !== Fill::FILL_NONE) {
            if (
                (in_array($fill->getFillType(), ['', Fill::FILL_SOLID], true) || !$fill->getEndColor()->getRGB())
                && $fill->getStartColor()->getRGB()
            ) {
                $value = '#' . $fill->getStartColor()->getRGB();
                $css['background-color'] = $value;
            } elseif ($fill->getEndColor()->getRGB()) {
                $value = '#' . $fill->getEndColor()->getRGB();
                $css['background-color'] = $value;
            }
        }

        return $css;
    }

    /**
     * Generate HTML footer.
     */
    public function generateHTMLFooter(): string
    {
        // Construct HTML
        $html = '';
        $html .= '  </body>' . PHP_EOL;
        $html .= '</html>' . PHP_EOL;

        return $html;
    }

    private function generateTableTagInline(Worksheet $worksheet, string $id): string
    {
        $style = isset($this->cssStyles['table'])
            ? $this->assembleCSS($this->cssStyles['table']) : '';

        $prntgrid = $worksheet->getPrintGridlines();
        $viewgrid = $this->isPdf ? $prntgrid : $worksheet->getShowGridlines();
        if ($viewgrid && $prntgrid) {
            $html = "    <table border='1' cellpadding='1' $id cellspacing='1' style='$style' class='gridlines gridlinesp'>" . PHP_EOL;
        } elseif ($viewgrid) {
            $html = "    <table border='0' cellpadding='0' $id cellspacing='0' style='$style' class='gridlines'>" . PHP_EOL;
        } elseif ($prntgrid) {
            $html = "    <table border='0' cellpadding='0' $id cellspacing='0' style='$style' class='gridlinesp'>" . PHP_EOL;
        } else {
            $html = "    <table border='0' cellpadding='1' $id cellspacing='0' style='$style'>" . PHP_EOL;
        }

        return $html;
    }

    private function generateTableTag(Worksheet $worksheet, string $id, string &$html, int $sheetIndex): void
    {
        if (!$this->useInlineCss) {
            $gridlines = $worksheet->getShowGridlines() ? ' gridlines' : '';
            $gridlinesp = $worksheet->getPrintGridlines() ? ' gridlinesp' : '';
            $html .= "    <table border='0' cellpadding='0' cellspacing='0' $id class='sheet$sheetIndex$gridlines$gridlinesp'>" . PHP_EOL;
        } else {
            $html .= $this->generateTableTagInline($worksheet, $id);
        }
    }

    /**
     * Generate table header.
     *
     * @param Worksheet $worksheet The worksheet for the table we are writing
     * @param bool $showid whether or not to add id to table tag
     */
    private function generateTableHeader(Worksheet $worksheet, bool $showid = true): string
    {
        $sheetIndex = $worksheet->getParentOrThrow()->getIndex($worksheet);

        // Construct HTML
        $html = '';
        $id = $showid ? "id='sheet$sheetIndex'" : '';
        if ($showid) {
            $html .= "<div style='page: page$sheetIndex'>" . PHP_EOL;
        } else {
            $html .= "<div style='page: page$sheetIndex' class='scrpgbrk'>" . PHP_EOL;
        }

        $this->generateTableTag($worksheet, $id, $html, $sheetIndex);

        // Write <col> elements
        $highestColumnIndex = Coordinate::columnIndexFromString($worksheet->getHighestColumn()) - 1;
        $i = -1;
        while ($i++ < $highestColumnIndex) {
            if (!$this->useInlineCss) {
                $html .= '        <col class="col' . $i . '" />' . PHP_EOL;
            } else {
                $style = isset($this->cssStyles['table.sheet' . $sheetIndex . ' col.col' . $i])
                    ? $this->assembleCSS($this->cssStyles['table.sheet' . $sheetIndex . ' col.col' . $i]) : '';
                $html .= '        <col style="' . $style . '" />' . PHP_EOL;
            }
        }

        return $html;
    }

    /**
     * Generate table footer.
     */
    private function generateTableFooter(): string
    {
        return '    </tbody></table>' . PHP_EOL . '</div>' . PHP_EOL;
    }

    /**
     * Generate row start.
     *
     * @param int $sheetIndex Sheet index (0-based)
     * @param int $row row number
     */
    private function generateRowStart(Worksheet $worksheet, int $sheetIndex, int $row): string
    {
        $html = '';
        if (count($worksheet->getBreaks()) > 0) {
            $breaks = $worksheet->getRowBreaks();

            // check if a break is needed before this row
            if (isset($breaks['A' . $row])) {
                // close table: </table>
                $html .= $this->generateTableFooter();
                if ($this->isPdf && $this->useInlineCss) {
                    $html .= '<div style="page-break-before:always" />';
                }

                // open table again: <table> + <col> etc.
                $html .= $this->generateTableHeader($worksheet, false);
                $html .= '<tbody>' . PHP_EOL;
            }
        }

        // Write row start
        if (!$this->useInlineCss) {
            $html .= '          <tr class="row' . $row . '">' . PHP_EOL;
        } else {
            $style = isset($this->cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $row])
                ? $this->assembleCSS($this->cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $row]) : '';

            $html .= '          <tr style="' . $style . '">' . PHP_EOL;
        }

        return $html;
    }

    private function generateRowCellCss(Worksheet $worksheet, string $cellAddress, int $row, int $columnNumber): array
    {
        $cell = ($cellAddress > '') ? $worksheet->getCellCollection()->get($cellAddress) : '';
        $coordinate = Coordinate::stringFromColumnIndex($columnNumber + 1) . ($row + 1);
        if (!$this->useInlineCss) {
            $cssClass = 'column' . $columnNumber;
        } else {
            $cssClass = [];
            // The statements below do nothing.
            // Commenting out the code rather than deleting it
            // in case someone can figure out what their intent was.
            //if ($cellType == 'th') {
            //    if (isset($this->cssStyles['table.sheet' . $sheetIndex . ' th.column' . $colNum])) {
            //        $this->cssStyles['table.sheet' . $sheetIndex . ' th.column' . $colNum];
            //    }
            //} else {
            //    if (isset($this->cssStyles['table.sheet' . $sheetIndex . ' td.column' . $colNum])) {
            //        $this->cssStyles['table.sheet' . $sheetIndex . ' td.column' . $colNum];
            //    }
            //}
            // End of mystery statements.
        }

        return [$cell, $cssClass, $coordinate];
    }

    private function generateRowCellDataValueRich(RichText $richText): string
    {
        $cellData = '';
        // Loop through rich text elements
        $elements = $richText->getRichTextElements();
        foreach ($elements as $element) {
            // Rich text start?
            if ($element instanceof Run) {
                $cellEnd = '';
                if ($element->getFont() !== null) {
                    $cellData .= '<span style="' . $this->assembleCSS($this->createCSSStyleFont($element->getFont())) . '">';

                    if ($element->getFont()->getSuperscript()) {
                        $cellData .= '<sup>';
                        $cellEnd = '</sup>';
                    } elseif ($element->getFont()->getSubscript()) {
                        $cellData .= '<sub>';
                        $cellEnd = '</sub>';
                    }
                } else {
                    $cellData .= '<span>';
                }

                // Convert UTF8 data to PCDATA
                $cellText = $element->getText();
                $cellData .= htmlspecialchars($cellText, Settings::htmlEntityFlags());

                $cellData .= $cellEnd;

                $cellData .= '</span>';
            } else {
                // Convert UTF8 data to PCDATA
                $cellText = $element->getText();
                $cellData .= htmlspecialchars($cellText, Settings::htmlEntityFlags());
            }
        }

        return nl2br($cellData);
    }

    private function generateRowCellDataValue(Worksheet $worksheet, Cell $cell, string &$cellData): void
    {
        if ($cell->getValue() instanceof RichText) {
            $cellData .= $this->generateRowCellDataValueRich($cell->getValue());
        } else {
            if ($this->preCalculateFormulas) {
                $origData = $cell->getCalculatedValue();
                if ($this->betterBoolean && is_bool($origData)) {
                    $origData2 = $origData ? $this->getTrue : $this->getFalse;
                } else {
                    $origData2 = $cell->getCalculatedValueString();
                }
            } else {
                $origData = $cell->getValue();
                if ($this->betterBoolean && is_bool($origData)) {
                    $origData2 = $origData ? $this->getTrue : $this->getFalse;
                } else {
                    $origData2 = $cell->getValueString();
                }
            }
            $formatCode = $worksheet->getParentOrThrow()->getCellXfByIndex($cell->getXfIndex())->getNumberFormat()->getFormatCode();

            $cellData = NumberFormat::toFormattedString(
                $origData2,
                $formatCode ?? NumberFormat::FORMAT_GENERAL,
                [$this, 'formatColor']
            );

            if ($cellData === $origData) {
                $cellData = htmlspecialchars($cellData, Settings::htmlEntityFlags());
            }
            if ($worksheet->getParentOrThrow()->getCellXfByIndex($cell->getXfIndex())->getFont()->getSuperscript()) {
                $cellData = '<sup>' . $cellData . '</sup>';
            } elseif ($worksheet->getParentOrThrow()->getCellXfByIndex($cell->getXfIndex())->getFont()->getSubscript()) {
                $cellData = '<sub>' . $cellData . '</sub>';
            }
        }
    }

    private function generateRowCellData(Worksheet $worksheet, null|Cell|string $cell, array|string &$cssClass): string
    {
        $cellData = '&nbsp;';
        if ($cell instanceof Cell) {
            $cellData = '';
            // Don't know what this does, and no test cases.
            //if ($cell->getParent() === null) {
            //    $cell->attach($worksheet);
            //}
            // Value
            $this->generateRowCellDataValue($worksheet, $cell, $cellData);

            // Converts the cell content so that spaces occuring at beginning of each new line are replaced by &nbsp;
            // Example: "  Hello\n to the world" is converted to "&nbsp;&nbsp;Hello\n&nbsp;to the world"
            $cellData = Preg::replace('/(?m)(?:^|\G) /', '&nbsp;', $cellData);

            // convert newline "\n" to '<br>'
            $cellData = nl2br($cellData);

            // Extend CSS class?
            $dataType = $cell->getDataType();
            if ($this->betterBoolean && $this->preCalculateFormulas && $dataType === DataType::TYPE_FORMULA) {
                $calculatedValue = $cell->getCalculatedValue();
                if (is_bool($calculatedValue)) {
                    $dataType = DataType::TYPE_BOOL;
                } elseif (is_numeric($calculatedValue)) {
                    $dataType = DataType::TYPE_NUMERIC;
                } elseif (is_string($calculatedValue)) {
                    $dataType = DataType::TYPE_STRING;
                }
            }
            if (!$this->useInlineCss && is_string($cssClass)) {
                $cssClass .= ' style' . $cell->getXfIndex();
                $cssClass .= ' ' . $dataType;
            } elseif (is_array($cssClass)) {
                $index = $cell->getXfIndex();
                $styleIndex = 'td.style' . $index . ', th.style' . $index;
                if (isset($this->cssStyles[$styleIndex])) {
                    $cssClass = array_merge($cssClass, $this->cssStyles[$styleIndex]);
                }

                // General horizontal alignment: Actual horizontal alignment depends on dataType
                $sharedStyle = $worksheet->getParentOrThrow()->getCellXfByIndex($cell->getXfIndex());
                if (
                    $sharedStyle->getAlignment()->getHorizontal() == Alignment::HORIZONTAL_GENERAL
                    && isset($this->cssStyles['.' . $cell->getDataType()]['text-align'])
                ) {
                    $cssClass['text-align'] = $this->cssStyles['.' . $dataType]['text-align'];
                }
            }
        } else {
            // Use default borders for empty cell
            if (is_string($cssClass)) {
                $cssClass .= ' style0';
            }
        }

        return $cellData;
    }

    private function generateRowIncludeCharts(Worksheet $worksheet, string $coordinate): string
    {
        return $this->includeCharts ? $this->writeChartInCell($worksheet, $coordinate) : '';
    }

    private function generateRowSpans(string $html, int $rowSpan, int $colSpan): string
    {
        $html .= ($colSpan > 1) ? (' colspan="' . $colSpan . '"') : '';
        $html .= ($rowSpan > 1) ? (' rowspan="' . $rowSpan . '"') : '';

        return $html;
    }

    private function generateRowWriteCell(
        string &$html,
        Worksheet $worksheet,
        string $coordinate,
        string $cellType,
        string $cellData,
        int $colSpan,
        int $rowSpan,
        array|string $cssClass,
        int $colNum,
        int $sheetIndex,
        int $row
    ): void {
        // Image?
        $htmlx = $this->writeImageInCell($coordinate);
        // Chart?
        $htmlx .= $this->generateRowIncludeCharts($worksheet, $coordinate);
        // Column start
        $html .= '            <' . $cellType;
        if ($this->betterBoolean) {
            $dataType = $worksheet->getCell($coordinate)->getDataType();
            if ($dataType === DataType::TYPE_BOOL) {
                $html .= ' data-type="' . DataType::TYPE_BOOL . '"';
            } elseif ($dataType === DataType::TYPE_FORMULA && $this->preCalculateFormulas && is_bool($worksheet->getCell($coordinate)->getCalculatedValue())) {
                $html .= ' data-type="' . DataType::TYPE_BOOL . '"';
            } elseif (is_numeric($cellData) && $worksheet->getCell($coordinate)->getDataType() === DataType::TYPE_STRING) {
                $html .= ' data-type="' . DataType::TYPE_STRING . '"';
            }
        }
        if (!$this->useInlineCss && !$this->isPdf && is_string($cssClass)) {
            $html .= ' class="' . $cssClass . '"';
            if ($htmlx) {
                $html .= " style='position: relative;'";
            }
        } else {
            //** Necessary redundant code for the sake of \PhpOffice\PhpSpreadsheet\Writer\Pdf **
            // We must explicitly write the width of the <td> element because TCPDF
            // does not recognize e.g. <col style="width:42pt">
            if ($this->useInlineCss) {
                $xcssClass = is_array($cssClass) ? $cssClass : [];
            } else {
                if (is_string($cssClass)) {
                    $html .= ' class="' . $cssClass . '"';
                }
                $xcssClass = [];
            }
            $width = 0;
            $i = $colNum - 1;
            $e = $colNum + $colSpan - 1;
            while ($i++ < $e) {
                if (isset($this->columnWidths[$sheetIndex][$i])) {
                    $width += $this->columnWidths[$sheetIndex][$i];
                }
            }
            $xcssClass['width'] = (string) $width . 'pt';
            // We must also explicitly write the height of the <td> element because TCPDF
            // does not recognize e.g. <tr style="height:50pt">
            if (isset($this->cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $row]['height'])) {
                $height = $this->cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $row]['height'];
                $xcssClass['height'] = $height;
            }
            //** end of redundant code **
            if ($this->useInlineCss) {
                foreach (['border-top', 'border-bottom', 'border-right', 'border-left'] as $borderType) {
                    if (($xcssClass[$borderType] ?? '') === 'none #000000') {
                        unset($xcssClass[$borderType]);
                    }
                }
            }

            if ($htmlx) {
                $xcssClass['position'] = 'relative';
            }
            $html .= ' style="' . $this->assembleCSS($xcssClass) . '"';
            if ($this->useInlineCss) {
                $html .= ' class="gridlines gridlinesp"';
            }
        }
        $html = $this->generateRowSpans($html, $rowSpan, $colSpan);

        $html .= '>';
        $html .= $htmlx;

        $html .= $this->writeComment($worksheet, $coordinate);

        // Cell data
        $html .= $cellData;

        // Column end
        $html .= '</' . $cellType . '>' . PHP_EOL;
    }

    /**
     * Generate row.
     *
     * @param array<int, mixed> $values Array containing cells in a row
     * @param int $row Row number (0-based)
     * @param string $cellType eg: 'td'
     */
    private function generateRow(Worksheet $worksheet, array $values, int $row, string $cellType): string
    {
        // Sheet index
        $sheetIndex = $worksheet->getParentOrThrow()->getIndex($worksheet);
        $html = $this->generateRowStart($worksheet, $sheetIndex, $row);

        // Write cells
        $colNum = 0;
        $tcpdfInited = false;
        foreach ($values as $key => $cellAddress) {
            if ($this instanceof Pdf\Mpdf) {
                $colNum = $key - 1;
            } elseif ($this instanceof Pdf\Tcpdf) {
                // It appears that Tcpdf requires first cell in tr.
                $colNum = $key - 1;
                if (!$tcpdfInited && $key !== 1) {
                    $tempspan = ($colNum > 1) ? " colspan='$colNum'" : '';
                    $html .= "<td$tempspan></td>\n";
                }
                $tcpdfInited = true;
            }
            [$cell, $cssClass, $coordinate] = $this->generateRowCellCss($worksheet, $cellAddress, $row, $colNum);

            // Cell Data
            $cellData = $this->generateRowCellData($worksheet, $cell, $cssClass);

            // Hyperlink?
            if ($worksheet->hyperlinkExists($coordinate) && !$worksheet->getHyperlink($coordinate)->isInternal()) {
                $url = $worksheet->getHyperlink($coordinate)->getUrl();
                $urlDecode1 = html_entity_decode($url, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $urlTrim = Preg::replace('/^\s+/u', '', $urlDecode1);
                $parseScheme = Preg::isMatch('/^([\w\s\x00-\x1f]+):/u', strtolower($urlTrim), $matches);
                if ($parseScheme && !in_array($matches[1], ['http', 'https', 'file', 'ftp', 'mailto', 's3'], true)) {
                    $cellData = htmlspecialchars($url, Settings::htmlEntityFlags());
                    $cellData = self::replaceControlChars($cellData);
                } else {
                    $tooltip = $worksheet->getHyperlink($coordinate)->getTooltip();
                    $tooltipOut = empty($tooltip) ? '' : (' title="' . htmlspecialchars($tooltip) . '"');
                    $cellData = '<a href="'
                        . htmlspecialchars($url) . '"'
                        . $tooltipOut
                        . '>' . $cellData . '</a>';
                }
            }

            // Should the cell be written or is it swallowed by a rowspan or colspan?
            $writeCell = !(isset($this->isSpannedCell[$worksheet->getParentOrThrow()->getIndex($worksheet)][$row + 1][$colNum])
                && $this->isSpannedCell[$worksheet->getParentOrThrow()->getIndex($worksheet)][$row + 1][$colNum]);

            // Colspan and Rowspan
            $colSpan = 1;
            $rowSpan = 1;
            if (isset($this->isBaseCell[$worksheet->getParentOrThrow()->getIndex($worksheet)][$row + 1][$colNum])) {
                $spans = $this->isBaseCell[$worksheet->getParentOrThrow()->getIndex($worksheet)][$row + 1][$colNum];
                $rowSpan = $spans['rowspan'];
                $colSpan = $spans['colspan'];

                //    Also apply style from last cell in merge to fix borders -
                //        relies on !important for non-none border declarations in createCSSStyleBorder
                $endCellCoord = Coordinate::stringFromColumnIndex($colNum + $colSpan) . ($row + $rowSpan);
                if (!$this->useInlineCss && is_string($cssClass)) {
                    $cssClass .= ' style' . $worksheet->getCell($endCellCoord)->getXfIndex();
                } else {
                    $endBorders = $this->spreadsheet->getCellXfByIndex($worksheet->getCell($endCellCoord)->getXfIndex())->getBorders();
                    $altBorders = $this->createCSSStyleBorders($endBorders);
                    foreach ($altBorders as $altKey => $altValue) {
                        if (str_contains($altValue, '!important')) {
                            $cssClass[$altKey] = $altValue;
                        }
                    }
                }
            }

            // Write
            if ($writeCell) {
                $this->generateRowWriteCell($html, $worksheet, $coordinate, $cellType, $cellData, $colSpan, $rowSpan, $cssClass, $colNum, $sheetIndex, $row);
            }

            // Next column
            ++$colNum;
        }

        // Write row end
        $html .= '          </tr>' . PHP_EOL;

        // Return
        return $html;
    }

    private static function replaceNonAscii(array $matches): string
    {
        return '&#' . mb_ord($matches[0], 'UTF-8') . ';';
    }

    private static function replaceControlChars(string $convert): string
    {
        return (string) preg_replace_callback(
            '/[\x00-\x1f]/',
            [self::class, 'replaceNonAscii'],
            $convert
        );
    }

    /**
     * Takes array where of CSS properties / values and converts to CSS string.
     */
    private function assembleCSS(array $values = []): string
    {
        $pairs = [];
        foreach ($values as $property => $value) {
            $pairs[] = $property . ':' . $value;
        }
        $string = implode('; ', $pairs);

        return $string;
    }

    /**
     * Get images root.
     */
    public function getImagesRoot(): string
    {
        return $this->imagesRoot;
    }

    /**
     * Set images root.
     *
     * @return $this
     */
    public function setImagesRoot(string $imagesRoot): static
    {
        $this->imagesRoot = $imagesRoot;

        return $this;
    }

    /**
     * Get embed images.
     */
    public function getEmbedImages(): bool
    {
        return $this->embedImages;
    }

    /**
     * Set embed images.
     *
     * @return $this
     */
    public function setEmbedImages(bool $embedImages): static
    {
        $this->embedImages = $embedImages;

        return $this;
    }

    /**
     * Get use inline CSS?
     */
    public function getUseInlineCss(): bool
    {
        return $this->useInlineCss;
    }

    /**
     * Set use inline CSS?
     *
     * @return $this
     */
    public function setUseInlineCss(bool $useInlineCss): static
    {
        $this->useInlineCss = $useInlineCss;

        return $this;
    }

    /**
     * Add color to formatted string as inline style.
     *
     * @param string $value Plain formatted value without color
     * @param string $format Format code
     */
    public function formatColor(string $value, string $format): string
    {
        return self::formatColorStatic($value, $format);
    }

    /**
     * Add color to formatted string as inline style.
     *
     * @param string $value Plain formatted value without color
     * @param string $format Format code
     */
    public static function formatColorStatic(string $value, string $format): string
    {
        // Color information, e.g. [Red] is always at the beginning
        $color = null; // initialize
        $matches = [];

        $color_regex = '/^\[[a-zA-Z]+\]/';
        if (Preg::isMatch($color_regex, $format, $matches)) {
            $color = str_replace(['[', ']'], '', $matches[0]);
            $color = strtolower($color);
        }

        // convert to PCDATA
        $result = htmlspecialchars($value, Settings::htmlEntityFlags());

        // color span tag
        if ($color !== null) {
            $result = '<span style="color:' . $color . '">' . $result . '</span>';
        }

        return $result;
    }

    /**
     * Calculate information about HTML colspan and rowspan which is not always the same as Excel's.
     */
    private function calculateSpans(): void
    {
        if ($this->spansAreCalculated) {
            return;
        }
        // Identify all cells that should be omitted in HTML due to cell merge.
        // In HTML only the upper-left cell should be written and it should have
        //   appropriate rowspan / colspan attribute
        $sheetIndexes = $this->sheetIndex !== null
            ? [$this->sheetIndex] : range(0, $this->spreadsheet->getSheetCount() - 1);

        foreach ($sheetIndexes as $sheetIndex) {
            $sheet = $this->spreadsheet->getSheet($sheetIndex);

            $candidateSpannedRow = [];

            // loop through all Excel merged cells
            foreach ($sheet->getMergeCells() as $cells) {
                [$cells] = Coordinate::splitRange($cells);
                $first = $cells[0];
                $last = $cells[1];

                [$fc, $fr] = Coordinate::indexesFromString($first);
                $fc = $fc - 1;

                [$lc, $lr] = Coordinate::indexesFromString($last);
                $lc = $lc - 1;

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

            $this->calculateSpansOmitRows($sheet, $sheetIndex, $candidateSpannedRow);

            // TODO: Same for columns
        }

        // We have calculated the spans
        $this->spansAreCalculated = true;
    }

    private function calculateSpansOmitRows(Worksheet $sheet, int $sheetIndex, array $candidateSpannedRow): void
    {
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

                    if (!in_array($baseCell, $adjustedBaseCells, true)) {
                        // subtract rowspan by 1
                        --$this->isBaseCell[$sheetIndex][$baseCell[0]][$baseCell[1]]['rowspan'];
                        $adjustedBaseCells[] = $baseCell;
                    }
                }
            }
        }
    }

    /**
     * Write a comment in the same format as LibreOffice.
     *
     * @see https://github.com/LibreOffice/core/blob/9fc9bf3240f8c62ad7859947ab8a033ac1fe93fa/sc/source/filter/html/htmlexp.cxx#L1073-L1092
     */
    private function writeComment(Worksheet $worksheet, string $coordinate): string
    {
        $result = '';
        if (!$this->isPdf && isset($worksheet->getComments()[$coordinate])) {
            $sanitizedString = $this->generateRowCellDataValueRich($worksheet->getComment($coordinate)->getText());
            $dir = ($worksheet->getComment($coordinate)->getTextboxDirection() === Comment::TEXTBOX_DIRECTION_RTL) ? ' dir="rtl"' : '';
            $align = strtolower($worksheet->getComment($coordinate)->getAlignment());
            $alignment = Alignment::HORIZONTAL_ALIGNMENT_FOR_HTML[$align] ?? '';
            if ($alignment !== '') {
                $alignment = " style=\"text-align:$alignment\"";
            }
            if ($sanitizedString !== '') {
                $result .= '<a class="comment-indicator"></a>';
                $result .= "<div class=\"comment\"$dir$alignment>" . $sanitizedString . '</div>';
                $result .= PHP_EOL;
            }
        }

        return $result;
    }

    public function getOrientation(): ?string
    {
        // Expect Pdf classes to override this method.
        return $this->isPdf ? PageSetup::ORIENTATION_PORTRAIT : null;
    }

    /**
     * Generate @page declarations.
     */
    private function generatePageDeclarations(bool $generateSurroundingHTML): string
    {
        // Ensure that Spans have been calculated?
        $this->calculateSpans();

        // Fetch sheets
        $sheets = [];
        if ($this->sheetIndex === null) {
            $sheets = $this->spreadsheet->getAllSheets();
        } else {
            $sheets[] = $this->spreadsheet->getSheet($this->sheetIndex);
        }

        // Construct HTML
        $htmlPage = $generateSurroundingHTML ? ('<style type="text/css">' . PHP_EOL) : '';

        // Loop all sheets
        $sheetId = 0;
        foreach ($sheets as $worksheet) {
            $htmlPage .= "@page page$sheetId { ";
            $left = StringHelper::formatNumber($worksheet->getPageMargins()->getLeft()) . 'in; ';
            $htmlPage .= 'margin-left: ' . $left;
            $right = StringHelper::FormatNumber($worksheet->getPageMargins()->getRight()) . 'in; ';
            $htmlPage .= 'margin-right: ' . $right;
            $top = StringHelper::FormatNumber($worksheet->getPageMargins()->getTop()) . 'in; ';
            $htmlPage .= 'margin-top: ' . $top;
            $bottom = StringHelper::FormatNumber($worksheet->getPageMargins()->getBottom()) . 'in; ';
            $htmlPage .= 'margin-bottom: ' . $bottom;
            $orientation = $this->getOrientation() ?? $worksheet->getPageSetup()->getOrientation();
            if ($orientation === PageSetup::ORIENTATION_LANDSCAPE) {
                $htmlPage .= 'size: landscape; ';
            } elseif ($orientation === PageSetup::ORIENTATION_PORTRAIT) {
                $htmlPage .= 'size: portrait; ';
            }
            $htmlPage .= '}' . PHP_EOL;
            ++$sheetId;
        }
        $htmlPage .= implode(PHP_EOL, [
            '.navigation {page-break-after: always;}',
            '.scrpgbrk, div + div {page-break-before: always;}',
            '@media screen {',
            '  .gridlines td {border: 1px solid black;}',
            '  .gridlines th {border: 1px solid black;}',
            '  body>div {margin-top: 5px;}',
            '  body>div:first-child {margin-top: 0;}',
            '  .scrpgbrk {margin-top: 1px;}',
            '}',
            '@media print {',
            '  .gridlinesp td {border: 1px solid black;}',
            '  .gridlinesp th {border: 1px solid black;}',
            '  .navigation {display: none;}',
            '}',
            '',
        ]);
        $htmlPage .= $generateSurroundingHTML ? ('</style>' . PHP_EOL) : '';

        return $htmlPage;
    }

    private function shouldGenerateRow(Worksheet $sheet, int $row): bool
    {
        if (!($this instanceof Pdf\Mpdf || $this instanceof Pdf\Tcpdf)) {
            return true;
        }

        return $sheet->isRowVisible($row);
    }

    private function shouldGenerateColumn(Worksheet $sheet, string $colStr): bool
    {
        if (!($this instanceof Pdf\Mpdf || $this instanceof Pdf\Tcpdf)) {
            return true;
        }
        if (!$sheet->columnDimensionExists($colStr)) {
            return true;
        }

        return $sheet->getColumnDimension($colStr)->getVisible();
    }

    public function getBetterBoolean(): bool
    {
        return $this->betterBoolean;
    }

    public function setBetterBoolean(bool $betterBoolean): self
    {
        $this->betterBoolean = $betterBoolean;

        return $this;
    }
}
