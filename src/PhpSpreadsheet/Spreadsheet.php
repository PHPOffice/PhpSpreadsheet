<?php

namespace PhpOffice\PhpSpreadsheet;

/**
 * PhpSpreadsheet.
 *
 * Copyright (c) 2006 - 2016 PhpSpreadsheet
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category   PHPSpreadsheet
 *
 * @copyright  Copyright (c) 2006 PHPOffice (http://www.github.com/PHPOffice)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */
class Spreadsheet
{
    /**
     * Unique ID.
     *
     * @var string
     */
    private $uniqueID;

    /**
     * Document properties.
     *
     * @var Document\Properties
     */
    private $properties;

    /**
     * Document security.
     *
     * @var Document\Security
     */
    private $security;

    /**
     * Collection of Worksheet objects.
     *
     * @var Worksheet[]
     */
    private $workSheetCollection = [];

    /**
     * Calculation Engine.
     *
     * @var Calculation
     */
    private $calculationEngine;

    /**
     * Active sheet index.
     *
     * @var int
     */
    private $activeSheetIndex = 0;

    /**
     * Named ranges.
     *
     * @var NamedRange[]
     */
    private $namedRanges = [];

    /**
     * CellXf supervisor.
     *
     * @var Style
     */
    private $cellXfSupervisor;

    /**
     * CellXf collection.
     *
     * @var Style[]
     */
    private $cellXfCollection = [];

    /**
     * CellStyleXf collection.
     *
     * @var Style[]
     */
    private $cellStyleXfCollection = [];

    /**
     * hasMacros : this workbook have macros ?
     *
     * @var bool
     */
    private $hasMacros = false;

    /**
     * macrosCode : all macros code (the vbaProject.bin file, this include form, code,  etc.), null if no macro.
     *
     * @var binary
     */
    private $macrosCode;
    /**
     * macrosCertificate : if macros are signed, contains vbaProjectSignature.bin file, null if not signed.
     *
     * @var binary
     */
    private $macrosCertificate;

    /**
     * ribbonXMLData : null if workbook is'nt Excel 2007 or not contain a customized UI.
     *
     * @var null|string
     */
    private $ribbonXMLData;

    /**
     * ribbonBinObjects : null if workbook is'nt Excel 2007 or not contain embedded objects (picture(s)) for Ribbon Elements
     * ignored if $ribbonXMLData is null.
     *
     * @var null|array
     */
    private $ribbonBinObjects;

    /**
     * The workbook has macros ?
     *
     * @return bool
     */
    public function hasMacros()
    {
        return $this->hasMacros;
    }

    /**
     * Define if a workbook has macros.
     *
     * @param bool $hasMacros true|false
     */
    public function setHasMacros($hasMacros)
    {
        $this->hasMacros = (bool) $hasMacros;
    }

    /**
     * Set the macros code.
     *
     * @param string $macroCode string|null
     */
    public function setMacrosCode($macroCode)
    {
        $this->macrosCode = $macroCode;
        $this->setHasMacros(!is_null($macroCode));
    }

    /**
     * Return the macros code.
     *
     * @return string|null
     */
    public function getMacrosCode()
    {
        return $this->macrosCode;
    }

    /**
     * Set the macros certificate.
     *
     * @param string|null $certificate
     */
    public function setMacrosCertificate($certificate)
    {
        $this->macrosCertificate = $certificate;
    }

    /**
     * Is the project signed ?
     *
     * @return bool true|false
     */
    public function hasMacrosCertificate()
    {
        return !is_null($this->macrosCertificate);
    }

    /**
     * Return the macros certificate.
     *
     * @return string|null
     */
    public function getMacrosCertificate()
    {
        return $this->macrosCertificate;
    }

    /**
     * Remove all macros, certificate from spreadsheet.
     */
    public function discardMacros()
    {
        $this->hasMacros = false;
        $this->macrosCode = null;
        $this->macrosCertificate = null;
    }

