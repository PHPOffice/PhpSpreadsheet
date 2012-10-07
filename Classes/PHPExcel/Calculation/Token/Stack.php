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
 * @package    PHPExcel_Calculation
 * @copyright  Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license	http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version	##VERSION##, ##DATE##
 */


class PHPExcel_Calculation_Token_Stack {

	private $_stack = array();
	private $_count = 0;


	public function count() {
		return $this->_count;
	}	//	function count()


	public function push($type,$value,$reference=null) {
		$this->_stack[$this->_count++] = array('type'		=> $type,
											   'value'		=> $value,
											   'reference'	=> $reference
											  );
		if ($type == 'Function') {
			$localeFunction = PHPExcel_Calculation::_localeFunc($value);
			if ($localeFunction != $value) {
				$this->_stack[($this->_count - 1)]['localeValue'] = $localeFunction;
			}
		}
	}	//	function push()


	public function pop() {
		if ($this->_count > 0) {
			return $this->_stack[--$this->_count];
		}
		return null;
	}	//	function pop()


	public function last($n=1) {
		if ($this->_count-$n < 0) {
			return null;
		}
		return $this->_stack[$this->_count-$n];
	}	//	function last()


	function __construct() {
	}

}	//	class PHPExcel_Calculation_Token_Stack
