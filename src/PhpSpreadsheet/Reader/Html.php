<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
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
    ];

    protected $rowspan = [];

    /**
     * Create a new HTML Reader instance.
     */
    public function __construct()
    {
        $this->readFilter = new DefaultReadFilter();
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
                            switch ($attributeName) {
                                case 'content':
                                    //    TODO
                                    //    Extract character set, so we can convert to UTF-8 if required
                                    break;
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
                        if ($cellContent > '') {
                            $cellContent .= ' ';
                        }
                        $this->processDomElement($child, $sheet, $row, $column, $cellContent);
                        if ($cellContent > '') {
                            $cellContent .= ' ';
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
                            //    If we're inside a table, replace with a \n
                            $cellContent .= "\n";
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
                        ++$row;

                        break;
                    case 'th':
                    case 'td':
                        $this->processDomElement($child, $sheet, $row, $column, $cellContent);

                        // apply inline style
                        $this->applyInlineStyle($sheet, $row, $column, $attributeArray);

                        while (isset($this->rowspan[$column . $row])) {
                            ++$column;
                        }

                        $this->flushCell($sheet, $column, $row, $cellContent);

                        if (isset($attributeArray['rowspan'], $attributeArray['colspan'])) {
                            //create merging rowspan and colspan
                            $columnTo = $column;
                            for ($i = 0; $i < $attributeArray['colspan'] - 1; ++$i) {
                                ++$columnTo;
                            }
                            $range = $column . $row . ':' . $columnTo . ($row + $attributeArray['rowspan'] - 1);
                            foreach (Coordinate::extractAllCellReferencesInRange($range) as $value) {
                                $this->rowspan[$value] = true;
                            }
                            $sheet->mergeCells($range);
                            $column = $columnTo;
                        } elseif (isset($attributeArray['rowspan'])) {
                            //create merging rowspan
                            $range = $column . $row . ':' . $column . ($row + $attributeArray['rowspan'] - 1);
                            foreach (Coordinate::extractAllCellReferencesInRange($range) as $value) {
                                $this->rowspan[$value] = true;
                            }
                            $sheet->mergeCells($range);
                        } elseif (isset($attributeArray['colspan'])) {
                            //create merging colspan
                            $columnTo = $column;
                            for ($i = 0; $i < $attributeArray['colspan'] - 1; ++$i) {
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

        // Create new sheet
        while ($spreadsheet->getSheetCount() <= $this->sheetIndex) {
            $spreadsheet->createSheet();
        }
        $spreadsheet->setActiveSheetIndex($this->sheetIndex);

        //    Create a new DOM object
        $dom = new DOMDocument();
        //    Reload the HTML file into the DOM object
        $loaded = $dom->loadHTML(mb_convert_encoding($this->securityScanFile($pFilename), 'HTML-ENTITIES', 'UTF-8'));
        if ($loaded === false) {
            throw new Exception('Failed to load ' . $pFilename . ' as a DOM Document');
        }

        //    Discard white space
        $dom->preserveWhiteSpace = false;

        $row = 0;
        $column = 'A';
        $content = '';
        $this->processDomElement($dom, $spreadsheet->getActiveSheet(), $row, $column, $content);

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
     * @return HTML
     */
    public function setSheetIndex($pValue)
    {
        $this->sheetIndex = $pValue;

        return $this;
    }

    /**
     * Scan theXML for use of <!ENTITY to prevent XXE/XEE attacks.
     *
     * @param string $xml
     *
     * @throws Exception
     */
    public function securityScan($xml)
    {
        $pattern = '/\\0?' . implode('\\0?', str_split('<!ENTITY')) . '\\0?/';
        if (preg_match($pattern, $xml)) {
            throw new Exception('Detected use of ENTITY in XML, spreadsheet file load() aborted to prevent XXE/XEE attacks');
        }

        return $xml;
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
     * @param array $attributeArray
     * @param int $row
     * @param string $column
     */
    private function applyInlineStyle(&$sheet, $row, $column, $attributeArray)
    {
        if (!isset($attributeArray['style'])) {
            return;
        }

        $supported_styles = ['background-color', 'color'];

        // add color styles (background & text) from dom element,currently support : td & th, using ONLY inline css style with RGB color
        $styles = explode(';', $attributeArray['style']);
        foreach ($styles as $st) {
            $value = explode(':', $st);

            if (empty(trim($value[0])) || !in_array(trim($value[0]), $supported_styles)) {
                continue;
            }

            //check if has #, so we can get clean hex
            if (substr(trim($value[1]), 0, 1) == '#') {
                $style_color = substr(trim($value[1]), 1);
            }

            if (empty($style_color)) {
                continue;
            }

            switch (trim($value[0])) {
                case 'background-color':
                    $sheet->getStyle($column . $row)->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => "{$style_color}"]]]);

                    break;
                case 'color':
                    $sheet->getStyle($column . $row)->applyFromArray(['font' => ['color' => ['rgb' => "$style_color}"]]]);

                    break;
            }
        }
    }
}