    /**
     * set ribbon XML data.
     *
     * @param null|mixed $target
     * @param null|mixed $xmlData
     */
    public function setRibbonXMLData($target, $xmlData)
    {
        if (!is_null($target) && !is_null($xmlData)) {
            $this->ribbonXMLData = ['target' => $target, 'data' => $xmlData];
        } else {
            $this->ribbonXMLData = null;
        }
    }

    /**
     * retrieve ribbon XML Data.
     *
     * return string|null|array
     *
     * @param string $what
     *
     * @return string
     */
    public function getRibbonXMLData($what = 'all') //we need some constants here...
    {
        $returnData = null;
        $what = strtolower($what);
        switch ($what) {
            case 'all':
                $returnData = $this->ribbonXMLData;
                break;
            case 'target':
            case 'data':
                if (is_array($this->ribbonXMLData) && isset($this->ribbonXMLData[$what])) {
                    $returnData = $this->ribbonXMLData[$what];
                }
                break;
        }

        return $returnData;
    }

    /**
     * store binaries ribbon objects (pictures).
     *
     * @param null|mixed $BinObjectsNames
     * @param null|mixed $BinObjectsData
     */
    public function setRibbonBinObjects($BinObjectsNames, $BinObjectsData)
    {
        if (!is_null($BinObjectsNames) && !is_null($BinObjectsData)) {
            $this->ribbonBinObjects = ['names' => $BinObjectsNames, 'data' => $BinObjectsData];
        } else {
            $this->ribbonBinObjects = null;
        }
    }

    /**
     * return the extension of a filename. Internal use for a array_map callback (php<5.3 don't like lambda function).
     *
     * @param mixed $ThePath
     */
    private function getExtensionOnly($ThePath)
    {
        return pathinfo($ThePath, PATHINFO_EXTENSION);
    }

    /**
     * retrieve Binaries Ribbon Objects.
     *
     * @param mixed $what
     */
    public function getRibbonBinObjects($what = 'all')
    {
        $ReturnData = null;
        $what = strtolower($what);
        switch ($what) {
            case 'all':
                return $this->ribbonBinObjects;
                break;
            case 'names':
            case 'data':
                if (is_array($this->ribbonBinObjects) && isset($this->ribbonBinObjects[$what])) {
                    $ReturnData = $this->ribbonBinObjects[$what];
                }
                break;
            case 'types':
                if (is_array($this->ribbonBinObjects) &&
                    isset($this->ribbonBinObjects['data']) && is_array($this->ribbonBinObjects['data'])) {
                    $tmpTypes = array_keys($this->ribbonBinObjects['data']);
                    $ReturnData = array_unique(array_map([$this, 'getExtensionOnly'], $tmpTypes));
                } else {
                    $ReturnData = []; // the caller want an array... not null if empty
                }
                break;
        }

        return $ReturnData;
    }

    /**
     * This workbook have a custom UI ?
     *
     * @return bool
     */
    public function hasRibbon()
    {
        return !is_null($this->ribbonXMLData);
    }

    /**
     * This workbook have additionnal object for the ribbon ?
     *
     * @return bool
     */
    public function hasRibbonBinObjects()
    {
        return !is_null($this->ribbonBinObjects);
    }

    /**
     * Check if a sheet with a specified code name already exists.
     *
     * @param string $pSheetCodeName Name of the worksheet to check
     *
     * @return bool
     */
    public function sheetCodeNameExists($pSheetCodeName)
    {
        return $this->getSheetByCodeName($pSheetCodeName) !== null;
    }

    /**
     * Get sheet by code name. Warning : sheet don't have always a code name !
     *
     * @param string $pName Sheet name
     *
     * @return Worksheet
     */
    public function getSheetByCodeName($pName)
    {
        $worksheetCount = count($this->workSheetCollection);
        for ($i = 0; $i < $worksheetCount; ++$i) {
            if ($this->workSheetCollection[$i]->getCodeName() == $pName) {
                return $this->workSheetCollection[$i];
            }
        }

        return null;
    }

