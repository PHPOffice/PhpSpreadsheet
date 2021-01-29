<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\HashTable;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing as WorksheetDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Chart;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Comments;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\ContentTypes;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\DocProps;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\RelsRibbon;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\RelsVBA;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\StringTable;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Style;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Theme;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Workbook;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet;
use ZipArchive;
use ZipStream\Exception\OverflowException;
use ZipStream\Option\Archive;
use ZipStream\ZipStream;

class Xlsx extends BaseWriter
{
    /**
     * Office2003 compatibility.
     *
     * @var bool
     */
    private $office2003compatibility = false;

    /**
     * Private writer parts.
     *
     * @var Xlsx\WriterPart[]
     */
    private $writerParts = [];

    /**
     * Private Spreadsheet.
     *
     * @var Spreadsheet
     */
    private $spreadSheet;

    /**
     * Private string table.
     *
     * @var string[]
     */
    private $stringTable = [];

    /**
     * Private unique Conditional HashTable.
     *
     * @var HashTable
     */
    private $stylesConditionalHashTable;

    /**
     * Private unique Style HashTable.
     *
     * @var HashTable
     */
    private $styleHashTable;

    /**
     * Private unique Fill HashTable.
     *
     * @var HashTable
     */
    private $fillHashTable;

    /**
     * Private unique \PhpOffice\PhpSpreadsheet\Style\Font HashTable.
     *
     * @var HashTable
     */
    private $fontHashTable;

    /**
     * Private unique Borders HashTable.
     *
     * @var HashTable
     */
    private $bordersHashTable;

    /**
     * Private unique NumberFormat HashTable.
     *
     * @var HashTable
     */
    private $numFmtHashTable;

    /**
     * Private unique \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\BaseDrawing HashTable.
     *
     * @var HashTable
     */
    private $drawingHashTable;

    /**
     * Private handle for zip stream.
     *
     * @var ZipStream
     */
    private $zip;

    /**
     * Create a new Xlsx Writer.
     */
    public function __construct(Spreadsheet $spreadsheet)
    {
        // Assign PhpSpreadsheet
        $this->setSpreadsheet($spreadsheet);

        $writerPartsArray = [
            'stringtable' => StringTable::class,
            'contenttypes' => ContentTypes::class,
            'docprops' => DocProps::class,
            'rels' => Rels::class,
            'theme' => Theme::class,
            'style' => Style::class,
            'workbook' => Workbook::class,
            'worksheet' => Worksheet::class,
            'drawing' => Drawing::class,
            'comments' => Comments::class,
            'chart' => Chart::class,
            'relsvba' => RelsVBA::class,
            'relsribbonobjects' => RelsRibbon::class,
        ];

        //    Initialise writer parts
        //        and Assign their parent IWriters
        foreach ($writerPartsArray as $writer => $class) {
            $this->writerParts[$writer] = new $class($this);
        }

        $hashTablesArray = ['stylesConditionalHashTable', 'fillHashTable', 'fontHashTable',
            'bordersHashTable', 'numFmtHashTable', 'drawingHashTable',
            'styleHashTable',
        ];

        // Set HashTable variables
        foreach ($hashTablesArray as $tableName) {
            $this->$tableName = new HashTable();
        }
    }

    /**
     * Get writer part.
     *
     * @param string $pPartName Writer part name
     *
     * @return \PhpOffice\PhpSpreadsheet\Writer\Xlsx\WriterPart
     */
    public function getWriterPart($pPartName)
    {
        if ($pPartName != '' && isset($this->writerParts[strtolower($pPartName)])) {
            return $this->writerParts[strtolower($pPartName)];
        }

        return null;
    }

