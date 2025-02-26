<?php

namespace PhpOffice\PhpSpreadsheet\Shared\OLE\PPS;

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
use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS;

/**
 * Class for creating Root PPS's for OLE containers.
 *
 * @author   Xavier Noguer <xnoguer@php.net>
 */
class Root extends PPS
{
    /**
     * @var resource
     */
    private $fileHandle;

    private ?int $smallBlockSize = null;

    private ?int $bigBlockSize = null;

    /**
     * @param null|float|int $time_1st A timestamp
     * @param null|float|int $time_2nd A timestamp
     * @param File[] $raChild
     */
    public function __construct($time_1st, $time_2nd, array $raChild)
    {
        parent::__construct(null, OLE::ascToUcs('Root Entry'), OLE::OLE_PPS_TYPE_ROOT, null, null, null, $time_1st, $time_2nd, null, $raChild);
    }

    /**
     * Method for saving the whole OLE container (including files).
     * In fact, if called with an empty argument (or '-'), it saves to a
     * temporary file and then outputs it's contents to stdout.
     * If a resource pointer to a stream created by fopen() is passed
     * it will be used, but you have to close such stream by yourself.
     *
     * @param resource $fileHandle the name of the file or stream where to save the OLE container
     *
     * @return bool true on success
     */
    public function save($fileHandle): bool
    {
        $this->fileHandle = $fileHandle;

        // Initial Setting for saving
        $this->bigBlockSize = (int) (2 ** (
            (isset($this->bigBlockSize)) ? self::adjust2($this->bigBlockSize) : 9
        ));
        $this->smallBlockSize = (int) (2 ** (
            (isset($this->smallBlockSize)) ? self::adjust2($this->smallBlockSize) : 6
        ));

        // Make an array of PPS's (for Save)
        $aList = [];
        PPS::savePpsSetPnt($aList, [$this]);
        // calculate values for header
        [$iSBDcnt, $iBBcnt, $iPPScnt] = $this->calcSize($aList); //, $rhInfo);
        // Save Header
        $this->saveHeader((int) $iSBDcnt, (int) $iBBcnt, (int) $iPPScnt);

        // Make Small Data string (write SBD)
        $this->_data = $this->makeSmallData($aList);

        // Write BB
        $this->saveBigData((int) $iSBDcnt, $aList);
        // Write PPS
        $this->savePps($aList);
        // Write Big Block Depot and BDList and Adding Header informations
        $this->saveBbd((int) $iSBDcnt, (int) $iBBcnt, (int) $iPPScnt);

        return true;
    }

    /**
     * Calculate some numbers.
     *
     * @param array $raList Reference to an array of PPS's
     *
     * @return float[] The array of numbers
     */
    private function calcSize(array &$raList): array
    {
        // Calculate Basic Setting
        [$iSBDcnt, $iBBcnt, $iPPScnt] = [0, 0, 0];
        $iSBcnt = 0;
        $iCount = count($raList);
        for ($i = 0; $i < $iCount; ++$i) {
            if ($raList[$i]->Type == OLE::OLE_PPS_TYPE_FILE) {
                $raList[$i]->Size = $raList[$i]->getDataLen();
                if ($raList[$i]->Size < OLE::OLE_DATA_SIZE_SMALL) {
                    $iSBcnt += floor($raList[$i]->Size / $this->smallBlockSize)
                        + (($raList[$i]->Size % $this->smallBlockSize) ? 1 : 0);
                } else {
                    $iBBcnt += (floor($raList[$i]->Size / $this->bigBlockSize)
                        + (($raList[$i]->Size % $this->bigBlockSize) ? 1 : 0));
                }
            }
        }
        $iSmallLen = $iSBcnt * $this->smallBlockSize;
        $iSlCnt = floor($this->bigBlockSize / OLE::OLE_LONG_INT_SIZE);
        $iSBDcnt = floor($iSBcnt / $iSlCnt) + (($iSBcnt % $iSlCnt) ? 1 : 0);
        $iBBcnt += (floor($iSmallLen / $this->bigBlockSize)
            + (($iSmallLen % $this->bigBlockSize) ? 1 : 0));
        $iCnt = count($raList);
        $iBdCnt = $this->bigBlockSize / OLE::OLE_PPS_SIZE;
        $iPPScnt = (floor($iCnt / $iBdCnt) + (($iCnt % $iBdCnt) ? 1 : 0));

        return [$iSBDcnt, $iBBcnt, $iPPScnt];
    }