    /**
     * Create a new PhpSpreadsheet with one Worksheet.
     */
    public function __construct()
    {
        $this->uniqueID = uniqid();
        $this->calculationEngine = new Calculation($this);

        // Initialise worksheet collection and add one worksheet
        $this->workSheetCollection = [];
        $this->workSheetCollection[] = new Worksheet($this);
        $this->activeSheetIndex = 0;

        // Create document properties
        $this->properties = new Document\Properties();

        // Create document security
        $this->security = new Document\Security();

        // Set named ranges
        $this->namedRanges = [];

        // Create the cellXf supervisor
        $this->cellXfSupervisor = new Style(true);
        $this->cellXfSupervisor->bindParent($this);

        // Create the default style
        $this->addCellXf(new Style());
        $this->addCellStyleXf(new Style());
    }

    /**
     * Code to execute when this worksheet is unset().
     */
    public function __destruct()
    {
        $this->calculationEngine = null;
        $this->disconnectWorksheets();
    }

    /**
     * Disconnect all worksheets from this PhpSpreadsheet workbook object,
     * typically so that the PhpSpreadsheet object can be unset.
     */
    public function disconnectWorksheets()
    {
        $worksheet = null;
        foreach ($this->workSheetCollection as $k => &$worksheet) {
            $worksheet->disconnectCells();
            $this->workSheetCollection[$k] = null;
        }
        unset($worksheet);
        $this->workSheetCollection = [];
    }

    /**
     * Return the calculation engine for this worksheet.
     *
     * @return Calculation
     */
    public function getCalculationEngine()
    {
        return $this->calculationEngine;
    }

    /**
     * Get properties.
     *
     * @return Document\Properties
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Set properties.
     *
     * @param Document\Properties $pValue
     */
    public function setProperties(Document\Properties $pValue)
    {
        $this->properties = $pValue;
    }

    /**
     * Get security.
     *
     * @return Document\Security
     */
    public function getSecurity()
    {
        return $this->security;
    }

    /**
     * Set security.
     *
     * @param Document\Security $pValue
     */
    public function setSecurity(Document\Security $pValue)
    {
        $this->security = $pValue;
    }

    /**
     * Get active sheet.
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function getActiveSheet()
    {
        return $this->getSheet($this->activeSheetIndex);
    }

    /**
     * Create sheet and add it to this workbook.
     *
     * @param int|null $sheetIndex Index where sheet should go (0,1,..., or null for last)
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function createSheet($sheetIndex = null)
    {
        $newSheet = new Worksheet($this);
        $this->addSheet($newSheet, $sheetIndex);

        return $newSheet;
    }

    /**
     * Check if a sheet with a specified name already exists.
     *
     * @param string $pSheetName Name of the worksheet to check
     *
     * @return bool
     */
    public function sheetNameExists($pSheetName)
    {
        return $this->getSheetByName($pSheetName) !== null;
    }

    /**
     * Add sheet.
     *
     * @param Worksheet $pSheet
     * @param int|null $iSheetIndex Index where sheet should go (0,1,..., or null for last)
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function addSheet(Worksheet $pSheet, $iSheetIndex = null)
    {
        if ($this->sheetNameExists($pSheet->getTitle())) {
            throw new Exception(
                "Workbook already contains a worksheet named '{$pSheet->getTitle()}'. Rename this worksheet first."
            );
        }

        if ($iSheetIndex === null) {
            if ($this->activeSheetIndex < 0) {
                $this->activeSheetIndex = 0;
            }
            $this->workSheetCollection[] = $pSheet;
        } else {
            // Insert the sheet at the requested index
            array_splice(
                $this->workSheetCollection,
                $iSheetIndex,
                0,
                [$pSheet]
            );

            // Adjust active sheet index if necessary
            if ($this->activeSheetIndex >= $iSheetIndex) {
                ++$this->activeSheetIndex;
            }
        }

        if ($pSheet->getParent() === null) {
            $pSheet->rebindParent($this);
        }

        return $pSheet;
    }

    /**
     * Remove sheet by index.
     *
     * @param int $pIndex Active sheet index
     *
     * @throws Exception
     */
    public function removeSheetByIndex($pIndex)
    {
        $numSheets = count($this->workSheetCollection);
        if ($pIndex > $numSheets - 1) {
            throw new Exception(
                "You tried to remove a sheet by the out of bounds index: {$pIndex}. The actual number of sheets is {$numSheets}."
            );
        }
        array_splice($this->workSheetCollection, $pIndex, 1);

        // Adjust active sheet index if necessary
        if (($this->activeSheetIndex >= $pIndex) &&
            ($pIndex > count($this->workSheetCollection) - 1)) {
            --$this->activeSheetIndex;
        }
    }

