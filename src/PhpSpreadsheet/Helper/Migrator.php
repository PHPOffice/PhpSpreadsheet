<?php

namespace PhpOffice\PhpSpreadsheet\Helper;

class Migrator
{
    /**
     * @var string[]
     */
    private $from;

    /**
     * @var string[]
     */
    private $to;

    public function __construct()
    {
        $this->from = array_keys($this->getMapping());
        $this->to = array_values($this->getMapping());
    }

    /**
     * Return the ordered mapping from old PHPExcel class names to new PhpSpreadsheet one.
     *
     * @return string[]
     */
    public function getMapping()
    {
        // Order matters here, we should have the deepest namespaces first (the most "unique" strings)
        $classes = [
            'PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE_Blip' => \PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer\BSE\Blip::class,
            'PHPExcel_Shared_Escher_DgContainer_SpgrContainer_SpContainer' => \PhpOffice\PhpSpreadsheet\Shared\Escher\DgContainer\SpgrContainer\SpContainer::class,
            'PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE' => \PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer\BSE::class,
            'PHPExcel_Shared_Escher_DgContainer_SpgrContainer' => \PhpOffice\PhpSpreadsheet\Shared\Escher\DgContainer\SpgrContainer::class,
            'PHPExcel_Shared_Escher_DggContainer_BstoreContainer' => \PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer::class,
            'PHPExcel_Shared_OLE_PPS_File' => \PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\File::class,
            'PHPExcel_Shared_OLE_PPS_Root' => \PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\Root::class,
            'PHPExcel_Worksheet_AutoFilter_Column_Rule' => \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule::class,
            'PHPExcel_Writer_OpenDocument_Cell_Comment' => \PhpOffice\PhpSpreadsheet\Writer\Ods\Cell\Comment::class,
            'PHPExcel_Calculation_Token_Stack' => \PhpOffice\PhpSpreadsheet\Calculation\Token\Stack::class,
            'PHPExcel_Chart_Renderer_jpgraph' => \PhpOffice\PhpSpreadsheet\Chart\Renderer\JpGraph::class,
            'PHPExcel_Reader_Excel5_Escher' => \PhpOffice\PhpSpreadsheet\Reader\Xls\Escher::class,
            'PHPExcel_Reader_Excel5_MD5' => \PhpOffice\PhpSpreadsheet\Reader\Xls\MD5::class,
            'PHPExcel_Reader_Excel5_RC4' => \PhpOffice\PhpSpreadsheet\Reader\Xls\RC4::class,
            'PHPExcel_Reader_Excel2007_Chart' => \PhpOffice\PhpSpreadsheet\Reader\Xlsx\Chart::class,
            'PHPExcel_Reader_Excel2007_Theme' => \PhpOffice\PhpSpreadsheet\Reader\Xlsx\Theme::class,
            'PHPExcel_Shared_Escher_DgContainer' => \PhpOffice\PhpSpreadsheet\Shared\Escher\DgContainer::class,
            'PHPExcel_Shared_Escher_DggContainer' => \PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer::class,
            'CholeskyDecomposition' => \PhpOffice\PhpSpreadsheet\Shared\JAMA\CholeskyDecomposition::class,
            'EigenvalueDecomposition' => \PhpOffice\PhpSpreadsheet\Shared\JAMA\EigenvalueDecomposition::class,
            'PHPExcel_Shared_JAMA_LUDecomposition' => \PhpOffice\PhpSpreadsheet\Shared\JAMA\LUDecomposition::class,
            'PHPExcel_Shared_JAMA_Matrix' => \PhpOffice\PhpSpreadsheet\Shared\JAMA\Matrix::class,
            'QRDecomposition' => \PhpOffice\PhpSpreadsheet\Shared\JAMA\QRDecomposition::class,
            'PHPExcel_Shared_JAMA_QRDecomposition' => \PhpOffice\PhpSpreadsheet\Shared\JAMA\QRDecomposition::class,
            'SingularValueDecomposition' => \PhpOffice\PhpSpreadsheet\Shared\JAMA\SingularValueDecomposition::class,
            'PHPExcel_Shared_OLE_ChainedBlockStream' => \PhpOffice\PhpSpreadsheet\Shared\OLE\ChainedBlockStream::class,
            'PHPExcel_Shared_OLE_PPS' => \PhpOffice\PhpSpreadsheet\Shared\OLE\PPS::class,
            'PHPExcel_Best_Fit' => \PhpOffice\PhpSpreadsheet\Shared\Trend\BestFit::class,
            'PHPExcel_Exponential_Best_Fit' => \PhpOffice\PhpSpreadsheet\Shared\Trend\ExponentialBestFit::class,
            'PHPExcel_Linear_Best_Fit' => \PhpOffice\PhpSpreadsheet\Shared\Trend\LinearBestFit::class,
            'PHPExcel_Logarithmic_Best_Fit' => \PhpOffice\PhpSpreadsheet\Shared\Trend\LogarithmicBestFit::class,
            'polynomialBestFit' => \PhpOffice\PhpSpreadsheet\Shared\Trend\PolynomialBestFit::class,
            'PHPExcel_Polynomial_Best_Fit' => \PhpOffice\PhpSpreadsheet\Shared\Trend\PolynomialBestFit::class,
            'powerBestFit' => \PhpOffice\PhpSpreadsheet\Shared\Trend\PowerBestFit::class,
            'PHPExcel_Power_Best_Fit' => \PhpOffice\PhpSpreadsheet\Shared\Trend\PowerBestFit::class,
            'trendClass' => \PhpOffice\PhpSpreadsheet\Shared\Trend\Trend::class,
            'PHPExcel_Worksheet_AutoFilter_Column' => \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column::class,
            'PHPExcel_Worksheet_Drawing_Shadow' => \PhpOffice\PhpSpreadsheet\Worksheet\Drawing\Shadow::class,
            'PHPExcel_Writer_OpenDocument_Content' => \PhpOffice\PhpSpreadsheet\Writer\Ods\Content::class,
            'PHPExcel_Writer_OpenDocument_Meta' => \PhpOffice\PhpSpreadsheet\Writer\Ods\Meta::class,
            'PHPExcel_Writer_OpenDocument_MetaInf' => \PhpOffice\PhpSpreadsheet\Writer\Ods\MetaInf::class,
            'PHPExcel_Writer_OpenDocument_Mimetype' => \PhpOffice\PhpSpreadsheet\Writer\Ods\Mimetype::class,
            'PHPExcel_Writer_OpenDocument_Settings' => \PhpOffice\PhpSpreadsheet\Writer\Ods\Settings::class,
            'PHPExcel_Writer_OpenDocument_Styles' => \PhpOffice\PhpSpreadsheet\Writer\Ods\Styles::class,
            'PHPExcel_Writer_OpenDocument_Thumbnails' => \PhpOffice\PhpSpreadsheet\Writer\Ods\Thumbnails::class,
            'PHPExcel_Writer_OpenDocument_WriterPart' => \PhpOffice\PhpSpreadsheet\Writer\Ods\WriterPart::class,
            'PHPExcel_Writer_PDF_Core' => \PhpOffice\PhpSpreadsheet\Writer\Pdf::class,
            'PHPExcel_Writer_PDF_DomPDF' => \PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf::class,
            'PHPExcel_Writer_PDF_mPDF' => \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf::class,
            'PHPExcel_Writer_PDF_tcPDF' => \PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf::class,
            'PHPExcel_Writer_Excel5_BIFFwriter' => \PhpOffice\PhpSpreadsheet\Writer\Xls\BIFFwriter::class,
            'PHPExcel_Writer_Excel5_Escher' => \PhpOffice\PhpSpreadsheet\Writer\Xls\Escher::class,
            'PHPExcel_Writer_Excel5_Font' => \PhpOffice\PhpSpreadsheet\Writer\Xls\Font::class,
            'PHPExcel_Writer_Excel5_Parser' => \PhpOffice\PhpSpreadsheet\Writer\Xls\Parser::class,
            'PHPExcel_Writer_Excel5_Workbook' => \PhpOffice\PhpSpreadsheet\Writer\Xls\Workbook::class,
            'PHPExcel_Writer_Excel5_Worksheet' => \PhpOffice\PhpSpreadsheet\Writer\Xls\Worksheet::class,
            'PHPExcel_Writer_Excel5_Xf' => \PhpOffice\PhpSpreadsheet\Writer\Xls\Xf::class,
            'PHPExcel_Writer_Excel2007_Chart' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Chart::class,
            'PHPExcel_Writer_Excel2007_Comments' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Comments::class,
            'PHPExcel_Writer_Excel2007_ContentTypes' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\ContentTypes::class,
            'PHPExcel_Writer_Excel2007_DocProps' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\DocProps::class,
            'PHPExcel_Writer_Excel2007_Drawing' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Drawing::class,
            'PHPExcel_Writer_Excel2007_Rels' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels::class,
            'PHPExcel_Writer_Excel2007_RelsRibbon' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\RelsRibbon::class,
            'PHPExcel_Writer_Excel2007_RelsVBA' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\RelsVBA::class,
            'PHPExcel_Writer_Excel2007_StringTable' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\StringTable::class,
            'PHPExcel_Writer_Excel2007_Style' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Style::class,
            'PHPExcel_Writer_Excel2007_Theme' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Theme::class,
            'PHPExcel_Writer_Excel2007_Workbook' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Workbook::class,
            'PHPExcel_Writer_Excel2007_Worksheet' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet::class,
            'PHPExcel_Writer_Excel2007_WriterPart' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx\WriterPart::class,
            'PHPExcel_CachedObjectStorage_CacheBase' => \PhpOffice\PhpSpreadsheet\Collection\Cells::class,
            'PHPExcel_CalcEngine_CyclicReferenceStack' => \PhpOffice\PhpSpreadsheet\Calculation\Engine\CyclicReferenceStack::class,
            'PHPExcel_CalcEngine_Logger' => \PhpOffice\PhpSpreadsheet\Calculation\Engine\Logger::class,
            'PHPExcel_Calculation_Functions' => \PhpOffice\PhpSpreadsheet\Calculation\Functions::class,
            'PHPExcel_Calculation_Function' => \PhpOffice\PhpSpreadsheet\Calculation\Category::class,
            'PHPExcel_Calculation_Database' => \PhpOffice\PhpSpreadsheet\Calculation\Database::class,
            'PHPExcel_Calculation_DateTime' => \PhpOffice\PhpSpreadsheet\Calculation\DateTime::class,
            'PHPExcel_Calculation_Engineering' => \PhpOffice\PhpSpreadsheet\Calculation\Engineering::class,
            'PHPExcel_Calculation_Exception' => \PhpOffice\PhpSpreadsheet\Calculation\Exception::class,
            'PHPExcel_Calculation_ExceptionHandler' => \PhpOffice\PhpSpreadsheet\Calculation\ExceptionHandler::class,
            'PHPExcel_Calculation_Financial' => \PhpOffice\PhpSpreadsheet\Calculation\Financial::class,
            'PHPExcel_Calculation_FormulaParser' => \PhpOffice\PhpSpreadsheet\Calculation\FormulaParser::class,
            'PHPExcel_Calculation_FormulaToken' => \PhpOffice\PhpSpreadsheet\Calculation\FormulaToken::class,
            'PHPExcel_Calculation_Logical' => \PhpOffice\PhpSpreadsheet\Calculation\Logical::class,
            'PHPExcel_Calculation_LookupRef' => \PhpOffice\PhpSpreadsheet\Calculation\LookupRef::class,
            'PHPExcel_Calculation_MathTrig' => \PhpOffice\PhpSpreadsheet\Calculation\MathTrig::class,
            'PHPExcel_Calculation_Statistical' => \PhpOffice\PhpSpreadsheet\Calculation\Statistical::class,
            'PHPExcel_Calculation_TextData' => \PhpOffice\PhpSpreadsheet\Calculation\TextData::class,
            'PHPExcel_Cell_AdvancedValueBinder' => \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder::class,
            'PHPExcel_Cell_DataType' => \PhpOffice\PhpSpreadsheet\Cell\DataType::class,
            'PHPExcel_Cell_DataValidation' => \PhpOffice\PhpSpreadsheet\Cell\DataValidation::class,
            'PHPExcel_Cell_DefaultValueBinder' => \PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder::class,
            'PHPExcel_Cell_Hyperlink' => \PhpOffice\PhpSpreadsheet\Cell\Hyperlink::class,
            'PHPExcel_Cell_IValueBinder' => \PhpOffice\PhpSpreadsheet\Cell\IValueBinder::class,
            'PHPExcel_Chart_Axis' => \PhpOffice\PhpSpreadsheet\Chart\Axis::class,
            'PHPExcel_Chart_DataSeries' => \PhpOffice\PhpSpreadsheet\Chart\DataSeries::class,
            'PHPExcel_Chart_DataSeriesValues' => \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues::class,
            'PHPExcel_Chart_Exception' => \PhpOffice\PhpSpreadsheet\Chart\Exception::class,
            'PHPExcel_Chart_GridLines' => \PhpOffice\PhpSpreadsheet\Chart\GridLines::class,
            'PHPExcel_Chart_Layout' => \PhpOffice\PhpSpreadsheet\Chart\Layout::class,
            'PHPExcel_Chart_Legend' => \PhpOffice\PhpSpreadsheet\Chart\Legend::class,
            'PHPExcel_Chart_PlotArea' => \PhpOffice\PhpSpreadsheet\Chart\PlotArea::class,
            'PHPExcel_Properties' => \PhpOffice\PhpSpreadsheet\Chart\Properties::class,
            'PHPExcel_Chart_Title' => \PhpOffice\PhpSpreadsheet\Chart\Title::class,
            'PHPExcel_DocumentProperties' => \PhpOffice\PhpSpreadsheet\Document\Properties::class,
            'PHPExcel_DocumentSecurity' => \PhpOffice\PhpSpreadsheet\Document\Security::class,
            'PHPExcel_Helper_HTML' => \PhpOffice\PhpSpreadsheet\Helper\Html::class,
            'PHPExcel_Reader_Abstract' => \PhpOffice\PhpSpreadsheet\Reader\BaseReader::class,
            'PHPExcel_Reader_CSV' => \PhpOffice\PhpSpreadsheet\Reader\Csv::class,
            'PHPExcel_Reader_DefaultReadFilter' => \PhpOffice\PhpSpreadsheet\Reader\DefaultReadFilter::class,
            'PHPExcel_Reader_Excel2003XML' => \PhpOffice\PhpSpreadsheet\Reader\Xml::class,
            'PHPExcel_Reader_Exception' => \PhpOffice\PhpSpreadsheet\Reader\Exception::class,
            'PHPExcel_Reader_Gnumeric' => \PhpOffice\PhpSpreadsheet\Reader\Gnumeric::class,
            'PHPExcel_Reader_HTML' => \PhpOffice\PhpSpreadsheet\Reader\Html::class,
            'PHPExcel_Reader_IReadFilter' => \PhpOffice\PhpSpreadsheet\Reader\IReadFilter::class,
            'PHPExcel_Reader_IReader' => \PhpOffice\PhpSpreadsheet\Reader\IReader::class,
            'PHPExcel_Reader_OOCalc' => \PhpOffice\PhpSpreadsheet\Reader\Ods::class,
            'PHPExcel_Reader_SYLK' => \PhpOffice\PhpSpreadsheet\Reader\Slk::class,
            'PHPExcel_Reader_Excel5' => \PhpOffice\PhpSpreadsheet\Reader\Xls::class,
            'PHPExcel_Reader_Excel2007' => \PhpOffice\PhpSpreadsheet\Reader\Xlsx::class,
            'PHPExcel_RichText_ITextElement' => \PhpOffice\PhpSpreadsheet\RichText\ITextElement::class,
            'PHPExcel_RichText_Run' => \PhpOffice\PhpSpreadsheet\RichText\Run::class,
            'PHPExcel_RichText_TextElement' => \PhpOffice\PhpSpreadsheet\RichText\TextElement::class,
            'PHPExcel_Shared_CodePage' => \PhpOffice\PhpSpreadsheet\Shared\CodePage::class,
            'PHPExcel_Shared_Date' => \PhpOffice\PhpSpreadsheet\Shared\Date::class,
            'PHPExcel_Shared_Drawing' => \PhpOffice\PhpSpreadsheet\Shared\Drawing::class,
            'PHPExcel_Shared_Escher' => \PhpOffice\PhpSpreadsheet\Shared\Escher::class,
            'PHPExcel_Shared_File' => \PhpOffice\PhpSpreadsheet\Shared\File::class,
            'PHPExcel_Shared_Font' => \PhpOffice\PhpSpreadsheet\Shared\Font::class,
            'PHPExcel_Shared_OLE' => \PhpOffice\PhpSpreadsheet\Shared\OLE::class,
            'PHPExcel_Shared_OLERead' => \PhpOffice\PhpSpreadsheet\Shared\OLERead::class,
            'PHPExcel_Shared_PasswordHasher' => \PhpOffice\PhpSpreadsheet\Shared\PasswordHasher::class,
            'PHPExcel_Shared_String' => \PhpOffice\PhpSpreadsheet\Shared\StringHelper::class,
            'PHPExcel_Shared_TimeZone' => \PhpOffice\PhpSpreadsheet\Shared\TimeZone::class,
            'PHPExcel_Shared_XMLWriter' => \PhpOffice\PhpSpreadsheet\Shared\XMLWriter::class,
            'PHPExcel_Shared_Excel5' => \PhpOffice\PhpSpreadsheet\Shared\Xls::class,
            'PHPExcel_Style_Alignment' => \PhpOffice\PhpSpreadsheet\Style\Alignment::class,
            'PHPExcel_Style_Border' => \PhpOffice\PhpSpreadsheet\Style\Border::class,
            'PHPExcel_Style_Borders' => \PhpOffice\PhpSpreadsheet\Style\Borders::class,
            'PHPExcel_Style_Color' => \PhpOffice\PhpSpreadsheet\Style\Color::class,
            'PHPExcel_Style_Conditional' => \PhpOffice\PhpSpreadsheet\Style\Conditional::class,
            'PHPExcel_Style_Fill' => \PhpOffice\PhpSpreadsheet\Style\Fill::class,
            'PHPExcel_Style_Font' => \PhpOffice\PhpSpreadsheet\Style\Font::class,
            'PHPExcel_Style_NumberFormat' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::class,
            'PHPExcel_Style_Protection' => \PhpOffice\PhpSpreadsheet\Style\Protection::class,
            'PHPExcel_Style_Supervisor' => \PhpOffice\PhpSpreadsheet\Style\Supervisor::class,
            'PHPExcel_Worksheet_AutoFilter' => \PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter::class,
            'PHPExcel_Worksheet_BaseDrawing' => \PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing::class,
            'PHPExcel_Worksheet_CellIterator' => \PhpOffice\PhpSpreadsheet\Worksheet\CellIterator::class,
            'PHPExcel_Worksheet_Column' => \PhpOffice\PhpSpreadsheet\Worksheet\Column::class,
            'PHPExcel_Worksheet_ColumnCellIterator' => \PhpOffice\PhpSpreadsheet\Worksheet\ColumnCellIterator::class,
            'PHPExcel_Worksheet_ColumnDimension' => \PhpOffice\PhpSpreadsheet\Worksheet\ColumnDimension::class,
            'PHPExcel_Worksheet_ColumnIterator' => \PhpOffice\PhpSpreadsheet\Worksheet\ColumnIterator::class,
            'PHPExcel_Worksheet_Drawing' => \PhpOffice\PhpSpreadsheet\Worksheet\Drawing::class,
            'PHPExcel_Worksheet_HeaderFooter' => \PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooter::class,
            'PHPExcel_Worksheet_HeaderFooterDrawing' => \PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing::class,
            'PHPExcel_WorksheetIterator' => \PhpOffice\PhpSpreadsheet\Worksheet\Iterator::class,
            'PHPExcel_Worksheet_MemoryDrawing' => \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::class,
            'PHPExcel_Worksheet_PageMargins' => \PhpOffice\PhpSpreadsheet\Worksheet\PageMargins::class,
            'PHPExcel_Worksheet_PageSetup' => \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::class,
            'PHPExcel_Worksheet_Protection' => \PhpOffice\PhpSpreadsheet\Worksheet\Protection::class,
            'PHPExcel_Worksheet_Row' => \PhpOffice\PhpSpreadsheet\Worksheet\Row::class,
            'PHPExcel_Worksheet_RowCellIterator' => \PhpOffice\PhpSpreadsheet\Worksheet\RowCellIterator::class,
            'PHPExcel_Worksheet_RowDimension' => \PhpOffice\PhpSpreadsheet\Worksheet\RowDimension::class,
            'PHPExcel_Worksheet_RowIterator' => \PhpOffice\PhpSpreadsheet\Worksheet\RowIterator::class,
            'PHPExcel_Worksheet_SheetView' => \PhpOffice\PhpSpreadsheet\Worksheet\SheetView::class,
            'PHPExcel_Writer_Abstract' => \PhpOffice\PhpSpreadsheet\Writer\BaseWriter::class,
            'PHPExcel_Writer_CSV' => \PhpOffice\PhpSpreadsheet\Writer\Csv::class,
            'PHPExcel_Writer_Exception' => \PhpOffice\PhpSpreadsheet\Writer\Exception::class,
            'PHPExcel_Writer_HTML' => \PhpOffice\PhpSpreadsheet\Writer\Html::class,
            'PHPExcel_Writer_IWriter' => \PhpOffice\PhpSpreadsheet\Writer\IWriter::class,
            'PHPExcel_Writer_OpenDocument' => \PhpOffice\PhpSpreadsheet\Writer\Ods::class,
            'PHPExcel_Writer_PDF' => \PhpOffice\PhpSpreadsheet\Writer\Pdf::class,
            'PHPExcel_Writer_Excel5' => \PhpOffice\PhpSpreadsheet\Writer\Xls::class,
            'PHPExcel_Writer_Excel2007' => \PhpOffice\PhpSpreadsheet\Writer\Xlsx::class,
            'PHPExcel_CachedObjectStorageFactory' => \PhpOffice\PhpSpreadsheet\Collection\CellsFactory::class,
            'PHPExcel_Calculation' => \PhpOffice\PhpSpreadsheet\Calculation\Calculation::class,
            'PHPExcel_Cell' => \PhpOffice\PhpSpreadsheet\Cell\Cell::class,
            'PHPExcel_Chart' => \PhpOffice\PhpSpreadsheet\Chart\Chart::class,
            'PHPExcel_Comment' => \PhpOffice\PhpSpreadsheet\Comment::class,
            'PHPExcel_Exception' => \PhpOffice\PhpSpreadsheet\Exception::class,
            'PHPExcel_HashTable' => \PhpOffice\PhpSpreadsheet\HashTable::class,
            'PHPExcel_IComparable' => \PhpOffice\PhpSpreadsheet\IComparable::class,
            'PHPExcel_IOFactory' => \PhpOffice\PhpSpreadsheet\IOFactory::class,
            'PHPExcel_NamedRange' => \PhpOffice\PhpSpreadsheet\NamedRange::class,
            'PHPExcel_ReferenceHelper' => \PhpOffice\PhpSpreadsheet\ReferenceHelper::class,
            'PHPExcel_RichText' => \PhpOffice\PhpSpreadsheet\RichText\RichText::class,
            'PHPExcel_Settings' => \PhpOffice\PhpSpreadsheet\Settings::class,
            'PHPExcel_Style' => \PhpOffice\PhpSpreadsheet\Style\Style::class,
            'PHPExcel_Worksheet' => \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::class,
        ];

        $methods = [
            'MINUTEOFHOUR' => 'MINUTE',
            'SECONDOFMINUTE' => 'SECOND',
            'DAYOFWEEK' => 'WEEKDAY',
            'WEEKOFYEAR' => 'WEEKNUM',
            'ExcelToPHPObject' => 'excelToDateTimeObject',
            'ExcelToPHP' => 'excelToTimestamp',
            'FormattedPHPToExcel' => 'formattedPHPToExcel',
            'Cell::absoluteCoordinate' => 'Coordinate::absoluteCoordinate',
            'Cell::absoluteReference' => 'Coordinate::absoluteReference',
            'Cell::buildRange' => 'Coordinate::buildRange',
            'Cell::columnIndexFromString' => 'Coordinate::columnIndexFromString',
            'Cell::coordinateFromString' => 'Coordinate::coordinateFromString',
            'Cell::extractAllCellReferencesInRange' => 'Coordinate::extractAllCellReferencesInRange',
            'Cell::getRangeBoundaries' => 'Coordinate::getRangeBoundaries',
            'Cell::mergeRangesInCollection' => 'Coordinate::mergeRangesInCollection',
            'Cell::rangeBoundaries' => 'Coordinate::rangeBoundaries',
            'Cell::rangeDimension' => 'Coordinate::rangeDimension',
            'Cell::splitRange' => 'Coordinate::splitRange',
            'Cell::stringFromColumnIndex' => 'Coordinate::stringFromColumnIndex',
        ];

        // Keep '\' prefix for class names
        $prefixedClasses = [];
        foreach ($classes as $key => &$value) {
            $value = str_replace('PhpOffice\\', '\\PhpOffice\\', $value);
            $prefixedClasses['\\' . $key] = $value;
        }
        $mapping = $prefixedClasses + $classes + $methods;

        return $mapping;
    }

