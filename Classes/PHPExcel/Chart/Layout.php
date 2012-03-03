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
	private $_layoutTarget = null;

	/**
	 * X Mode
	 *
	 * @var string
	 */
	private $_xMode		= null;

	/**
	 * Y Mode
	 *
	 * @var string
	 */
	private $_yMode		= null;

	/**
	 * X-Position
	 *
	 * @var float
	 */
	private $_xPos		= null;

	/**
	 * Y-Position
	 *
	 * @var float
	 */
	private $_yPos		= null;

	/**
	 * width
	 *
	 * @var float
	 */
	private $_width		= null;

	/**
	 * height
	 *
	 * @var float
	 */
	private $_height	= null;


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

}
