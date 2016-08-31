<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

/**
 * Copyright (c) 2006 - 2016 PhpSpreadsheet
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
 * @category   PhpSpreadsheet
 * @copyright  Copyright (c) 2006 - 2016 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
abstract class CellIterator
{
    /**
     * \PhpOffice\PhpSpreadsheet\Worksheet to iterate
     *
     * @var \PhpOffice\PhpSpreadsheet\Worksheet
     */
    protected $subject;

    /**
     * Current iterator position
     *
     * @var mixed
     */
    protected $position = null;

    /**
     * Iterate only existing cells
     *
     * @var bool
     */
    protected $onlyExistingCells = false;

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->subject);
    }

    /**
     * Get loop only existing cells
     *
     * @return bool
     */
    public function getIterateOnlyExistingCells()
    {
        return $this->onlyExistingCells;
    }

    /**
     * Validate start/end values for "IterateOnlyExistingCells" mode, and adjust if necessary
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    abstract protected function adjustForExistingOnlyRange();

    /**
     * Set the iterator to loop only existing cells
     *
     * @param    bool        $value
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function setIterateOnlyExistingCells($value = true)
    {
        $this->onlyExistingCells = (boolean) $value;

        $this->adjustForExistingOnlyRange();
    }
}