    /**
     * Get sheet by index.
     *
     * @param int $pIndex Sheet index
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function getSheet($pIndex)
    {
        if (!isset($this->workSheetCollection[$pIndex])) {
            $numSheets = $this->getSheetCount();
            throw new Exception(
                "Your requested sheet index: {$pIndex} is out of bounds. The actual number of sheets is {$numSheets}."
            );
        }

        return $this->workSheetCollection[$pIndex];
    }

    /**
     * Get all sheets.
     *
     * @return Worksheet[]
     */
    public function getAllSheets()
    {
        return $this->workSheetCollection;
    }

    /**
     * Get sheet by name.
     *
     * @param string $pName Sheet name
     *
     * @return Worksheet
     */
    public function getSheetByName($pName)
    {
        $worksheetCount = count($this->workSheetCollection);
        for ($i = 0; $i < $worksheetCount; ++$i) {
            if ($this->workSheetCollection[$i]->getTitle() === $pName) {
                return $this->workSheetCollection[$i];
            }
        }

        return null;
    }

    /**
     * Get index for sheet.
     *
     * @param Worksheet $pSheet
     *
     * @throws Exception
     *
     * @return int index
     */
    public function getIndex(Worksheet $pSheet)
    {
        foreach ($this->workSheetCollection as $key => $value) {
            if ($value->getHashCode() == $pSheet->getHashCode()) {
                return $key;
            }
        }

        throw new Exception('Sheet does not exist.');
    }

    /**
     * Set index for sheet by sheet name.
     *
     * @param string $sheetName Sheet name to modify index for
     * @param int $newIndex New index for the sheet
     *
     * @throws Exception
     *
     * @return int New sheet index
     */
    public function setIndexByName($sheetName, $newIndex)
    {
        $oldIndex = $this->getIndex($this->getSheetByName($sheetName));
        $pSheet = array_splice(
            $this->workSheetCollection,
            $oldIndex,
            1
        );
        array_splice(
            $this->workSheetCollection,
            $newIndex,
            0,
            $pSheet
        );

        return $newIndex;
    }

    /**
     * Get sheet count.
     *
     * @return int
     */
    public function getSheetCount()
    {
        return count($this->workSheetCollection);
    }

    /**
     * Get active sheet index.
     *
     * @return int Active sheet index
     */
    public function getActiveSheetIndex()
    {
        return $this->activeSheetIndex;
    }

    /**
     * Set active sheet index.
     *
     * @param int $pIndex Active sheet index
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function setActiveSheetIndex($pIndex)
    {
        $numSheets = count($this->workSheetCollection);

        if ($pIndex > $numSheets - 1) {
            throw new Exception(
                "You tried to set a sheet active by the out of bounds index: {$pIndex}. The actual number of sheets is {$numSheets}."
            );
        }
        $this->activeSheetIndex = $pIndex;

        return $this->getActiveSheet();
    }

    /**
     * Set active sheet index by name.
     *
     * @param string $pValue Sheet title
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function setActiveSheetIndexByName($pValue)
    {
        if (($worksheet = $this->getSheetByName($pValue)) instanceof Worksheet) {
            $this->setActiveSheetIndex($this->getIndex($worksheet));

            return $worksheet;
        }

        throw new Exception('Workbook does not contain sheet:' . $pValue);
    }

    /**
     * Get sheet names.
     *
     * @return string[]
     */
    public function getSheetNames()
    {
        $returnValue = [];
        $worksheetCount = $this->getSheetCount();
        for ($i = 0; $i < $worksheetCount; ++$i) {
            $returnValue[] = $this->getSheet($i)->getTitle();
        }

        return $returnValue;
    }

