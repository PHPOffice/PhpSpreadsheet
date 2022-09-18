<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use DateTime;
use DateTimeZone;
use PhpOffice\PhpSpreadsheet\Cell\AddressHelper;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\Reader\Xml\PageSettings;
use PhpOffice\PhpSpreadsheet\Reader\Xml\Properties;
use PhpOffice\PhpSpreadsheet\Reader\Xml\Style;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SimpleXMLElement;

/**
 * Reader for SpreadsheetML, the XML schema for Microsoft Office Excel 2003.
 */
class Xml extends BaseReader
{
    /**
     * Formats.
     *
     * @var array
     */
    protected $styles = [];

    /**
     * Create a new Excel2003XML Reader instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->securityScanner = XmlScanner::getInstance($this);
    }

    private $fileContents = '';

    public static function xmlMappings(): array
    {
        return array_merge(
            Style\Fill::FILL_MAPPINGS,
            Style\Border::BORDER_MAPPINGS
        );
    }

    /**
     * Can the current IReader read the file?
     */
    public function canRead(string $filename): bool
    {
        //    Office                    xmlns:o="urn:schemas-microsoft-com:office:office"
        //    Excel                    xmlns:x="urn:schemas-microsoft-com:office:excel"
        //    XML Spreadsheet            xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
        //    Spreadsheet component    xmlns:c="urn:schemas-microsoft-com:office:component:spreadsheet"
        //    XML schema                 xmlns:s="uuid:BDC6E3F0-6DA3-11d1-A2A3-00AA00C14882"
        //    XML data type            xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882"
        //    MS-persist recordset    xmlns:rs="urn:schemas-microsoft-com:rowset"
        //    Rowset                    xmlns:z="#RowsetSchema"
        //

        $signature = [
            '<?xml version="1.0"',
            'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet',
        ];

        // Open file
        $data = file_get_contents($filename);

        // Why?
        //$data = str_replace("'", '"', $data); // fix headers with single quote

        $valid = true;
        foreach ($signature as $match) {
            // every part of the signature must be present
            if (strpos($data, $match) === false) {
                $valid = false;

                break;
            }
        }

        //    Retrieve charset encoding
        if (preg_match('/<?xml.*encoding=[\'"](.*?)[\'"].*?>/m', $data, $matches)) {
            $charSet = strtoupper($matches[1]);
            if (preg_match('/^ISO-8859-\d[\dL]?$/i', $charSet) === 1) {
                $data = StringHelper::convertEncoding($data, 'UTF-8', $charSet);
                $data = (string) preg_replace('/(<?xml.*encoding=[\'"]).*?([\'"].*?>)/um', '$1' . 'UTF-8' . '$2', $data, 1);
            }
        }
        $this->fileContents = $data;

        return $valid;
    }

    /**
     * Check if the file is a valid SimpleXML.
     *
     * @param string $filename
     *
     * @return false|SimpleXMLElement
     */
    public function trySimpleXMLLoadString($filename)
    {
        try {
            $xml = simplexml_load_string(
                $this->securityScanner->scan($this->fileContents ?: file_get_contents($filename)),
                'SimpleXMLElement',
                Settings::getLibXmlLoaderOptions()
            );
        } catch (\Exception $e) {
            throw new Exception('Cannot load invalid XML file: ' . $filename, 0, $e);
        }
        $this->fileContents = '';

        return $xml;
    }

    /**
     * Reads names of the worksheets from a file, without parsing the whole file to a Spreadsheet object.
     *
     * @param string $filename
     *
     * @return array
     */
    public function listWorksheetNames($filename)
    {
        File::assertFile($filename);
        if (!$this->canRead($filename)) {
            throw new Exception($filename . ' is an Invalid Spreadsheet file.');
        }

        $worksheetNames = [];

        $xml = $this->trySimpleXMLLoadString($filename);
        if ($xml === false) {
            throw new Exception("Problem reading {$filename}");
        }

        $namespaces = $xml->getNamespaces(true);

        $xml_ss = $xml->children($namespaces['ss']);
        foreach ($xml_ss->Worksheet as $worksheet) {
            $worksheet_ss = self::getAttributes($worksheet, $namespaces['ss']);
            $worksheetNames[] = (string) $worksheet_ss['Name'];
        }

        return $worksheetNames;
    }

