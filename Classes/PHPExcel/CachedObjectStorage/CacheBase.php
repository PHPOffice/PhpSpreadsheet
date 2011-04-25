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
 * @package    PHPExcel_CachedObjectStorage
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/**
 * PHPExcel_CachedObjectStorage_CacheBase
 *
 * @category   PHPExcel
 * @package    PHPExcel_CachedObjectStorage
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_CachedObjectStorage_CacheBase {

	/**
	 *	Parent worksheet
	 *
	 *	@var PHPExcel_Worksheet
	 */
	protected $_parent;

	/**
	 *	The currently active Cell
	 *
	 *	@var PHPExcel_Cell
	 */
	protected $_currentObject = null;

	/**
	 *	Coordinate address of the currently active Cell
	 *
	 *	@var string
	 */
	protected $_currentObjectID = null;


	/**
	 *	Flag indicating whether the currently active Cell requires saving
	 *
	 *	@var boolean
	 */
	protected $_currentCellIsDirty = true;

	/**
	 *	An array of cells or cell pointers for the worksheet cells held in this cache,
	 *		and indexed by their coordinate address within the worksheet
	 *
	 *	@var array of mixed
	 */
	protected $_cellCache = array();


	public function __construct(PHPExcel_Worksheet $parent) {
		//	Set our parent worksheet.
		//	This is maintained within the cache controller to facilitate re-attaching it to PHPExcel_Cell objects when
		//		they are woken from a serialized state
		$this->_parent = $parent;
	}	//	function __construct()


	/**
	 *	Is a value set in the current PHPExcel_CachedObjectStorage_ICache for an indexed cell?
	 *
	 *	@param	string		$pCoord		Coordinate address of the cell to check
	 *	@return	void
	 *	@return	boolean
	 */
	public function isDataSet($pCoord) {
		if ($pCoord === $this->_currentObjectID) {
			return true;
		}
		//	Check if the requested entry exists in the cache
		return isset($this->_cellCache[$pCoord]);
	}	//	function isDataSet()


    /**
     *	Add or Update a cell in cache
     *
     *	@param	PHPExcel_Cell	$cell		Cell to update
	 *	@return	void
     *	@throws	Exception
     */
	public function updateCacheData(PHPExcel_Cell $cell) {
		return $this->addCacheData($cell->getCoordinate(),$cell);
	}	//	function updateCacheData()


    /**
     *	Delete a cell in cache identified by coordinate address
     *
     *	@param	string			$pCoord		Coordinate address of the cell to delete
     *	@throws	Exception
     */
	public function deleteCacheData($pCoord) {
		if ($pCoord === $this->_currentObjectID) {
			$this->_currentObject->detach();
			$this->_currentObjectID = $this->_currentObject = null;
		}

		if (is_object($this->_cellCache[$pCoord])) {
			$this->_cellCache[$pCoord]->detach();
			unset($this->_cellCache[$pCoord]);
		}
		$this->_currentCellIsDirty = false;
	}	//	function deleteCacheData()


	/**
	 *	Get a list of all cell addresses currently held in cache
	 *
	 *	@return	array of string
	 */
	public function getCellList() {
		return array_keys($this->_cellCache);
	}	//	function getCellList()


	/**
	 *	Sort the list of all cell addresses currently held in cache by row and column
	 *
	 *	@return	void
	 */
	public function getSortedCellList() {
		$sortKeys = array();
		foreach (array_keys($this->_cellCache) as $coord) {
			list($column,$row) = sscanf($coord,'%[A-Z]%d');
			$sortKeys[sprintf('%09d%3s',$row,$column)] = $coord;
		}
		ksort($sortKeys);

		return array_values($sortKeys);
	}	//	function sortCellList()


	protected function _getUniqueID() {
		if (function_exists('posix_getpid')) {
			$baseUnique = posix_getpid();
		} else {
			$baseUnique = mt_rand();
		}
		return uniqid($baseUnique,true);
	}

	/**
	 *	Clone the cell collection
	 *
	 *	@return	void
	 */
	public function copyCellCollection(PHPExcel_Worksheet $parent) {
		$this->_parent = $parent;
		if ((!is_null($this->_currentObject)) && (is_object($this->_currentObject))) {
			$this->_currentObject->attach($parent);
		}
	}	//	function copyCellCollection()

}