    /**
     * Add external sheet.
     *
     * @param Worksheet $pSheet External sheet to add
     * @param int|null $iSheetIndex Index where sheet should go (0,1,..., or null for last)
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function addExternalSheet(Worksheet $pSheet, $iSheetIndex = null)
    {
        if ($this->sheetNameExists($pSheet->getTitle())) {
            throw new Exception("Workbook already contains a worksheet named '{$pSheet->getTitle()}'. Rename the external sheet first.");
        }

        // count how many cellXfs there are in this workbook currently, we will need this below
        $countCellXfs = count($this->cellXfCollection);

        // copy all the shared cellXfs from the external workbook and append them to the current
        foreach ($pSheet->getParent()->getCellXfCollection() as $cellXf) {
            $this->addCellXf(clone $cellXf);
        }

        // move sheet to this workbook
        $pSheet->rebindParent($this);

        // update the cellXfs
        foreach ($pSheet->getCoordinates(false) as $coordinate) {
            $cell = $pSheet->getCell($coordinate);
            $cell->setXfIndex($cell->getXfIndex() + $countCellXfs);
        }

        return $this->addSheet($pSheet, $iSheetIndex);
    }

    /**
     * Get named ranges.
     *
     * @return NamedRange[]
     */
    public function getNamedRanges()
    {
        return $this->namedRanges;
    }

    /**
     * Add named range.
     *
     * @param NamedRange $namedRange
     *
     * @return bool
     */
    public function addNamedRange(NamedRange $namedRange)
    {
        if ($namedRange->getScope() == null) {
            // global scope
            $this->namedRanges[$namedRange->getName()] = $namedRange;
        } else {
            // local scope
            $this->namedRanges[$namedRange->getScope()->getTitle() . '!' . $namedRange->getName()] = $namedRange;
        }

        return true;
    }

    /**
     * Get named range.
     *
     * @param string $namedRange
     * @param Worksheet|null $pSheet Scope. Use null for global scope
     *
     * @return NamedRange|null
     */
    public function getNamedRange($namedRange, Worksheet $pSheet = null)
    {
        $returnValue = null;

        if ($namedRange != '' && ($namedRange !== null)) {
            // first look for global defined name
            if (isset($this->namedRanges[$namedRange])) {
                $returnValue = $this->namedRanges[$namedRange];
            }

            // then look for local defined name (has priority over global defined name if both names exist)
            if (($pSheet !== null) && isset($this->namedRanges[$pSheet->getTitle() . '!' . $namedRange])) {
                $returnValue = $this->namedRanges[$pSheet->getTitle() . '!' . $namedRange];
            }
        }

        return $returnValue;
    }

    /**
     * Remove named range.
     *
     * @param string $namedRange
     * @param Worksheet|null $pSheet scope: use null for global scope
     *
     * @return Spreadsheet
     */
    public function removeNamedRange($namedRange, Worksheet $pSheet = null)
    {
        if ($pSheet === null) {
            if (isset($this->namedRanges[$namedRange])) {
                unset($this->namedRanges[$namedRange]);
            }
        } else {
            if (isset($this->namedRanges[$pSheet->getTitle() . '!' . $namedRange])) {
                unset($this->namedRanges[$pSheet->getTitle() . '!' . $namedRange]);
            }
        }

        return $this;
    }

    /**
     * Get worksheet iterator.
     *
     * @return Worksheet\Iterator
     */
    public function getWorksheetIterator()
    {
        return new Worksheet\Iterator($this);
    }

