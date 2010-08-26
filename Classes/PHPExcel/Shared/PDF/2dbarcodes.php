<?php
//============================================================+
// File name   : 2dbarcodes.php
// Begin       : 2009-04-07
// Last Update : 2009-08-17
// Version     : 1.0.000
// License     : GNU LGPL (http://www.gnu.org/copyleft/lesser.html)
// 	----------------------------------------------------------------------------
//  Copyright (C) 2008-2009 Nicola Asuni - Tecnick.com S.r.l.
// 	
// 	This program is free software: you can redistribute it and/or modify
// 	it under the terms of the GNU Lesser General Public License as published by
// 	the Free Software Foundation, either version 2.1 of the License, or
// 	(at your option) any later version.
// 	
// 	This program is distributed in the hope that it will be useful,
// 	but WITHOUT ANY WARRANTY; without even the implied warranty of
// 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// 	GNU Lesser General Public License for more details.
// 	
// 	You should have received a copy of the GNU Lesser General Public License
// 	along with this program.  If not, see <http://www.gnu.org/licenses/>.
// 	
// 	See LICENSE.TXT file for more information.
//  ----------------------------------------------------------------------------
//
// Description : PHP class to creates array representations for 
//               2D barcodes to be used with TCPDF.
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com S.r.l.
//               Via della Pace, 11
//               09044 Quartucciu (CA)
//               ITALY
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * PHP class to creates array representations for 2D barcodes to be used with TCPDF.
 * @package com.tecnick.tcpdf
 * @abstract Functions for generating string representation of 2D barcodes.
 * @author Nicola Asuni
 * @copyright 2008-2009 Nicola Asuni - Tecnick.com S.r.l (www.tecnick.com) Via Della Pace, 11 - 09044 - Quartucciu (CA) - ITALY - www.tecnick.com - info@tecnick.com
 * @link http://www.tcpdf.org
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @version 1.0.000
 */

	/**
	* PHP class to creates array representations for 2D barcodes to be used with TCPDF (http://www.tcpdf.org).<br>
	* @name TCPDFBarcode
	* @package com.tecnick.tcpdf
	* @version 1.0.000
	* @author Nicola Asuni
	* @link http://www.tcpdf.org
	* @license http://www.gnu.org/copyleft/lesser.html LGPL
	*/
class TCPDF2DBarcode {
	
	/**
	 * @var array representation of barcode.
	 * @access protected
	 */
	protected $barcode_array;
	
	/**
	 * This is the class constructor. 
	 * Return an array representations for 2D barcodes:<ul>
	 * <li>$arrcode['code'] code to be printed on text label</li>
	 * <li>$arrcode['num_rows'] required number of rows</li>
	 * <li>$arrcode['num_cols'] required number of columns</li>
	 * <li>$arrcode['bcode'][$r][$c] value of the cell is $r row and $c column (0 = transparent, 1 = black)</li></ul>
	 * @param string $code code to print
 	 * @param string $type type of barcode: <ul><li>TEST</li><li>...TO BE IMPLEMENTED</li></ul>
	 */
	public function __construct($code, $type) {
		$this->setBarcode($code, $type);
	}
	
	/** 
	 * Return an array representations of barcode.
 	 * @return array
	 */
	public function getBarcodeArray() {
		return $this->barcode_array;
	}
	
	/** 
	 * Set the barcode.
	 * @param string $code code to print
 	 * @param string $type type of barcode: <ul><li>TEST</li><li>...TO BE IMPLEMENTED</li></ul>
 	 * @return array
	 */
	public function setBarcode($code, $type) {
		$mode = explode(',', $type);
		switch (strtoupper($mode[0])) {
			case 'TEST': { // TEST MODE
				$this->barcode_array['num_rows'] = 5;
				$this->barcode_array['num_cols'] = 15;
				$this->barcode_array['bcode'] = array(
					array(1,1,1,0,1,1,1,0,1,1,1,0,1,1,1),
					array(0,1,0,0,1,0,0,0,1,0,0,0,0,1,0),
					array(0,1,0,0,1,1,0,0,1,1,1,0,0,1,0),
					array(0,1,0,0,1,0,0,0,0,0,1,0,0,1,0),
					array(0,1,0,0,1,1,1,0,1,1,1,0,0,1,0)
				);
				break;
			}
			
			// ... Add here real 2D barcodes ...
			
			default: {
				$this->barcode_array = false;
			}
		}
	}
} // end of class

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
