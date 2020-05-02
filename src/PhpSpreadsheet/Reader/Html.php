<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/** PhpSpreadsheet root directory */
class Html extends BaseReader
{
    /**
     * Sample size to read to determine if it's HTML or not.
     */
    const TEST_SAMPLE_SIZE = 2048;

    /**
     * Input encoding.
     *
     * @var string
     */
    protected $inputEncoding = 'ANSI';

    /**
     * Sheet index to read.
     *
     * @var int
     */
    protected $sheetIndex = 0;

    /**
     * Formats.
     *
     * @var array
     */
    protected $formats = [
        'h1' => [
            'font' => [
                'bold' => true,
                'size' => 24,
            ],
        ], //    Bold, 24pt
        'h2' => [
            'font' => [
                'bold' => true,
                'size' => 18,
            ],
        ], //    Bold, 18pt
        'h3' => [
            'font' => [
                'bold' => true,
                'size' => 13.5,
            ],
        ], //    Bold, 13.5pt
        'h4' => [
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ], //    Bold, 12pt
        'h5' => [
            'font' => [
                'bold' => true,
                'size' => 10,
            ],
        ], //    Bold, 10pt
        'h6' => [
            'font' => [
                'bold' => true,
                'size' => 7.5,
            ],
        ], //    Bold, 7.5pt
        'a' => [
            'font' => [
                'underline' => true,
                'color' => [
                    'argb' => Color::COLOR_BLUE,
                ],
            ],
        ], //    Blue underlined
        'hr' => [
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => [
                        Color::COLOR_BLACK,
                    ],
                ],
            ],
        ], //    Bottom border
        'strong' => [
            'font' => [
                'bold' => true,
            ],
        ], //    Bold
        'b' => [
            'font' => [
                'bold' => true,
            ],
        ], //    Bold
        'i' => [
            'font' => [
                'italic' => true,
            ],
        ], //    Italic
        'em' => [
            'font' => [
                'italic' => true,
            ],
        ], //    Italic
    ];

    protected $rowspan = [];

    /**
     * Create a new HTML Reader instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->securityScanner = XmlScanner::getInstance($this);
    }

    /**
     * Validate that the current file is an HTML file.
     *
     * @param string $pFilename
     *
     * @return bool
     */
    public function canRead($pFilename)
    {
        // Check if file exists
        try {
            $this->openFile($pFilename);
        } catch (Exception $e) {
            return false;
        }

        $beginning = $this->readBeginning();
        $startWithTag = self::startsWithTag($beginning);
        $containsTags = self::containsTags($beginning);
        $endsWithTag = self::endsWithTag($this->readEnding());

        fclose($this->fileHandle);

        return $startWithTag && $containsTags && $endsWithTag;
    }

    private function readBeginning()
    {
        fseek($this->fileHandle, 0);

        return fread($this->fileHandle, self::TEST_SAMPLE_SIZE);
    }

    private function readEnding()
    {
        $meta = stream_get_meta_data($this->fileHandle);
        $filename = $meta['uri'];

        $size = filesize($filename);
        if ($size === 0) {
            return '';
        }

        $blockSize = self::TEST_SAMPLE_SIZE;
        if ($size < $blockSize) {
            $blockSize = $size;
        }

        fseek($this->fileHandle, $size - $blockSize);

        return fread($this->fileHandle, $blockSize);
    }

    private static function startsWithTag($data)
    {
        return '<' === substr(trim($data), 0, 1);
    }

    private static function endsWithTag($data)
    {
        return '>' === substr(trim($data), -1, 1);
    }

    private static function containsTags($data)
    {
        return strlen($data) !== strlen(strip_tags($data));
    }

    /**
     * Loads Spreadsheet from file.
     *
     * @param string $pFilename
     *
     * @throws Exception
     *
     * @return Spreadsheet
     */
    public function load($pFilename)
    {
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Load into this instance
        return $this->loadIntoExisting($pFilename, $spreadsheet);
    }

    /**
     * Set input encoding.
     *
     * @param string $pValue Input encoding, eg: 'ANSI'
     *
     * @return $this
     */
    public function setInputEncoding($pValue)
    {
        $this->inputEncoding = $pValue;

        return $this;
    }

    /**
     * Get input encoding.
     *
     * @return string
     */
    public function getInputEncoding()
    {
        return $this->inputEncoding;
    }

    //    Data Array used for testing only, should write to Spreadsheet object on completion of tests
    protected $dataArray = [];

    protected $tableLevel = 0;

    protected $nestedColumn = ['A'];

    protected function setTableStartColumn($column)
    {
        if ($this->tableLevel == 0) {
            $column = 'A';
        }
        ++$this->tableLevel;
        $this->nestedColumn[$this->tableLevel] = $column;

        return $this->nestedColumn[$this->tableLevel];
    }

    protected function getTableStartColumn()
    {
        return $this->nestedColumn[$this->tableLevel];
    }

    protected function releaseTableStartColumn()
    {
        --$this->tableLevel;

        return array_pop($this->nestedColumn);
    }

    protected function flushCell(Worksheet $sheet, $column, $row, &$cellContent)
    {
        if (is_string($cellContent)) {
            //    Simple String content
            if (trim($cellContent) > '') {
                //    Only actually write it if there's content in the string
                //    Write to worksheet to be done here...
                //    ... we return the cell so we can mess about with styles more easily
                $sheet->setCellValue($column . $row, $cellContent);
                $this->dataArray[$row][$column] = $cellContent;
            }
        } else {
            //    We have a Rich Text run
            //    TODO
            $this->dataArray[$row][$column] = 'RICH TEXT: ' . $cellContent;
        }
        $cellContent = (string) '';
    }

    /**
     * @param DOMNode $element
     * @param Worksheet $sheet
     * @param int $row
     * @param string $column
     * @param string $cellContent
     */
    protected function processDomElement(DOMNode $element, Worksheet $sheet, &$row, &$column, &$cellContent)
    {
        foreach ($element->childNodes as $child) {
            if ($child instanceof DOMText) {
                $domText = preg_replace('/\s+/u', ' ', trim($child->nodeValue));
                if (is_string($cellContent)) {
                    //    simply append the text if the cell content is a plain text string
                    $cellContent .= $domText;
                }
                //    but if we have a rich text run instead, we need to append it correctly
                    //    TODO
            } elseif ($child instanceof DOMElement) {
                $attributeArray = [];
                foreach ($child->attributes as $attribute) {
                    $attributeArray[$attribute->name] = $attribute->value;
                }

                switch ($child->nodeName) {
                    case 'meta':
                        foreach ($attributeArray as $attributeName => $attributeValue) {
                            // Extract character set, so we can convert to UTF-8 if required
                            if ($attributeName === 'charset') {
                                $this->setInputEncoding($attributeValue);
                            }
                        }
                        $this->processDomElement($child, $sheet, $row, $column, $cellContent);

                        break;
                    case 'title':
                        $this->processDomElement($child, $sheet, $row, $column, $cellContent);
                        $sheet->setTitle($cellContent, true, false);
                        $cellContent = '';

                        break;
                    case 'span':
                    case 'div':
                    case 'font':
                    case 'i':
                    case 'em':
                    case 'strong':
                    case 'b':
                        if (isset($attributeArray['class']) && $attributeArray['class'] === 'comment') {
                            $sheet->getComment($column . $row)
                                ->getText()
                                ->createTextRun($child->textContent);

                            break;
                        }

                        if ($cellContent > '') {
                            $cellContent .= ' ';
                        }
                        $this->processDomElement($child, $sheet, $row, $column, $cellContent);
                        if ($cellContent > '') {
                            $cellContent .= ' ';
                        }

                        if (isset($this->formats[$child->nodeName])) {
                            $sheet->getStyle($column . $row)->applyFromArray($this->formats[$child->nodeName]);
                        }

                        break;
                    case 'hr':
                        $this->flushCell($sheet, $column, $row, $cellContent);
                        ++$row;
                        if (isset($this->formats[$child->nodeName])) {
                            $sheet->getStyle($column . $row)->applyFromArray($this->formats[$child->nodeName]);
                        } else {
                            $cellContent = '----------';
                            $this->flushCell($sheet, $column, $row, $cellContent);
                        }
                        ++$row;
                        // Add a break after a horizontal rule, simply by allowing the code to dropthru
                        // no break
                    case 'br':
                        if ($this->tableLevel > 0) {
                            //    If we're inside a table, replace with a \n and set the cell to wrap
                            $cellContent .= "\n";
                            $sheet->getStyle($column . $row)->getAlignment()->setWrapText(true);
                        } else {
                            //    Otherwise flush our existing content and move the row cursor on
                            $this->flushCell($sheet, $column, $row, $cellContent);
                            ++$row;
                        }

                        break;
                    case 'a':
                        foreach ($attributeArray as $attributeName => $attributeValue) {
                            switch ($attributeName) {
                                case 'href':
                                    $sheet->getCell($column . $row)->getHyperlink()->setUrl($attributeValue);
                                    if (isset($this->formats[$child->nodeName])) {
                                        $sheet->getStyle($column . $row)->applyFromArray($this->formats[$child->nodeName]);
                                    }

                                    break;
                                case 'class':
                                    if ($attributeValue === 'comment-indicator') {
                                        break; // Ignore - it's just a red square.
                                    }
                            }
                        }
                        $cellContent .= ' ';
                        $this->processDomElement($child, $sheet, $row, $column, $cellContent);

                        break;
                    case 'h1':
                    case 'h2':
                    case 'h3':
                    case 'h4':
                    case 'h5':
                    case 'h6':
                    case 'ol':
                    case 'ul':
                    case 'p':
                        if ($this->tableLevel > 0) {
                            //    If we're inside a table, replace with a \n
                            $cellContent .= "\n";
                            $this->processDomElement($child, $sheet, $row, $column, $cellContent);
                        } else {
                            if ($cellContent > '') {
                                $this->flushCell($sheet, $column, $row, $cellContent);
                                ++$row;
                            }
                            $this->processDomElement($child, $sheet, $row, $column, $cellContent);
                            $this->flushCell($sheet, $column, $row, $cellContent);

                            if (isset($this->formats[$child->nodeName])) {
                                $sheet->getStyle($column . $row)->applyFromArray($this->formats[$child->nodeName]);
                            }

                            ++$row;
                            $column = 'A';
                        }

                        break;
                    case 'li':
                        if ($this->tableLevel > 0) {
                            //    If we're inside a table, replace with a \n
                            $cellContent .= "\n";
                            $this->processDomElement($child, $sheet, $row, $column, $cellContent);
                        } else {
                            if ($cellContent > '') {
                                $this->flushCell($sheet, $column, $row, $cellContent);
                            }
                            ++$row;
                            $this->processDomElement($child, $sheet, $row, $column, $cellContent);
                            $this->flushCell($sheet, $column, $row, $cellContent);
                            $column = 'A';
                        }

                        break;
                    case 'img':
                        $this->insertImage($sheet, $column, $row, $attributeArray);

                        break;
                    case 'table':
                        $this->flushCell($sheet, $column, $row, $cellContent);
                        $column = $this->setTableStartColumn($column);
                        if ($this->tableLevel > 1) {
                            --$row;
                        }
                        $this->processDomElement($child, $sheet, $row, $column, $cellContent);
                        $column = $this->releaseTableStartColumn();
                        if ($this->tableLevel > 1) {
                            ++$column;
                        } else {
                            ++$row;
                        }

                        break;
                    case 'thead':
                    case 'tbody':
                        $this->processDomElement($child, $sheet, $row, $column, $cellContent);

                        break;
                    case 'tr':
                        $column = $this->getTableStartColumn();
                        $cellContent = '';
                        $this->processDomElement($child, $sheet, $row, $column, $cellContent);

                        if (isset($attributeArray['height'])) {
                            $sheet->getRowDimension($row)->setRowHeight($attributeArray['height']);
                        }

                        ++$row;

                        break;
                    case 'th':
                    case 'td':
                        $this->processDomElement($child, $sheet, $row, $column, $cellContent);

                        while (isset($this->rowspan[$column . $row])) {
                            ++$column;
                        }

                        // apply inline style
                        $this->applyInlineStyle($sheet, $row, $column, $attributeArray);

                        $this->flushCell($sheet, $column, $row, $cellContent);

                        if (isset($attributeArray['rowspan'], $attributeArray['colspan'])) {
                            //create merging rowspan and colspan
                            $columnTo = $column;
                            for ($i = 0; $i < (int) $attributeArray['colspan'] - 1; ++$i) {
                                ++$columnTo;
                            }
                            $range = $column . $row . ':' . $columnTo . ($row + (int) $attributeArray['rowspan'] - 1);
                            foreach (Coordinate::extractAllCellReferencesInRange($range) as $value) {
                                $this->rowspan[$value] = true;
                            }
                            $sheet->mergeCells($range);
                            $column = $columnTo;
                        } elseif (isset($attributeArray['rowspan'])) {
                            //create merging rowspan
                            $range = $column . $row . ':' . $column . ($row + (int) $attributeArray['rowspan'] - 1);
                            foreach (Coordinate::extractAllCellReferencesInRange($range) as $value) {
                                $this->rowspan[$value] = true;
                            }
                            $sheet->mergeCells($range);
                        } elseif (isset($attributeArray['colspan'])) {
                            //create merging colspan
                            $columnTo = $column;
                            for ($i = 0; $i < (int) $attributeArray['colspan'] - 1; ++$i) {
                                ++$columnTo;
                            }
                            $sheet->mergeCells($column . $row . ':' . $columnTo . $row);
                            $column = $columnTo;
                        } elseif (isset($attributeArray['bgcolor'])) {
                            $sheet->getStyle($column . $row)->applyFromArray(
                                [
                                    'fill' => [
                                        'fillType' => Fill::FILL_SOLID,
                                        'color' => ['rgb' => $attributeArray['bgcolor']],
                                    ],
                                ]
                            );
                        }

                        if (isset($attributeArray['width'])) {
                            $sheet->getColumnDimension($column)->setWidth($attributeArray['width']);
                        }

                        if (isset($attributeArray['height'])) {
                            $sheet->getRowDimension($row)->setRowHeight($attributeArray['height']);
                        }

                        if (isset($attributeArray['align'])) {
                            $sheet->getStyle($column . $row)->getAlignment()->setHorizontal($attributeArray['align']);
                        }

                        if (isset($attributeArray['valign'])) {
                            $sheet->getStyle($column . $row)->getAlignment()->setVertical($attributeArray['valign']);
                        }

                        if (isset($attributeArray['data-format'])) {
                            $sheet->getStyle($column . $row)->getNumberFormat()->setFormatCode($attributeArray['data-format']);
                        }

                        ++$column;

                        break;
                    case 'body':
                        $row = 1;
                        $column = 'A';
                        $cellContent = '';
                        $this->tableLevel = 0;
                        $this->processDomElement($child, $sheet, $row, $column, $cellContent);

                        break;
                    default:
                        $this->processDomElement($child, $sheet, $row, $column, $cellContent);
                }
            }
        }
    }

    /**
     * Loads PhpSpreadsheet from file into PhpSpreadsheet instance.
     *
     * @param string $pFilename
     * @param Spreadsheet $spreadsheet
     *
     * @throws Exception
     *
     * @return Spreadsheet
     */
    public function loadIntoExisting($pFilename, Spreadsheet $spreadsheet)
    {
        // Validate
        if (!$this->canRead($pFilename)) {
            throw new Exception($pFilename . ' is an Invalid HTML file.');
        }

        // Create a new DOM object
        $dom = new DOMDocument();
        // Reload the HTML file into the DOM object
        $loaded = $dom->loadHTML(mb_convert_encoding($this->securityScanner->scanFile($pFilename), 'HTML-ENTITIES', 'UTF-8'));
        if ($loaded === false) {
            throw new Exception('Failed to load ' . $pFilename . ' as a DOM Document');
        }

        return $this->loadDocument($dom, $spreadsheet);
    }

    /**
     * Spreadsheet from content.
     *
     * @param string $content
     * @param null|Spreadsheet $spreadsheet
     *
     * @return Spreadsheet
     */
    public function loadFromString($content, ?Spreadsheet $spreadsheet = null): Spreadsheet
    {
        //    Create a new DOM object
        $dom = new DOMDocument();
        //    Reload the HTML file into the DOM object
        $loaded = $dom->loadHTML(mb_convert_encoding($this->securityScanner->scan($content), 'HTML-ENTITIES', 'UTF-8'));
        if ($loaded === false) {
            throw new Exception('Failed to load content as a DOM Document');
        }

        return $this->loadDocument($dom, $spreadsheet ?? new Spreadsheet());
    }

    /**
     * Loads PhpSpreadsheet from DOMDocument into PhpSpreadsheet instance.
     *
     * @param DOMDocument $document
     * @param Spreadsheet $spreadsheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     *
     * @return Spreadsheet
     */
    private function loadDocument(DOMDocument $document, Spreadsheet $spreadsheet): Spreadsheet
    {
        while ($spreadsheet->getSheetCount() <= $this->sheetIndex) {
            $spreadsheet->createSheet();
        }
        $spreadsheet->setActiveSheetIndex($this->sheetIndex);

        // Discard white space
        $document->preserveWhiteSpace = false;

        $row = 0;
        $column = 'A';
        $content = '';
        $this->rowspan = [];
        $this->processDomElement($document, $spreadsheet->getActiveSheet(), $row, $column, $content);

        // Return
        return $spreadsheet;
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
     * Apply inline css inline style.
     *
     * NOTES :
     * Currently only intended for td & th element,
     * and only takes 'background-color' and 'color'; property with HEX color
     *
     * TODO :
     * - Implement to other propertie, such as border
     *
     * @param Worksheet $sheet
     * @param int $row
     * @param string $column
     * @param array $attributeArray
     */
    private function applyInlineStyle(&$sheet, $row, $column, $attributeArray)
    {
        if (!isset($attributeArray['style'])) {
            return;
        }

        $cellStyle = $sheet->getStyle($column . $row);

        // add color styles (background & text) from dom element,currently support : td & th, using ONLY inline css style with RGB color
        $styles = explode(';', $attributeArray['style']);
        foreach ($styles as $st) {
            $value = explode(':', $st);
            $styleName = isset($value[0]) ? trim($value[0]) : null;
            $styleValue = isset($value[1]) ? trim($value[1]) : null;

            if (!$styleName) {
                continue;
            }

            switch ($styleName) {
                case 'background':
                case 'background-color':
                    $styleColor = $this->getStyleColor($styleValue);

                    if (!$styleColor) {
                        continue 2;
                    }

                    $cellStyle->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $styleColor]]]);

                    break;
                case 'color':
                    $styleColor = $this->getStyleColor($styleValue);

                    if (!$styleColor) {
                        continue 2;
                    }

                    $cellStyle->applyFromArray(['font' => ['color' => ['rgb' => $styleColor]]]);

                    break;

                case 'border':
                    $this->setBorderStyle($cellStyle, $styleValue, 'allBorders');

                    break;

                case 'border-top':
                    $this->setBorderStyle($cellStyle, $styleValue, 'top');

                    break;

                case 'border-bottom':
                    $this->setBorderStyle($cellStyle, $styleValue, 'bottom');

                    break;

                case 'border-left':
                    $this->setBorderStyle($cellStyle, $styleValue, 'left');

                    break;

                case 'border-right':
                    $this->setBorderStyle($cellStyle, $styleValue, 'right');

                    break;

                case 'font-size':
                    $cellStyle->getFont()->setSize(
                        (float) $styleValue
                    );

                    break;

                case 'font-weight':
                    if ($styleValue === 'bold' || $styleValue >= 500) {
                        $cellStyle->getFont()->setBold(true);
                    }

                    break;

                case 'font-style':
                    if ($styleValue === 'italic') {
                        $cellStyle->getFont()->setItalic(true);
                    }

                    break;

                case 'font-family':
                    $cellStyle->getFont()->setName(str_replace('\'', '', $styleValue));

                    break;

                case 'text-decoration':
                    switch ($styleValue) {
                        case 'underline':
                            $cellStyle->getFont()->setUnderline(Font::UNDERLINE_SINGLE);

                            break;
                        case 'line-through':
                            $cellStyle->getFont()->setStrikethrough(true);

                            break;
                    }

                    break;

                case 'text-align':
                    $cellStyle->getAlignment()->setHorizontal($styleValue);

                    break;

                case 'vertical-align':
                    $cellStyle->getAlignment()->setVertical($styleValue);

                    break;

                case 'width':
                    $sheet->getColumnDimension($column)->setWidth(
                        str_replace('px', '', $styleValue)
                    );

                    break;

                case 'height':
                    $sheet->getRowDimension($row)->setRowHeight(
                        str_replace('px', '', $styleValue)
                    );

                    break;

                case 'word-wrap':
                    $cellStyle->getAlignment()->setWrapText(
                        $styleValue === 'break-word'
                    );

                    break;

                case 'text-indent':
                    $cellStyle->getAlignment()->setIndent(
                        (int) str_replace(['px'], '', $styleValue)
                    );

                    break;
            }
        }
    }

    /**
     * Check if has #, so we can get clean hex.
     *
     * @param $value
     *
     * @return null|string
     */
    public function getStyleColor($value)
    {
        if (strpos($value, '#') === 0) {
            return substr($value, 1);
        }

        return null;
    }

    /**
     * @param Worksheet $sheet
     * @param string    $column
     * @param int       $row
     * @param array     $attributes
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function insertImage(Worksheet $sheet, $column, $row, array $attributes)
    {
        if (!isset($attributes['src'])) {
            return;
        }

        $src = urldecode($attributes['src']);
        $width = isset($attributes['width']) ? (float) $attributes['width'] : null;
        $height = isset($attributes['height']) ? (float) $attributes['height'] : null;
        $name = isset($attributes['alt']) ? (float) $attributes['alt'] : null;

        $drawing = new Drawing();
        $drawing->setPath($src);
        $drawing->setWorksheet($sheet);
        $drawing->setCoordinates($column . $row);
        $drawing->setOffsetX(0);
        $drawing->setOffsetY(10);
        $drawing->setResizeProportional(true);

        if ($name) {
            $drawing->setName($name);
        }

        if ($width) {
            $drawing->setWidth((int) $width);
        }

        if ($height) {
            $drawing->setHeight((int) $height);
        }

        $sheet->getColumnDimension($column)->setWidth(
            $drawing->getWidth() / 6
        );

        $sheet->getRowDimension($row)->setRowHeight(
            $drawing->getHeight() * 0.9
        );
    }

    /**
     * Map html border style to PhpSpreadsheet border style.
     *
     * @param  string $style
     *
     * @return null|string
     */
    public function getBorderStyle($style)
    {
        switch ($style) {
            case 'solid':
                return Border::BORDER_THIN;
            case 'dashed':
                return Border::BORDER_DASHED;
            case 'dotted':
                return Border::BORDER_DOTTED;
            case 'medium':
                return Border::BORDER_MEDIUM;
            case 'thick':
                return Border::BORDER_THICK;
            case 'none':
                return Border::BORDER_NONE;
            case 'dash-dot':
                return Border::BORDER_DASHDOT;
            case 'dash-dot-dot':
                return Border::BORDER_DASHDOTDOT;
            case 'double':
                return Border::BORDER_DOUBLE;
            case 'hair':
                return Border::BORDER_HAIR;
            case 'medium-dash-dot':
                return Border::BORDER_MEDIUMDASHDOT;
            case 'medium-dash-dot-dot':
                return Border::BORDER_MEDIUMDASHDOTDOT;
            case 'medium-dashed':
                return Border::BORDER_MEDIUMDASHED;
            case 'slant-dash-dot':
                return Border::BORDER_SLANTDASHDOT;
        }

        return null;
    }

    /**
     * @param Style  $cellStyle
     * @param string $styleValue
     * @param string $type
     */
    private function setBorderStyle(Style $cellStyle, $styleValue, $type)
    {
        [, $borderStyle, $color] = explode(' ', $styleValue);

        $cellStyle->applyFromArray([
            'borders' => [
                $type => [
                    'borderStyle' => $this->getBorderStyle($borderStyle),
                    'color' => ['rgb' => $this->getStyleColor($color)],
                ],
            ],
        ]);
    }
}