    /**
     * Return worksheet info (Name, Last Column Letter, Last Column Index, Total Rows, Total Columns).
     *
     * @param string $filename
     *
     * @return array
     */
    public function listWorksheetInfo($filename)
    {
        File::assertFile($filename);
        if (!$this->canRead($filename)) {
            throw new Exception($filename . ' is an Invalid Spreadsheet file.');
        }

        $worksheetInfo = [];

        $xml = $this->trySimpleXMLLoadString($filename);
        if ($xml === false) {
            throw new Exception("Problem reading {$filename}");
        }

        $namespaces = $xml->getNamespaces(true);

        $worksheetID = 1;
        $xml_ss = $xml->children($namespaces['ss']);
        foreach ($xml_ss->Worksheet as $worksheet) {
            $worksheet_ss = self::getAttributes($worksheet, $namespaces['ss']);

            $tmpInfo = [];
            $tmpInfo['worksheetName'] = '';
            $tmpInfo['lastColumnLetter'] = 'A';
            $tmpInfo['lastColumnIndex'] = 0;
            $tmpInfo['totalRows'] = 0;
            $tmpInfo['totalColumns'] = 0;

            $tmpInfo['worksheetName'] = "Worksheet_{$worksheetID}";
            if (isset($worksheet_ss['Name'])) {
                $tmpInfo['worksheetName'] = (string) $worksheet_ss['Name'];
            }

            if (isset($worksheet->Table->Row)) {
                $rowIndex = 0;

                foreach ($worksheet->Table->Row as $rowData) {
                    $columnIndex = 0;
                    $rowHasData = false;

                    foreach ($rowData->Cell as $cell) {
                        if (isset($cell->Data)) {
                            $tmpInfo['lastColumnIndex'] = max($tmpInfo['lastColumnIndex'], $columnIndex);
                            $rowHasData = true;
                        }

                        ++$columnIndex;
                    }

                    ++$rowIndex;

                    if ($rowHasData) {
                        $tmpInfo['totalRows'] = max($tmpInfo['totalRows'], $rowIndex);
                    }
                }
            }

            $tmpInfo['lastColumnLetter'] = Coordinate::stringFromColumnIndex($tmpInfo['lastColumnIndex'] + 1);
            $tmpInfo['totalColumns'] = $tmpInfo['lastColumnIndex'] + 1;

            $worksheetInfo[] = $tmpInfo;
            ++$worksheetID;
        }

        return $worksheetInfo;
    }

    /**
     * Loads Spreadsheet from file.
     */
    protected function loadSpreadsheetFromFile(string $filename): Spreadsheet
    {
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        // Load into this instance
        return $this->loadIntoExisting($filename, $spreadsheet);
    }