    /**
     * Copy workbook (!= clone!).
     *
     * @return Spreadsheet
     */
    public function copy()
    {
        $copied = clone $this;

        $worksheetCount = count($this->workSheetCollection);
        for ($i = 0; $i < $worksheetCount; ++$i) {
            $this->workSheetCollection[$i] = $this->workSheetCollection[$i]->copy();
            $this->workSheetCollection[$i]->rebindParent($this);
        }

        return $copied;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        foreach ($this as $key => $val) {
            if (is_object($val) || (is_array($val))) {
                $this->{$key} = unserialize(serialize($val));
            }
        }
    }

    /**
     * Get the workbook collection of cellXfs.
     *
     * @return Style[]
     */
    public function getCellXfCollection()
    {
        return $this->cellXfCollection;
    }

    /**
     * Get cellXf by index.
     *
     * @param int $pIndex
     *
     * @return Style
     */
    public function getCellXfByIndex($pIndex)
    {
        return $this->cellXfCollection[$pIndex];
    }

    /**
     * Get cellXf by hash code.
     *
     * @param string $pValue
     *
     * @return Style|false
     */
    public function getCellXfByHashCode($pValue)
    {
        foreach ($this->cellXfCollection as $cellXf) {
            if ($cellXf->getHashCode() == $pValue) {
                return $cellXf;
            }
        }

        return false;
    }

    /**
     * Check if style exists in style collection.
     *
     * @param Style $pCellStyle
     *
     * @return bool
     */
    public function cellXfExists($pCellStyle)
    {
        return in_array($pCellStyle, $this->cellXfCollection, true);
    }

    /**
     * Get default style.
     *
     * @throws Exception
     *
     * @return Style
     */
    public function getDefaultStyle()
    {
        if (isset($this->cellXfCollection[0])) {
            return $this->cellXfCollection[0];
        }
        throw new Exception('No default style found for this workbook');
    }

    /**
     * Add a cellXf to the workbook.
     *
     * @param Style $style
     */
    public function addCellXf(Style $style)
    {
        $this->cellXfCollection[] = $style;
        $style->setIndex(count($this->cellXfCollection) - 1);
    }

    /**
     * Remove cellXf by index. It is ensured that all cells get their xf index updated.
     *
     * @param int $pIndex Index to cellXf
     *
     * @throws Exception
     */
    public function removeCellXfByIndex($pIndex)
    {
        if ($pIndex > count($this->cellXfCollection) - 1) {
            throw new Exception('CellXf index is out of bounds.');
        }

        // first remove the cellXf
        array_splice($this->cellXfCollection, $pIndex, 1);

        // then update cellXf indexes for cells
        foreach ($this->workSheetCollection as $worksheet) {
            foreach ($worksheet->getCoordinates(false) as $coordinate) {
                $cell = $worksheet->getCell($coordinate);
                $xfIndex = $cell->getXfIndex();
                if ($xfIndex > $pIndex) {
                    // decrease xf index by 1
                    $cell->setXfIndex($xfIndex - 1);
                } elseif ($xfIndex == $pIndex) {
                    // set to default xf index 0
                    $cell->setXfIndex(0);
                }
            }
        }
    }

    /**
     * Get the cellXf supervisor.
     *
     * @return Style
     */
    public function getCellXfSupervisor()
    {
        return $this->cellXfSupervisor;
    }

    /**
     * Get the workbook collection of cellStyleXfs.
     *
     * @return Style[]
     */
    public function getCellStyleXfCollection()
    {
        return $this->cellStyleXfCollection;
    }

    /**
     * Get cellStyleXf by index.
     *
     * @param int $pIndex Index to cellXf
     *
     * @return Style
     */
    public function getCellStyleXfByIndex($pIndex)
    {
        return $this->cellStyleXfCollection[$pIndex];
    }

