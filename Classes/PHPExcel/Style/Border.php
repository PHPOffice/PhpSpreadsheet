<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2011 PHPExcel
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
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license	http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version	##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Style_Border
 *
 * @category   PHPExcel
 * @package	PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Style_Border implements PHPExcel_IComparable
{
	/* Border style */
	const BORDER_NONE				= 'none';
	const BORDER_DASHDOT			= 'dashDot';
	const BORDER_DASHDOTDOT			= 'dashDotDot';
	const BORDER_DASHED				= 'dashed';
	const BORDER_DOTTED				= 'dotted';
	const BORDER_DOUBLE				= 'double';
	const BORDER_HAIR				= 'hair';
	const BORDER_MEDIUM				= 'medium';
	const BORDER_MEDIUMDASHDOT		= 'mediumDashDot';
	const BORDER_MEDIUMDASHDOTDOT	= 'mediumDashDotDot';
	const BORDER_MEDIUMDASHED		= 'mediumDashed';
	const BORDER_SLANTDASHDOT		= 'slantDashDot';
	const BORDER_THICK				= 'thick';
	const BORDER_THIN				= 'thin';

	/**
	 * Border style
	 *
	 * @var string
	 */
	private $_borderStyle	= PHPExcel_Style_Border::BORDER_NONE;

	/**
	 * Border color
	 *
	 * @var PHPExcel_Style_Color
	 */
	private $_color;

	/**
	 * Supervisor?
	 *
	 * @var boolean
	 */
	private $_isSupervisor;

	/**
	 * Parent. Only used for supervisor
	 *
	 * @var PHPExcel_Style_Borders
	 */
	private $_parent;

	/**
	 * Parent property name
	 *
	 * @var string
	 */
	private $_parentPropertyName;

	/**
	 * Create a new PHPExcel_Style_Border
	 */
	public function __construct($isSupervisor = false)
	{
		// Supervisor?
		$this->_isSupervisor = $isSupervisor;

		// Initialise values
		$this->_color			= new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_BLACK, $isSupervisor);

		// bind parent if we are a supervisor
		if ($isSupervisor) {
			$this->_color->bindParent($this, '_color');
		}
	}

	/**
	 * Bind parent. Only used for supervisor
	 *
	 * @param PHPExcel_Style_Borders $parent
	 * @param string $parentPropertyName
	 * @return PHPExcel_Style_Border
	 */
	public function bindParent($parent, $parentPropertyName)
	{
		$this->_parent = $parent;
		$this->_parentPropertyName = $parentPropertyName;
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
	 * @return PHPExcel_Style_Border
	 * @throws Exception
	 */
	public function getSharedComponent()
	{
		switch ($this->_parentPropertyName) {
			case '_allBorders':
			case '_horizontal':
			case '_inside':
			case '_outline':
			case '_vertical':
				throw new Exception('Cannot get shared component for a pseudo-border.');
				break;

			case '_bottom':
				return $this->_parent->getSharedComponent()->getBottom();
				break;

			case '_diagonal':
				return $this->_parent->getSharedComponent()->getDiagonal();
				break;

			case '_left':
				return $this->_parent->getSharedComponent()->getLeft();
				break;

			case '_right':
				return $this->_parent->getSharedComponent()->getRight();
				break;

			case '_top':
				return $this->_parent->getSharedComponent()->getTop();
				break;

		}
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
		switch ($this->_parentPropertyName) {
		case '_allBorders':
			$key = 'allborders';
			break;

		case '_bottom':
			$key = 'bottom';
			break;

		case '_diagonal':
			$key = 'diagonal';
			break;

		case '_horizontal':
			$key = 'horizontal';
			break;

		case '_inside':
			$key = 'inside';
			break;

		case '_left':
			$key = 'left';
			break;

		case '_outline':
			$key = 'outline';
			break;

		case '_right':
			$key = 'right';
			break;

		case '_top':
			$key = 'top';
			break;

		case '_vertical':
			$key = 'vertical';
			break;
		}
		return $this->_parent->getStyleArray(array($key => $array));
	}

	/**
	 * Apply styles from array
	 *
	 * <code>
	 * $objPHPExcel->getActiveSheet()->getStyle('B2')->getBorders()->getTop()->applyFromArray(
	 *		array(
	 *			'style' => PHPExcel_Style_Border::BORDER_DASHDOT,
	 *			'color' => array(
	 *				'rgb' => '808080'
	 *			)
	 *		)
	 * );
	 * </code>
	 *
	 * @param	array	$pStyles	Array containing style information
	 * @throws	Exception
	 * @return PHPExcel_Style_Border
	 */
	public function applyFromArray($pStyles = null) {
		if (is_array($pStyles)) {
			if ($this->_isSupervisor) {
				$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($pStyles));
			} else {
				if (array_key_exists('style', $pStyles)) {
					$this->setBorderStyle($pStyles['style']);
				}
				if (array_key_exists('color', $pStyles)) {
					$this->getColor()->applyFromArray($pStyles['color']);
				}
			}
		} else {
			throw new Exception("Invalid style array passed.");
		}
		return $this;
	}

	/**
	 * Get Border style
	 *
	 * @return string
	 */
	public function getBorderStyle() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getBorderStyle();
		}
		return $this->_borderStyle;
	}

	/**
	 * Set Border style
	 *
	 * @param string $pValue
	 * @return PHPExcel_Style_Border
	 */
	public function setBorderStyle($pValue = PHPExcel_Style_Border::BORDER_NONE) {

		if ($pValue == '') {
			$pValue = PHPExcel_Style_Border::BORDER_NONE;
		}
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('style' => $pValue));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		} else {
			$this->_borderStyle = $pValue;
		}
		return $this;
	}

	/**
	 * Get Border Color
	 *
	 * @return PHPExcel_Style_Color
	 */
	public function getColor() {
		return $this->_color;
	}

	/**
	 * Set Border Color
	 *
	 * @param	PHPExcel_Style_Color $pValue
	 * @throws	Exception
	 * @return PHPExcel_Style_Border
	 */
	public function setColor(PHPExcel_Style_Color $pValue = null) {
		// make sure parameter is a real color and not a supervisor
		$color = $pValue->getIsSupervisor() ? $pValue->getSharedComponent() : $pValue;

		if ($this->_isSupervisor) {
			$styleArray = $this->getColor()->getStyleArray(array('argb' => $color->getARGB()));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		} else {
			$this->_color = $color;
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
			  $this->_borderStyle
			. $this->_color->getHashCode()
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
