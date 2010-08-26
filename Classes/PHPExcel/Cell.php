<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2010 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category	PHPExcel
 * @package		PHPExcel_Cell
 * @copyright	Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license		http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version		##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Cell
 *
 * @category   PHPExcel
 * @package	PHPExcel_Cell
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Cell
{
	/**
	 * Value binder to use
	 *
	 * @var PHPExcel_Cell_IValueBinder
	 */
	private static $_valueBinder = null;

	/**
	 * Column of the cell
	 *
	 * @var string
	 */
	private $_column;

	/**
	 * Row of the cell
	 *
	 * @var int
	 */
	private $_row;

	/**
	 * Value of the cell
	 *
	 * @var mixed
	 */
	private $_value;

	/**
	 * Calculated value of the cell (used for caching)
	 *
	 * @var mixed
	 */
	private $_calculatedValue = null;

	/**
	 * Type of the cell data
	 *
	 * @var string
	 */
	private $_dataType;

	/**
	 * Parent worksheet
	 *
	 * @var PHPExcel_Worksheet
	 */
	private $_parent;

	/**
	 * Index to cellXf
	 *
	 * @var int
	 */
	private $_xfIndex;

	/**
	 * Attributes of the formula
	 *
	 *
	 */
	private $_formulaAttributes;


	/**
	 * Send notification to the cache controller
	 * @return void
	 **/
	public function notifyCacheController() {
		$this->_parent->getCellCacheController()->updateCacheData($this);
		return $this;
	}

	public function detach() {
		$this->_parent = null;
	}

	public function attach($parent) {
		$this->_parent = $parent;
	}


	/**
	 * Create a new Cell
	 *
	 * @param	string				$pColumn
	 * @param	int				$pRow
	 * @param	mixed				$pValue
	 * @param	string				$pDataType
	 * @param	PHPExcel_Worksheet	$pSheet
	 * @throws	Exception
	 */
	public function __construct($pColumn = 'A', $pRow = 1, $pValue = null, $pDataType = null, PHPExcel_Worksheet $pSheet = null)
	{
		// Initialise cell coordinate
		$this->_column = strtoupper($pColumn);
		$this->_row = $pRow;

		// Initialise cell value
		$this->_value = $pValue;

		// Set worksheet
		$this->_parent = $pSheet;

		// Set datatype?
		if (!is_null($pDataType)) {
			$this->_dataType = $pDataType;
		} else {
			if (!self::getValueBinder()->bindValue($this, $pValue)) {
				throw new Exception("Value could not be bound to cell.");
			}
		}

		// set default index to cellXf
		$this->_xfIndex = 0;
	}

	/**
	 * Get cell coordinate column
	 *
	 * @return string
	 */
	public function getColumn()
	{
		return $this->_column;
	}

	/**
	 * Get cell coordinate row
	 *
	 * @return int
	 */
	public function getRow()
	{
		return $this->_row;
	}

	/**
	 * Get cell coordinate
	 *
	 * @return string
	 */
	public function getCoordinate()
	{
		return $this->_column . $this->_row;
	}

	/**
	 * Get cell value
	 *
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->_value;
	}

	/**
	 * Set cell value
	 *
	 * This clears the cell formula.
	 *
	 * @param mixed	$pValue					Value
	 * @return PHPExcel_Cell
	 */
	public function setValue($pValue = null)
	{
		if (!self::getValueBinder()->bindValue($this, $pValue)) {
			throw new Exception("Value could not be bound to cell.");
		}
		return $this;
	}

	/**
	 * Set cell value (with explicit data type given)
	 *
	 * @param mixed	$pValue			Value
	 * @param string	$pDataType		Explicit data type
	 * @return PHPExcel_Cell
	 * @throws Exception
	 */
	public function setValueExplicit($pValue = null, $pDataType = PHPExcel_Cell_DataType::TYPE_STRING)
	{
		// set the value according to data type
		switch ($pDataType) {
			case PHPExcel_Cell_DataType::TYPE_STRING:
			case PHPExcel_Cell_DataType::TYPE_NULL:
			case PHPExcel_Cell_DataType::TYPE_INLINE:
				$this->_value = PHPExcel_Cell_DataType::checkString($pValue);
				break;

			case PHPExcel_Cell_DataType::TYPE_NUMERIC:
				$this->_value = (float)$pValue;
				break;

			case PHPExcel_Cell_DataType::TYPE_FORMULA:
				$this->_value = (string)$pValue;
				break;

			case PHPExcel_Cell_DataType::TYPE_BOOL:
				$this->_value = (bool)$pValue;
				break;

			case PHPExcel_Cell_DataType::TYPE_ERROR:
				$this->_value = PHPExcel_Cell_DataType::checkErrorCode($pValue);
				break;

			default:
				throw new Exception('Invalid datatype: ' . $pDataType);
				break;
		}

		// set the datatype
		$this->_dataType = $pDataType;

		return $this->notifyCacheController();
	}

	/**
	 * Get calculated cell value
	 *
	 * @return mixed
	 */
	public function getCalculatedValue($resetLog=true)
	{
//		echo 'Cell '.$this->getCoordinate().' value is a '.$this->_dataType.' with a value of '.$this->getValue().'<br />';
		if ($this->_dataType == PHPExcel_Cell_DataType::TYPE_FORMULA) {
			try {
//				echo 'Cell value for '.$this->getCoordinate().' is a formula: Calculating value<br />';
				$result = PHPExcel_Calculation::getInstance()->calculateCellValue($this,$resetLog);
//				echo $this->getCoordinate().' calculation result is '.$result.'<br />';
			} catch ( Exception $ex ) {
//				echo 'Calculation Exception: '.$ex->getMessage().'<br />';
				$result = '#N/A';
				throw(new Exception($this->getParent()->getTitle().'!'.$this->getCoordinate().' -> '.$ex->getMessage()));
			}

			if ($result === '#Not Yet Implemented') {
//				echo 'Returning fallback value of '.$this->_calculatedValue.' for cell '.$this->getCoordinate().'<br />';
				return $this->_calculatedValue; // Fallback if calculation engine does not support the formula.
			}
//			echo 'Returning calculated value of '.$result.' for cell '.$this->getCoordinate().'<br />';
			return $result;
		}

		if (is_null($this->_value)) {
//			echo 'Cell '.$this->getCoordinate().' has no value, formula or otherwise<br />';
			return null;
		}
//		echo 'Cell value for '.$this->getCoordinate().' is not a formula: Returning data value of '.$this->_value.'<br />';
		return $this->_value;
	}

	/**
	 * Set calculated value (used for caching)
	 *
	 * @param mixed $pValue	Value
	 * @return PHPExcel_Cell
	 */
	public function setCalculatedValue($pValue = null)
	{
		if (!is_null($pValue)) {
			$this->_calculatedValue = $pValue;
		}

		return $this->notifyCacheController();
	}

	/**
	 * Get old calculated value (cached)
	 *
	 * @return mixed
	 */
	public function getOldCalculatedValue()
	{
		return $this->_calculatedValue;
	}

	/**
	 * Get cell data type
	 *
	 * @return string
	 */
	public function getDataType()
	{
		return $this->_dataType;
	}

	/**
	 * Set cell data type
	 *
	 * @param string $pDataType
	 * @return PHPExcel_Cell
	 */
	public function setDataType($pDataType = PHPExcel_Cell_DataType::TYPE_STRING)
	{
		$this->_dataType = $pDataType;

		return $this->notifyCacheController();
	}

	/**
	 * Has Data validation?
	 *
	 * @return boolean
	 */
	public function hasDataValidation()
	{
		if (!isset($this->_parent)) {
			throw new Exception('Cannot check for data validation when cell is not bound to a worksheet');
		}

		return $this->_parent->dataValidationExists($this->getCoordinate());
	}

	/**
	 * Get Data validation
	 *
	 * @return PHPExcel_Cell_DataValidation
	 */
	public function getDataValidation()
	{
		if (!isset($this->_parent)) {
			throw new Exception('Cannot get data validation for cell that is not bound to a worksheet');
		}

		return $this->_parent->getDataValidation($this->getCoordinate());
	}

	/**
	 * Set Data validation
	 *
	 * @param	PHPExcel_Cell_DataValidation	$pDataValidation
	 * @throws	Exception
	 * @return PHPExcel_Cell
	 */
	public function setDataValidation(PHPExcel_Cell_DataValidation $pDataValidation = null)
	{
		if (!isset($this->_parent)) {
			throw new Exception('Cannot set data validation for cell that is not bound to a worksheet');
		}

		$this->_parent->setDataValidation($this->getCoordinate(), $pDataValidation);

		return $this->notifyCacheController();
	}

	/**
	 * Has Hyperlink
	 *
	 * @return boolean
	 */
	public function hasHyperlink()
	{
		if (!isset($this->_parent)) {
			throw new Exception('Cannot check for hyperlink when cell is not bound to a worksheet');
		}

		return $this->_parent->hyperlinkExists($this->getCoordinate());
	}

	/**
	 * Get Hyperlink
	 *
	 * @throws Exception
	 * @return PHPExcel_Cell_Hyperlink
	 */
	public function getHyperlink()
	{
		if (!isset($this->_parent)) {
			throw new Exception('Cannot get hyperlink for cell that is not bound to a worksheet');
		}

		return $this->_parent->getHyperlink($this->getCoordinate());
	}

	/**
	 * Set Hyperlink
	 *
	 * @param	PHPExcel_Cell_Hyperlink	$pHyperlink
	 * @throws	Exception
	 * @return PHPExcel_Cell
	 */
	public function setHyperlink(PHPExcel_Cell_Hyperlink $pHyperlink = null)
	{
		if (!isset($this->_parent)) {
			throw new Exception('Cannot set hyperlink for cell that is not bound to a worksheet');
		}

		$this->_parent->setHyperlink($this->getCoordinate(), $pHyperlink);

		return $this->notifyCacheController();
	}

	/**
	 * Get parent
	 *
	 * @return PHPExcel_Worksheet
	 */
	public function getParent() {
		return $this->_parent;
	}

	/**
	 * Re-bind parent
	 *
	 * @param PHPExcel_Worksheet $parent
	 * @return PHPExcel_Cell
	 */
	public function rebindParent(PHPExcel_Worksheet $parent) {
		$this->_parent = $parent;

		return $this->notifyCacheController();
	}

	/**
	 * Is cell in a specific range?
	 *
	 * @param	string	$pRange		Cell range (e.g. A1:A1)
	 * @return	boolean
	 */
	public function isInRange($pRange = 'A1:A1')
	{
		list($rangeStart,$rangeEnd) = PHPExcel_Cell::rangeBoundaries($pRange);

		// Translate properties
		$myColumn	= PHPExcel_Cell::columnIndexFromString($this->getColumn()) - 1;
		$myRow		= $this->getRow();

		// Verify if cell is in range
		return (($rangeStart[0] <= $myColumn) && ($rangeEnd[0] >= $myColumn) &&
				($rangeStart[1] <= $myRow) && ($rangeEnd[1] >= $myRow)
			   );
	}

	/**
	 * Coordinate from string
	 *
	 * @param	string	$pCoordinateString
	 * @return	array	Array containing column and row (indexes 0 and 1)
	 * @throws	Exception
	 */
	public static function coordinateFromString($pCoordinateString = 'A1')
	{
		if (strpos($pCoordinateString,':') !== false) {
			throw new Exception('Cell coordinate string can not be a range of cells.');
		} else if ($pCoordinateString == '') {
			throw new Exception('Cell coordinate can not be zero-length string.');
		} else if (preg_match("/([$]?[A-Z]+)([$]?\d+)/", $pCoordinateString, $matches)) {
			list(, $column, $row) = $matches;
			return array($column, $row);
		} else {
			throw new Exception('Invalid cell coordinate.');
		}
	}

	/**
	 * Make string coordinate absolute
	 *
	 * @param	string	$pCoordinateString
	 * @return	string	Absolute coordinate
	 * @throws	Exception
	 */
	public static function absoluteCoordinate($pCoordinateString = 'A1')
	{
		if (strpos($pCoordinateString,':') === false && strpos($pCoordinateString,',') === false) {
			// Create absolute coordinate
			list($column, $row) = PHPExcel_Cell::coordinateFromString($pCoordinateString);
			return '$' . $column . '$' . $row;
		} else {
			throw new Exception("Coordinate string should not be a cell range.");
		}
	}

	/**
	 * Split range into coordinate strings
	 *
	 * @param	string	$pRange
	 * @return	array	Array containg one or more arrays containing one or two coordinate strings
	 */
	public static function splitRange($pRange = 'A1:A1')
	{
		$exploded = explode(',', $pRange);
		for ($i = 0; $i < count($exploded); ++$i) {
			$exploded[$i] = explode(':', $exploded[$i]);
		}
		return $exploded;
	}

	/**
	 * Build range from coordinate strings
	 *
	 * @param	array	$pRange	Array containg one or more arrays containing one or two coordinate strings
	 * @return  string	String representation of $pRange
	 * @throws	Exception
	 */
	public static function buildRange($pRange)
	{
		// Verify range
		if (!is_array($pRange) || count($pRange) == 0 || !is_array($pRange[0])) {
			throw new Exception('Range does not contain any information.');
		}

		// Build range
		$imploded = array();
		for ($i = 0; $i < count($pRange); ++$i) {
			$pRange[$i] = implode(':', $pRange[$i]);
		}
		$imploded = implode(',', $pRange);

		return $imploded;
	}

	/**
	 * Calculate range boundaries
	 *
	 * @param	string	$pRange		Cell range (e.g. A1:A1)
	 * @return	array	Range coordinates (Start Cell, End Cell) where Start Cell and End Cell are arrays (Column Number, Row Number)
	 */
	public static function rangeBoundaries($pRange = 'A1:A1')
	{
		// Uppercase coordinate
		$pRange = strtoupper($pRange);

		// Extract range
		if (strpos($pRange, ':') === false) {
			$rangeA = $rangeB = $pRange;
		} else {
			list($rangeA, $rangeB) = explode(':', $pRange);
		}

		// Calculate range outer borders
		$rangeStart = PHPExcel_Cell::coordinateFromString($rangeA);
		$rangeEnd	= PHPExcel_Cell::coordinateFromString($rangeB);

		// Translate column into index
		$rangeStart[0]	= PHPExcel_Cell::columnIndexFromString($rangeStart[0]);
		$rangeEnd[0]	= PHPExcel_Cell::columnIndexFromString($rangeEnd[0]);

		return array($rangeStart, $rangeEnd);
	}

	/**
	 * Calculate range dimension
	 *
	 * @param	string	$pRange		Cell range (e.g. A1:A1)
	 * @return	array	Range dimension (width, height)
	 */
	public static function rangeDimension($pRange = 'A1:A1')
	{
		// Calculate range outer borders
		list($rangeStart,$rangeEnd) = PHPExcel_Cell::rangeBoundaries($pRange);

		return array( ($rangeEnd[0] - $rangeStart[0] + 1), ($rangeEnd[1] - $rangeStart[1] + 1) );
	}

	/**
	 * Calculate range boundaries
	 *
	 * @param	string	$pRange		Cell range (e.g. A1:A1)
	 * @return	array	Range boundaries (staring Column, starting Row, Final Column, Final Row)
	 */
	public static function getRangeBoundaries($pRange = 'A1:A1')
	{
		// Uppercase coordinate
		$pRange = strtoupper($pRange);

		// Extract range
		if (strpos($pRange, ':') === false) {
			$rangeA = $pRange;
			$rangeB = $pRange;
		} else {
			list($rangeA, $rangeB) = explode(':', $pRange);
		}

		return array( self::coordinateFromString($rangeA), self::coordinateFromString($rangeB));
	}

	/**
	 * Column index from string
	 *
	 * @param	string $pString
	 * @return	int Column index (base 1 !!!)
	 * @throws	Exception
	 */
	public static function columnIndexFromString($pString = 'A')
	{
		static $lookup = array(
			'A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13,
			'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26
		);

		if (isset($lookup[$pString]))
			return $lookup[$pString];

		// Convert to uppercase
		$pString = strtoupper($pString);

		$strLen = strlen($pString);
		// Convert column to integer
		if ($strLen == 1) {
			return (ord($pString{0}) - 64);
		} elseif ($strLen == 2) {
			return $result = ((1 + (ord($pString{0}) - 65)) * 26) + (ord($pString{1}) - 64);
		} elseif ($strLen == 3) {
			return ((1 + (ord($pString{0}) - 65)) * 676) + ((1 + (ord($pString{1}) - 65)) * 26) + (ord($pString{2}) - 64);
		} else {
			throw new Exception("Column string index can not be " . ($strLen != 0 ? "longer than 3 characters" : "empty") . ".");
		}
	}

	/**
	 * String from columnindex
	 *
	 * @param int $pColumnIndex Column index (base 0 !!!)
	 * @return string
	 */
	public static function stringFromColumnIndex($pColumnIndex = 0)
	{
		// Determine column string
		if ($pColumnIndex < 26) {
			return chr(65 + $pColumnIndex);
		}
		return PHPExcel_Cell::stringFromColumnIndex((int)($pColumnIndex / 26) -1).chr(65 + $pColumnIndex%26) ;
	}

	/**
	 * Extract all cell references in range
	 *
	 * @param	string	$pRange		Range (e.g. A1 or A1:A10 or A1:A10 A100:A1000)
	 * @return	array	Array containing single cell references
	 */
	public static function extractAllCellReferencesInRange($pRange = 'A1') {
		// Returnvalue
		$returnValue = array();

		// Explode spaces
		$aExplodeSpaces = explode(' ', str_replace('$', '', strtoupper($pRange)));
		foreach ($aExplodeSpaces as $explodedSpaces) {
			// Single cell?
			if (strpos($explodedSpaces,':') === false && strpos($explodedSpaces,',') === false) {
				$col = 'A';
				$row = 1;
				list($col, $row) = PHPExcel_Cell::coordinateFromString($explodedSpaces);

				if (strlen($col) <= 2) {
					$returnValue[] = $explodedSpaces;
				}

				continue;
			}

			// Range...
			$range = PHPExcel_Cell::splitRange($explodedSpaces);
			for ($i = 0; $i < count($range); ++$i) {
				// Single cell?
				if (count($range[$i]) == 1) {
					$col = 'A';
					$row = 1;
					list($col, $row) = PHPExcel_Cell::coordinateFromString($range[$i]);

					if (strlen($col) <= 2) {
						$returnValue[] = $explodedSpaces;
					}
				}

				// Range...
				$rangeStart		= $rangeEnd		= '';
				$startingCol	= $startingRow	= $endingCol	= $endingRow	= 0;

				list($rangeStart, $rangeEnd)		= $range[$i];
				list($startingCol, $startingRow)	= PHPExcel_Cell::coordinateFromString($rangeStart);
				list($endingCol, $endingRow)		= PHPExcel_Cell::coordinateFromString($rangeEnd);

				// Conversions...
				$startingCol	= PHPExcel_Cell::columnIndexFromString($startingCol);
				$endingCol		= PHPExcel_Cell::columnIndexFromString($endingCol);

				// Current data
				$currentCol	= --$startingCol;
				$currentRow	= $startingRow;

				// Loop cells
				while ($currentCol < $endingCol) {
					$loopColumn = PHPExcel_Cell::stringFromColumnIndex($currentCol);
					while ($currentRow <= $endingRow) {
						$returnValue[] = $loopColumn.$currentRow;
						++$currentRow;
					}
					++$currentCol;
					$currentRow = $startingRow;
				}
			}
		}

		// Return value
		return $returnValue;
	}

	/**
	 * Compare 2 cells
	 *
	 * @param	PHPExcel_Cell	$a	Cell a
	 * @param	PHPExcel_Cell	$a	Cell b
	 * @return	int		Result of comparison (always -1 or 1, never zero!)
	 */
	public static function compareCells(PHPExcel_Cell $a, PHPExcel_Cell $b)
	{
		if ($a->_row < $b->_row) {
			return -1;
		} elseif ($a->_row > $b->_row) {
			return 1;
		} elseif (PHPExcel_Cell::columnIndexFromString($a->_column) < PHPExcel_Cell::columnIndexFromString($b->_column)) {
			return -1;
		} else {
			return 1;
		}
	}

	/**
	 * Get value binder to use
	 *
	 * @return PHPExcel_Cell_IValueBinder
	 */
	public static function getValueBinder() {
		if (is_null(self::$_valueBinder)) {
			self::$_valueBinder = new PHPExcel_Cell_DefaultValueBinder();
		}

		return self::$_valueBinder;
	}

	/**
	 * Set value binder to use
	 *
	 * @param PHPExcel_Cell_IValueBinder $binder
	 * @throws Exception
	 */
	public static function setValueBinder(PHPExcel_Cell_IValueBinder $binder = null) {
		if (is_null($binder)) {
			throw new Exception("A PHPExcel_Cell_IValueBinder is required for PHPExcel to function correctly.");
		}

		self::$_valueBinder = $binder;
	}

	/**
	 * Implement PHP __clone to create a deep clone, not just a shallow copy.
	 */
	public function __clone() {
		$vars = get_object_vars($this);
		foreach ($vars as $key => $value) {
			if ((is_object($value)) && ($key != '_parent')) {
				$this->$key = clone $value;
			} else {
				$this->$key = $value;
			}
		}
	}

	/**
	 * Get index to cellXf
	 *
	 * @return int
	 */
	public function getXfIndex()
	{
		return $this->_xfIndex;
	}

	/**
	 * Set index to cellXf
	 *
	 * @param int $pValue
	 * @return PHPExcel_Cell
	 */
	public function setXfIndex($pValue = 0)
	{
		$this->_xfIndex = $pValue;

		return $this->notifyCacheController();
	}


	public function setFormulaAttributes($pAttributes)
	{
		$this->_formulaAttributes = $pAttributes;
		return $this;
	}

	public function getFormulaAttributes()
	{
		return $this->_formulaAttributes;
	}

}

