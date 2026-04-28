<?php

namespace PhpOffice\PhpSpreadsheetBenchmarks;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\SharedFormula;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SimpleXMLElement;
use XMLReader;

class XlsxStreamingReadClass extends Xlsx
{
    /**
     * Load sheet data using XMLReader for memory-efficient streaming.
     *
     * @param string[][] $richData
     * @param Worksheet $docSheet the worksheet to populate
     * @param string $fileWorksheetPath path to worksheet XML within the zip
     * @param string $mainNS the main spreadsheetml namespace
     * @param array<int, mixed> $sharedStrings shared string table
     * @param object[] $styles style objects array
     */
    protected function loadSheetData(
        ?SimpleXMLElement $xmlSheetNS,
        string $filename,
        string $dir,
        array $richData,
        Worksheet $docSheet,
        string $fileWorksheetPath,
        string $mainNS,
        array $sharedStrings,
        array $styles,
    ): void {
        $xmlContent = $this->getFromZipArchive($this->zip, $fileWorksheetPath);
        if ($xmlContent === '') {
            return; // @codeCoverageIgnore
        }

        $xml = new XMLReader();
        $xml->xml(
            $this->getSecurityScannerOrThrow()->scan($xmlContent),
            null,
            $this->parseHuge ? LIBXML_PARSEHUGE : 0
        );
        $xml->setParserProperty(XMLReader::SUBST_ENTITIES, true);

        // Free the raw XML string now that XMLReader has consumed it
        unset($xmlContent);

        $inSheetData = false;
        $cIndex = 1;

        while ($xml->read()) {
            // Look for the sheetData element to start processing
            if ($xml->localName === 'sheetData' && $xml->namespaceURI === $mainNS) {
                if ($xml->nodeType === XMLReader::ELEMENT) {
                    $inSheetData = true;
                    if ($xml->isEmptyElement) {
                        break; // empty sheetData @codeCoverageIgnore
                    }
                } elseif ($xml->nodeType === XMLReader::END_ELEMENT) {
                    break; // end of sheetData
                }

                continue;
            }

            if (!$inSheetData) {
                continue;
            }

            // Process row elements
            if ($xml->localName === 'row' && $xml->nodeType === XMLReader::ELEMENT && $xml->namespaceURI === $mainNS) {
                $rowIndex = 1;

                if ($xml->isEmptyElement) { // @codeCoverageIgnoreStart
                    ++$cIndex;

                    continue; // @codeCoverageIgnoreEnd
                }

                // Read cell elements within this row
                while ($xml->read()) {
                    if ($xml->localName === 'row' && $xml->nodeType === XMLReader::END_ELEMENT) {
                        break; // end of row
                    }

                    if ($xml->localName === 'c' && $xml->nodeType === XMLReader::ELEMENT && $xml->namespaceURI === $mainNS) {
                        $r = $xml->getAttribute('r') ?? '';
                        if ($r === '') {
                            $r = Coordinate::stringFromColumnIndex($rowIndex) . $cIndex; // @codeCoverageIgnore
                        }
                        $cellDataType = $xml->getAttribute('t') ?? '';
                        $originalCellDataTypeNumeric = $cellDataType === '';
                        $styleIndex = (int) ($xml->getAttribute('s') ?? 0);
                        $value = null;
                        $calculatedValue = null;

                        // Read cell?
                        $coordinates = Coordinate::coordinateFromString($r);

                        // Parse the cell's inner XML using SimpleXML for the <c> element subtree only.
                        // This keeps memory-efficient row-by-row iteration while reusing
                        // existing parsing logic for formulas, inline strings, etc.
                        $cellXml = null;
                        if (!$xml->isEmptyElement) {
                            $outerXml = $xml->readOuterXml();
                            if ($outerXml !== '') {
                                // readOuterXml() typically includes inherited namespace declarations.
                                // If the namespace is missing, wrap the element with it.
                                if (!str_contains($outerXml, 'xmlns')) { // @codeCoverageIgnoreStart
                                    $outerXml = '<c xmlns="' . $mainNS . '"' . substr($outerXml, 2);
                                } // @codeCoverageIgnoreEnd
                                $cellXmlRoot = @simplexml_load_string($outerXml);
                                if ($cellXmlRoot !== false) {
                                    $cellXml = $cellXmlRoot->children($mainNS);
                                }
                            }
                        }

                        if (!$this->getReadFilter()->readCell($coordinates[0], (int) $coordinates[1], $docSheet->getTitle())) {
                            // @codeCoverageIgnoreStart
                            // Handle shared formulas for filtered cells
                            if ($cellXml !== null && isset($cellXml->f)) {
                                $fAttrs = $cellXml->f->attributes();
                                if (isset($fAttrs['t']) && strtolower((string) $fAttrs['t']) === 'shared') {
                                    $this->processStreamingFormula($cellXml, $r, $cellDataType, $value, $calculatedValue, false);
                                }
                            }
                            ++$rowIndex;

                            continue;
                            // @codeCoverageIgnoreEnd
                        }

                        // Determine if cell contains a formula
                        $useFormula = false;
                        if ($cellXml !== null && isset($cellXml->f)) {
                            $fStr = (string) $cellXml->f;
                            $fAttrs = $cellXml->f->attributes();
                            $useFormula = $fStr !== '' || (isset($fAttrs['t']) && strtolower((string) $fAttrs['t']) === 'shared');
                        }

                        // Get value element text
                        $vValue = ($cellXml !== null && isset($cellXml->v)) ? (string) $cellXml->v : null;

                        switch ($cellDataType) {
                            case DataType::TYPE_STRING:
                                // Shared string
                                if ($vValue !== null && $vValue !== '') {
                                    $ssIndex = (int) $vValue;
                                    $value = $sharedStrings[$ssIndex] ?? '';
                                    if ($value instanceof RichText) {
                                        $value = clone $value; // @codeCoverageIgnore
                                    }
                                } else {
                                    $value = ''; // @codeCoverageIgnore
                                }

                                break;

                            case DataType::TYPE_BOOL:
                                if (!$useFormula) {
                                    if ($vValue !== null) {
                                        $value = ($vValue === '1');
                                    } else {
                                        $value = null; // @codeCoverageIgnore
                                        $cellDataType = DataType::TYPE_NULL; // @codeCoverageIgnore
                                    }
                                } else {
                                    // @codeCoverageIgnoreStart
                                    $this->processStreamingFormula($cellXml, $r, $cellDataType, $value, $calculatedValue);
                                    if ($cellXml !== null && isset($cellXml->f)) {
                                        $this->storeStreamingFormulaAttributes($cellXml->f, $docSheet, $r);
                                    }
                                    // @codeCoverageIgnoreEnd
                                }

                                break;

                            case DataType::TYPE_STRING2:
                                if ($useFormula) {
                                    $this->processStreamingFormula($cellXml, $r, $cellDataType, $value, $calculatedValue);
                                    if ($cellXml !== null && isset($cellXml->f)) {
                                        $this->storeStreamingFormulaAttributes($cellXml->f, $docSheet, $r);
                                    }
                                } else {
                                    $value = $vValue; // @codeCoverageIgnore
                                }

                                break;

                            case DataType::TYPE_INLINE:
                                if ($useFormula) {
                                    // @codeCoverageIgnoreStart
                                    $this->processStreamingFormula($cellXml, $r, $cellDataType, $value, $calculatedValue);
                                    if ($cellXml !== null && isset($cellXml->f)) {
                                        $this->storeStreamingFormulaAttributes($cellXml->f, $docSheet, $r);
                                    }
                                    // @codeCoverageIgnoreEnd
                                } elseif ($cellXml !== null && isset($cellXml->is)) {
                                    $value = $this->parseRichText($cellXml->is);
                                }

                                break;

                            case DataType::TYPE_ERROR:
                                if (!$useFormula) {
                                    $value = $vValue;
                                } else {
                                    $this->processStreamingFormula($cellXml, $r, $cellDataType, $value, $calculatedValue);
                                }

                                break;

                            default:
                                // Numeric or untyped
                                if (!$useFormula) {
                                    $value = $vValue;
                                    if ($value !== null && is_numeric($value)) {
                                        $value += 0;
                                        $cellDataType = DataType::TYPE_NUMERIC;
                                    }
                                } else {
                                    $this->processStreamingFormula($cellXml, $r, $cellDataType, $value, $calculatedValue);
                                    if (is_numeric($calculatedValue)) {
                                        $calculatedValue += 0;
                                    }
                                    if ($cellXml !== null && isset($cellXml->f)) {
                                        $this->storeStreamingFormulaAttributes($cellXml->f, $docSheet, $r);
                                    }
                                }

                                break;
                        }

                        // Read empty cells or cells that are not empty
                        if ($this->readEmptyCells || ($value !== null && $value !== '')) {
                            // Rich text?
                            if ($value instanceof RichText && $this->readDataOnly) {
                                $value = $value->getPlainText(); // @codeCoverageIgnore
                            }

                            $cell = $docSheet->getCell($r);
                            // Assign value
                            if ($cellDataType !== '') {
                                if ($cellDataType === DataType::TYPE_NUMERIC && ($value === '' || $value === null)) {
                                    $cellDataType = DataType::TYPE_NULL; // @codeCoverageIgnore
                                }
                                if ($cellDataType !== DataType::TYPE_NULL) {
                                    $cell->setValueExplicit($value, $cellDataType);
                                }
                            } else {
                                $cell->setValue($value);
                            }
                            if ($calculatedValue !== null) {
                                $cell->setCalculatedValue($calculatedValue, $originalCellDataTypeNumeric);
                            }

                            // Style information?
                            if (!$this->readDataOnly) {
                                $cAttrS = isset($styles[$styleIndex]) ? $styleIndex : 0;
                                $cell->setXfIndex($cAttrS);
                                if ($cellDataType === DataType::TYPE_FORMULA && isset($styles[$cAttrS]) && $styles[$cAttrS]->quotePrefix === true) { //* @phpstan-ignore-line
                                    $holdSelected = $docSheet->getSelectedCells(); // @codeCoverageIgnore
                                    $cell->getStyle()->setQuotePrefix(false); // @codeCoverageIgnore
                                    $docSheet->setSelectedCells($holdSelected); // @codeCoverageIgnore
                                }
                            }
                        }

                        ++$rowIndex;
                    }
                }

                ++$cIndex;
            }
        }

        $xml->close();
    }

