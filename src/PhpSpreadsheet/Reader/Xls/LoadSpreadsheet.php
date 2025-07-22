<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Shared\CodePage;
use PhpOffice\PhpSpreadsheet\Shared\Escher as SharedEscher;
use PhpOffice\PhpSpreadsheet\Shared\Escher\DgContainer\SpgrContainer\SpContainer;
use PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer\BstoreContainer\BSE;
use PhpOffice\PhpSpreadsheet\Shared\Xls as SharedXls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LoadSpreadsheet extends Xls
{
    /**
     * Loads PhpSpreadsheet from file.
     */
    protected function loadSpreadsheetFromFile2(string $filename, Xls $xls): Spreadsheet
    {
        // Read the OLE file
        $xls->loadOLE($filename);

        // Initialisations
        $xls->spreadsheet = $this->newSpreadsheet();
        $xls->spreadsheet->setValueBinder($xls->valueBinder);
        $xls->spreadsheet->removeSheetByIndex(0); // remove 1st sheet
        if (!$xls->readDataOnly) {
            $xls->spreadsheet->removeCellStyleXfByIndex(0); // remove the default style
            $xls->spreadsheet->removeCellXfByIndex(0); // remove the default style
        }

        // Read the summary information stream (containing meta data)
        $xls->readSummaryInformation();

        // Read the Additional document summary information stream (containing application-specific meta data)
        $xls->readDocumentSummaryInformation();

        // total byte size of Excel data (workbook global substream + sheet substreams)
        $xls->dataSize = strlen($xls->data);

        // initialize
        $xls->pos = 0;
        $xls->codepage = $xls->codepage ?: CodePage::DEFAULT_CODE_PAGE;
        $xls->formats = [];
        $xls->objFonts = [];
        $xls->palette = [];
        $xls->sheets = [];
        $xls->externalBooks = [];
        $xls->ref = [];
        $xls->definedname = []; //* @phpstan-ignore-line
        $xls->sst = [];
        $xls->drawingGroupData = '';
        $xls->xfIndex = 0;
        $xls->mapCellXfIndex = [];
        $xls->mapCellStyleXfIndex = [];

        // Parse Workbook Global Substream
        while ($xls->pos < $xls->dataSize) {
            $code = self::getUInt2d($xls->data, $xls->pos);

            match ($code) {
                self::XLS_TYPE_BOF => $xls->readBof(),
                self::XLS_TYPE_FILEPASS => $xls->readFilepass(),
                self::XLS_TYPE_CODEPAGE => $xls->readCodepage(),
                self::XLS_TYPE_DATEMODE => $xls->readDateMode(),
                self::XLS_TYPE_FONT => $xls->readFont(),
                self::XLS_TYPE_FORMAT => $xls->readFormat(),
                self::XLS_TYPE_XF => $xls->readXf(),
                self::XLS_TYPE_XFEXT => $xls->readXfExt(),
                self::XLS_TYPE_STYLE => $xls->readStyle(),
                self::XLS_TYPE_PALETTE => $xls->readPalette(),
                self::XLS_TYPE_SHEET => $xls->readSheet(),
                self::XLS_TYPE_EXTERNALBOOK => $xls->readExternalBook(),
                self::XLS_TYPE_EXTERNNAME => $xls->readExternName(),
                self::XLS_TYPE_EXTERNSHEET => $xls->readExternSheet(),
                self::XLS_TYPE_DEFINEDNAME => $xls->readDefinedName(),
                self::XLS_TYPE_MSODRAWINGGROUP => $xls->readMsoDrawingGroup(),
                self::XLS_TYPE_SST => $xls->readSst(),
                self::XLS_TYPE_EOF => $xls->readDefault(),
                default => $xls->readDefault(),
            };

            if ($code === self::XLS_TYPE_EOF) {
                break;
            }
        }

        // Resolve indexed colors for font, fill, and border colors
        // Cannot be resolved already in XF record, because PALETTE record comes afterwards
        if (!$xls->readDataOnly) {
            foreach ($xls->objFonts as $objFont) {
                if (isset($objFont->colorIndex)) {
                    $color = Color::map($objFont->colorIndex, $xls->palette, $xls->version);
                    $objFont->getColor()->setRGB($color['rgb']);
                }
            }

            foreach ($xls->spreadsheet->getCellXfCollection() as $objStyle) {
                // fill start and end color
                $fill = $objStyle->getFill();

                if (isset($fill->startcolorIndex)) {
                    $startColor = Color::map($fill->startcolorIndex, $xls->palette, $xls->version);
                    $fill->getStartColor()->setRGB($startColor['rgb']);
                }
                if (isset($fill->endcolorIndex)) {
                    $endColor = Color::map($fill->endcolorIndex, $xls->palette, $xls->version);
                    $fill->getEndColor()->setRGB($endColor['rgb']);
                }

                // border colors
                $top = $objStyle->getBorders()->getTop();
                $right = $objStyle->getBorders()->getRight();
                $bottom = $objStyle->getBorders()->getBottom();
                $left = $objStyle->getBorders()->getLeft();
                $diagonal = $objStyle->getBorders()->getDiagonal();

                if (isset($top->colorIndex)) {
                    $borderTopColor = Color::map($top->colorIndex, $xls->palette, $xls->version);
                    $top->getColor()->setRGB($borderTopColor['rgb']);
                }
                if (isset($right->colorIndex)) {
                    $borderRightColor = Color::map($right->colorIndex, $xls->palette, $xls->version);
                    $right->getColor()->setRGB($borderRightColor['rgb']);
                }
                if (isset($bottom->colorIndex)) {
                    $borderBottomColor = Color::map($bottom->colorIndex, $xls->palette, $xls->version);
                    $bottom->getColor()->setRGB($borderBottomColor['rgb']);
                }
                if (isset($left->colorIndex)) {
                    $borderLeftColor = Color::map($left->colorIndex, $xls->palette, $xls->version);
                    $left->getColor()->setRGB($borderLeftColor['rgb']);
                }
                if (isset($diagonal->colorIndex)) {
                    $borderDiagonalColor = Color::map($diagonal->colorIndex, $xls->palette, $xls->version);
                    $diagonal->getColor()->setRGB($borderDiagonalColor['rgb']);
                }
            }
        }

        // treat MSODRAWINGGROUP records, workbook-level Escher
        $escherWorkbook = null;
        if (!$xls->readDataOnly && $xls->drawingGroupData) {
            $escher = new SharedEscher();
            $reader = new Escher($escher);
            $escherWorkbook = $reader->load($xls->drawingGroupData);
        }

        // Parse the individual sheets
        $xls->activeSheetSet = false;
        foreach ($xls->sheets as $sheet) {
            $selectedCells = '';
            if ($sheet['sheetType'] != 0x00) {
                // 0x00: Worksheet, 0x02: Chart, 0x06: Visual Basic module
                continue;
            }

            // check if sheet should be skipped
            if (isset($xls->loadSheetsOnly) && !in_array($sheet['name'], $xls->loadSheetsOnly)) {
                continue;
            }

            // add sheet to PhpSpreadsheet object
            $xls->phpSheet = $xls->spreadsheet->createSheet();
            //    Use false for $updateFormulaCellReferences to prevent adjustment of worksheet references in formula
            //        cells... during the load, all formulae should be correct, and we're simply bringing the worksheet
            //        name in line with the formula, not the reverse
            $xls->phpSheet->setTitle($sheet['name'], false, false);
            $xls->phpSheet->setSheetState($sheet['sheetState']);

            $xls->pos = $sheet['offset'];

            // Initialize isFitToPages. May change after reading SHEETPR record.
            $xls->isFitToPages = false;

            // Initialize drawingData
            $xls->drawingData = '';

            // Initialize objs
            $xls->objs = [];

            // Initialize shared formula parts
            $xls->sharedFormulaParts = [];

            // Initialize shared formulas
            $xls->sharedFormulas = [];

            // Initialize text objs
            $xls->textObjects = [];

            // Initialize cell annotations
            $xls->cellNotes = [];
            $xls->textObjRef = -1;

            while ($xls->pos <= $xls->dataSize - 4) {
                $code = self::getUInt2d($xls->data, $xls->pos);

                switch ($code) {
                    case self::XLS_TYPE_BOF:
                        $xls->readBof();

                        break;
                    case self::XLS_TYPE_PRINTGRIDLINES:
                        $xls->readPrintGridlines();

                        break;
                    case self::XLS_TYPE_DEFAULTROWHEIGHT:
                        $xls->readDefaultRowHeight();

                        break;
                    case self::XLS_TYPE_SHEETPR:
                        $xls->readSheetPr();

                        break;
                    case self::XLS_TYPE_HORIZONTALPAGEBREAKS:
                        $xls->readHorizontalPageBreaks();

                        break;
                    case self::XLS_TYPE_VERTICALPAGEBREAKS:
                        $xls->readVerticalPageBreaks();

                        break;
                    case self::XLS_TYPE_HEADER:
                        $xls->readHeader();

                        break;
                    case self::XLS_TYPE_FOOTER:
                        $xls->readFooter();

                        break;
                    case self::XLS_TYPE_HCENTER:
                        $xls->readHcenter();

                        break;
                    case self::XLS_TYPE_VCENTER:
                        $xls->readVcenter();

                        break;
                    case self::XLS_TYPE_LEFTMARGIN:
                        $xls->readLeftMargin();

                        break;
                    case self::XLS_TYPE_RIGHTMARGIN:
                        $xls->readRightMargin();

                        break;
                    case self::XLS_TYPE_TOPMARGIN:
                        $xls->readTopMargin();

                        break;
                    case self::XLS_TYPE_BOTTOMMARGIN:
                        $xls->readBottomMargin();

                        break;
                    case self::XLS_TYPE_PAGESETUP:
                        $xls->readPageSetup();

                        break;
                    case self::XLS_TYPE_PROTECT:
                        $xls->readProtect();

                        break;
                    case self::XLS_TYPE_SCENPROTECT:
                        $xls->readScenProtect();

                        break;
                    case self::XLS_TYPE_OBJECTPROTECT:
                        $xls->readObjectProtect();

                        break;
                    case self::XLS_TYPE_PASSWORD:
                        $xls->readPassword();

                        break;
                    case self::XLS_TYPE_DEFCOLWIDTH:
                        $xls->readDefColWidth();

                        break;
                    case self::XLS_TYPE_COLINFO:
                        $xls->readColInfo();

                        break;
                    case self::XLS_TYPE_DIMENSION:
                        $xls->readDefault();

                        break;
                    case self::XLS_TYPE_ROW:
                        $xls->readRow();

                        break;
                    case self::XLS_TYPE_DBCELL:
                        $xls->readDefault();

                        break;
                    case self::XLS_TYPE_RK:
                        $xls->readRk();

                        break;
                    case self::XLS_TYPE_LABELSST:
                        $xls->readLabelSst();

                        break;
                    case self::XLS_TYPE_MULRK:
                        $xls->readMulRk();

                        break;
                    case self::XLS_TYPE_NUMBER:
                        $xls->readNumber();

                        break;
                    case self::XLS_TYPE_FORMULA:
                        $xls->readFormula();

                        break;
                    case self::XLS_TYPE_SHAREDFMLA:
                        $xls->readSharedFmla();

                        break;
                    case self::XLS_TYPE_BOOLERR:
                        $xls->readBoolErr();

                        break;
                    case self::XLS_TYPE_MULBLANK:
                        $xls->readMulBlank();

                        break;
                    case self::XLS_TYPE_LABEL:
                        $xls->readLabel();

                        break;
                    case self::XLS_TYPE_BLANK:
                        $xls->readBlank();

                        break;
                    case self::XLS_TYPE_MSODRAWING:
                        $xls->readMsoDrawing();

                        break;
                    case self::XLS_TYPE_OBJ:
                        $xls->readObj();

                        break;
                    case self::XLS_TYPE_WINDOW2:
                        $xls->readWindow2();

                        break;
                    case self::XLS_TYPE_PAGELAYOUTVIEW:
                        $xls->readPageLayoutView();

                        break;
                    case self::XLS_TYPE_SCL:
                        $xls->readScl();

                        break;
                    case self::XLS_TYPE_PANE:
                        $xls->readPane();

                        break;
                    case self::XLS_TYPE_SELECTION:
                        $selectedCells = $xls->readSelection();

                        break;
                    case self::XLS_TYPE_MERGEDCELLS:
                        $xls->readMergedCells();

                        break;
                    case self::XLS_TYPE_HYPERLINK:
                        $xls->readHyperLink();

                        break;
                    case self::XLS_TYPE_DATAVALIDATIONS:
                        $xls->readDataValidations();

                        break;
                    case self::XLS_TYPE_DATAVALIDATION:
                        $xls->readDataValidation();

                        break;
                    case self::XLS_TYPE_CFHEADER:
                        /** @var string[] */
                        $cellRangeAddresses = $xls->readCFHeader();

                        break;
                    case self::XLS_TYPE_CFRULE:
                        $xls->readCFRule($cellRangeAddresses ?? []);

                        break;
                    case self::XLS_TYPE_SHEETLAYOUT:
                        $xls->readSheetLayout();

                        break;
                    case self::XLS_TYPE_SHEETPROTECTION:
                        $xls->readSheetProtection();

                        break;
                    case self::XLS_TYPE_RANGEPROTECTION:
                        $xls->readRangeProtection();

                        break;
                    case self::XLS_TYPE_NOTE:
                        $xls->readNote();

                        break;
                    case self::XLS_TYPE_TXO:
                        $xls->readTextObject();

                        break;
                    case self::XLS_TYPE_CONTINUE:
                        $xls->readContinue();

                        break;
                    case self::XLS_TYPE_EOF:
                        $xls->readDefault();

                        break 2;
                    default:
                        $xls->readDefault();

                        break;
                }
            }

            // treat MSODRAWING records, sheet-level Escher
            if (!$xls->readDataOnly && $xls->drawingData) {
                $escherWorksheet = new SharedEscher();
                $reader = new Escher($escherWorksheet);
                $escherWorksheet = $reader->load($xls->drawingData);

                // get all spContainers in one long array, so they can be mapped to OBJ records
                /** @var SpContainer[] $allSpContainers */
                $allSpContainers = method_exists($escherWorksheet, 'getDgContainer') ? $escherWorksheet->getDgContainer()->getSpgrContainer()->getAllSpContainers() : [];
            }

            // treat OBJ records
            foreach ($xls->objs as $n => $obj) {
                // the first shape container never has a corresponding OBJ record, hence $n + 1
                if (isset($allSpContainers[$n + 1])) {
                    $spContainer = $allSpContainers[$n + 1];

                    // we skip all spContainers that are a part of a group shape since we cannot yet handle those
                    if ($spContainer->getNestingLevel() > 1) {
                        continue;
                    }

                    // calculate the width and height of the shape
                    /** @var int $startRow */
                    [$startColumn, $startRow] = Coordinate::coordinateFromString($spContainer->getStartCoordinates());
                    /** @var int $endRow */
                    [$endColumn, $endRow] = Coordinate::coordinateFromString($spContainer->getEndCoordinates());

                    $startOffsetX = $spContainer->getStartOffsetX();
                    $startOffsetY = $spContainer->getStartOffsetY();
                    $endOffsetX = $spContainer->getEndOffsetX();
                    $endOffsetY = $spContainer->getEndOffsetY();

                    $width = SharedXls::getDistanceX($xls->phpSheet, $startColumn, $startOffsetX, $endColumn, $endOffsetX);
                    $height = SharedXls::getDistanceY($xls->phpSheet, $startRow, $startOffsetY, $endRow, $endOffsetY);

                    // calculate offsetX and offsetY of the shape
                    $offsetX = (int) ($startOffsetX * SharedXls::sizeCol($xls->phpSheet, $startColumn) / 1024);
                    $offsetY = (int) ($startOffsetY * SharedXls::sizeRow($xls->phpSheet, $startRow) / 256);

                    /** @var int[] $obj */
                    switch ($obj['otObjType']) {
                        case 0x19:
                            // Note
                            if (isset($xls->cellNotes[$obj['idObjID']])) {
                                //$cellNote = $xls->cellNotes[$obj['idObjID']];

                                if (isset($xls->textObjects[$obj['idObjID']])) {
                                    $textObject = $xls->textObjects[$obj['idObjID']];
                                    $xls->cellNotes[$obj['idObjID']]['objTextData'] = $textObject; //* @phpstan-ignore-line
                                }
                            }

                            break;
                        case 0x08:
                            // picture
                            // get index to BSE entry (1-based)
                            /** @var int */
                            $BSEindex = $spContainer->getOPT(0x0104);

                            // If there is no BSE Index, we will fail here and other fields are not read.
                            // Fix by checking here.
                            // TODO: Why is there no BSE Index? Is this a new Office Version? Password protected field?
                            // More likely : a uncompatible picture
                            if (!$BSEindex) {
                                continue 2;
                            }

                            if ($escherWorkbook) {
                                /** @var BSE[] */
                                $BSECollection = method_exists($escherWorkbook, 'getDggContainer') ? $escherWorkbook->getDggContainer()->getBstoreContainer()->getBSECollection() : [];
                                $BSE = $BSECollection[$BSEindex - 1];
                                $blipType = $BSE->getBlipType();

                                // need check because some blip types are not supported by Escher reader such as EMF
                                if ($blip = $BSE->getBlip()) {
                                    $ih = imagecreatefromstring($blip->getData());
                                    if ($ih !== false) {
                                        $drawing = new MemoryDrawing();
                                        $drawing->setImageResource($ih);

                                        // width, height, offsetX, offsetY
                                        $drawing->setResizeProportional(false);
                                        $drawing->setWidth($width);
                                        $drawing->setHeight($height);
                                        $drawing->setOffsetX($offsetX);
                                        $drawing->setOffsetY($offsetY);

                                        switch ($blipType) {
                                            case BSE::BLIPTYPE_JPEG:
                                                $drawing->setRenderingFunction(MemoryDrawing::RENDERING_JPEG);
                                                $drawing->setMimeType(MemoryDrawing::MIMETYPE_JPEG);

                                                break;
                                            case BSE::BLIPTYPE_PNG:
                                                imagealphablending($ih, false);
                                                imagesavealpha($ih, true);
                                                $drawing->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
                                                $drawing->setMimeType(MemoryDrawing::MIMETYPE_PNG);

                                                break;
                                        }

                                        $drawing->setWorksheet($xls->phpSheet);
                                        $drawing->setCoordinates($spContainer->getStartCoordinates());
                                    }
                                }
                            }

                            break;
                        default:
                            // other object type
                            break;
                    }
                }
            }

            // treat SHAREDFMLA records
            if ($xls->version == self::XLS_BIFF8) {
                foreach ($xls->sharedFormulaParts as $cell => $baseCell) {
                    /** @var int $row */
                    [$column, $row] = Coordinate::coordinateFromString($cell);
                    if ($xls->getReadFilter()->readCell($column, $row, $xls->phpSheet->getTitle())) {
                        /** @var string */
                        $temp = $xls->sharedFormulas[$baseCell];
                        $formula = $xls->getFormulaFromStructure($temp, $cell);
                        $xls->phpSheet->getCell($cell)->setValueExplicit('=' . $formula, DataType::TYPE_FORMULA);
                    }
                }
            }

            if (!empty($xls->cellNotes)) {
                foreach ($xls->cellNotes as $note => $noteDetails) {
                    /** @var array{author: string, cellRef: string, objTextData?: mixed[]} $noteDetails */
                    if (!isset($noteDetails['objTextData'])) {
                        if (isset($xls->textObjects[$note])) {
                            $textObject = $xls->textObjects[$note];
                            $noteDetails['objTextData'] = $textObject;
                        } else {
                            $noteDetails['objTextData']['text'] = '';
                        }
                    }
                    $cellAddress = str_replace('$', '', $noteDetails['cellRef']);
                    /** @var string */
                    $tempDetails = $noteDetails['objTextData']['text'];
                    $xls->phpSheet
                        ->getComment($cellAddress)
                        ->setAuthor($noteDetails['author'])
                        ->setText(
                            $xls->parseRichText($tempDetails)
                        );
                }
            }
            if ($selectedCells !== '') {
                $xls->phpSheet->setSelectedCells($selectedCells);
            }
        }
        if ($xls->activeSheetSet === false) {
            $xls->spreadsheet->setActiveSheetIndex(0);
        }

        // add the named ranges (defined names)
        foreach ($xls->definedname as $definedName) {
            /** @var array{isBuiltInName: int, name: string, formula: string, scope: int} $definedName */
            if ($definedName['isBuiltInName']) {
                switch ($definedName['name']) {
                    case pack('C', 0x06):
                        // print area
                        //    in general, formula looks like this: Foo!$C$7:$J$66,Bar!$A$1:$IV$2
                        $ranges = explode(',', $definedName['formula']); // FIXME: what if sheetname contains comma?

                        $extractedRanges = [];
                        $sheetName = '';
                        /** @var non-empty-string $range */
                        foreach ($ranges as $range) {
                            // $range should look like one of these
                            //        Foo!$C$7:$J$66
                            //        Bar!$A$1:$IV$2
                            $explodes = Worksheet::extractSheetTitle($range, true, true);
                            $sheetName = (string) $explodes[0];
                            if (!str_contains($explodes[1], ':')) {
                                $explodes[1] = $explodes[1] . ':' . $explodes[1];
                            }
                            $extractedRanges[] = str_replace('$', '', $explodes[1]); // C7:J66
                        }
                        if ($docSheet = $xls->spreadsheet->getSheetByName($sheetName)) {
                            $docSheet->getPageSetup()->setPrintArea(implode(',', $extractedRanges)); // C7:J66,A1:IV2
                        }

                        break;
                    case pack('C', 0x07):
                        // print titles (repeating rows)
                        // Assuming BIFF8, there are 3 cases
                        // 1. repeating rows
                        //        formula looks like this: Sheet!$A$1:$IV$2
                        //        rows 1-2 repeat
                        // 2. repeating columns
                        //        formula looks like this: Sheet!$A$1:$B$65536
                        //        columns A-B repeat
                        // 3. both repeating rows and repeating columns
                        //        formula looks like this: Sheet!$A$1:$B$65536,Sheet!$A$1:$IV$2
                        $ranges = explode(',', $definedName['formula']); // FIXME: what if sheetname contains comma?
                        foreach ($ranges as $range) {
                            // $range should look like this one of these
                            //        Sheet!$A$1:$B$65536
                            //        Sheet!$A$1:$IV$2
                            if (str_contains($range, '!')) {
                                $explodes = Worksheet::extractSheetTitle($range, true, true);
                                $docSheet = $xls->spreadsheet->getSheetByName($explodes[0]);
                                if ($docSheet) {
                                    $extractedRange = $explodes[1];
                                    $extractedRange = str_replace('$', '', $extractedRange);

                                    $coordinateStrings = explode(':', $extractedRange);
                                    if (count($coordinateStrings) == 2) {
                                        [$firstColumn, $firstRow] = Coordinate::coordinateFromString($coordinateStrings[0]);
                                        [$lastColumn, $lastRow] = Coordinate::coordinateFromString($coordinateStrings[1]);
                                        $firstRow = (int) $firstRow;
                                        $lastRow = (int) $lastRow;

                                        if ($firstColumn == 'A' && $lastColumn == 'IV') {
                                            // then we have repeating rows
                                            $docSheet->getPageSetup()->setRowsToRepeatAtTop([$firstRow, $lastRow]);
                                        } elseif ($firstRow == 1 && $lastRow == 65536) {
                                            // then we have repeating columns
                                            $docSheet->getPageSetup()->setColumnsToRepeatAtLeft([$firstColumn, $lastColumn]);
                                        }
                                    }
                                }
                            }
                        }

                        break;
                }
            } else {
                // Extract range
                $formula = $definedName['formula'];
                if (str_contains($formula, '!')) {
                    $explodes = Worksheet::extractSheetTitle($formula, true, true);
                    $docSheet = $xls->spreadsheet->getSheetByName($explodes[0]);
                    if ($docSheet) {
                        $extractedRange = $explodes[1];

                        $localOnly = ($definedName['scope'] === 0) ? false : true;

                        $scope = ($definedName['scope'] === 0) ? null : $xls->spreadsheet->getSheetByName($xls->sheets[$definedName['scope'] - 1]['name']);

                        $xls->spreadsheet->addNamedRange(new NamedRange((string) $definedName['name'], $docSheet, $extractedRange, $localOnly, $scope));
                    }
                }
                //    Named Value
                //    TODO Provide support for named values
            }
        }
        $xls->data = '';

        return $xls->spreadsheet;
    }
}
