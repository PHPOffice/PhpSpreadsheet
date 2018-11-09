<?php

namespace PhpOffice\PhpSpreadsheet\Shared\OLE;

// vim: set expandtab tabstop=4 shiftwidth=4:
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Xavier Noguer <xnoguer@php.net>                              |
// | Based on OLE::Storage_Lite by Kawai, Takanori                        |
// +----------------------------------------------------------------------+
//
use PhpOffice\PhpSpreadsheet\Shared\OLE;

/**
 * Class for creating PPS's for OLE containers.
 *
 * @author   Xavier Noguer <xnoguer@php.net>
 *
 * @category PhpSpreadsheet
 */
class PPS
{
    /**
     * The PPS index.
     *
     * @var int
     */
    public $No;

    /**
     * The PPS name (in Unicode).
     *
     * @var string
     */
    public $Name;

    /**
     * The PPS type. Dir, Root or File.
     *
     * @var int
     */
    public $Type;

    /**
     * The index of the previous PPS.
     *
     * @var int
     */
    public $PrevPps;

    /**
     * The index of the next PPS.
     *
     * @var int
     */
    public $NextPps;

    /**
     * The index of it's first child if this is a Dir or Root PPS.
     *
     * @var int
     */
    public $DirPps;

    /**
     * A timestamp.
     *
     * @var int
     */
    public $Time1st;

    /**
     * A timestamp.
     *
     * @var int
     */
    public $Time2nd;

    /**
     * Starting block (small or big) for this PPS's data  inside the container.
     *
     * @var int
     */
    public $startBlock;

    /**
     * The size of the PPS's data (in bytes).
     *
     * @var int
     */
    public $Size;

    /**
     * The PPS's data (only used if it's not using a temporary file).
     *
     * @var string
     */
    public $_data;

    /**
     * Array of child PPS's (only used by Root and Dir PPS's).
     *
     * @var array
     */
    public $children = [];

    /**
     * Pointer to OLE container.
     *
     * @var OLE
     */
    public $ole;

    /**
     * The constructor.
     *
     * @param int $No The PPS index
     * @param string $name The PPS name
     * @param int $type The PPS type. Dir, Root or File
     * @param int $prev The index of the previous PPS
     * @param int $next The index of the next PPS
     * @param int $dir The index of it's first child if this is a Dir or Root PPS
     * @param int $time_1st A timestamp
     * @param int $time_2nd A timestamp
     * @param string $data The (usually binary) source data of the PPS
     * @param array $children Array containing children PPS for this PPS
     */
    public function __construct($No, $name, $type, $prev, $next, $dir, $time_1st, $time_2nd, $data, $children)
    {
        $this->No = $No;
        $this->Name = $name;
        $this->Type = $type;
        $this->PrevPps = $prev;
        $this->NextPps = $next;
        $this->DirPps = $dir;
        $this->Time1st = $time_1st;
        $this->Time2nd = $time_2nd;
        $this->_data = $data;
        $this->children = $children;
        if ($data != '') {
            $this->Size = strlen($data);
        } else {
            $this->Size = 0;
        }
    }

    /**
     * Returns the amount of data saved for this PPS.
     *
     * @return int The amount of data (in bytes)
     */
    public function getDataLen()
    {
        if (!isset($this->_data)) {
            return 0;
        }

        return strlen($this->_data);
    }

    /**
     * Returns a string with the PPS's WK (What is a WK?).
     *
     * @return string The binary string
     */
    public function _getPpsWk()
    {
        $ret = str_pad($this->Name, 64, "\x00");

        $ret .= pack('v', strlen($this->Name) + 2)  // 66
            . pack('c', $this->Type)              // 67
            . pack('c', 0x00) //UK                // 68
            . pack('V', $this->PrevPps) //Prev    // 72
            . pack('V', $this->NextPps) //Next    // 76
            . pack('V', $this->DirPps)  //Dir     // 80
            . "\x00\x09\x02\x00"                  // 84
            . "\x00\x00\x00\x00"                  // 88
            . "\xc0\x00\x00\x00"                  // 92
            . "\x00\x00\x00\x46"                  // 96 // Seems to be ok only for Root
            . "\x00\x00\x00\x00"                  // 100
            . OLE::localDateToOLE($this->Time1st)          // 108
            . OLE::localDateToOLE($this->Time2nd)          // 116
            . pack('V', isset($this->startBlock) ? $this->startBlock : 0)  // 120
            . pack('V', $this->Size)               // 124
            . pack('V', 0); // 128
        return $ret;
    }

    /**
     * Updates index and pointers to previous, next and children PPS's for this
     * PPS. I don't think it'll work with Dir PPS's.
     *
     * @param array &$raList Reference to the array of PPS's for the whole OLE
     *                          container
     * @param mixed $to_save
     * @param mixed $depth
     *
     * @return int The index for this PPS
     */
    public static function _savePpsSetPnt(&$raList, $to_save, $depth = 0)
    {
        if (!is_array($to_save) || (empty($to_save))) {
            return 0xFFFFFFFF;
        } elseif (count($to_save) == 1) {
            $cnt = count($raList);
            // If the first entry, it's the root... Don't clone it!
            $raList[$cnt] = ($depth == 0) ? $to_save[0] : clone $to_save[0];
            $raList[$cnt]->No = $cnt;
            $raList[$cnt]->PrevPps = 0xFFFFFFFF;
            $raList[$cnt]->NextPps = 0xFFFFFFFF;
            $raList[$cnt]->DirPps = self::_savePpsSetPnt($raList, @$raList[$cnt]->children, $depth++);
        } else {
            $iPos = floor(count($to_save) / 2);
            $aPrev = array_slice($to_save, 0, $iPos);
            $aNext = array_slice($to_save, $iPos + 1);
            $cnt = count($raList);
            // If the first entry, it's the root... Don't clone it!
            $raList[$cnt] = ($depth == 0) ? $to_save[$iPos] : clone $to_save[$iPos];
            $raList[$cnt]->No = $cnt;
            $raList[$cnt]->PrevPps = self::_savePpsSetPnt($raList, $aPrev, $depth++);
            $raList[$cnt]->NextPps = self::_savePpsSetPnt($raList, $aNext, $depth++);
            $raList[$cnt]->DirPps = self::_savePpsSetPnt($raList, @$raList[$cnt]->children, $depth++);
        }

        return $cnt;
    }
}
