<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Escher;

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
class DggContainer
{
    /**
     * Maximum shape index of all shapes in all drawings increased by one
     *
     * @var int
     */
    private $spIdMax;

    /**
     * Total number of drawings saved
     *
     * @var int
     */
    private $cDgSaved;

    /**
     * Total number of shapes saved (including group shapes)
     *
     * @var int
     */
    private $cSpSaved;

    /**
     * BLIP Store Container
     *
     * @var DggContainer\BstoreContainer
     */
    private $bstoreContainer;

    /**
     * Array of options for the drawing group
     *
     * @var array
     */
    private $OPT = [];

    /**
     * Array of identifier clusters containg information about the maximum shape identifiers
     *
     * @var array
     */
    private $IDCLs = [];

    /**
     * Get maximum shape index of all shapes in all drawings (plus one)
     *
     * @return int
     */
    public function getSpIdMax()
    {
        return $this->spIdMax;
    }

    /**
     * Set maximum shape index of all shapes in all drawings (plus one)
     *
     * @param int
     */
    public function setSpIdMax($value)
    {
        $this->spIdMax = $value;
    }

    /**
     * Get total number of drawings saved
     *
     * @return int
     */
    public function getCDgSaved()
    {
        return $this->cDgSaved;
    }

    /**
     * Set total number of drawings saved
     *
     * @param int
     */
    public function setCDgSaved($value)
    {
        $this->cDgSaved = $value;
    }

    /**
     * Get total number of shapes saved (including group shapes)
     *
     * @return int
     */
    public function getCSpSaved()
    {
        return $this->cSpSaved;
    }

    /**
     * Set total number of shapes saved (including group shapes)
     *
     * @param int
     */
    public function setCSpSaved($value)
    {
        $this->cSpSaved = $value;
    }

    /**
     * Get BLIP Store Container
     *
     * @return DggContainer\BstoreContainer
     */
    public function getBstoreContainer()
    {
        return $this->bstoreContainer;
    }

    /**
     * Set BLIP Store Container
     *
     * @param DggContainer\BstoreContainer $bstoreContainer
     */
    public function setBstoreContainer($bstoreContainer)
    {
        $this->bstoreContainer = $bstoreContainer;
    }

    /**
     * Set an option for the drawing group
     *
     * @param int $property The number specifies the option
     * @param mixed $value
     */
    public function setOPT($property, $value)
    {
        $this->OPT[$property] = $value;
    }

    /**
     * Get an option for the drawing group
     *
     * @param int $property The number specifies the option
     * @return mixed
     */
    public function getOPT($property)
    {
        if (isset($this->OPT[$property])) {
            return $this->OPT[$property];
        }

        return null;
    }

    /**
     * Get identifier clusters
     *
     * @return array
     */
    public function getIDCLs()
    {
        return $this->IDCLs;
    }

    /**
     * Set identifier clusters. array(<drawingId> => <max shape id>, ...)
     *
     * @param array $pValue
     */
    public function setIDCLs($pValue)
    {
        $this->IDCLs = $pValue;
    }
}
