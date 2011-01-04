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
 * @package    PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Style_Color
 *
 * @category   PHPExcel
 * @package    PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Style_Color implements PHPExcel_IComparable
{
	/* Colors */
	const COLOR_BLACK						= 'FF000000';
	const COLOR_WHITE						= 'FFFFFFFF';
	const COLOR_RED							= 'FFFF0000';
	const COLOR_DARKRED						= 'FF800000';
	const COLOR_BLUE						= 'FF0000FF';
	const COLOR_DARKBLUE					= 'FF000080';
	const COLOR_GREEN						= 'FF00FF00';
	const COLOR_DARKGREEN					= 'FF008000';
	const COLOR_YELLOW						= 'FFFFFF00';
	const COLOR_DARKYELLOW					= 'FF808000';

	/**
	 * Indexed colors array
	 *
	 * @var array
	 */
	private static $_indexedColors;

	/**
	 * ARGB - Alpha RGB
	 *
	 * @var string
	 */
	private $_argb;

	/**
	 * Supervisor?
	 *
	 * @var boolean
	 */
	private $_isSupervisor;

	/**
	 * Parent. Only used for supervisor
	 *
	 * @var mixed
	 */
	private $_parent;

	/**
	 * Parent property name
	 *
	 * @var string
	 */
	private $_parentPropertyName;

    /**
     * Create a new PHPExcel_Style_Color
     *
     * @param string $pARGB
     */
    public function __construct($pARGB = PHPExcel_Style_Color::COLOR_BLACK, $isSupervisor = false)
    {
    	// Supervisor?
		$this->_isSupervisor = $isSupervisor;

    	// Initialise values
    	$this->_argb			= $pARGB;
    }

	/**
	 * Bind parent. Only used for supervisor
	 *
	 * @param mixed $parent
	 * @param string $parentPropertyName
	 * @return PHPExcel_Style_Color
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
	 * @return PHPExcel_Style_Color
	 */
	public function getSharedComponent()
	{
		switch ($this->_parentPropertyName) {
		case '_endColor':
			return $this->_parent->getSharedComponent()->getEndColor();
			break;

		case '_color':
			return $this->_parent->getSharedComponent()->getColor();
			break;

		case '_startColor':
			return $this->_parent->getSharedComponent()->getStartColor();
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
		case '_endColor':
			$key = 'endcolor';
			break;

		case '_color':
			$key = 'color';
			break;

		case '_startColor':
			$key = 'startcolor';
			break;

		}
		return $this->_parent->getStyleArray(array($key => $array));
	}

    /**
     * Apply styles from array
     *
     * <code>
     * $objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->getColor()->applyFromArray( array('rgb' => '808080') );
     * </code>
     *
     * @param	array	$pStyles	Array containing style information
     * @throws	Exception
     * @return PHPExcel_Style_Color
     */
	public function applyFromArray($pStyles = null) {
		if (is_array($pStyles)) {
			if ($this->_isSupervisor) {
				$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($pStyles));
			} else {
				if (array_key_exists('rgb', $pStyles)) {
					$this->setRGB($pStyles['rgb']);
				}
				if (array_key_exists('argb', $pStyles)) {
					$this->setARGB($pStyles['argb']);
				}
			}
		} else {
			throw new Exception("Invalid style array passed.");
		}
		return $this;
	}

    /**
     * Get ARGB
     *
     * @return string
     */
    public function getARGB() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getARGB();
		}
    	return $this->_argb;
    }

    /**
     * Set ARGB
     *
     * @param string $pValue
     * @return PHPExcel_Style_Color
     */
    public function setARGB($pValue = PHPExcel_Style_Color::COLOR_BLACK) {
    	if ($pValue == '') {
    		$pValue = PHPExcel_Style_Color::COLOR_BLACK;
    	}
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('argb' => $pValue));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		} else {
			$this->_argb = $pValue;
		}
		return $this;
    }

    /**
     * Get RGB
     *
     * @return string
     */
    public function getRGB() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getRGB();
		}
    	return substr($this->_argb, 2);
    }

    /**
     * Set RGB
     *
     * @param string $pValue
     * @return PHPExcel_Style_Color
     */
    public function setRGB($pValue = '000000') {
        if ($pValue == '') {
    		$pValue = '000000';
    	}
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('argb' => 'FF' . $pValue));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		} else {
			$this->_argb = 'FF' . $pValue;
		}
		return $this;
    }

	private static function _getColourComponent($RGB,$offset,$hex=true) {
		$colour = substr($RGB,$offset,2);
		if (!$hex)
			$colour = hexdec($colour);
		return $colour;
	}

	public static function getRed($RGB,$hex=true) {
		if (strlen($RGB) == 8) {
			return self::_getColourComponent($RGB,2,$hex);
		} elseif (strlen($RGB) == 6) {
			return self::_getColourComponent($RGB,0,$hex);
		}
	}

	public static function getGreen($RGB,$hex=true) {
		if (strlen($RGB) == 8) {
			return self::_getColourComponent($RGB,4,$hex);
		} elseif (strlen($RGB) == 6) {
			return self::_getColourComponent($RGB,2,$hex);
		}
	}

	public static function getBlue($RGB,$hex=true) {
		if (strlen($RGB) == 8) {
			return self::_getColourComponent($RGB,6,$hex);
		} elseif (strlen($RGB) == 6) {
			return self::_getColourComponent($RGB,4,$hex);
		}
	}

	/**
     * Adjust the brightness of a color
     *
     * @param	string		$hex	The colour as an RGB value (e.g. FF00CCCC or CCDDEE
     * @param	float		$adjustPercentage	The percentage by which to adjust the colour as a float from -1 to 1
     * @return	string		The adjusted colour as an RGB value (e.g. FF00CCCC or CCDDEE
     */
	public static function changeBrightness($hex, $adjustPercentage) {
		$red	= self::getRed($hex,false);
		$green	= self::getGreen($hex,false);
		$blue	= self::getBlue($hex,false);
		if ($adjustPercentage > 0) {
			$red	+= (255 - $red) * $adjustPercentage;
			$green	+= (255 - $green) * $adjustPercentage;
			$blue	+= (255 - $blue) * $adjustPercentage;
		} else {
			$red	+= $red * $adjustPercentage;
			$green	+= $green * $adjustPercentage;
			$blue	+= $blue * $adjustPercentage;
		}

		if ($red < 0) $red = 0;
		elseif ($red > 255) $red = 255;
		if ($green < 0) $green = 0;
		elseif ($green > 255) $green = 255;
		if ($blue < 0) $blue = 0;
		elseif ($blue > 255) $blue = 255;

		return strtoupper(	str_pad(dechex($red), 2, '0', 0) .
							str_pad(dechex($green), 2, '0', 0) .
							str_pad(dechex($blue), 2, '0', 0)
						 );
	}

	/**
     * Get indexed color
     *
     * @param	int		$pIndex
     * @return	PHPExcel_Style_Color
     */
    public static function indexedColor($pIndex, $background=false) {
    	// Clean parameter
		$pIndex = intval($pIndex);

    	// Indexed colors
    	if (is_null(self::$_indexedColors)) {
			self::$_indexedColors = array();
			self::$_indexedColors[] = '00000000';
			self::$_indexedColors[] = '00FFFFFF';
			self::$_indexedColors[] = '00FF0000';
			self::$_indexedColors[] = '0000FF00';
			self::$_indexedColors[] = '000000FF';
			self::$_indexedColors[] = '00FFFF00';
			self::$_indexedColors[] = '00FF00FF';
			self::$_indexedColors[] = '0000FFFF';
			self::$_indexedColors[] = '00000000';
			self::$_indexedColors[] = '00FFFFFF';
			self::$_indexedColors[] = '00FF0000';
			self::$_indexedColors[] = '0000FF00';
			self::$_indexedColors[] = '000000FF';
			self::$_indexedColors[] = '00FFFF00';
			self::$_indexedColors[] = '00FF00FF';
			self::$_indexedColors[] = '0000FFFF';
			self::$_indexedColors[] = '00800000';
			self::$_indexedColors[] = '00008000';
			self::$_indexedColors[] = '00000080';
			self::$_indexedColors[] = '00808000';
			self::$_indexedColors[] = '00800080';
			self::$_indexedColors[] = '00008080';
			self::$_indexedColors[] = '00C0C0C0';
			self::$_indexedColors[] = '00808080';
			self::$_indexedColors[] = '009999FF';
			self::$_indexedColors[] = '00993366';
			self::$_indexedColors[] = '00FFFFCC';
			self::$_indexedColors[] = '00CCFFFF';
			self::$_indexedColors[] = '00660066';
			self::$_indexedColors[] = '00FF8080';
			self::$_indexedColors[] = '000066CC';
			self::$_indexedColors[] = '00CCCCFF';
			self::$_indexedColors[] = '00000080';
			self::$_indexedColors[] = '00FF00FF';
			self::$_indexedColors[] = '00FFFF00';
			self::$_indexedColors[] = '0000FFFF';
			self::$_indexedColors[] = '00800080';
			self::$_indexedColors[] = '00800000';
			self::$_indexedColors[] = '00008080';
			self::$_indexedColors[] = '000000FF';
			self::$_indexedColors[] = '0000CCFF';
			self::$_indexedColors[] = '00CCFFFF';
			self::$_indexedColors[] = '00CCFFCC';
			self::$_indexedColors[] = '00FFFF99';
			self::$_indexedColors[] = '0099CCFF';
			self::$_indexedColors[] = '00FF99CC';
			self::$_indexedColors[] = '00CC99FF';
			self::$_indexedColors[] = '00FFCC99';
			self::$_indexedColors[] = '003366FF';
			self::$_indexedColors[] = '0033CCCC';
			self::$_indexedColors[] = '0099CC00';
			self::$_indexedColors[] = '00FFCC00';
			self::$_indexedColors[] = '00FF9900';
			self::$_indexedColors[] = '00FF6600';
			self::$_indexedColors[] = '00666699';
			self::$_indexedColors[] = '00969696';
			self::$_indexedColors[] = '00003366';
			self::$_indexedColors[] = '00339966';
			self::$_indexedColors[] = '00003300';
			self::$_indexedColors[] = '00333300';
			self::$_indexedColors[] = '00993300';
			self::$_indexedColors[] = '00993366';
			self::$_indexedColors[] = '00333399';
			self::$_indexedColors[] = '00333333';
    	}

		if (array_key_exists($pIndex, self::$_indexedColors)) {
			return new PHPExcel_Style_Color(self::$_indexedColors[$pIndex]);
		}

		if ($background) {
	    	return new PHPExcel_Style_Color('FFFFFFFF');
		}
    	return new PHPExcel_Style_Color('FF000000');
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
    		  $this->_argb
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
