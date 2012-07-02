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
 * @package	PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license	http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version	##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Style_Alignment
 *
 * @category   PHPExcel
 * @package	PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Style_Alignment implements PHPExcel_IComparable
{
	/* Horizontal alignment styles */
	const HORIZONTAL_GENERAL				= 'general';
	const HORIZONTAL_LEFT					= 'left';
	const HORIZONTAL_RIGHT					= 'right';
	const HORIZONTAL_CENTER					= 'center';
	const HORIZONTAL_CENTER_CONTINUOUS		= 'centerContinuous';
	const HORIZONTAL_JUSTIFY				= 'justify';

	/* Vertical alignment styles */
	const VERTICAL_BOTTOM					= 'bottom';
	const VERTICAL_TOP						= 'top';
	const VERTICAL_CENTER					= 'center';
	const VERTICAL_JUSTIFY					= 'justify';

	/**
	 * Horizontal
	 *
	 * @var string
	 */
	private $_horizontal	= PHPExcel_Style_Alignment::HORIZONTAL_GENERAL;

	/**
	 * Vertical
	 *
	 * @var string
	 */
	private $_vertical		= PHPExcel_Style_Alignment::VERTICAL_BOTTOM;

	/**
	 * Text rotation
	 *
	 * @var int
	 */
	private $_textRotation	= 0;

	/**
	 * Wrap text
	 *
	 * @var boolean
	 */
	private $_wrapText		= false;

	/**
	 * Shrink to fit
	 *
	 * @var boolean
	 */
	private $_shrinkToFit	= false;

	/**
	 * Indent - only possible with horizontal alignment left and right
	 *
	 * @var int
	 */
	private $_indent		= 0;

	/**
	 * Parent Borders
	 *
	 * @var _parentPropertyName string
	 */
	private $_parentPropertyName;

	/**
	 * Supervisor?
	 *
	 * @var boolean
	 */
	private $_isSupervisor;

	/**
	 * Parent. Only used for supervisor
	 *
	 * @var PHPExcel_Style
	 */
	private $_parent;

	/**
	 * Create a new PHPExcel_Style_Alignment
	 *
	 * @param	boolean	$isSupervisor	Flag indicating if this is a supervisor or not
	 *									Leave this value at default unless you understand exactly what
	 *										its ramifications are
	 * @param	boolean	$isConditional	Flag indicating if this is a conditional style or not
	 *									Leave this value at default unless you understand exactly what
	 *										its ramifications are
	 */
	public function __construct($isSupervisor = false, $isConditional = false)
	{
		// Supervisor?
		$this->_isSupervisor = $isSupervisor;

		if ($isConditional) {
			$this->_horizontal		= NULL;
			$this->_vertical		= NULL;
			$this->_textRotation	= NULL;
		}
	}

	/**
	 * Bind parent. Only used for supervisor
	 *
	 * @param PHPExcel $parent
	 * @return PHPExcel_Style_Alignment
	 */
	public function bindParent($parent)
	{
		$this->_parent = $parent;
		return $this;
	}

	/**
	 * Is this a supervisor or a real style component?
	 *
	 * @return boolean
	 */
	public function getIsSupervisor()
	{
		return $this->_isSupervisor;
	}

	/**
	 * Get the shared style component for the currently active cell in currently active sheet.
	 * Only used for style supervisor
	 *
	 * @return PHPExcel_Style_Alignment
	 */
	public function getSharedComponent()
	{
		return $this->_parent->getSharedComponent()->getAlignment();
	}

	/**
	 * Get the currently active sheet. Only used for supervisor
	 *
	 * @return PHPExcel_Worksheet
	 */
	public function getActiveSheet()
	{
		return $this->_parent->getActiveSheet();
	}

	/**
	 * Get the currently active cell coordinate in currently active sheet.
	 * Only used for supervisor
	 *
	 * @return string E.g. 'A1'
	 */
	public function getSelectedCells()
	{
		return $this->getActiveSheet()->getSelectedCells();
	}

	/**
	 * Get the currently active cell coordinate in currently active sheet.
	 * Only used for supervisor
	 *
	 * @return string E.g. 'A1'
	 */
	public function getActiveCell()
	{
		return $this->getActiveSheet()->getActiveCell();
	}

	/**
	 * Build style array from subcomponents
	 *
	 * @param array $array
	 * @return array
	 */
	public function getStyleArray($array)
	{
		return array('alignment' => $array);
	}

	/**
	 * Apply styles from array
	 *
	 * <code>
	 * $objPHPExcel->getActiveSheet()->getStyle('B2')->getAlignment()->applyFromArray(
	 *		array(
	 *			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
	 *			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
	 *			'rotation'   => 0,
	 *			'wrap'	   => true
	 *		)
	 * );
	 * </code>
	 *
	 * @param	array	$pStyles	Array containing style information
	 * @throws	Exception
	 * @return PHPExcel_Style_Alignment
	 */
	public function applyFromArray($pStyles = null) {
		if (is_array($pStyles)) {
			if ($this->_isSupervisor) {
				$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($pStyles));
			} else {
				if (array_key_exists('horizontal', $pStyles)) {
					$this->setHorizontal($pStyles['horizontal']);
				}
				if (array_key_exists('vertical', $pStyles)) {
					$this->setVertical($pStyles['vertical']);
				}
				if (array_key_exists('rotation', $pStyles)) {
					$this->setTextRotation($pStyles['rotation']);
				}
				if (array_key_exists('wrap', $pStyles)) {
					$this->setWrapText($pStyles['wrap']);
				}
				if (array_key_exists('shrinkToFit', $pStyles)) {
					$this->setShrinkToFit($pStyles['shrinkToFit']);
				}
				if (array_key_exists('indent', $pStyles)) {
					$this->setIndent($pStyles['indent']);
				}
			}
		} else {
			throw new Exception("Invalid style array passed.");
		}
		return $this;
	}

	/**
	 * Get Horizontal
	 *
	 * @return string
	 */
	public function getHorizontal() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getHorizontal();
		}
		return $this->_horizontal;
	}

	/**
	 * Set Horizontal
	 *
	 * @param string $pValue
	 * @return PHPExcel_Style_Alignment
	 */
	public function setHorizontal($pValue = PHPExcel_Style_Alignment::HORIZONTAL_GENERAL) {
		if ($pValue == '') {
			$pValue = PHPExcel_Style_Alignment::HORIZONTAL_GENERAL;
		}

		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('horizontal' => $pValue));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		}
		else {
			$this->_horizontal = $pValue;
		}
		return $this;
	}

	/**
	 * Get Vertical
	 *
	 * @return string
	 */
	public function getVertical() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getVertical();
		}
		return $this->_vertical;
	}

	/**
	 * Set Vertical
	 *
	 * @param string $pValue
	 * @return PHPExcel_Style_Alignment
	 */
	public function setVertical($pValue = PHPExcel_Style_Alignment::VERTICAL_BOTTOM) {
		if ($pValue == '') {
			$pValue = PHPExcel_Style_Alignment::VERTICAL_BOTTOM;
		}

		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('vertical' => $pValue));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		} else {
			$this->_vertical = $pValue;
		}
		return $this;
	}

	/**
	 * Get TextRotation
	 *
	 * @return int
	 */
	public function getTextRotation() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getTextRotation();
		}
		return $this->_textRotation;
	}

	/**
	 * Set TextRotation
	 *
	 * @param int $pValue
	 * @throws Exception
	 * @return PHPExcel_Style_Alignment
	 */
	public function setTextRotation($pValue = 0) {
		// Excel2007 value 255 => PHPExcel value -165
		if ($pValue == 255) {
			$pValue = -165;
		}

		// Set rotation
		if ( ($pValue >= -90 && $pValue <= 90) || $pValue == -165 ) {
			if ($this->_isSupervisor) {
				$styleArray = $this->getStyleArray(array('rotation' => $pValue));
				$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
			} else {
				$this->_textRotation = $pValue;
			}
		} else {
			throw new Exception("Text rotation should be a value between -90 and 90.");
		}

		return $this;
	}

	/**
	 * Get Wrap Text
	 *
	 * @return boolean
	 */
	public function getWrapText() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getWrapText();
		}
		return $this->_wrapText;
	}

	/**
	 * Set Wrap Text
	 *
	 * @param boolean $pValue
	 * @return PHPExcel_Style_Alignment
	 */
	public function setWrapText($pValue = false) {
		if ($pValue == '') {
			$pValue = false;
		}
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('wrap' => $pValue));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		} else {
			$this->_wrapText = $pValue;
		}
		return $this;
	}

	/**
	 * Get Shrink to fit
	 *
	 * @return boolean
	 */
	public function getShrinkToFit() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getShrinkToFit();
		}
		return $this->_shrinkToFit;
	}

	/**
	 * Set Shrink to fit
	 *
	 * @param boolean $pValue
	 * @return PHPExcel_Style_Alignment
	 */
	public function setShrinkToFit($pValue = false) {
		if ($pValue == '') {
			$pValue = false;
		}
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('shrinkToFit' => $pValue));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		} else {
			$this->_shrinkToFit = $pValue;
		}
		return $this;
	}

	/**
	 * Get indent
	 *
	 * @return int
	 */
	public function getIndent() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getIndent();
		}
		return $this->_indent;
	}

	/**
	 * Set indent
	 *
	 * @param int $pValue
	 * @return PHPExcel_Style_Alignment
	 */
	public function setIndent($pValue = 0) {
		if ($pValue > 0) {
			if ($this->getHorizontal() != self::HORIZONTAL_GENERAL && $this->getHorizontal() != self::HORIZONTAL_LEFT && $this->getHorizontal() != self::HORIZONTAL_RIGHT) {
				$pValue = 0; // indent not supported
			}
		}
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('indent' => $pValue));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		} else {
			$this->_indent = $pValue;
		}
		return $this;
	}

	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */
	public function getHashCode() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getHashCode();
		}
		return md5(
			  $this->_horizontal
			. $this->_vertical
			. $this->_textRotation
			. ($this->_wrapText ? 't' : 'f')
			. ($this->_shrinkToFit ? 't' : 'f')
			. $this->_indent
			. __CLASS__
		);
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
}