    /**
     * Get cellStyleXf by hash code.
     *
     * @param string $pValue
     *
     * @return Style|false
     */
    public function getCellStyleXfByHashCode($pValue)
    {
        foreach ($this->cellStyleXfCollection as $cellStyleXf) {
            if ($cellStyleXf->getHashCode() == $pValue) {
                return $cellStyleXf;
            }
        }

        return false;
    }

    /**
     * Add a cellStyleXf to the workbook.
     *
     * @param Style $pStyle
     */
    public function addCellStyleXf(Style $pStyle)
    {
        $this->cellStyleXfCollection[] = $pStyle;
        $pStyle->setIndex(count($this->cellStyleXfCollection) - 1);
    }

    /**
     * Remove cellStyleXf by index.
     *
     * @param int $pIndex Index to cellXf
     *
     * @throws Exception
     */
    public function removeCellStyleXfByIndex($pIndex)
    {
        if ($pIndex > count($this->cellStyleXfCollection) - 1) {
            throw new Exception('CellStyleXf index is out of bounds.');
        }
        array_splice($this->cellStyleXfCollection, $pIndex, 1);
    }

    /**
     * Eliminate all unneeded cellXf and afterwards update the xfIndex for all cells
     * and columns in the workbook.
     */
    public function garbageCollect()
    {
        // how many references are there to each cellXf ?
        $countReferencesCellXf = [];
        foreach ($this->cellXfCollection as $index => $cellXf) {
            $countReferencesCellXf[$index] = 0;
        }

        foreach ($this->getWorksheetIterator() as $sheet) {
            // from cells
            foreach ($sheet->getCoordinates(false) as $coordinate) {
                $cell = $sheet->getCell($coordinate);
                ++$countReferencesCellXf[$cell->getXfIndex()];
            }

            // from row dimensions
            foreach ($sheet->getRowDimensions() as $rowDimension) {
                if ($rowDimension->getXfIndex() !== null) {
                    ++$countReferencesCellXf[$rowDimension->getXfIndex()];
                }
            }

            // from column dimensions
            foreach ($sheet->getColumnDimensions() as $columnDimension) {
                ++$countReferencesCellXf[$columnDimension->getXfIndex()];
            }
        }

        // remove cellXfs without references and create mapping so we can update xfIndex
        // for all cells and columns
        $countNeededCellXfs = 0;
        foreach ($this->cellXfCollection as $index => $cellXf) {
            if ($countReferencesCellXf[$index] > 0 || $index == 0) { // we must never remove the first cellXf
                ++$countNeededCellXfs;
            } else {
                unset($this->cellXfCollection[$index]);
            }
            $map[$index] = $countNeededCellXfs - 1;
        }
        $this->cellXfCollection = array_values($this->cellXfCollection);

        // update the index for all cellXfs
        foreach ($this->cellXfCollection as $i => $cellXf) {
            $cellXf->setIndex($i);
        }

        // make sure there is always at least one cellXf (there should be)
        if (empty($this->cellXfCollection)) {
            $this->cellXfCollection[] = new Style();
        }

        // update the xfIndex for all cells, row dimensions, column dimensions
        foreach ($this->getWorksheetIterator() as $sheet) {
            // for all cells
            foreach ($sheet->getCoordinates(false) as $coordinate) {
                $cell = $sheet->getCell($coordinate);
                $cell->setXfIndex($map[$cell->getXfIndex()]);
            }

            // for all row dimensions
            foreach ($sheet->getRowDimensions() as $rowDimension) {
                if ($rowDimension->getXfIndex() !== null) {
                    $rowDimension->setXfIndex($map[$rowDimension->getXfIndex()]);
                }
            }

            // for all column dimensions
            foreach ($sheet->getColumnDimensions() as $columnDimension) {
                $columnDimension->setXfIndex($map[$columnDimension->getXfIndex()]);
            }

            // also do garbage collection for all the sheets
            $sheet->garbageCollect();
        }
    }

    /**
     * Return the unique ID value assigned to this spreadsheet workbook.
     *
     * @return string
     */
    public function getID()
    {
        return $this->uniqueID;
    }
}