    /**
     * Save PhpSpreadsheet to file.
     *
     * @param resource|string $pFilename
     */
    public function save($pFilename): void
    {
        // garbage collect
        $this->pathNames = [];
        $this->spreadSheet->garbageCollect();

        $this->openFileHandle($pFilename);

        $saveDebugLog = Calculation::getInstance($this->spreadSheet)->getDebugLog()->getWriteDebugLog();
        Calculation::getInstance($this->spreadSheet)->getDebugLog()->setWriteDebugLog(false);
        $saveDateReturnType = Functions::getReturnDateType();
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);

        // Create string lookup table
        $this->stringTable = [];
        for ($i = 0; $i < $this->spreadSheet->getSheetCount(); ++$i) {
            $this->stringTable = $this->getWriterPart('StringTable')->createStringTable($this->spreadSheet->getSheet($i), $this->stringTable);
        }

        // Create styles dictionaries
        $this->styleHashTable->addFromSource($this->getWriterPart('Style')->allStyles($this->spreadSheet));
        $this->stylesConditionalHashTable->addFromSource($this->getWriterPart('Style')->allConditionalStyles($this->spreadSheet));
        $this->fillHashTable->addFromSource($this->getWriterPart('Style')->allFills($this->spreadSheet));
        $this->fontHashTable->addFromSource($this->getWriterPart('Style')->allFonts($this->spreadSheet));
        $this->bordersHashTable->addFromSource($this->getWriterPart('Style')->allBorders($this->spreadSheet));
        $this->numFmtHashTable->addFromSource($this->getWriterPart('Style')->allNumberFormats($this->spreadSheet));

        // Create drawing dictionary
        $this->drawingHashTable->addFromSource($this->getWriterPart('Drawing')->allDrawings($this->spreadSheet));

        $options = new Archive();
        $options->setEnableZip64(false);
        $options->setOutputStream($this->fileHandle);

        $this->zip = new ZipStream(null, $options);

        // Add [Content_Types].xml to ZIP file
        $this->addZipFile('[Content_Types].xml', $this->getWriterPart('ContentTypes')->writeContentTypes($this->spreadSheet, $this->includeCharts));

        //if hasMacros, add the vbaProject.bin file, Certificate file(if exists)
        if ($this->spreadSheet->hasMacros()) {
            $macrosCode = $this->spreadSheet->getMacrosCode();
            if ($macrosCode !== null) {
                // we have the code ?
                $this->addZipFile('xl/vbaProject.bin', $macrosCode); //allways in 'xl', allways named vbaProject.bin
                if ($this->spreadSheet->hasMacrosCertificate()) {
                    //signed macros ?
                    // Yes : add the certificate file and the related rels file
                    $this->addZipFile('xl/vbaProjectSignature.bin', $this->spreadSheet->getMacrosCertificate());
                    $this->addZipFile('xl/_rels/vbaProject.bin.rels', $this->getWriterPart('RelsVBA')->writeVBARelationships($this->spreadSheet));
                }
            }
        }
        //a custom UI in this workbook ? add it ("base" xml and additional objects (pictures) and rels)
        if ($this->spreadSheet->hasRibbon()) {
            $tmpRibbonTarget = $this->spreadSheet->getRibbonXMLData('target');
            $this->addZipFile($tmpRibbonTarget, $this->spreadSheet->getRibbonXMLData('data'));
            if ($this->spreadSheet->hasRibbonBinObjects()) {
                $tmpRootPath = dirname($tmpRibbonTarget) . '/';
                $ribbonBinObjects = $this->spreadSheet->getRibbonBinObjects('data'); //the files to write
                foreach ($ribbonBinObjects as $aPath => $aContent) {
                    $this->addZipFile($tmpRootPath . $aPath, $aContent);
                }
                //the rels for files
                $this->addZipFile($tmpRootPath . '_rels/' . basename($tmpRibbonTarget) . '.rels', $this->getWriterPart('RelsRibbonObjects')->writeRibbonRelationships($this->spreadSheet));
            }
        }

