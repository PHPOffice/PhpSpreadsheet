<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ListFunctions extends Xls
{
    /**
     * Reads names of the worksheets from a file, without parsing the whole file to a PhpSpreadsheet object.
     *
     * @return string[]
     */
    protected function listWorksheetNames2(string $filename, Xls $xls): array
    {
        File::assertFile($filename);

        $worksheetNames = [];

        // Read the OLE file
        $xls->loadOLE($filename);

        // total byte size of Excel data (workbook global substream + sheet substreams)
        $xls->dataSize = strlen($xls->data);

        $xls->pos = 0;
        $xls->sheets = [];

        // Parse Workbook Global Substream
        while ($xls->pos < $xls->dataSize) {
            $code = self::getUInt2d($xls->data, $xls->pos);

            match ($code) {
                self::XLS_TYPE_BOF => $xls->readBof(),
                self::XLS_TYPE_SHEET => $xls->readSheet(),
                self::XLS_TYPE_EOF => $xls->readDefault(),
                self::XLS_TYPE_CODEPAGE => $xls->readCodepage(),
                default => $xls->readDefault(),
            };

            if ($code === self::XLS_TYPE_EOF) {
                break;
            }
        }

        foreach ($xls->sheets as $sheet) {
            if ($sheet['sheetType'] === 0x00) {
                // 0x00: Worksheet, 0x02: Chart, 0x06: Visual Basic module
                $worksheetNames[] = $sheet['name'];
            }
        }

        return $worksheetNames;
    }

    /**
     * Return worksheet info (Name, Last Column Letter, Last Column Index, Total Rows, Total Columns).
     *
     * @return array<int, array{worksheetName: string, lastColumnLetter: string, lastColumnIndex: int, totalRows: int, totalColumns: int, sheetState: string}>
     */
    protected function listWorksheetInfo2(string $filename, Xls $xls): array
    {
        File::assertFile($filename);

        $worksheetInfo = [];

        // Read the OLE file
        $xls->loadOLE($filename);

        // total byte size of Excel data (workbook global substream + sheet substreams)
        $xls->dataSize = strlen($xls->data);

        // initialize
        $xls->pos = 0;
        $xls->sheets = [];

        // Parse Workbook Global Substream
        while ($xls->pos < $xls->dataSize) {
            $code = self::getUInt2d($xls->data, $xls->pos);

            match ($code) {
                self::XLS_TYPE_BOF => $xls->readBof(),
                self::XLS_TYPE_SHEET => $xls->readSheet(),
                self::XLS_TYPE_EOF => $xls->readDefault(),
                self::XLS_TYPE_CODEPAGE => $xls->readCodepage(),
                default => $xls->readDefault(),
            };

            if ($code === self::XLS_TYPE_EOF) {
                break;
            }
        }

        // Parse the individual sheets
        foreach ($xls->sheets as $sheet) {
            if ($sheet['sheetType'] !== 0x00) {
                // 0x00: Worksheet
                // 0x02: Chart
                // 0x06: Visual Basic module
                continue;
            }

            $tmpInfo = [];
            $tmpInfo['worksheetName'] = StringHelper::convertToString($sheet['name']);
            $tmpInfo['lastColumnLetter'] = 'A';
            $tmpInfo['lastColumnIndex'] = 0;
            $tmpInfo['totalRows'] = 0;
            $tmpInfo['totalColumns'] = 0;
            $tmpInfo['sheetState'] = StringHelper::convertToString($sheet['sheetState']);

            $xls->pos = $sheet['offset'];

            while ($xls->pos <= $xls->dataSize - 4) {
                $code = self::getUInt2d($xls->data, $xls->pos);

                switch ($code) {
                    case self::XLS_TYPE_RK:
                    case self::XLS_TYPE_LABELSST:
                    case self::XLS_TYPE_NUMBER:
                    case self::XLS_TYPE_FORMULA:
                    case self::XLS_TYPE_BOOLERR:
                    case self::XLS_TYPE_LABEL:
                    case self::XLS_TYPE_MULRK:
                        $length = self::getUInt2d($xls->data, $xls->pos + 2);
                        $recordData = $xls->readRecordData($xls->data, $xls->pos + 4, $length);

                        // move stream pointer to next record
                        $xls->pos += 4 + $length;

                        $rowIndex = self::getUInt2d($recordData, 0) + 1;
                        if ($code === self::XLS_TYPE_MULRK) {
                            $columnIndex = self::getUInt2d($recordData, $length - 2);
                        } else {
                            $columnIndex = self::getUInt2d($recordData, 2);
                        }

                        $tmpInfo['totalRows'] = max($tmpInfo['totalRows'], $rowIndex);
                        $tmpInfo['lastColumnIndex'] = max($tmpInfo['lastColumnIndex'], $columnIndex);

                        break;
                    case self::XLS_TYPE_BOF:
                        $xls->readBof();

                        break;
                    case self::XLS_TYPE_EOF:
                        $xls->readDefault();

                        break 2;
                    default:
                        $xls->readDefault();

                        break;
                }
            }

            $tmpInfo['lastColumnLetter'] = Coordinate::stringFromColumnIndex($tmpInfo['lastColumnIndex'] + 1);
            $tmpInfo['totalColumns'] = $tmpInfo['lastColumnIndex'] + 1;

            $worksheetInfo[] = $tmpInfo;
        }

        return $worksheetInfo;
    }

    /**
     * Return worksheet info (Name, Last Column Letter, Last Column Index, Total Rows, Total Columns).
     *
     * @return array<int, array{worksheetName: string, dimensionsMinR: int, dimensionsMinC: int, dimensionsMaxR: int, dimensionsMaxC: int, lastColumnLetter: string}>
     */
    protected function listWorksheetDimensions2(string $filename, Xls $xls): array
    {
        File::assertFile($filename);

        $worksheetInfo = [];

        // Read the OLE file
        $xls->loadOLE($filename);

        // total byte size of Excel data (workbook global substream + sheet substreams)
        $xls->dataSize = strlen($xls->data);

        // initialize
        $xls->pos = 0;
        $xls->sheets = [];

        // Parse Workbook Global Substream
        while ($xls->pos < $xls->dataSize) {
            $code = self::getUInt2d($xls->data, $xls->pos);

            match ($code) {
                self::XLS_TYPE_BOF => $xls->readBof(),
                self::XLS_TYPE_SHEET => $xls->readSheet(),
                self::XLS_TYPE_EOF => $xls->readDefault(),
                self::XLS_TYPE_CODEPAGE => $xls->readCodepage(),
                default => $xls->readDefault(),
            };

            if ($code === self::XLS_TYPE_EOF) {
                break;
            }
        }

        // Parse the individual sheets
        foreach ($xls->sheets as $sheet) {
            if ($sheet['sheetType'] !== 0x00) {
                // 0x00: Worksheet
                // 0x02: Chart
                // 0x06: Visual Basic module
                continue;
            }

            $tmpInfo = [];
            $tmpInfo['worksheetName'] = StringHelper::convertToString($sheet['name']);
            $tmpInfo['dimensionsMinR'] = -1;
            $tmpInfo['dimensionsMaxR'] = -1;
            $tmpInfo['dimensionsMinC'] = -1;
            $tmpInfo['dimensionsMaxC'] = -1;
            $tmpInfo['lastColumnLetter'] = '';

            $xls->pos = $sheet['offset'];

            while ($xls->pos <= $xls->dataSize - 4) {
                $code = self::getUInt2d($xls->data, $xls->pos);

                switch ($code) {
                    case self::XLS_TYPE_BOF:
                        $xls->readBof();

                        break;
                    case self::XLS_TYPE_EOF:
                        $xls->readDefault();

                        break 2;
                    case self::XLS_TYPE_DIMENSION:
                        $length = self::getUInt2d($xls->data, $xls->pos + 2);
                        if ($length === 14) {
                            $dimensionsData = substr($xls->data, $xls->pos + 4, $length);
                            $data = unpack('VrwMic/VrwMac/vcolMic/vcolMac/vreserved', $dimensionsData);
                            if (is_array($data)) {
                                /** @var int[] $data */
                                $tmpInfo['dimensionsMinR'] = $data['rwMic'];
                                $tmpInfo['dimensionsMaxR'] = $data['rwMac'];
                                $tmpInfo['dimensionsMinC'] = $data['colMic'];
                                $tmpInfo['dimensionsMaxC'] = $data['colMac'];
                                $tmpInfo['lastColumnLetter'] = Coordinate::stringFromColumnIndex($tmpInfo['dimensionsMaxC']);
                            }
                        }
                        $xls->readDefault();

                        break;
                    default:
                        $xls->readDefault();

                        break;
                }
            }

            $worksheetInfo[] = $tmpInfo;
        }

        return $worksheetInfo;
    }
}
