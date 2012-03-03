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
 * @category   PHPExcel
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Worksheet_SheetView
 *
 * @category   PHPExcel
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Worksheet_SheetView
{
	/**
	 * ZoomScale
	 *
	 * Valid values range from 10 to 400.
	 *
	 * @var int
	 */
	private $_zoomScale			= 100;

	/**
	 * ZoomScaleNormal
	 *
	 * Valid values range from 10 to 400.
	 *
	 * @var int
	 */
	private $_zoomScaleNormal	= 100;

    /**
     * Create a new PHPExcel_Worksheet_SheetView
     */
    public function __construct()
    {
    }

	/**
	 * Get ZoomScale
	 *
	 * @return int
	 */
	public function getZoomScale() {
		return $this->_zoomScale;
	}

	/**
	 * Set ZoomScale
	 *
	 * Valid values range from 10 to 400.
	 *
	 * @param 	int 	$pValue
	 * @throws 	Exception
	 * @return PHPExcel_Worksheet_SheetView
	 */
	public function setZoomScale($pValue = 100) {
		// Microsoft Office Excel 2007 only allows setting a scale between 10 and 400 via the user interface,
		// but it is apparently still able to handle any scale >= 1
		if (($pValue >= 1) || is_null($pValue)) {
			$this->_zoomScale = $pValue;
		} else {
			throw new Exception("Scale must be greater than or equal to 1.");
		}
		return $this;
	}

	/**
	 * Get ZoomScaleNormal
	 *
	 * @return int
	 */
	public function getZoomScaleNormal() {
		return $this->_zoomScaleNormal;
	}

	/**
	 * Set ZoomScale
	 *
	 * Valid values range from 10 to 400.
	 *
	 * @param 	int 	$pValue
	 * @throws 	Exception
	 * @return PHPExcel_Worksheet_SheetView
	 */
	public function setZoomScaleNormal($pValue = 100) {
		if (($pValue >= 1) || is_null($pValue)) {
			$this->_zoomScaleNormal = $pValue;
		} else {
			throw new Exception("Scale must be greater than or equal to 1.");
		}
		return $this;
	}

	/**
	 * Implement PHP __clone to create a deep clone, not just a shallow copy.
	 */
	public function __clone() {
		$vars = get_object_vars($this);
		foreach ($vars as $key => $value) {
			if (is_object($value)) {
				$this->$key = clone $value;
			} else {
				$this->$key = $value;
			}
		}
	}
}
