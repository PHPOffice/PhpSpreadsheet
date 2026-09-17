<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;

/**
 * Based on PERL Spreadsheet::WriteExcel module (by John McNamara)
 * Ported to PHP for PEAR::Spreadsheet_Excel_Writer_BIFFwriter (by Xavier Noguer)
 * Relicensed under the MIT License by both authors.
 */
class BIFFwriter
{
    /**
     * The byte order of this architecture. 0 => little endian, 1 => big endian.
     */
    private static ?int $byteOrder = null;

    /**
     * The string containing the data of the BIFF stream.
     */
    public ?string $_data;

    /**
     * The size of the data in bytes. Should be the same as strlen($this->_data).
     */
    public int $_datasize;

    /**
     * The maximum length for a BIFF record (excluding record header and length field). See addContinue().
     *
     * @see addContinue()
     */
    private int $limit = 8224;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->_data = '';
        $this->_datasize = 0;
    }

    /**
     * Determine the byte order and store it as class data to avoid
     * recalculating it for each call to new().
     */
    public static function getByteOrder(): int
    {
        if (!isset(self::$byteOrder)) {
            // Check if "pack" gives the required IEEE 64bit float
            $teststr = pack('d', 1.2345);
            $number = pack('C8', 0x8D, 0x97, 0x6E, 0x12, 0x83, 0xC0, 0xF3, 0x3F);
            if ($number == $teststr) {
                $byte_order = 0; // Little Endian
            } elseif ($number == strrev($teststr)) {
                $byte_order = 1; // Big Endian
            } else {
                // Give up. I'll fix this in a later version.
                throw new WriterException('Required floating point format not supported on this platform.');
            }
            self::$byteOrder = $byte_order;
        }

        return self::$byteOrder;
    }

    /**
     * General storage function.
     *
     * @param string $data binary data to append
     */
    protected function append(string $data): void
    {
        if (strlen($data) - 4 > $this->limit) {
            $data = $this->addContinue($data);
        }
        $this->_data .= $data;
        $this->_datasize += strlen($data);
    }

    /**
     * General storage function like append, but returns string instead of modifying $this->_data.
     *
     * @param string $data binary data to write
     */
    public function writeData(string $data): string
    {
        if (strlen($data) - 4 > $this->limit) {
            $data = $this->addContinue($data);
        }
        $this->_datasize += strlen($data);

        return $data;
    }

    /**
     * Writes Excel BOF record to indicate the beginning of a stream or
     * sub-stream in the BIFF file.
     *
     * @param int $type type of BIFF file to write: 0x0005 Workbook,
     *                       0x0010 Worksheet
     */
    protected function storeBof(int $type): void
    {
        $record = 0x0809; // Record identifier    (BIFF5-BIFF8)
        $length = 0x0010;

        // by inspection of real files, MS Office Excel 2007 writes the following
        $unknown = pack('VV', 0x000100D1, 0x00000406);

        $build = 0x0DBB; //    Excel 97
        $year = 0x07CC; //    Excel 97

        $version = 0x0600; //    BIFF8

        $header = pack('vv', $record, $length);
        $data = pack('vvvv', $version, $type, $build, $year);
        $this->append($header . $data . $unknown);
    }

    /**
     * Writes Excel EOF record to indicate the end of a BIFF stream.
     */
    protected function storeEof(): void
    {
        $record = 0x000A; // Record identifier
        $length = 0x0000; // Number of bytes to follow

        $header = pack('vv', $record, $length);
        $this->append($header);
    }

    /**
     * Writes Excel EOF record to indicate the end of a BIFF stream.
     */
    public function writeEof(): string
    {
        $record = 0x000A; // Record identifier
        $length = 0x0000; // Number of bytes to follow
        $header = pack('vv', $record, $length);

        return $this->writeData($header);
    }

    /**
     * Excel limits the size of BIFF records. In Excel 5 the limit is 2084 bytes. In
     * Excel 97 the limit is 8228 bytes. Records that are longer than these limits
     * must be split up into CONTINUE blocks.
     *
     * This function takes a long BIFF record and inserts CONTINUE records as
     * necessary.
     *
     * @param string $data The original binary data to be written
     *
     * @return string A very convenient string of continue blocks
     */
    private function addContinue(string $data): string
    {
        $limit = $this->limit;
        $record = 0x003C; // Record identifier

        // The first 2080/8224 bytes remain intact. However, we have to change
        // the length field of the record.
        $tmp = substr($data, 0, 2) . pack('v', $limit) . substr($data, 4, $limit);

        $header = pack('vv', $record, $limit); // Headers for continue records

        // Retrieve chunks of 2080/8224 bytes +4 for the header.
        $data_length = strlen($data);
        for ($i = $limit + 4; $i < ($data_length - $limit); $i += $limit) {
            $tmp .= $header;
            $tmp .= substr($data, $i, $limit);
        }

        // Retrieve the last chunk of data
        $header = pack('vv', $record, strlen($data) - $i);
        $tmp .= $header;
        $tmp .= substr($data, $i);

        return $tmp;
    }
}