        // Add relationships to ZIP file
        $this->addZipFile('_rels/.rels', $this->getWriterPart('Rels')->writeRelationships($this->spreadSheet));
        $this->addZipFile('xl/_rels/workbook.xml.rels', $this->getWriterPart('Rels')->writeWorkbookRelationships($this->spreadSheet));

        // Add document properties to ZIP file
        $this->addZipFile('docProps/app.xml', $this->getWriterPart('DocProps')->writeDocPropsApp($this->spreadSheet));
        $this->addZipFile('docProps/core.xml', $this->getWriterPart('DocProps')->writeDocPropsCore($this->spreadSheet));
        $customPropertiesPart = $this->getWriterPart('DocProps')->writeDocPropsCustom($this->spreadSheet);
        if ($customPropertiesPart !== null) {
            $this->addZipFile('docProps/custom.xml', $customPropertiesPart);
        }

        // Add theme to ZIP file
        $this->addZipFile('xl/theme/theme1.xml', $this->getWriterPart('Theme')->writeTheme($this->spreadSheet));

        // Add string table to ZIP file
        $this->addZipFile('xl/sharedStrings.xml', $this->getWriterPart('StringTable')->writeStringTable($this->stringTable));

        // Add styles to ZIP file
        $this->addZipFile('xl/styles.xml', $this->getWriterPart('Style')->writeStyles($this->spreadSheet));

        // Add workbook to ZIP file
        $this->addZipFile('xl/workbook.xml', $this->getWriterPart('Workbook')->writeWorkbook($this->spreadSheet, $this->preCalculateFormulas));

        $chartCount = 0;
        // Add worksheets
        for ($i = 0; $i < $this->spreadSheet->getSheetCount(); ++$i) {
            $this->addZipFile('xl/worksheets/sheet' . ($i + 1) . '.xml', $this->getWriterPart('Worksheet')->writeWorksheet($this->spreadSheet->getSheet($i), $this->stringTable, $this->includeCharts));
            if ($this->includeCharts) {
                $charts = $this->spreadSheet->getSheet($i)->getChartCollection();
                if (count($charts) > 0) {
                    foreach ($charts as $chart) {
                        $this->addZipFile('xl/charts/chart' . ($chartCount + 1) . '.xml', $this->getWriterPart('Chart')->writeChart($chart, $this->preCalculateFormulas));
                        ++$chartCount;
                    }
                }
            }
        }

