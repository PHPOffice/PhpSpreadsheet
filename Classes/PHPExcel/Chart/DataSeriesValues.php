<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2012 PHPExcel
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
 * @package		PHPExcel_Chart
 * @copyright	Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license		http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version		##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Chart_DataSeriesValues
 *
 * @category	PHPExcel
 * @package		PHPExcel_Chart
 * @copyright	Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Chart_DataSeriesValues
{

	/**
	 * Series Data Type
	 *
	 * @var	string
	 */
	private $_dataType = null;

	/**
	 * Series Data Source
	 *
	 * @var	string
	 */
	private $_dataSource = null;

	/**
	 * Format Code
	 *
	 * @var	string
	 */
	private $_formatCode = null;

	/**
	 * Series Point Marker
	 *
	 * @var	string
	 */
	private $_marker = null;

	/**
	 * Point Count (The number of datapoints in the dataseries)
	 *
	 * @var	integer
	 */
	private $_pointCount = 0;

	/**
	 * Data Values
	 *
	 * @var	array of mixed
	 */
	private $_dataValues = array();

	/**
	 * Create a new PHPExcel_Chart_DataSeriesValues object
	 */
	public function __construct($dataType = null, $dataSource = null, $formatCode = null, $pointCount = 0, $dataValues = array(), $marker = null)
	{
		$this->_dataType = $dataType;
		$this->_dataSource = $dataSource;
		$this->_formatCode = $formatCode;
		$this->_pointCount = $pointCount;
		$this->_dataValues = $dataValues;
		$this->_marker = $marker;
	}

	/**
	 * Get Series Data Type
	 *
	 * @return	string
	 */
	public function getDataType() {
		return $this->_dataType;
	}

	/**
	 * Set Series Data Type
	 *
	 * @param	string	$dataType
	 * @return	PHPExcel_Chart_DataSeriesValues
	 */
	public function setDataType($dataType = 'Number') {
		$this->_dataType = $dataType;

		return $this;
	}

	/**
	 * Get Series Data Source (formula)
	 *
	 * @return	string
	 */
	public function getDataSource() {
		return $this->_dataSource;
	}

	/**
	 * Set Series Data Source (formula)
	 *
	 * @param	string	$dataSource
	 * @return	PHPExcel_Chart_DataSeriesValues
	 */
	public function setDataSource($dataSource = null, $refreshDataValues = true) {
		$this->_dataSource = $dataSource;

		if ($refreshDataValues) {
			//	TO DO
		}

		return $this;
	}

	/**
	 * Get Point Marker
	 *
	 * @return string
	 */
	public function getPointMarker() {
		return $this->_marker;
	}

	/**
	 * Set Point Marker
	 *
	 * @param	string	$marker
	 * @return	PHPExcel_Chart_DataSeriesValues
	 */
	public function setPointMarker($marker = null) {
		$this->_marker = $marker;

		return $this;
	}

	/**
	 * Get Series Format Code
	 *
	 * @return	string
	 */
	public function getFormatCode() {
		return $this->_formatCode;
	}

	/**
	 * Set Series Format Code
	 *
	 * @param	string	$formatCode
	 * @return	PHPExcel_Chart_DataSeriesValues
	 */
	public function setFormatCode($formatCode = null) {
		$this->_formatCode = $formatCode;

		return $this;
	}

	/**
	 * Get Series Point Count
	 *
	 * @return	integer
	 */
	public function getPointCount() {
		return $this->_pointCount;
	}

	/**
	 * Identify if the Data Series is a multi-level or a simple series
	 *
	 * @return	boolean
	 */
	public function isMultiLevelSeries() {
		if (count($this->_dataValues) > 0) {
			return is_array($this->_dataValues[0]);
		}
		return null;
	}

	/**
	 * Return the level count of a multi-level Data Series
	 *
	 * @return	boolean
	 */
	public function multiLevelCount() {
		$levelCount = 0;
		foreach($this->_dataValues as $dataValueSet) {
			$levelCount = max($levelCount,count($dataValueSet));
		}
		return $levelCount;
	}

	/**
	 * Get Series Data Values
	 *
	 * @return	array of mixed
	 */
	public function getDataValues() {
		return $this->_dataValues;
	}

	/**
	 * Get the first Series Data value
	 *
	 * @return	mixed
	 */
	public function getDataValue() {
		$count = count($this->_dataValues);
		if ($count == 0) {
			return null;
		} elseif ($count == 1) {
			return $this->_dataValues[0];
		}
		return $this->_dataValues;
	}

	/**
	 * Set Series Data Values
	 *
	 * @param	array	$dataValues
	 * @param	boolean	$refreshDataSource
	 *					TRUE - refresh the value of _dataSource based on the values of $dataValues
	 *					FALSE - don't change the value of _dataSource
	 * @return	PHPExcel_Chart_DataSeriesValues
	 */
	public function setDataValues($dataValues = array(), $refreshDataSource = true) {
		$this->_dataValues = PHPExcel_Calculation_Functions::flattenArray($dataValues);
		$this->_pointCount = count($dataValues);

		if ($refreshDataSource) {
			//	TO DO
		}

		return $this;
	}

	public function refresh(PHPExcel_Worksheet $worksheet) {
        if ($this->_dataSource !== NULL) {
        	$calcEngine = PHPExcel_Calculation::getInstance();
			$this->_dataValues = PHPExcel_Calculation::_unwrapResult(
			    $calcEngine->_calculateFormulaValue(
			        $this->_dataSource
			    )
			);
		}
	}

}
