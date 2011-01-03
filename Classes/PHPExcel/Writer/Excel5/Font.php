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
 * @package    PHPExcel_Writer_Excel5
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Writer_Excel5_Font
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel5
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_Excel5_Font
{
	/**
	 * BIFF version
	 *
	 * @var int
	 */
	private $_BIFFVersion;

	/**
	 * Color index
	 *
	 * @var int
	 */
	private $_colorIndex;

	/**
	 * Font
	 *
	 * @var PHPExcel_Style_Font
	 */
	private $_font;

	/**
	 * Constructor
	 *
	 * @param PHPExcel_Style_Font $font
	 */
	public function __construct(PHPExcel_Style_Font $font = null)
	{
		$this->_BIFFVersion = 0x0600;
		$this->_colorIndex = 0x7FFF;
		$this->_font = $font;
	}

	/**
	 * Set the color index
	 *
	 * @param int $colorIndex
	 */
	public function setColorIndex($colorIndex)
	{
		$this->_colorIndex = $colorIndex;
	}

	/**
	 * Get font record data
	 *
	 * @return string
	 */
	public function writeFont()
	{
		$font_outline = 0;
		$font_shadow = 0;

		$icv = $this->_colorIndex; // Index to color palette
		if ($this->_font->getSuperScript()) {
			$sss = 1;
		} else if ($this->_font->getSubScript()) {
			$sss = 2;
		} else {
			$sss = 0;
		}
		$bFamily = 0; // Font family
		$bCharSet = PHPExcel_Shared_Font::getCharsetFromFontName($this->_font->getName()); // Character set

		$record = 0x31; // Record identifier
		$reserved = 0x00; // Reserved
		$grbit = 0x00; // Font attributes
		if ($this->_font->getItalic()) {
			$grbit |= 0x02;
		}
		if ($this->_font->getStrikethrough()) {
			$grbit |= 0x08;
		}
		if ($font_outline) {
			$grbit |= 0x10;
		}
		if ($font_shadow) {
			$grbit |= 0x20;
		}

		if ($this->_BIFFVersion == 0x0500) {
			$data = pack("vvvvvCCCCC",
				$this->_font->getSize() * 20,
				$grbit,
				$icv,
				$this->_mapBold($this->_font->getBold()),
				$sss,
				$this->_mapUnderline($this->_font->getUnderline()),
				$bFamily,
				$bCharSet,
				$reserved,
				strlen($this->_font->getName())
			);
			$data .= $this->_font->getName();
		} elseif ($this->_BIFFVersion == 0x0600) {
			$data = pack("vvvvvCCCC",
				$this->_font->getSize() * 20,
				$grbit,
				$icv,
				$this->_mapBold($this->_font->getBold()),
				$sss,
				$this->_mapUnderline($this->_font->getUnderline()),
				$bFamily,
				$bCharSet,
				$reserved
			);
			$data .= PHPExcel_Shared_String::UTF8toBIFF8UnicodeShort($this->_font->getName());
		}

		$length = strlen($data);
		$header = pack("vv", $record, $length);

		return($header . $data);
	}

	/**
	 * Set BIFF version
	 *
	 * @param int $BIFFVersion
	 */
	public function setBIFFVersion($BIFFVersion)
	{
		$this->_BIFFVersion = $BIFFVersion;
	}

	/**
	 * Map to BIFF5-BIFF8 codes for bold
	 *
	 * @param boolean $bold
	 * @return int
	 */
	private function _mapBold($bold) {
		if ($bold) {
			return 0x2BC;
		}
		return 0x190;
	}

	/**
	 * Map underline
	 *
	 * @param string
	 * @return int
	 */
	private function _mapUnderline($underline) {
		switch ($underline) {
			case PHPExcel_Style_Font::UNDERLINE_NONE:				return 0x00;
			case PHPExcel_Style_Font::UNDERLINE_SINGLE:				return 0x01;
			case PHPExcel_Style_Font::UNDERLINE_DOUBLE:				return 0x02;
			case PHPExcel_Style_Font::UNDERLINE_SINGLEACCOUNTING:	return 0x21;
			case PHPExcel_Style_Font::UNDERLINE_DOUBLEACCOUNTING:	return 0x22;
			default:												return 0x00;
		}
	}

}