        $chartRef1 = 0;
        // Add worksheet relationships (drawings, ...)
        for ($i = 0; $i < $this->spreadSheet->getSheetCount(); ++$i) {
            // Add relationships
            $this->addZipFile('xl/worksheets/_rels/sheet' . ($i + 1) . '.xml.rels', $this->getWriterPart('Rels')->writeWorksheetRelationships($this->spreadSheet->getSheet($i), ($i + 1), $this->includeCharts));

            // Add unparsedLoadedData
            $sheetCodeName = $this->spreadSheet->getSheet($i)->getCodeName();
            $unparsedLoadedData = $this->spreadSheet->getUnparsedLoadedData();
            if (isset($unparsedLoadedData['sheets'][$sheetCodeName]['ctrlProps'])) {
                foreach ($unparsedLoadedData['sheets'][$sheetCodeName]['ctrlProps'] as $ctrlProp) {
                    $this->addZipFile($ctrlProp['filePath'], $ctrlProp['content']);
                }
            }
            if (isset($unparsedLoadedData['sheets'][$sheetCodeName]['printerSettings'])) {
                foreach ($unparsedLoadedData['sheets'][$sheetCodeName]['printerSettings'] as $ctrlProp) {
                    $this->addZipFile($ctrlProp['filePath'], $ctrlProp['content']);
                }
            }

            $drawings = $this->spreadSheet->getSheet($i)->getDrawingCollection();
            $drawingCount = count($drawings);
            if ($this->includeCharts) {
                $chartCount = $this->spreadSheet->getSheet($i)->getChartCount();
            }

            // Add drawing and image relationship parts
            if (($drawingCount > 0) || ($chartCount > 0)) {
                // Drawing relationships
                $this->addZipFile('xl/drawings/_rels/drawing' . ($i + 1) . '.xml.rels', $this->getWriterPart('Rels')->writeDrawingRelationships($this->spreadSheet->getSheet($i), $chartRef1, $this->includeCharts));

                // Drawings
                $this->addZipFile('xl/drawings/drawing' . ($i + 1) . '.xml', $this->getWriterPart('Drawing')->writeDrawings($this->spreadSheet->getSheet($i), $this->includeCharts));
            } elseif (isset($unparsedLoadedData['sheets'][$sheetCodeName]['drawingAlternateContents'])) {
                // Drawings
                $this->addZipFile('xl/drawings/drawing' . ($i + 1) . '.xml', $this->getWriterPart('Drawing')->writeDrawings($this->spreadSheet->getSheet($i), $this->includeCharts));
            }

            // Add unparsed drawings
            if (isset($unparsedLoadedData['sheets'][$sheetCodeName]['Drawings'])) {
                foreach ($unparsedLoadedData['sheets'][$sheetCodeName]['Drawings'] as $relId => $drawingXml) {
                    $drawingFile = array_search($relId, $unparsedLoadedData['sheets'][$sheetCodeName]['drawingOriginalIds']);
                    if ($drawingFile !== false) {
                        $drawingFile = ltrim($drawingFile, '.');
                        $this->addZipFile('xl' . $drawingFile, $drawingXml);
                    }
                }
            }

            // Add comment relationship parts
            if (count($this->spreadSheet->getSheet($i)->getComments()) > 0) {
                // VML Comments
                $this->addZipFile('xl/drawings/vmlDrawing' . ($i + 1) . '.vml', $this->getWriterPart('Comments')->writeVMLComments($this->spreadSheet->getSheet($i)));

                // Comments
                $this->addZipFile('xl/comments' . ($i + 1) . '.xml', $this->getWriterPart('Comments')->writeComments($this->spreadSheet->getSheet($i)));
            }

            // Add unparsed relationship parts
            if (isset($unparsedLoadedData['sheets'][$sheetCodeName]['vmlDrawings'])) {
                foreach ($unparsedLoadedData['sheets'][$sheetCodeName]['vmlDrawings'] as $vmlDrawing) {
                    $this->addZipFile($vmlDrawing['filePath'], $vmlDrawing['content']);
                }
            }

            // Add header/footer relationship parts
            if (count($this->spreadSheet->getSheet($i)->getHeaderFooter()->getImages()) > 0) {
                // VML Drawings
                $this->addZipFile('xl/drawings/vmlDrawingHF' . ($i + 1) . '.vml', $this->getWriterPart('Drawing')->writeVMLHeaderFooterImages($this->spreadSheet->getSheet($i)));

                // VML Drawing relationships
                $this->addZipFile('xl/drawings/_rels/vmlDrawingHF' . ($i + 1) . '.vml.rels', $this->getWriterPart('Rels')->writeHeaderFooterDrawingRelationships($this->spreadSheet->getSheet($i)));

                // Media
                foreach ($this->spreadSheet->getSheet($i)->getHeaderFooter()->getImages() as $image) {
                    $this->addZipFile('xl/media/' . $image->getIndexedFilename(), file_get_contents($image->getPath()));
                }
            }
        }