    /**
     * Search in all files in given directory.
     *
     * @param string $path
     */
    private function recursiveReplace($path)
    {
        $patterns = [
            '/*.md',
            '/*.txt',
            '/*.TXT',
            '/*.php',
            '/*.phpt',
            '/*.php3',
            '/*.php4',
            '/*.php5',
            '/*.phtml',
        ];

        foreach ($patterns as $pattern) {
            foreach (glob($path . $pattern) as $file) {
                if (strpos($path, '/vendor/') !== false) {
                    echo $file . " skipped\n";

                    continue;
                }
                $original = file_get_contents($file);
                $converted = $this->replace($original);

                if ($original !== $converted) {
                    echo $file . " converted\n";
                    file_put_contents($file, $converted);
                }
            }
        }

        // Do the recursion in subdirectory
        foreach (glob($path . '/*', GLOB_ONLYDIR) as $subpath) {
            if (strpos($subpath, $path . '/') === 0) {
                $this->recursiveReplace($subpath);
            }
        }
    }

    public function migrate()
    {
        $path = realpath(getcwd());
        echo 'This will search and replace recursively in ' . $path . PHP_EOL;
        echo 'You MUST backup your files first, or you risk losing data.' . PHP_EOL;
        echo 'Are you sure ? (y/n)';

        $confirm = fread(STDIN, 1);
        if ($confirm === 'y') {
            $this->recursiveReplace($path);
        }
    }

    /**
     * Migrate the given code from PHPExcel to PhpSpreadsheet.
     *
     * @param string $original
     *
     * @return string
     */
    public function replace($original)
    {
        $converted = str_replace($this->from, $this->to, $original);

        // The string "PHPExcel" gets special treatment because of how common it might be.
        // This regex requires a word boundary around the string, and it can't be
        // preceded by $ or -> (goal is to filter out cases where a variable is named $PHPExcel or similar)
        $converted = preg_replace('~(?<!\$|->)(\b|\\\\)PHPExcel\b~', '\\' . \PhpOffice\PhpSpreadsheet\Spreadsheet::class, $converted);

        return $converted;
    }
}