    /**
     * Helper function for caculating a magic value for block sizes.
     *
     * @param int $i2 The argument
     *
     * @see save()
     */
    private static function adjust2(int $i2): float
    {
        $iWk = log($i2) / log(2);

        return ($iWk > floor($iWk)) ? floor($iWk) + 1 : $iWk;
    }

    /**
     * Save OLE header.
     */
    private function saveHeader(int $iSBDcnt, int $iBBcnt, int $iPPScnt): void
    {
        $FILE = $this->fileHandle;

        // Calculate Basic Setting
        $iBlCnt = $this->bigBlockSize / OLE::OLE_LONG_INT_SIZE;
        $i1stBdL = ($this->bigBlockSize - 0x4C) / OLE::OLE_LONG_INT_SIZE;

        $iBdExL = 0;
        $iAll = $iBBcnt + $iPPScnt + $iSBDcnt;
        $iAllW = $iAll;
        $iBdCntW = floor($iAllW / $iBlCnt) + (($iAllW % $iBlCnt) ? 1 : 0);
        $iBdCnt = floor(($iAll + $iBdCntW) / $iBlCnt) + ((($iAllW + $iBdCntW) % $iBlCnt) ? 1 : 0);

        // Calculate BD count
        if ($iBdCnt > $i1stBdL) {
            while (1) {
                ++$iBdExL;
                ++$iAllW;
                $iBdCntW = floor($iAllW / $iBlCnt) + (($iAllW % $iBlCnt) ? 1 : 0);
                $iBdCnt = floor(($iAllW + $iBdCntW) / $iBlCnt) + ((($iAllW + $iBdCntW) % $iBlCnt) ? 1 : 0);
                if ($iBdCnt <= ($iBdExL * $iBlCnt + $i1stBdL)) {
                    break;
                }
            }
        }

        // Save Header
        fwrite(
            $FILE,
            "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1"
            . "\x00\x00\x00\x00"
            . "\x00\x00\x00\x00"
            . "\x00\x00\x00\x00"
            . "\x00\x00\x00\x00"
            . pack('v', 0x3B)
            . pack('v', 0x03)
            . pack('v', -2)
            . pack('v', 9)
            . pack('v', 6)
            . pack('v', 0)
            . "\x00\x00\x00\x00"
            . "\x00\x00\x00\x00"
            . pack('V', $iBdCnt)
            . pack('V', $iBBcnt + $iSBDcnt) //ROOT START
            . pack('V', 0)
            . pack('V', 0x1000)
            . pack('V', $iSBDcnt ? 0 : -2) //Small Block Depot
            . pack('V', $iSBDcnt)
        );
        // Extra BDList Start, Count
        if ($iBdCnt < $i1stBdL) {
            fwrite(
                $FILE,
                pack('V', -2) // Extra BDList Start
                . pack('V', 0)// Extra BDList Count
            );
        } else {
            fwrite($FILE, pack('V', $iAll + $iBdCnt) . pack('V', $iBdExL));
        }

        // BDList
        for ($i = 0; $i < $i1stBdL && $i < $iBdCnt; ++$i) {
            fwrite($FILE, pack('V', $iAll + $i));
        }
        if ($i < $i1stBdL) {
            $jB = $i1stBdL - $i;
            for ($j = 0; $j < $jB; ++$j) {
                fwrite($FILE, (pack('V', -1)));
            }
        }
    }