    /**
     * Process a formula from a streaming-parsed cell element.
     */
    private function processStreamingFormula(
        ?SimpleXMLElement $cellXml,
        string $r,
        string &$cellDataType,
        mixed &$value,
        mixed &$calculatedValue,
        bool $updateSharedCells = true,
    ): void {
        if ($cellXml === null || !isset($cellXml->f)) {
            return; // @codeCoverageIgnore
        }

        $originalDataType = $cellDataType;
        $cellDataType = DataType::TYPE_FORMULA;
        $formula = self::replacePrefixes((string) $cellXml->f);
        $value = "=$formula";

        // Calculated value from <v>
        $calculatedValue = isset($cellXml->v) ? (string) $cellXml->v : null;
        if ($calculatedValue !== null && is_numeric($calculatedValue)) {
            $calculatedValue += 0;
        }

        // Handle TYPE_BOOL calculated values - match castToBoolean behavior
        if ($originalDataType === DataType::TYPE_BOOL && $calculatedValue !== null) {
            $calculatedValue = (bool) $calculatedValue;
        }

        // Shared formula?
        $attr = $cellXml->f->attributes();
        if (isset($attr['t']) && strtolower((string) $attr['t']) === 'shared') {
            $instance = (string) $attr['si'];

            if (!isset($this->sharedFormulae[$instance])) {
                $this->sharedFormulae[$instance] = new SharedFormula($r, $value);
            } elseif ($updateSharedCells) {
                $master = Coordinate::indexesFromString($this->sharedFormulae[$instance]->master());
                $current = Coordinate::indexesFromString($r);

                $difference = [0, 0];
                $difference[0] = $current[0] - $master[0];
                $difference[1] = $current[1] - $master[1];

                $value = $this->referenceHelper->updateFormulaReferences(
                    $this->sharedFormulae[$instance]->formula(),
                    'A1',
                    $difference[0],
                    $difference[1]
                );
            }
        }
    }

    /**
     * Store formula attributes from a streaming-parsed formula element.
     */
    private function storeStreamingFormulaAttributes(SimpleXMLElement $f, Worksheet $docSheet, string $r): void
    {
        $formulaAttributes = [];
        $attributes = $f->attributes();
        if (isset($attributes['t'])) {
            $formulaAttributes['t'] = (string) $attributes['t'];
        }
        if (isset($attributes['ref'])) {
            $formulaAttributes['ref'] = (string) $attributes['ref'];
        }
        if (!empty($formulaAttributes)) {
            $docSheet->getCell($r)->setFormulaAttributes($formulaAttributes);
        }
    }
}