        // Add media
        for ($i = 0; $i < $this->getDrawingHashTable()->count(); ++$i) {
            if ($this->getDrawingHashTable()->getByIndex($i) instanceof WorksheetDrawing) {
                $imageContents = null;
                $imagePath = $this->getDrawingHashTable()->getByIndex($i)->getPath();
                if (strpos($imagePath, 'zip://') !== false) {
                    $imagePath = substr($imagePath, 6);
                    $imagePathSplitted = explode('#', $imagePath);

                    $imageZip = new ZipArchive();
                    $imageZip->open($imagePathSplitted[0]);
                    $imageContents = $imageZip->getFromName($imagePathSplitted[1]);
                    $imageZip->close();
                    unset($imageZip);
                } else {
                    $imageContents = file_get_contents($imagePath);
                }

                $this->addZipFile('xl/media/' . str_replace(' ', '_', $this->getDrawingHashTable()->getByIndex($i)->getIndexedFilename()), $imageContents);
            } elseif ($this->getDrawingHashTable()->getByIndex($i) instanceof MemoryDrawing) {
                ob_start();
                call_user_func(
                    $this->getDrawingHashTable()->getByIndex($i)->getRenderingFunction(),
                    $this->getDrawingHashTable()->getByIndex($i)->getImageResource()
                );
                $imageContents = ob_get_contents();
                ob_end_clean();

                $this->addZipFile('xl/media/' . str_replace(' ', '_', $this->getDrawingHashTable()->getByIndex($i)->getIndexedFilename()), $imageContents);
            }
        }

        Functions::setReturnDateType($saveDateReturnType);
        Calculation::getInstance($this->spreadSheet)->getDebugLog()->setWriteDebugLog($saveDebugLog);

        // Close file
        try {
            $this->zip->finish();
        } catch (OverflowException $e) {
            throw new WriterException('Could not close resource.');
        }

        $this->maybeCloseFileHandle();
    }

    /**
     * Get Spreadsheet object.
     *
     * @return Spreadsheet
     */
    public function getSpreadsheet()
    {
        return $this->spreadSheet;
    }

    /**
     * Set Spreadsheet object.
     *
     * @param Spreadsheet $spreadsheet PhpSpreadsheet object
     *
     * @return $this
     */
    public function setSpreadsheet(Spreadsheet $spreadsheet)
    {
        $this->spreadSheet = $spreadsheet;

        return $this;
    }

    /**
     * Get string table.
     *
     * @return string[]
     */
    public function getStringTable()
    {
        return $this->stringTable;
    }

    /**
     * Get Style HashTable.
     *
     * @return HashTable
     */
    public function getStyleHashTable()
    {
        return $this->styleHashTable;
    }

    /**
     * Get Conditional HashTable.
     *
     * @return HashTable
     */
    public function getStylesConditionalHashTable()
    {
        return $this->stylesConditionalHashTable;
    }

    /**
     * Get Fill HashTable.
     *
     * @return HashTable
     */
    public function getFillHashTable()
    {
        return $this->fillHashTable;
    }

    /**
     * Get \PhpOffice\PhpSpreadsheet\Style\Font HashTable.
     *
     * @return HashTable
     */
    public function getFontHashTable()
    {
        return $this->fontHashTable;
    }

    /**
     * Get Borders HashTable.
     *
     * @return HashTable
     */
    public function getBordersHashTable()
    {
        return $this->bordersHashTable;
    }

    /**
     * Get NumberFormat HashTable.
     *
     * @return HashTable
     */
    public function getNumFmtHashTable()
    {
        return $this->numFmtHashTable;
    }

    /**
     * Get \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\BaseDrawing HashTable.
     *
     * @return HashTable
     */
    public function getDrawingHashTable()
    {
        return $this->drawingHashTable;
    }

    /**
     * Get Office2003 compatibility.
     *
     * @return bool
     */
    public function getOffice2003Compatibility()
    {
        return $this->office2003compatibility;
    }

    /**
     * Set Office2003 compatibility.
     *
     * @param bool $pValue Office2003 compatibility?
     *
     * @return $this
     */
    public function setOffice2003Compatibility($pValue)
    {
        $this->office2003compatibility = $pValue;

        return $this;
    }

    private $pathNames = [];

    private function addZipFile(string $path, string $content): void
    {
        if (!in_array($path, $this->pathNames)) {
            $this->pathNames[] = $path;
            $this->zip->addFile($path, $content);
        }
    }
}