    /**
     * Saving big data (PPS's with data bigger than \PhpOffice\PhpSpreadsheet\Shared\OLE::OLE_DATA_SIZE_SMALL).
     *
     * @param array $raList Reference to array of PPS's
     */
    private function saveBigData(int $iStBlk, array &$raList): void
    {
        $FILE = $this->fileHandle;

        // cycle through PPS's
        $iCount = count($raList);
        for ($i = 0; $i < $iCount; ++$i) {
            if ($raList[$i]->Type != OLE::OLE_PPS_TYPE_DIR) {
                $raList[$i]->Size = $raList[$i]->getDataLen();
                if (($raList[$i]->Size >= OLE::OLE_DATA_SIZE_SMALL) || (($raList[$i]->Type == OLE::OLE_PPS_TYPE_ROOT) && isset($raList[$i]->_data))) {
                    fwrite($FILE, $raList[$i]->_data);

                    if ($raList[$i]->Size % $this->bigBlockSize) {
                        fwrite($FILE, str_repeat("\x00", $this->bigBlockSize - ($raList[$i]->Size % $this->bigBlockSize)));
                    }
                    // Set For PPS
                    $raList[$i]->startBlock = $iStBlk;
                    $iStBlk
                        += (floor($raList[$i]->Size / $this->bigBlockSize)
                            + (($raList[$i]->Size % $this->bigBlockSize) ? 1 : 0));
                }
            }
        }
    }

    /**
     * get small data (PPS's with data smaller than \PhpOffice\PhpSpreadsheet\Shared\OLE::OLE_DATA_SIZE_SMALL).
     *
     * @param array $raList Reference to array of PPS's
     */
    private function makeSmallData(array &$raList): string
    {
        $sRes = '';
        $FILE = $this->fileHandle;
        $iSmBlk = 0;

        $iCount = count($raList);
        for ($i = 0; $i < $iCount; ++$i) {
            // Make SBD, small data string
            if ($raList[$i]->Type == OLE::OLE_PPS_TYPE_FILE) {
                if ($raList[$i]->Size <= 0) {
                    continue;
                }
                if ($raList[$i]->Size < OLE::OLE_DATA_SIZE_SMALL) {
                    $iSmbCnt = floor($raList[$i]->Size / $this->smallBlockSize)
                        + (($raList[$i]->Size % $this->smallBlockSize) ? 1 : 0);
                    // Add to SBD
                    $jB = $iSmbCnt - 1;
                    for ($j = 0; $j < $jB; ++$j) {
                        fwrite($FILE, pack('V', $j + $iSmBlk + 1));
                    }
                    fwrite($FILE, pack('V', -2));

                    // Add to Data String(this will be written for RootEntry)
                    $sRes .= $raList[$i]->_data;
                    if ($raList[$i]->Size % $this->smallBlockSize) {
                        $sRes .= str_repeat("\x00", $this->smallBlockSize - ($raList[$i]->Size % $this->smallBlockSize));
                    }
                    // Set for PPS
                    $raList[$i]->startBlock = $iSmBlk;
                    $iSmBlk += $iSmbCnt;
                }
            }
        }
        $iSbCnt = floor($this->bigBlockSize / OLE::OLE_LONG_INT_SIZE);
        if ($iSmBlk % $iSbCnt) {
            $iB = $iSbCnt - ($iSmBlk % $iSbCnt);
            for ($i = 0; $i < $iB; ++$i) {
                fwrite($FILE, pack('V', -1));
            }
        }

        return $sRes;
    }

    /**
     * Saves all the PPS's WKs.
     *
     * @param array $raList Reference to an array with all PPS's
     */
    private function savePps(array &$raList): void
    {
        // Save each PPS WK
        $iC = count($raList);
        for ($i = 0; $i < $iC; ++$i) {
            fwrite($this->fileHandle, $raList[$i]->getPpsWk());
        }
        // Adjust for Block
        $iCnt = count($raList);
        $iBCnt = $this->bigBlockSize / OLE::OLE_PPS_SIZE;
        if ($iCnt % $iBCnt) {
            fwrite($this->fileHandle, str_repeat("\x00", ($iBCnt - ($iCnt % $iBCnt)) * OLE::OLE_PPS_SIZE));
        }
    }

