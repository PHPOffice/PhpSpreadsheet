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
 * PHPExcel_Chart_Layout
 *
 * @category	PHPExcel
 * @package		PHPExcel_Chart
 * @copyright	Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Chart_Layout
{
	/**
	 * layoutTarget
	 *
	 * @var string
	 */
	private $_layoutTarget = NULL;

	/**
	 * X Mode
	 *
	 * @var string
	 */
	private $_xMode		= NULL;

	/**
	 * Y Mode
	 *
	 * @var string
	 */
	private $_yMode		= NULL;

	/**
	 * X-Position
	 *
	 * @var float
	 */
	private $_xPos		= NULL;

	/**
	 * Y-Position
	 *
	 * @var float
	 */
	private $_yPos		= NULL;

	/**
	 * width
	 *
	 * @var float
	 */
	private $_width		= NULL;

	/**
	 * height
	 *
	 * @var float
	 */
	private $_height	= NULL;

	/**
	 * show legend key
	 * Specifies that legend keys should be shown in data labels
	 *
	 * @var boolean
	 */
	private $_showLegendKey	= NULL;

	/**
	 * show value
	 * Specifies that the value should be shown in a data label.
	 *
	 * @var boolean
	 */
	private $_showVal	= NULL;

	/**
	 * show category name
	 * Specifies that the category name should be shown in the data label.
	 *
	 * @var boolean
	 */
	private $_showCatName	= NULL;

	/**
	 * show data series name
	 * Specifies that the series name should be shown in the data label.
	 *
	 * @var boolean
	 */
	private $_showSerName	= NULL;

	/**
	 * show percentage
	 * Specifies that the percentage should be shown in the data label.
	 *
	 * @var boolean
	 */
	private $_showPercent	= NULL;

	/**
	 * show bubble size
	 *
	 * @var boolean
	 */
	private $_showBubbleSize	= NULL;

	/**
	 * show leader lines
	 * Specifies that leader lines should be shown for the data label.
	 *
	 * @var boolean
	 */
	private $_showLeaderLines	= NULL;


	/**
	 * Create a new PHPExcel_Chart_Layout
	 */
	public function __construct($layout=array())
	{
		if (isset($layout['layoutTarget']))	{ $this->_layoutTarget	= $layout['layoutTarget'];	}
		if (isset($layout['xMode']))		{ $this->_xMode			= $layout['xMode'];			}
		if (isset($layout['yMode']))		{ $this->_yMode			= $layout['yMode'];			}
		if (isset($layout['x']))			{ $this->_xPos			= (float) $layout['x'];		}
		if (isset($layout['y']))			{ $this->_yPos			= (float) $layout['y'];		}
		if (isset($layout['w']))			{ $this->_width			= (float) $layout['w'];		}
		if (isset($layout['h']))			{ $this->_height		= (float) $layout['h'];		}
	}

	/**
	 * Get Layout Target
	 *
	 * @return string
	 */
	public function getLayoutTarget() {
		return $this->_layoutTarget;
	}

	/**
	 * Set Layout Target
	 *
	 * @param Layout Target $value
	 */
	public function setLayoutTarget($value) {
		$this->_layoutTarget = $value;
	}

	/**
	 * Get X-Mode
	 *
	 * @return string
	 */
	public function getXMode() {
		return $this->_xMode;
	}

	/**
	 * Set X-Mode
	 *
	 * @param X-Mode $value
	 */
	public function setXMode($value) {
		$this->_xMode = $value;
	}

	/**
	 * Get Y-Mode
	 *
	 * @return string
	 */
	public function getYMode() {
		return $this->_xMode;
	}

	/**
	 * Set Y-Mode
	 *
	 * @param Y-Mode $value
	 */
	public function setYMode($value) {
		$this->_xMode = $value;
	}

	/**
	 * Get X-Position
	 *
	 * @return number
	 */
	public function getXPosition() {
		return $this->_xPos;
	}

	/**
	 * Set X-Position
	 *
	 * @param X-Position $value
	 */
	public function setXPosition($value) {
		$this->_xPos = $value;
	}

	/**
	 * Get Y-Position
	 *
	 * @return number
	 */
	public function getYPosition() {
		return $this->_yPos;
	}

	/**
	 * Set Y-Position
	 *
	 * @param Y-Position $value
	 */
	public function setYPosition($value) {
		$this->_yPos = $value;
	}

	/**
	 * Get Width
	 *
	 * @return number
	 */
	public function getWidth() {
		return $this->_width;
	}

	/**
	 * Set Width
	 *
	 * @param Width $value
	 */
	public function setWidth($value) {
		$this->_width = $value;
	}

	/**
	 * Get Height
	 *
	 * @return number
	 */
	public function getHeight() {
		return $this->_height;
	}

	/**
	 * Set Height
	 *
	 * @param Height $value
	 */
	public function setHeight($value) {
		$this->_height = $value;
	}


	/**
	 * Get show legend key
	 *
	 * @return boolean
	 */
	public function getShowLegendKey() {
		return $this->_showLegendKey;
	}

	/**
	 * Set show legend key
	 * Specifies that legend keys should be shown in data labels.
	 *
	 * @param boolean $value		Show legend key
	 */
	public function setShowLegendKey($value) {
		$this->_showLegendKey = $value;
	}

	/**
	 * Get show value
	 *
	 * @return boolean
	 */
	public function getShowVal() {
		return $this->_showVal;
	}

	/**
	 * Set show val
	 * Specifies that the value should be shown in data labels.
	 *
	 * @param boolean $value		Show val
	 */
	public function setShowVal($value) {
		$this->_showVal = $value;
	}

	/**
	 * Get show category name
	 *
	 * @return boolean
	 */
	public function getShowCatName() {
		return $this->_showCatName;
	}

	/**
	 * Set show cat name
	 * Specifies that the category name should be shown in data labels.
	 *
	 * @param boolean $value		Show cat name
	 */
	public function setShowCatName($value) {
		$this->_showCatName = $value;
	}

	/**
	 * Get show data series name
	 *
	 * @return boolean
	 */
	public function getShowSerName() {
		return $this->_showSerName;
	}

	/**
	 * Set show ser name
	 * Specifies that the series name should be shown in data labels.
	 *
	 * @param boolean $value		Show ser name
	 */
	public function setShowSerName($value) {
		$this->_showSerName = $value;
	}

	/**
	 * Get show percentage
	 *
	 * @return boolean
	 */
	public function getShowPercent() {
		return $this->_showPercent;
	}

	/**
	 * Set show percentage
	 * Specifies that the percentage should be shown in data labels.
	 *
	 * @param boolean $value		Show percentage
	 */
	public function setShowPercent($value) {
		$this->_showPercent = $value;
	}

	/**
	 * Get show bubble size
	 *
	 * @return boolean
	 */
	public function getShowBubbleSize() {
		return $this->_showBubbleSize;
	}

	/**
	 * Set show bubble size
	 * Specifies that the bubble size should be shown in data labels.
	 *
	 * @param boolean $value		Show bubble size
	 */
	public function setShowBubbleSize($value) {
		$this->_showBubbleSize = $value;
	}

	/**
	 * Get show leader lines
	 *
	 * @return boolean
	 */
	public function getShowLeaderLines() {
		return $this->_showLeaderLines;
	}

	/**
	 * Set show leader lines
	 * Specifies that leader lines should be shown in data labels.
	 *
	 * @param boolean $value		Show leader lines
	 */
	public function setShowLeaderLines($value) {
		$this->_showLeaderLines = $value;
	}

}
