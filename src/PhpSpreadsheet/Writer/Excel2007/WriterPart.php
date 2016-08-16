<?php

namespace PhpSpreadsheet\Writer\Excel2007;

/**
 * \PhpSpreadsheet\Writer\Excel2007\WriterPart
 *
 * Copyright (c) 2006 - 2015 PhpSpreadsheet
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
 * @copyright  Copyright (c) 2006 - 2015 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
abstract class WriterPart
{
    /**
     * Parent IWriter object
     *
     * @var \PhpSpreadsheet\Writer\IWriter
     */
    private $parentWriter;

    /**
     * Set parent IWriter object
     *
     * @param \PhpSpreadsheet\Writer\IWriter    $pWriter
     * @throws \PhpSpreadsheet\Writer\Exception
     */
    public function setParentWriter(\PhpSpreadsheet\Writer\IWriter $pWriter = null)
    {
        $this->parentWriter = $pWriter;
    }

    /**
     * Get parent IWriter object
     *
     * @throws \PhpSpreadsheet\Writer\Exception
     * @return \PhpSpreadsheet\Writer\IWriter
     */
    public function getParentWriter()
    {
        if (!is_null($this->parentWriter)) {
            return $this->parentWriter;
        } else {
            throw new \PhpSpreadsheet\Writer\Exception('No parent \\PhpSpreadsheet\\Writer\\IWriter assigned.');
        }
    }

    /**
     * Set parent IWriter object
     *
     * @param \PhpSpreadsheet\Writer\IWriter    $pWriter
     * @throws \PhpSpreadsheet\Writer\Exception
     */
    public function __construct(\PhpSpreadsheet\Writer\IWriter $pWriter = null)
    {
        if (!is_null($pWriter)) {
            $this->parentWriter = $pWriter;
        }
    }
}