    /**
     * Saving Big Block Depot.
     */
    private function saveBbd(int $iSbdSize, int $iBsize, int $iPpsCnt): void
    {
        $FILE = $this->fileHandle;
        // Calculate Basic Setting
        $iBbCnt = $this->bigBlockSize / OLE::OLE_LONG_INT_SIZE;
        $i1stBdL = ($this->bigBlockSize - 0x4C) / OLE::OLE_LONG_INT_SIZE;

        $iBdExL = 0;
        $iAll = $iBsize + $iPpsCnt + $iSbdSize;
        $iAllW = $iAll;
        $iBdCntW = floor($iAllW / $iBbCnt) + (($iAllW % $iBbCnt) ? 1 : 0);
        $iBdCnt = floor(($iAll + $iBdCntW) / $iBbCnt) + ((($iAllW + $iBdCntW) % $iBbCnt) ? 1 : 0);
        // Calculate BD count
        if ($iBdCnt > $i1stBdL) {
            while (1) {
                ++$iBdExL;
                ++$iAllW;
                $iBdCntW = floor($iAllW / $iBbCnt) + (($iAllW % $iBbCnt) ? 1 : 0);
                $iBdCnt = floor(($iAllW + $iBdCntW) / $iBbCnt) + ((($iAllW + $iBdCntW) % $iBbCnt) ? 1 : 0);
                if ($iBdCnt <= ($iBdExL * $iBbCnt + $i1stBdL)) {
                    break;
                }
            }
        }

        // Making BD
        // Set for SBD
        if ($iSbdSize > 0) {
            for ($i = 0; $i < ($iSbdSize - 1); ++$i) {
                fwrite($FILE, pack('V', $i + 1));
            }
            fwrite($FILE, pack('V', -2));
        }
        // Set for B
        for ($i = 0; $i < ($iBsize - 1); ++$i) {
            fwrite($FILE, pack('V', $i + $iSbdSize + 1));
        }
        fwrite($FILE, pack('V', -2));

        // Set for PPS
        for ($i = 0; $i < ($iPpsCnt - 1); ++$i) {
            fwrite($FILE, pack('V', $i + $iSbdSize + $iBsize + 1));
        }
        fwrite($FILE, pack('V', -2));
        // Set for BBD itself ( 0xFFFFFFFD : BBD)
        for ($i = 0; $i < $iBdCnt; ++$i) {
            fwrite($FILE, pack('V', 0xFFFFFFFD));
        }
        // Set for ExtraBDList
        for ($i = 0; $i < $iBdExL; ++$i) {
            fwrite($FILE, pack('V', 0xFFFFFFFC));
        }
        // Adjust for Block
        if (($iAllW + $iBdCnt) % $iBbCnt) {
            $iBlock = ($iBbCnt - (($iAllW + $iBdCnt) % $iBbCnt));
            for ($i = 0; $i < $iBlock; ++$i) {
                fwrite($FILE, pack('V', -1));
            }
        }
        // Extra BDList
        if ($iBdCnt > $i1stBdL) {
            $iN = 0;
            $iNb = 0;
            for ($i = $i1stBdL; $i < $iBdCnt; $i++, ++$iN) {
                if ($iN >= ($iBbCnt - 1)) {
                    $iN = 0;
                    ++$iNb;
                    fwrite($FILE, pack('V', $iAll + $iBdCnt + $iNb));
                }
                fwrite($FILE, pack('V', $iBsize + $iSbdSize + $iPpsCnt + $i));
            }
            if (($iBdCnt - $i1stBdL) % ($iBbCnt - 1)) {
                $iB = ($iBbCnt - 1) - (($iBdCnt - $i1stBdL) % ($iBbCnt - 1));
                for ($i = 0; $i < $iB; ++$i) {
                    fwrite($FILE, pack('V', -1));
                }
            }
            fwrite($FILE, pack('V', -2));
        }
    }
}
