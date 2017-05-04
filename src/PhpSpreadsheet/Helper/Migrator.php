<?php

namespace PhpOffice\PhpSpreadsheet\Helper;

class Migrator
{
    /**
     * Return the ordered mapping from old PHPExcel class names to new PhpSpreadsheet one.
     *
     * @return string[]
     */
    public function getMapping()
    {
        // Order matters here, we should have the deepest namespaces first (the most "unique" strings)
        $mapping = [
            'PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE_Blip' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\Escher\\DggContainer\\BstoreContainer\\BSE\\Blip',
            'PHPExcel_Shared_Escher_DgContainer_SpgrContainer_SpContainer' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\Escher\\DgContainer\\SpgrContainer\\SpContainer',
            'PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\Escher\\DggContainer\\BstoreContainer\\BSE',
            'PHPExcel_Shared_Escher_DgContainer_SpgrContainer' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\Escher\\DgContainer\\SpgrContainer',
            'PHPExcel_Shared_Escher_DggContainer_BstoreContainer' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\Escher\\DggContainer\\BstoreContainer',
            'PHPExcel_Shared_OLE_PPS_File' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\OLE\\PPS\\File',
            'PHPExcel_Shared_OLE_PPS_Root' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\OLE\\PPS\\Root',
            'PHPExcel_Worksheet_AutoFilter_Column_Rule' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet\\AutoFilter\\Column\\Rule',
            'PHPExcel_Writer_OpenDocument_Cell_Comment' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Cell\\Comment',
            'PHPExcel_Calculation_Token_Stack' => '\\PhpOffice\\PhpSpreadsheet\\Calculation\\Token\\Stack',
            'PHPExcel_Chart_Renderer_jpgraph' => '\\PhpOffice\\PhpSpreadsheet\\Chart\\Renderer\\JpGraph',
            'PHPExcel_Reader_Excel5_Escher' => '\\PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Escher',
            'PHPExcel_Reader_Excel5_MD5' => '\\PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\MD5',
            'PHPExcel_Reader_Excel5_RC4' => '\\PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\RC4',
            'PHPExcel_Reader_Excel2007_Chart' => '\\PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\Chart',
            'PHPExcel_Reader_Excel2007_Theme' => '\\PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\Theme',
            'PHPExcel_Shared_Escher_DgContainer' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\Escher\\DgContainer',
            'PHPExcel_Shared_Escher_DggContainer' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\Escher\\DggContainer',
            'CholeskyDecomposition' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\JAMA\\CholeskyDecomposition',
            'EigenvalueDecomposition' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\JAMA\\EigenvalueDecomposition',
            'PHPExcel_Shared_JAMA_LUDecomposition' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\JAMA\\LUDecomposition',
            'PHPExcel_Shared_JAMA_Matrix' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\JAMA\\Matrix',
            'QRDecomposition' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\JAMA\\QRDecomposition',
            'PHPExcel_Shared_JAMA_QRDecomposition' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\JAMA\\QRDecomposition',
            'SingularValueDecomposition' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\JAMA\\SingularValueDecomposition',
            'PHPExcel_Shared_OLE_ChainedBlockStream' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\OLE\\ChainedBlockStream',
            'PHPExcel_Shared_OLE_PPS' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\OLE\\PPS',
            'PHPExcel_Best_Fit' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\Trend\\BestFit',
            'PHPExcel_Exponential_Best_Fit' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\Trend\\ExponentialBestFit',
            'PHPExcel_Linear_Best_Fit' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\Trend\\LinearBestFit',
            'PHPExcel_Logarithmic_Best_Fit' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\Trend\\LogarithmicBestFit',
            'polynomialBestFit' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\Trend\\PolynomialBestFit',
            'PHPExcel_Polynomial_Best_Fit' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\Trend\\PolynomialBestFit',
            'powerBestFit' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\Trend\\PowerBestFit',
            'PHPExcel_Power_Best_Fit' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\Trend\\PowerBestFit',
            'trendClass' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\Trend\\Trend',
            'PHPExcel_Worksheet_AutoFilter_Column' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet\\AutoFilter\\Column',
            'PHPExcel_Worksheet_Drawing_Shadow' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet\\Drawing\\Shadow',
            'PHPExcel_Writer_OpenDocument_Content' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Content',
            'PHPExcel_Writer_OpenDocument_Meta' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Meta',
            'PHPExcel_Writer_OpenDocument_MetaInf' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\MetaInf',
            'PHPExcel_Writer_OpenDocument_Mimetype' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Mimetype',
            'PHPExcel_Writer_OpenDocument_Settings' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Settings',
            'PHPExcel_Writer_OpenDocument_Styles' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Styles',
            'PHPExcel_Writer_OpenDocument_Thumbnails' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Thumbnails',
            'PHPExcel_Writer_OpenDocument_WriterPart' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\WriterPart',
            'PHPExcel_Writer_PDF_Core' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Pdf\\Core',
            'PHPExcel_Writer_PDF_DomPDF' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Pdf\\DomPDF',
            'PHPExcel_Writer_PDF_mPDF' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Pdf\\MPDF',
            'PHPExcel_Writer_PDF_tcPDF' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Pdf\\TcPDF',
            'PHPExcel_Writer_Excel5_BIFFwriter' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\BIFFwriter',
            'PHPExcel_Writer_Excel5_Escher' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Escher',
            'PHPExcel_Writer_Excel5_Font' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Font',
            'PHPExcel_Writer_Excel5_Parser' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Parser',
            'PHPExcel_Writer_Excel5_Workbook' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Workbook',
            'PHPExcel_Writer_Excel5_Worksheet' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Worksheet',
            'PHPExcel_Writer_Excel5_Xf' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Xf',
            'PHPExcel_Writer_Excel2007_Chart' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Chart',
            'PHPExcel_Writer_Excel2007_Comments' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Comments',
            'PHPExcel_Writer_Excel2007_ContentTypes' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\ContentTypes',
            'PHPExcel_Writer_Excel2007_DocProps' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\DocProps',
            'PHPExcel_Writer_Excel2007_Drawing' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Drawing',
            'PHPExcel_Writer_Excel2007_Rels' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Rels',
            'PHPExcel_Writer_Excel2007_RelsRibbon' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\RelsRibbon',
            'PHPExcel_Writer_Excel2007_RelsVBA' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\RelsVBA',
            'PHPExcel_Writer_Excel2007_StringTable' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\StringTable',
            'PHPExcel_Writer_Excel2007_Style' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Style',
            'PHPExcel_Writer_Excel2007_Theme' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Theme',
            'PHPExcel_Writer_Excel2007_Workbook' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Workbook',
            'PHPExcel_Writer_Excel2007_Worksheet' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Worksheet',
            'PHPExcel_Writer_Excel2007_WriterPart' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\WriterPart',
            'PHPExcel_CachedObjectStorage_CacheBase' => '\\PhpOffice\\PhpSpreadsheet\\Collection\\Cells',
            'PHPExcel_CalcEngine_CyclicReferenceStack' => '\\PhpOffice\\PhpSpreadsheet\\CalcEngine\\CyclicReferenceStack',
            'PHPExcel_CalcEngine_Logger' => '\\PhpOffice\\PhpSpreadsheet\\CalcEngine\\Logger',
            'PHPExcel_Calculation_Functions' => '\\PhpOffice\\PhpSpreadsheet\\Calculation\\Functions',
            'PHPExcel_Calculation_Function' => '\\PhpOffice\\PhpSpreadsheet\\Calculation\\Category',
            'PHPExcel_Calculation_Database' => '\\PhpOffice\\PhpSpreadsheet\\Calculation\\Database',
            'PHPExcel_Calculation_DateTime' => '\\PhpOffice\\PhpSpreadsheet\\Calculation\\DateTime',
            'PHPExcel_Calculation_Engineering' => '\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering',
            'PHPExcel_Calculation_Exception' => '\\PhpOffice\\PhpSpreadsheet\\Calculation\\Exception',
            'PHPExcel_Calculation_ExceptionHandler' => '\\PhpOffice\\PhpSpreadsheet\\Calculation\\ExceptionHandler',
            'PHPExcel_Calculation_Financial' => '\\PhpOffice\\PhpSpreadsheet\\Calculation\\Financial',
            'PHPExcel_Calculation_FormulaParser' => '\\PhpOffice\\PhpSpreadsheet\\Calculation\\FormulaParser',
            'PHPExcel_Calculation_FormulaToken' => '\\PhpOffice\\PhpSpreadsheet\\Calculation\\FormulaToken',
            'PHPExcel_Calculation_Logical' => '\\PhpOffice\\PhpSpreadsheet\\Calculation\\Logical',
            'PHPExcel_Calculation_LookupRef' => '\\PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef',
            'PHPExcel_Calculation_MathTrig' => '\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
            'PHPExcel_Calculation_Statistical' => '\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical',
            'PHPExcel_Calculation_TextData' => '\\PhpOffice\\PhpSpreadsheet\\Calculation\\TextData',
            'PHPExcel_Cell_AdvancedValueBinder' => '\\PhpOffice\\PhpSpreadsheet\\Cell\\AdvancedValueBinder',
            'PHPExcel_Cell_DataType' => '\\PhpOffice\\PhpSpreadsheet\\Cell\\DataType',
            'PHPExcel_Cell_DataValidation' => '\\PhpOffice\\PhpSpreadsheet\\Cell\\DataValidation',
            'PHPExcel_Cell_DefaultValueBinder' => '\\PhpOffice\\PhpSpreadsheet\\Cell\\DefaultValueBinder',
            'PHPExcel_Cell_Hyperlink' => '\\PhpOffice\\PhpSpreadsheet\\Cell\\Hyperlink',
            'PHPExcel_Cell_IValueBinder' => '\\PhpOffice\\PhpSpreadsheet\\Cell\\IValueBinder',
            'PHPExcel_Chart_Axis' => '\\PhpOffice\\PhpSpreadsheet\\Chart\\Axis',
            'PHPExcel_Chart_DataSeries' => '\\PhpOffice\\PhpSpreadsheet\\Chart\\DataSeries',
            'PHPExcel_Chart_DataSeriesValues' => '\\PhpOffice\\PhpSpreadsheet\\Chart\\DataSeriesValues',
            'PHPExcel_Chart_Exception' => '\\PhpOffice\\PhpSpreadsheet\\Chart\\Exception',
            'PHPExcel_Chart_GridLines' => '\\PhpOffice\\PhpSpreadsheet\\Chart\\GridLines',
            'PHPExcel_Chart_Layout' => '\\PhpOffice\\PhpSpreadsheet\\Chart\\Layout',
            'PHPExcel_Chart_Legend' => '\\PhpOffice\\PhpSpreadsheet\\Chart\\Legend',
            'PHPExcel_Chart_PlotArea' => '\\PhpOffice\\PhpSpreadsheet\\Chart\\PlotArea',
            'PHPExcel_Properties' => '\\PhpOffice\\PhpSpreadsheet\\Chart\\Properties',
            'PHPExcel_Chart_Title' => '\\PhpOffice\\PhpSpreadsheet\\Chart\\Title',
            'PHPExcel_DocumentProperties' => '\\PhpOffice\\PhpSpreadsheet\\Document\\Properties',
            'PHPExcel_DocumentSecurity' => '\\PhpOffice\\PhpSpreadsheet\\Document\\Security',
            'PHPExcel_Helper_HTML' => '\\PhpOffice\\PhpSpreadsheet\\Helper\\Html',
            'PHPExcel_Reader_Abstract' => '\\PhpOffice\\PhpSpreadsheet\\Reader\\BaseReader',
            'PHPExcel_Reader_CSV' => '\\PhpOffice\\PhpSpreadsheet\\Reader\\Csv',
            'PHPExcel_Reader_DefaultReadFilter' => '\\PhpOffice\\PhpSpreadsheet\\Reader\\DefaultReadFilter',
            'PHPExcel_Reader_Excel2003XML' => '\\PhpOffice\\PhpSpreadsheet\\Reader\\Xml',
            'PHPExcel_Reader_Exception' => '\\PhpOffice\\PhpSpreadsheet\\Reader\\Exception',
            'PHPExcel_Reader_Gnumeric' => '\\PhpOffice\\PhpSpreadsheet\\Reader\\Gnumeric',
            'PHPExcel_Reader_HTML' => '\\PhpOffice\\PhpSpreadsheet\\Reader\\Html',
            'PHPExcel_Reader_IReadFilter' => '\\PhpOffice\\PhpSpreadsheet\\Reader\\IReadFilter',
            'PHPExcel_Reader_IReader' => '\\PhpOffice\\PhpSpreadsheet\\Reader\\IReader',
            'PHPExcel_Reader_OOCalc' => '\\PhpOffice\\PhpSpreadsheet\\Reader\\Ods',
            'PHPExcel_Reader_SYLK' => '\\PhpOffice\\PhpSpreadsheet\\Reader\\Slk',
            'PHPExcel_Reader_Excel5' => '\\PhpOffice\\PhpSpreadsheet\\Reader\\Xls',
            'PHPExcel_Reader_Excel2007' => '\\PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx',
            'PHPExcel_RichText_ITextElement' => '\\PhpOffice\\PhpSpreadsheet\\RichText\\ITextElement',
            'PHPExcel_RichText_Run' => '\\PhpOffice\\PhpSpreadsheet\\RichText\\Run',
            'PHPExcel_RichText_TextElement' => '\\PhpOffice\\PhpSpreadsheet\\RichText\\TextElement',
            'PHPExcel_Shared_CodePage' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\CodePage',
            'PHPExcel_Shared_Date' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\Date',
            'PHPExcel_Shared_Drawing' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\Drawing',
            'PHPExcel_Shared_Escher' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\Escher',
            'PHPExcel_Shared_File' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\File',
            'PHPExcel_Shared_Font' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\Font',
            'PHPExcel_Shared_OLE' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\OLE',
            'PHPExcel_Shared_OLERead' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\OLERead',
            'PHPExcel_Shared_PasswordHasher' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\PasswordHasher',
            'PHPExcel_Shared_String' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\StringHelper',
            'PHPExcel_Shared_TimeZone' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\TimeZone',
            'PHPExcel_Shared_XMLWriter' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\XMLWriter',
            'PHPExcel_Shared_Excel5' => '\\PhpOffice\\PhpSpreadsheet\\Shared\\Xls',
            'PHPExcel_Style_Alignment' => '\\PhpOffice\\PhpSpreadsheet\\Style\\Alignment',
            'PHPExcel_Style_Border' => '\\PhpOffice\\PhpSpreadsheet\\Style\\Border',
            'PHPExcel_Style_Borders' => '\\PhpOffice\\PhpSpreadsheet\\Style\\Borders',
            'PHPExcel_Style_Color' => '\\PhpOffice\\PhpSpreadsheet\\Style\\Color',
            'PHPExcel_Style_Conditional' => '\\PhpOffice\\PhpSpreadsheet\\Style\\Conditional',
            'PHPExcel_Style_Fill' => '\\PhpOffice\\PhpSpreadsheet\\Style\\Fill',
            'PHPExcel_Style_Font' => '\\PhpOffice\\PhpSpreadsheet\\Style\\Font',
            'PHPExcel_Style_NumberFormat' => '\\PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat',
            'PHPExcel_Style_Protection' => '\\PhpOffice\\PhpSpreadsheet\\Style\\Protection',
            'PHPExcel_Style_Supervisor' => '\\PhpOffice\\PhpSpreadsheet\\Style\\Supervisor',
            'PHPExcel_Worksheet_AutoFilter' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet\\AutoFilter',
            'PHPExcel_Worksheet_BaseDrawing' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet\\BaseDrawing',
            'PHPExcel_Worksheet_CellIterator' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet\\CellIterator',
            'PHPExcel_Worksheet_Column' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet\\Column',
            'PHPExcel_Worksheet_ColumnCellIterator' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet\\ColumnCellIterator',
            'PHPExcel_Worksheet_ColumnDimension' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet\\ColumnDimension',
            'PHPExcel_Worksheet_ColumnIterator' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet\\ColumnIterator',
            'PHPExcel_Worksheet_Drawing' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet\\Drawing',
            'PHPExcel_Worksheet_HeaderFooter' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet\\HeaderFooter',
            'PHPExcel_Worksheet_HeaderFooterDrawing' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet\\HeaderFooterDrawing',
            'PHPExcel_WorksheetIterator' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet\\Iterator',
            'PHPExcel_Worksheet_MemoryDrawing' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet\\MemoryDrawing',
            'PHPExcel_Worksheet_PageMargins' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet\\PageMargins',
            'PHPExcel_Worksheet_PageSetup' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet\\PageSetup',
            'PHPExcel_Worksheet_Protection' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet\\Protection',
            'PHPExcel_Worksheet_Row' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet\\Row',
            'PHPExcel_Worksheet_RowCellIterator' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet\\RowCellIterator',
            'PHPExcel_Worksheet_RowDimension' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet\\RowDimension',
            'PHPExcel_Worksheet_RowIterator' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet\\RowIterator',
            'PHPExcel_Worksheet_SheetView' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet\\SheetView',
            'PHPExcel_Writer_Abstract' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\BaseWriter',
            'PHPExcel_Writer_CSV' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Csv',
            'PHPExcel_Writer_Exception' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Exception',
            'PHPExcel_Writer_HTML' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Html',
            'PHPExcel_Writer_IWriter' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\IWriter',
            'PHPExcel_Writer_OpenDocument' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Ods',
            'PHPExcel_Writer_PDF' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Pdf',
            'PHPExcel_Writer_Excel5' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Xls',
            'PHPExcel_Writer_Excel2007' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx',
            'PHPExcel_CachedObjectStorageFactory' => '\\PhpOffice\\PhpSpreadsheet\\Collection\\CellsFactory',
            'PHPExcel_Calculation' => '\\PhpOffice\\PhpSpreadsheet\\Calculation',
            'PHPExcel_Cell' => '\\PhpOffice\\PhpSpreadsheet\\Cell',
            'PHPExcel_Chart' => '\\PhpOffice\\PhpSpreadsheet\\Chart',
            'PHPExcel_Comment' => '\\PhpOffice\\PhpSpreadsheet\\Comment',
            'PHPExcel_Exception' => '\\PhpOffice\\PhpSpreadsheet\\Exception',
            'PHPExcel_HashTable' => '\\PhpOffice\\PhpSpreadsheet\\HashTable',
            'PHPExcel_IComparable' => '\\PhpOffice\\PhpSpreadsheet\\IComparable',
            'PHPExcel_IOFactory' => '\\PhpOffice\\PhpSpreadsheet\\IOFactory',
            'PHPExcel_NamedRange' => '\\PhpOffice\\PhpSpreadsheet\\NamedRange',
            'PHPExcel_ReferenceHelper' => '\\PhpOffice\\PhpSpreadsheet\\ReferenceHelper',
            'PHPExcel_RichText' => '\\PhpOffice\\PhpSpreadsheet\\RichText',
            'PHPExcel_Settings' => '\\PhpOffice\\PhpSpreadsheet\\Settings',
            'PHPExcel_Style' => '\\PhpOffice\\PhpSpreadsheet\\Style',
            'PHPExcel_Worksheet' => '\\PhpOffice\\PhpSpreadsheet\\Worksheet',
            'PHPExcel' => '\\PhpOffice\\PhpSpreadsheet\\Spreadsheet',
            // methods
            'MINUTEOFHOUR' => 'MINUTE',
            'SECONDOFMINUTE' => 'SECOND',
            'DAYOFWEEK' => 'WEEKDAY',
            'WEEKOFYEAR' => 'WEEKNUM',
        ];

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
            '/*.php',
            '/*.txt',
            '/*.TXT',
        ];

        $from = array_keys($this->getMapping());
        $to = array_values($this->getMapping());

        foreach ($patterns as $pattern) {
            foreach (glob($path . $pattern) as $file) {
                $original = file_get_contents($file);
                $converted = str_replace($from, $to, $original);

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
}