    /**
     * Loads from file into Spreadsheet instance.
     *
     * @param string $filename
     *
     * @return Spreadsheet
     */
    public function loadIntoExisting($filename, Spreadsheet $spreadsheet)
    {
        File::assertFile($filename);
        if (!$this->canRead($filename)) {
            throw new Exception($filename . ' is an Invalid Spreadsheet file.');
        }

        $xml = $this->trySimpleXMLLoadString($filename);
        if ($xml === false) {
            throw new Exception("Problem reading {$filename}");
        }

        $namespaces = $xml->getNamespaces(true);

        (new Properties($spreadsheet))->readProperties($xml, $namespaces);

        $this->styles = (new Style())->parseStyles($xml, $namespaces);

        $worksheetID = 0;
        $xml_ss = $xml->children($namespaces['ss']);

        /** @var null|SimpleXMLElement $worksheetx */
        foreach ($xml_ss->Worksheet as $worksheetx) {
            $worksheet = $worksheetx ?? new SimpleXMLElement('<xml></xml>');
            $worksheet_ss = self::getAttributes($worksheet, $namespaces['ss']);

            if (
                isset($this->loadSheetsOnly, $worksheet_ss['Name']) &&
                (!in_array($worksheet_ss['Name'], $this->loadSheetsOnly))
            ) {
                continue;
            }

            // Create new Worksheet
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex($worksheetID);
            $worksheetName = '';
            if (isset($worksheet_ss['Name'])) {
                $worksheetName = (string) $worksheet_ss['Name'];
                //    Use false for $updateFormulaCellReferences to prevent adjustment of worksheet references in
                //        formula cells... during the load, all formulae should be correct, and we're simply bringing
                //        the worksheet name in line with the formula, not the reverse
                $spreadsheet->getActiveSheet()->setTitle($worksheetName, false, false);
            }

            // locally scoped defined names
            if (isset($worksheet->Names[0])) {
                foreach ($worksheet->Names[0] as $definedName) {
                    $definedName_ss = self::getAttributes($definedName, $namespaces['ss']);
                    $name = (string) $definedName_ss['Name'];
                    $definedValue = (string) $definedName_ss['RefersTo'];
                    $convertedValue = AddressHelper::convertFormulaToA1($definedValue);
                    if ($convertedValue[0] === '=') {
                        $convertedValue = substr($convertedValue, 1);
                    }
                    $spreadsheet->addDefinedName(DefinedName::createInstance($name, $spreadsheet->getActiveSheet(), $convertedValue, true));
                }
            }

            $columnID = 'A';
            if (isset($worksheet->Table->Column)) {
                foreach ($worksheet->Table->Column as $columnData) {
                    $columnData_ss = self::getAttributes($columnData, $namespaces['ss']);
                    if (isset($columnData_ss['Index'])) {
                        $columnID = Coordinate::stringFromColumnIndex((int) $columnData_ss['Index']);
                    }
                    if (isset($columnData_ss['Width'])) {
                        $columnWidth = $columnData_ss['Width'];
                        $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setWidth($columnWidth / 5.4);
                    }
                    ++$columnID;
                }
            }

            $rowID = 1;
            if (isset($worksheet->Table->Row)) {
                $additionalMergedCells = 0;
                foreach ($worksheet->Table->Row as $rowData) {
                    $rowHasData = false;
                    $row_ss = self::getAttributes($rowData, $namespaces['ss']);
                    if (isset($row_ss['Index'])) {
                        $rowID = (int) $row_ss['Index'];
                    }

                    $columnID = 'A';
                    foreach ($rowData->Cell as $cell) {
                        $cell_ss = self::getAttributes($cell, $namespaces['ss']);
                        if (isset($cell_ss['Index'])) {
                            $columnID = Coordinate::stringFromColumnIndex((int) $cell_ss['Index']);
                        }
                        $cellRange = $columnID . $rowID;

                        if ($this->getReadFilter() !== null) {
                            if (!$this->getReadFilter()->readCell($columnID, $rowID, $worksheetName)) {
                                ++$columnID;

                                continue;
                            }
                        }

                        if (isset($cell_ss['HRef'])) {
                            $spreadsheet->getActiveSheet()->getCell($cellRange)->getHyperlink()->setUrl((string) $cell_ss['HRef']);
                        }

                        if ((isset($cell_ss['MergeAcross'])) || (isset($cell_ss['MergeDown']))) {
                            $columnTo = $columnID;
                            if (isset($cell_ss['MergeAcross'])) {
                                $additionalMergedCells += (int) $cell_ss['MergeAcross'];
                                $columnTo = Coordinate::stringFromColumnIndex((int) (Coordinate::columnIndexFromString($columnID) + $cell_ss['MergeAcross']));
                            }
                            $rowTo = $rowID;
                            if (isset($cell_ss['MergeDown'])) {
                                $rowTo = $rowTo + $cell_ss['MergeDown'];
                            }
                            $cellRange .= ':' . $columnTo . $rowTo;
                            $spreadsheet->getActiveSheet()->mergeCells($cellRange, Worksheet::MERGE_CELL_CONTENT_HIDE);
                        }

                        $hasCalculatedValue = false;
                        $cellDataFormula = '';
                        if (isset($cell_ss['Formula'])) {
                            $cellDataFormula = $cell_ss['Formula'];
                            $hasCalculatedValue = true;
                        }
                        if (isset($cell->Data)) {
                            $cellData = $cell->Data;
                            $cellValue = (string) $cellData;
                            $type = DataType::TYPE_NULL;
                            $cellData_ss = self::getAttributes($cellData, $namespaces['ss']);
                            if (isset($cellData_ss['Type'])) {
                                $cellDataType = $cellData_ss['Type'];
                                switch ($cellDataType) {
                                    /*
                                    const TYPE_STRING        = 's';
                                    const TYPE_FORMULA        = 'f';
                                    const TYPE_NUMERIC        = 'n';
                                    const TYPE_BOOL            = 'b';
                                    const TYPE_NULL            = 'null';
                                    const TYPE_INLINE        = 'inlineStr';
                                    const TYPE_ERROR        = 'e';
                                    */
                                    case 'String':
                                        $type = DataType::TYPE_STRING;

                                        break;
                                    case 'Number':
                                        $type = DataType::TYPE_NUMERIC;
                                        $cellValue = (float) $cellValue;
                                        if (floor($cellValue) == $cellValue) {
                                            $cellValue = (int) $cellValue;
                                        }

                                        break;
                                    case 'Boolean':
                                        $type = DataType::TYPE_BOOL;
                                        $cellValue = ($cellValue != 0);

                                        break;
                                    case 'DateTime':
                                        $type = DataType::TYPE_NUMERIC;
                                        $dateTime = new DateTime($cellValue, new DateTimeZone('UTC'));
                                        $cellValue = Date::PHPToExcel($dateTime);

                                        break;
                                    case 'Error':
                                        $type = DataType::TYPE_ERROR;
                                        $hasCalculatedValue = false;

                                        break;
                                }
                            }

                            if ($hasCalculatedValue) {
                                $type = DataType::TYPE_FORMULA;
                                $columnNumber = Coordinate::columnIndexFromString($columnID);
                                $cellDataFormula = AddressHelper::convertFormulaToA1($cellDataFormula, $rowID, $columnNumber);
                            }

                            $spreadsheet->getActiveSheet()->getCell($columnID . $rowID)->setValueExplicit((($hasCalculatedValue) ? $cellDataFormula : $cellValue), $type);
                            if ($hasCalculatedValue) {
                                $spreadsheet->getActiveSheet()->getCell($columnID . $rowID)->setCalculatedValue($cellValue);
                            }
                            $rowHasData = true;
                        }

                        if (isset($cell->Comment)) {
                            $this->parseCellComment($cell->Comment, $namespaces, $spreadsheet, $columnID, $rowID);
                        }

                        if (isset($cell_ss['StyleID'])) {
                            $style = (string) $cell_ss['StyleID'];
                            if ((isset($this->styles[$style])) && (!empty($this->styles[$style]))) {
                                //if (!$spreadsheet->getActiveSheet()->cellExists($columnID . $rowID)) {
                                //    $spreadsheet->getActiveSheet()->getCell($columnID . $rowID)->setValue(null);
                                //}
                                $spreadsheet->getActiveSheet()->getStyle($cellRange)
                                    ->applyFromArray($this->styles[$style]);
                            }
                        }
                        ++$columnID;
                        while ($additionalMergedCells > 0) {
                            ++$columnID;
                            --$additionalMergedCells;
                        }
                    }

                    if ($rowHasData) {
                        if (isset($row_ss['Height'])) {
                            $rowHeight = $row_ss['Height'];
                            $spreadsheet->getActiveSheet()->getRowDimension($rowID)->setRowHeight((float) $rowHeight);
                        }
                    }

                    ++$rowID;
                }

                if (isset($namespaces['x'])) {
                    $xmlX = $worksheet->children($namespaces['x']);
                    if (isset($xmlX->WorksheetOptions)) {
                        (new PageSettings($xmlX, $namespaces))->loadPageSettings($spreadsheet);
                    }
                }
            }
            ++$worksheetID;
        }

        // Globally scoped defined names
        $activeWorksheet = $spreadsheet->setActiveSheetIndex(0);
        if (isset($xml->Names[0])) {
            foreach ($xml->Names[0] as $definedName) {
                $definedName_ss = self::getAttributes($definedName, $namespaces['ss']);
                $name = (string) $definedName_ss['Name'];
                $definedValue = (string) $definedName_ss['RefersTo'];
                $convertedValue = AddressHelper::convertFormulaToA1($definedValue);
                if ($convertedValue[0] === '=') {
                    $convertedValue = substr($convertedValue, 1);
                }
                $spreadsheet->addDefinedName(DefinedName::createInstance($name, $activeWorksheet, $convertedValue));
            }
        }

        // Return
        return $spreadsheet;
    }

    protected function parseCellComment(
        SimpleXMLElement $comment,
        array $namespaces,
        Spreadsheet $spreadsheet,
        string $columnID,
        int $rowID
    ): void {
        $commentAttributes = $comment->attributes($namespaces['ss']);
        $author = 'unknown';
        if (isset($commentAttributes->Author)) {
            $author = (string) $commentAttributes->Author;
        }

        $node = $comment->Data->asXML();
        $annotation = strip_tags((string) $node);
        $spreadsheet->getActiveSheet()->getComment($columnID . $rowID)
            ->setAuthor($author)
            ->setText($this->parseRichText($annotation));
    }

    protected function parseRichText(string $annotation): RichText
    {
        $value = new RichText();

        $value->createText($annotation);

        return $value;
    }

    private static function getAttributes(?SimpleXMLElement $simple, string $node): SimpleXMLElement
    {
        return ($simple === null)
            ? new SimpleXMLElement('<xml></xml>')
            : ($simple->attributes($node) ?? new SimpleXMLElement('<xml></xml>'));
    }
}
