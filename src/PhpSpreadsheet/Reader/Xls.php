<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Reader\Xls\Style\CellFont;
use PhpOffice\PhpSpreadsheet\Reader\Xls\Style\FillPattern;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\CodePage;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\Escher;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Shared\OLE;
use PhpOffice\PhpSpreadsheet\Shared\OLERead;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\SheetView;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// Original file header of ParseXL (used as the base for this class):
// --------------------------------------------------------------------------------
// Adapted from Excel_Spreadsheet_Reader developed by users bizon153,
// trex005, and mmp11 (SourceForge.net)
// https://sourceforge.net/projects/phpexcelreader/
// Primary changes made by canyoncasa (dvc) for ParseXL 1.00 ...
//     Modelled moreso after Perl Excel Parse/Write modules
//     Added Parse_Excel_Spreadsheet object
//         Reads a whole worksheet or tab as row,column array or as
//         associated hash of indexed rows and named column fields
//     Added variables for worksheet (tab) indexes and names
//     Added an object call for loading individual woorksheets
//     Changed default indexing defaults to 0 based arrays
//     Fixed date/time and percent formats
//     Includes patches found at SourceForge...
//         unicode patch by nobody
//         unpack("d") machine depedency patch by matchy
//         boundsheet utf16 patch by bjaenichen
//     Renamed functions for shorter names
//     General code cleanup and rigor, including <80 column width
//     Included a testcase Excel file and PHP example calls
//     Code works for PHP 5.x

// Primary changes made by canyoncasa (dvc) for ParseXL 1.10 ...
// http://sourceforge.net/tracker/index.php?func=detail&aid=1466964&group_id=99160&atid=623334
//     Decoding of formula conditions, results, and tokens.
//     Support for user-defined named cells added as an array "namedcells"
//         Patch code for user-defined named cells supports single cells only.
//         NOTE: this patch only works for BIFF8 as BIFF5-7 use a different
//         external sheet reference structure
class Xls extends XlsBase
{
    /**
     * Summary Information stream data.
     */
    protected ?string $summaryInformation = null;

    /**
     * Extended Summary Information stream data.
     */
    protected ?string $documentSummaryInformation = null;

    /**
     * Workbook stream data. (Includes workbook globals substream as well as sheet substreams).
     */
    protected string $data;

    /**
     * Size in bytes of $this->data.
     */
    protected int $dataSize;

    /**
     * Current position in stream.
     */
    protected int $pos;

    /**
     * Workbook to be returned by the reader.
     */
    protected Spreadsheet $spreadsheet;

    /**
     * Worksheet that is currently being built by the reader.
     */
    protected Worksheet $phpSheet;

    /**
     * BIFF version.
     */
    protected int $version = 0;

    /**
     * Shared formats.
     *
     * @var mixed[]
     */
    protected array $formats;

    /**
     * Shared fonts.
     *
     * @var Font[]
     */
    protected array $objFonts;

    /**
     * Color palette.
     *
     * @var string[][]
     */
    protected array $palette;

    /**
     * Worksheets.
     *
     * @var array<array{name: string, offset: int, sheetState: string, sheetType: int|string}>
     */
    protected array $sheets;

    /**
     * External books.
     *
     * @var mixed[][]
     */
    protected array $externalBooks;

    /**
     * REF structures. Only applies to BIFF8.
     *
     * @var array<int, array{'externalBookIndex': int, 'firstSheetIndex': int, 'lastSheetIndex': int}>
     */
    protected array $ref;

    /**
     * External names.
     *
     * @var array<array<string, mixed>|string>
     */
    protected array $externalNames;

    /**
     * Defined names.
     *
     * @var array{isBuiltInName: int, name: string, formula: string, scope: int}
     */
    protected array $definedname;

    /**
     * Shared strings. Only applies to BIFF8.
     *
     * @var array<array{value: string, fmtRuns: mixed[]}>
     */
    protected array $sst;

    /**
     * Panes are frozen? (in sheet currently being read). See WINDOW2 record.
     */
    protected bool $frozen;

    /**
     * Fit printout to number of pages? (in sheet currently being read). See SHEETPR record.
     */
    protected bool $isFitToPages;

    /**
     * Objects. One OBJ record contributes with one entry.
     *
     * @var mixed[]
     */
    protected array $objs;

    /**
     * Text Objects. One TXO record corresponds with one entry.
     *
     * @var array<array{text: string, format: string, alignment: int, rotation: int}>
     */
    protected array $textObjects;

    /**
     * Cell Annotations (BIFF8).
     *
     * @var mixed[]
     */
    protected array $cellNotes;

    /**
     * The combined MSODRAWINGGROUP data.
     */
    protected string $drawingGroupData;

    /**
     * The combined MSODRAWING data (per sheet).
     */
    protected string $drawingData;

    /**
     * Keep track of XF index.
     */
    protected int $xfIndex;

    /**
     * Mapping of XF index (that is a cell XF) to final index in cellXf collection.
     *
     * @var int[]
     */
    protected array $mapCellXfIndex;

    /**
     * Mapping of XF index (that is a style XF) to final index in cellStyleXf collection.
     *
     * @var int[]
     */
    protected array $mapCellStyleXfIndex;

    /**
     * The shared formulas in a sheet. One SHAREDFMLA record contributes with one value.
     *
     * @var mixed[]
     */
    protected array $sharedFormulas;

    /**
     * The shared formula parts in a sheet. One FORMULA record contributes with one value if it
     * refers to a shared formula.
     *
     * @var mixed[]
     */
    protected array $sharedFormulaParts;

    /**
     * The type of encryption in use.
     */
    protected int $encryption = 0;

    /**
     * The position in the stream after which contents are encrypted.
     */
    protected int $encryptionStartPos = 0;

    protected string $encryptionPassword = 'VelvetSweatshop';

    /**
     * The current RC4 decryption object.
     */
    protected ?Xls\RC4 $rc4Key = null;

    /**
     * The position in the stream that the RC4 decryption object was left at.
     */
    protected int $rc4Pos = 0;

    /**
     * The current MD5 context state.
     * It is set via call-by-reference to verifyPassword.
     */
    private string $md5Ctxt = '';

    protected int $textObjRef;

    protected string $baseCell;

    protected bool $activeSheetSet = false;

    /**
     * Reads names of the worksheets from a file, without parsing the whole file to a PhpSpreadsheet object.
     *
     * @return string[]
     */
    public function listWorksheetNames(string $filename): array
    {
        return (new Xls\ListFunctions())->listWorksheetNames2($filename, $this);
    }

    /**
     * Return worksheet info (Name, Last Column Letter, Last Column Index, Total Rows, Total Columns).
     *
     * @return array<int, array{worksheetName: string, lastColumnLetter: string, lastColumnIndex: int, totalRows: int, totalColumns: int, sheetState: string}>
     */
    public function listWorksheetInfo(string $filename): array
    {
        return (new Xls\ListFunctions())->listWorksheetInfo2($filename, $this);
    }

    /**
     * Loads PhpSpreadsheet from file.
     */
    protected function loadSpreadsheetFromFile(string $filename): Spreadsheet
    {
        return (new Xls\LoadSpreadsheet())->loadSpreadsheetFromFile2($filename, $this);
    }

    /**
     * Read record data from stream, decrypting as required.
     *
     * @param string $data Data stream to read from
     * @param int $pos Position to start reading from
     * @param int $len Record data length
     *
     * @return string Record data
     */
    protected function readRecordData(string $data, int $pos, int $len): string
    {
        $data = substr($data, $pos, $len);

        // File not encrypted, or record before encryption start point
        if ($this->encryption == self::MS_BIFF_CRYPTO_NONE || $pos < $this->encryptionStartPos) {
            return $data;
        }

        $recordData = '';
        if ($this->encryption == self::MS_BIFF_CRYPTO_RC4) {
            $oldBlock = floor($this->rc4Pos / self::REKEY_BLOCK);
            $block = (int) floor($pos / self::REKEY_BLOCK);
            $endBlock = (int) floor(($pos + $len) / self::REKEY_BLOCK);

            // Spin an RC4 decryptor to the right spot. If we have a decryptor sitting
            // at a point earlier in the current block, re-use it as we can save some time.
            if ($block != $oldBlock || $pos < $this->rc4Pos || !$this->rc4Key) {
                $this->rc4Key = $this->makeKey($block, $this->md5Ctxt);
                $step = $pos % self::REKEY_BLOCK;
            } else {
                $step = $pos - $this->rc4Pos;
            }
            $this->rc4Key->RC4(str_repeat("\0", $step));

            // Decrypt record data (re-keying at the end of every block)
            while ($block != $endBlock) {
                $step = self::REKEY_BLOCK - ($pos % self::REKEY_BLOCK);
                $recordData .= $this->rc4Key->RC4(substr($data, 0, $step));
                $data = substr($data, $step);
                $pos += $step;
                $len -= $step;
                ++$block;
                $this->rc4Key = $this->makeKey($block, $this->md5Ctxt);
            }
            $recordData .= $this->rc4Key->RC4(substr($data, 0, $len));

            // Keep track of the position of this decryptor.
            // We'll try and re-use it later if we can to speed things up
            $this->rc4Pos = $pos + $len;
        } elseif ($this->encryption == self::MS_BIFF_CRYPTO_XOR) {
            throw new Exception('XOr encryption not supported');
        }

        return $recordData;
    }

    /**
     * Use OLE reader to extract the relevant data streams from the OLE file.
     */
    protected function loadOLE(string $filename): void
    {
        // OLE reader
        $ole = new OLERead();
        // get excel data,
        $ole->read($filename);
        // Get workbook data: workbook stream + sheet streams
        $this->data = $ole->getStream($ole->wrkbook); // @phpstan-ignore-line
        // Get summary information data
        $this->summaryInformation = $ole->getStream($ole->summaryInformation);
        // Get additional document summary information data
        $this->documentSummaryInformation = $ole->getStream($ole->documentSummaryInformation);
    }

    /**
     * Read summary information.
     */
    protected function readSummaryInformation(): void
    {
        if (!isset($this->summaryInformation)) {
            return;
        }

        // offset: 0; size: 2; must be 0xFE 0xFF (UTF-16 LE byte order mark)
        // offset: 2; size: 2;
        // offset: 4; size: 2; OS version
        // offset: 6; size: 2; OS indicator
        // offset: 8; size: 16
        // offset: 24; size: 4; section count
        //$secCount = self::getInt4d($this->summaryInformation, 24);

        // offset: 28; size: 16; first section's class id: e0 85 9f f2 f9 4f 68 10 ab 91 08 00 2b 27 b3 d9
        // offset: 44; size: 4
        $secOffset = self::getInt4d($this->summaryInformation, 44);

        // section header
        // offset: $secOffset; size: 4; section length
        //$secLength = self::getInt4d($this->summaryInformation, $secOffset);

        // offset: $secOffset+4; size: 4; property count
        $countProperties = self::getInt4d($this->summaryInformation, $secOffset + 4);

        // initialize code page (used to resolve string values)
        $codePage = 'CP1252';

        // offset: ($secOffset+8); size: var
        // loop through property decarations and properties
        for ($i = 0; $i < $countProperties; ++$i) {
            // offset: ($secOffset+8) + (8 * $i); size: 4; property ID
            $id = self::getInt4d($this->summaryInformation, ($secOffset + 8) + (8 * $i));

            // Use value of property id as appropriate
            // offset: ($secOffset+12) + (8 * $i); size: 4; offset from beginning of section (48)
            $offset = self::getInt4d($this->summaryInformation, ($secOffset + 12) + (8 * $i));

            $type = self::getInt4d($this->summaryInformation, $secOffset + $offset);

            // initialize property value
            $value = null;

            // extract property value based on property type
            switch ($type) {
                case 0x02: // 2 byte signed integer
                    $value = self::getUInt2d($this->summaryInformation, $secOffset + 4 + $offset);

                    break;
                case 0x03: // 4 byte signed integer
                    $value = self::getInt4d($this->summaryInformation, $secOffset + 4 + $offset);

                    break;
                case 0x13: // 4 byte unsigned integer
                    // not needed yet, fix later if necessary
                    break;
                case 0x1E: // null-terminated string prepended by dword string length
                    $byteLength = self::getInt4d($this->summaryInformation, $secOffset + 4 + $offset);
                    $value = substr($this->summaryInformation, $secOffset + 8 + $offset, $byteLength);
                    $value = StringHelper::convertEncoding($value, 'UTF-8', $codePage);
                    $value = rtrim($value);

                    break;
                case 0x40: // Filetime (64-bit value representing the number of 100-nanosecond intervals since January 1, 1601)
                    // PHP-time
                    $value = OLE::OLE2LocalDate(substr($this->summaryInformation, $secOffset + 4 + $offset, 8));

                    break;
                case 0x47: // Clipboard format
                    // not needed yet, fix later if necessary
                    break;
            }

            switch ($id) {
                case 0x01:    //    Code Page
                    $codePage = CodePage::numberToName((int) $value);

                    break;
                case 0x02:    //    Title
                    $this->spreadsheet->getProperties()->setTitle("$value");

                    break;
                case 0x03:    //    Subject
                    $this->spreadsheet->getProperties()->setSubject("$value");

                    break;
                case 0x04:    //    Author (Creator)
                    $this->spreadsheet->getProperties()->setCreator("$value");

                    break;
                case 0x05:    //    Keywords
                    $this->spreadsheet->getProperties()->setKeywords("$value");

                    break;
                case 0x06:    //    Comments (Description)
                    $this->spreadsheet->getProperties()->setDescription("$value");

                    break;
                case 0x07:    //    Template
                    //    Not supported by PhpSpreadsheet
                    break;
                case 0x08:    //    Last Saved By (LastModifiedBy)
                    $this->spreadsheet->getProperties()->setLastModifiedBy("$value");

                    break;
                case 0x09:    //    Revision
                    //    Not supported by PhpSpreadsheet
                    break;
                case 0x0A:    //    Total Editing Time
                    //    Not supported by PhpSpreadsheet
                    break;
                case 0x0B:    //    Last Printed
                    //    Not supported by PhpSpreadsheet
                    break;
                case 0x0C:    //    Created Date/Time
                    $this->spreadsheet->getProperties()->setCreated($value);

                    break;
                case 0x0D:    //    Modified Date/Time
                    $this->spreadsheet->getProperties()->setModified($value);

                    break;
                case 0x0E:    //    Number of Pages
                    //    Not supported by PhpSpreadsheet
                    break;
                case 0x0F:    //    Number of Words
                    //    Not supported by PhpSpreadsheet
                    break;
                case 0x10:    //    Number of Characters
                    //    Not supported by PhpSpreadsheet
                    break;
                case 0x11:    //    Thumbnail
                    //    Not supported by PhpSpreadsheet
                    break;
                case 0x12:    //    Name of creating application
                    //    Not supported by PhpSpreadsheet
                    break;
                case 0x13:    //    Security
                    //    Not supported by PhpSpreadsheet
                    break;
            }
        }
    }

    /**
     * Read additional document summary information.
     */
    protected function readDocumentSummaryInformation(): void
    {
        if (!isset($this->documentSummaryInformation)) {
            return;
        }

        //    offset: 0;    size: 2;    must be 0xFE 0xFF (UTF-16 LE byte order mark)
        //    offset: 2;    size: 2;
        //    offset: 4;    size: 2;    OS version
        //    offset: 6;    size: 2;    OS indicator
        //    offset: 8;    size: 16
        //    offset: 24;    size: 4;    section count
        //$secCount = self::getInt4d($this->documentSummaryInformation, 24);

        // offset: 28;    size: 16;    first section's class id: 02 d5 cd d5 9c 2e 1b 10 93 97 08 00 2b 2c f9 ae
        // offset: 44;    size: 4;    first section offset
        $secOffset = self::getInt4d($this->documentSummaryInformation, 44);

        //    section header
        //    offset: $secOffset;    size: 4;    section length
        //$secLength = self::getInt4d($this->documentSummaryInformation, $secOffset);

        //    offset: $secOffset+4;    size: 4;    property count
        $countProperties = self::getInt4d($this->documentSummaryInformation, $secOffset + 4);

        // initialize code page (used to resolve string values)
        $codePage = 'CP1252';

        //    offset: ($secOffset+8);    size: var
        //    loop through property decarations and properties
        for ($i = 0; $i < $countProperties; ++$i) {
            //    offset: ($secOffset+8) + (8 * $i);    size: 4;    property ID
            $id = self::getInt4d($this->documentSummaryInformation, ($secOffset + 8) + (8 * $i));

            // Use value of property id as appropriate
            // offset: 60 + 8 * $i;    size: 4;    offset from beginning of section (48)
            $offset = self::getInt4d($this->documentSummaryInformation, ($secOffset + 12) + (8 * $i));

            $type = self::getInt4d($this->documentSummaryInformation, $secOffset + $offset);

            // initialize property value
            $value = null;

            // extract property value based on property type
            switch ($type) {
                case 0x02:    //    2 byte signed integer
                    $value = self::getUInt2d($this->documentSummaryInformation, $secOffset + 4 + $offset);

                    break;
                case 0x03:    //    4 byte signed integer
                    $value = self::getInt4d($this->documentSummaryInformation, $secOffset + 4 + $offset);

                    break;
                case 0x0B:  // Boolean
                    $value = self::getUInt2d($this->documentSummaryInformation, $secOffset + 4 + $offset);
                    $value = ($value == 0 ? false : true);

                    break;
                case 0x13:    //    4 byte unsigned integer
                    // not needed yet, fix later if necessary
                    break;
                case 0x1E:    //    null-terminated string prepended by dword string length
                    $byteLength = self::getInt4d($this->documentSummaryInformation, $secOffset + 4 + $offset);
                    $value = substr($this->documentSummaryInformation, $secOffset + 8 + $offset, $byteLength);
                    $value = StringHelper::convertEncoding($value, 'UTF-8', $codePage);
                    $value = rtrim($value);

                    break;
                case 0x40:    //    Filetime (64-bit value representing the number of 100-nanosecond intervals since January 1, 1601)
                    // PHP-Time
                    $value = OLE::OLE2LocalDate(substr($this->documentSummaryInformation, $secOffset + 4 + $offset, 8));

                    break;
                case 0x47:    //    Clipboard format
                    // not needed yet, fix later if necessary
                    break;
            }

            switch ($id) {
                case 0x01:    //    Code Page
                    $codePage = CodePage::numberToName((int) $value);

                    break;
                case 0x02:    //    Category
                    $this->spreadsheet->getProperties()->setCategory("$value");

                    break;
                case 0x03:    //    Presentation Target
                    //    Not supported by PhpSpreadsheet
                    break;
                case 0x04:    //    Bytes
                    //    Not supported by PhpSpreadsheet
                    break;
                case 0x05:    //    Lines
                    //    Not supported by PhpSpreadsheet
                    break;
                case 0x06:    //    Paragraphs
                    //    Not supported by PhpSpreadsheet
                    break;
                case 0x07:    //    Slides
                    //    Not supported by PhpSpreadsheet
                    break;
                case 0x08:    //    Notes
                    //    Not supported by PhpSpreadsheet
                    break;
                case 0x09:    //    Hidden Slides
                    //    Not supported by PhpSpreadsheet
                    break;
                case 0x0A:    //    MM Clips
                    //    Not supported by PhpSpreadsheet
                    break;
                case 0x0B:    //    Scale Crop
                    //    Not supported by PhpSpreadsheet
                    break;
                case 0x0C:    //    Heading Pairs
                    //    Not supported by PhpSpreadsheet
                    break;
                case 0x0D:    //    Titles of Parts
                    //    Not supported by PhpSpreadsheet
                    break;
                case 0x0E:    //    Manager
                    $this->spreadsheet->getProperties()->setManager("$value");

                    break;
                case 0x0F:    //    Company
                    $this->spreadsheet->getProperties()->setCompany("$value");

                    break;
                case 0x10:    //    Links up-to-date
                    //    Not supported by PhpSpreadsheet
                    break;
            }
        }
    }

    /**
     * Reads a general type of BIFF record. Does nothing except for moving stream pointer forward to next record.
     */
    protected function readDefault(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);

        // move stream pointer to next record
        $this->pos += 4 + $length;
    }

    /**
     *    The NOTE record specifies a comment associated with a particular cell. In Excel 95 (BIFF7) and earlier versions,
     *        this record stores a note (cell note). This feature was significantly enhanced in Excel 97.
     */
    protected function readNote(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->readDataOnly) {
            return;
        }

        $cellAddress = Xls\Biff8::readBIFF8CellAddress(substr($recordData, 0, 4));
        if ($this->version == self::XLS_BIFF8) {
            $noteObjID = self::getUInt2d($recordData, 6);
            $noteAuthor = self::readUnicodeStringLong(substr($recordData, 8));
            $noteAuthor = $noteAuthor['value'];
            $this->cellNotes[$noteObjID] = [
                'cellRef' => $cellAddress,
                'objectID' => $noteObjID,
                'author' => $noteAuthor,
            ];
        } else {
            $extension = false;
            if ($cellAddress == '$B$65536') {
                //    If the address row is -1 and the column is 0, (which translates as $B$65536) then this is a continuation
                //        note from the previous cell annotation. We're not yet handling this, so annotations longer than the
                //        max 2048 bytes will probably throw a wobbly.
                //$row = self::getUInt2d($recordData, 0);
                $extension = true;
                $arrayKeys = array_keys($this->phpSheet->getComments());
                $cellAddress = array_pop($arrayKeys);
            }

            $cellAddress = str_replace('$', '', (string) $cellAddress);
            //$noteLength = self::getUInt2d($recordData, 4);
            $noteText = trim(substr($recordData, 6));

            if ($extension) {
                //    Concatenate this extension with the currently set comment for the cell
                $comment = $this->phpSheet->getComment($cellAddress);
                $commentText = $comment->getText()->getPlainText();
                $comment->setText($this->parseRichText($commentText . $noteText));
            } else {
                //    Set comment for the cell
                $this->phpSheet->getComment($cellAddress)->setText($this->parseRichText($noteText));
//                                                    ->setAuthor($author)
            }
        }
    }

    /**
     * The TEXT Object record contains the text associated with a cell annotation.
     */
    protected function readTextObject(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->readDataOnly) {
            return;
        }

        // recordData consists of an array of subrecords looking like this:
        //    grbit: 2 bytes; Option Flags
        //    rot: 2 bytes; rotation
        //    cchText: 2 bytes; length of the text (in the first continue record)
        //    cbRuns: 2 bytes; length of the formatting (in the second continue record)
        // followed by the continuation records containing the actual text and formatting
        $grbitOpts = self::getUInt2d($recordData, 0);
        $rot = self::getUInt2d($recordData, 2);
        //$cchText = self::getUInt2d($recordData, 10);
        $cbRuns = self::getUInt2d($recordData, 12);
        $text = $this->getSplicedRecordData();

        /** @var int[] */
        $tempSplice = $text['spliceOffsets'];
        /** @var int */
        $temp = $tempSplice[0];
        /** @var int */
        $temp1 = $tempSplice[1];
        $textByte = $temp1 - $temp - 1;
        /** @var string */
        $textRecordData = $text['recordData'];
        $textStr = substr($textRecordData, $temp + 1, $textByte);
        // get 1 byte
        $is16Bit = ord($textRecordData[0]);
        // it is possible to use a compressed format,
        // which omits the high bytes of all characters, if they are all zero
        if (($is16Bit & 0x01) === 0) {
            $textStr = StringHelper::ConvertEncoding($textStr, 'UTF-8', 'ISO-8859-1');
        } else {
            $textStr = $this->decodeCodepage($textStr);
        }

        $this->textObjects[$this->textObjRef] = [
            'text' => $textStr,
            'format' => substr($textRecordData, $tempSplice[1], $cbRuns),
            'alignment' => $grbitOpts,
            'rotation' => $rot,
        ];
    }

    /**
     * Read BOF.
     */
    protected function readBof(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = substr($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 2; size: 2; type of the following data
        $substreamType = self::getUInt2d($recordData, 2);

        switch ($substreamType) {
            case self::XLS_WORKBOOKGLOBALS:
                $version = self::getUInt2d($recordData, 0);
                if (($version != self::XLS_BIFF8) && ($version != self::XLS_BIFF7)) {
                    throw new Exception('Cannot read this Excel file. Version is too old.');
                }
                $this->version = $version;

                break;
            case self::XLS_WORKSHEET:
                // do not use this version information for anything
                // it is unreliable (OpenOffice doc, 5.8), use only version information from the global stream
                break;
            default:
                // substream, e.g. chart
                // just skip the entire substream
                do {
                    $code = self::getUInt2d($this->data, $this->pos);
                    $this->readDefault();
                } while ($code != self::XLS_TYPE_EOF && $this->pos < $this->dataSize);

                break;
        }
    }

    public function setEncryptionPassword(string $encryptionPassword): self
    {
        $this->encryptionPassword = $encryptionPassword;

        return $this;
    }

    /**
     * FILEPASS.
     *
     * This record is part of the File Protection Block. It
     * contains information about the read/write password of the
     * file. All record contents following this record will be
     * encrypted.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     *
     * The decryption functions and objects used from here on in
     * are based on the source of Spreadsheet-ParseExcel:
     * https://metacpan.org/release/Spreadsheet-ParseExcel
     */
    protected function readFilepass(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);

        if ($length < 54) {
            throw new Exception('Unexpected file pass record length');
        }

        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (substr($recordData, 0, 2) !== "\x01\x00" || substr($recordData, 4, 2) !== "\x01\x00") {
            throw new Exception('Unsupported encryption algorithm');
        }
        if (!$this->verifyPassword($this->encryptionPassword, substr($recordData, 6, 16), substr($recordData, 22, 16), substr($recordData, 38, 16), $this->md5Ctxt)) {
            throw new Exception('Decryption password incorrect');
        }

        $this->encryption = self::MS_BIFF_CRYPTO_RC4;

        // Decryption required from the record after next onwards
        $this->encryptionStartPos = $this->pos + self::getUInt2d($this->data, $this->pos + 2);
    }

    /**
     * Make an RC4 decryptor for the given block.
     *
     * @param int $block Block for which to create decrypto
     * @param string $valContext MD5 context state
     */
    private function makeKey(int $block, string $valContext): Xls\RC4
    {
        $pwarray = str_repeat("\0", 64);

        for ($i = 0; $i < 5; ++$i) {
            $pwarray[$i] = $valContext[$i];
        }

        $pwarray[5] = chr($block & 0xFF);
        $pwarray[6] = chr(($block >> 8) & 0xFF);
        $pwarray[7] = chr(($block >> 16) & 0xFF);
        $pwarray[8] = chr(($block >> 24) & 0xFF);

        $pwarray[9] = "\x80";
        $pwarray[56] = "\x48";

        $md5 = new Xls\MD5();
        $md5->add($pwarray);

        $s = $md5->getContext();

        return new Xls\RC4($s);
    }

    /**
     * Verify RC4 file password.
     *
     * @param string $password Password to check
     * @param string $docid Document id
     * @param string $salt_data Salt data
     * @param string $hashedsalt_data Hashed salt data
     * @param string $valContext Set to the MD5 context of the value
     *
     * @return bool Success
     */
    private function verifyPassword(string $password, string $docid, string $salt_data, string $hashedsalt_data, string &$valContext): bool
    {
        $pwarray = str_repeat("\0", 64);

        $iMax = strlen($password);
        for ($i = 0; $i < $iMax; ++$i) {
            $o = ord(substr($password, $i, 1));
            $pwarray[2 * $i] = chr($o & 0xFF);
            $pwarray[2 * $i + 1] = chr(($o >> 8) & 0xFF);
        }
        $pwarray[2 * $i] = chr(0x80);
        $pwarray[56] = chr(($i << 4) & 0xFF);

        $md5 = new Xls\MD5();
        $md5->add($pwarray);

        $mdContext1 = $md5->getContext();

        $offset = 0;
        $keyoffset = 0;
        $tocopy = 5;

        $md5->reset();

        while ($offset != 16) {
            if ((64 - $offset) < 5) {
                $tocopy = 64 - $offset;
            }
            for ($i = 0; $i <= $tocopy; ++$i) {
                $pwarray[$offset + $i] = $mdContext1[$keyoffset + $i];
            }
            $offset += $tocopy;

            if ($offset == 64) {
                $md5->add($pwarray);
                $keyoffset = $tocopy;
                $tocopy = 5 - $tocopy;
                $offset = 0;

                continue;
            }

            $keyoffset = 0;
            $tocopy = 5;
            for ($i = 0; $i < 16; ++$i) {
                $pwarray[$offset + $i] = $docid[$i];
            }
            $offset += 16;
        }

        $pwarray[16] = "\x80";
        for ($i = 0; $i < 47; ++$i) {
            $pwarray[17 + $i] = "\0";
        }
        $pwarray[56] = "\x80";
        $pwarray[57] = "\x0a";

        $md5->add($pwarray);
        $valContext = $md5->getContext();

        $key = $this->makeKey(0, $valContext);

        $salt = $key->RC4($salt_data);
        $hashedsalt = $key->RC4($hashedsalt_data);

        $salt .= "\x80" . str_repeat("\0", 47);
        $salt[56] = "\x80";

        $md5->reset();
        $md5->add($salt);
        $mdContext2 = $md5->getContext();

        return $mdContext2 == $hashedsalt;
    }

    /**
     * CODEPAGE.
     *
     * This record stores the text encoding used to write byte
     * strings, stored as MS Windows code page identifier.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    protected function readCodepage(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; code page identifier
        $codepage = self::getUInt2d($recordData, 0);

        $this->codepage = CodePage::numberToName($codepage);
    }

    /**
     * DATEMODE.
     *
     * This record specifies the base date for displaying date
     * values. All dates are stored as count of days past this
     * base date. In BIFF2-BIFF4 this record is part of the
     * Calculation Settings Block. In BIFF5-BIFF8 it is
     * stored in the Workbook Globals Substream.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    protected function readDateMode(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; 0 = base 1900, 1 = base 1904
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
        $this->spreadsheet->setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
        if (ord($recordData[0]) == 1) {
            Date::setExcelCalendar(Date::CALENDAR_MAC_1904);
            $this->spreadsheet->setExcelCalendar(Date::CALENDAR_MAC_1904);
        }
    }

    /**
     * Read a FONT record.
     */
    protected function readFont(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            $objFont = new Font();

            // offset: 0; size: 2; height of the font (in twips = 1/20 of a point)
            $size = self::getUInt2d($recordData, 0);
            $objFont->setSize($size / 20);

            // offset: 2; size: 2; option flags
            // bit: 0; mask 0x0001; bold (redundant in BIFF5-BIFF8)
            // bit: 1; mask 0x0002; italic
            $isItalic = (0x0002 & self::getUInt2d($recordData, 2)) >> 1;
            if ($isItalic) {
                $objFont->setItalic(true);
            }

            // bit: 2; mask 0x0004; underlined (redundant in BIFF5-BIFF8)
            // bit: 3; mask 0x0008; strikethrough
            $isStrike = (0x0008 & self::getUInt2d($recordData, 2)) >> 3;
            if ($isStrike) {
                $objFont->setStrikethrough(true);
            }

            // offset: 4; size: 2; colour index
            $colorIndex = self::getUInt2d($recordData, 4);
            $objFont->colorIndex = $colorIndex;

            // offset: 6; size: 2; font weight
            $weight = self::getUInt2d($recordData, 6); // regular=400 bold=700
            if ($weight >= 550) {
                $objFont->setBold(true);
            }

            // offset: 8; size: 2; escapement type
            $escapement = self::getUInt2d($recordData, 8);
            CellFont::escapement($objFont, $escapement);

            // offset: 10; size: 1; underline type
            $underlineType = ord($recordData[10]);
            CellFont::underline($objFont, $underlineType);

            // offset: 11; size: 1; font family
            // offset: 12; size: 1; character set
            // offset: 13; size: 1; not used
            // offset: 14; size: var; font name
            if ($this->version == self::XLS_BIFF8) {
                $string = self::readUnicodeStringShort(substr($recordData, 14));
            } else {
                $string = $this->readByteStringShort(substr($recordData, 14));
            }
            /** @var string[] $string */
            $objFont->setName($string['value']);

            $this->objFonts[] = $objFont;
        }
    }

    /**
     * FORMAT.
     *
     * This record contains information about a number format.
     * All FORMAT records occur together in a sequential list.
     *
     * In BIFF2-BIFF4 other records referencing a FORMAT record
     * contain a zero-based index into this list. From BIFF5 on
     * the FORMAT record contains the index itself that will be
     * used by other records.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    protected function readFormat(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            $indexCode = self::getUInt2d($recordData, 0);

            if ($this->version == self::XLS_BIFF8) {
                $string = self::readUnicodeStringLong(substr($recordData, 2));
            } else {
                // BIFF7
                $string = $this->readByteStringShort(substr($recordData, 2));
            }

            $formatString = $string['value'];
            // Apache Open Office sets wrong case writing to xls - issue 2239
            if ($formatString === 'GENERAL') {
                $formatString = NumberFormat::FORMAT_GENERAL;
            }
            $this->formats[$indexCode] = $formatString;
        }
    }

    /**
     * XF - Extended Format.
     *
     * This record contains formatting information for cells, rows, columns or styles.
     * According to https://support.microsoft.com/en-us/help/147732 there are always at least 15 cell style XF
     * and 1 cell XF.
     * Inspection of Excel files generated by MS Office Excel shows that XF records 0-14 are cell style XF
     * and XF record 15 is a cell XF
     * We only read the first cell style XF and skip the remaining cell style XF records
     * We read all cell XF records.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    protected function readXf(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        $objStyle = new Style();

        if (!$this->readDataOnly) {
            // offset:  0; size: 2; Index to FONT record
            if (self::getUInt2d($recordData, 0) < 4) {
                $fontIndex = self::getUInt2d($recordData, 0);
            } else {
                // this has to do with that index 4 is omitted in all BIFF versions for some strange reason
                // check the OpenOffice documentation of the FONT record
                $fontIndex = self::getUInt2d($recordData, 0) - 1;
            }
            if (isset($this->objFonts[$fontIndex])) {
                $objStyle->setFont($this->objFonts[$fontIndex]);
            }

            // offset:  2; size: 2; Index to FORMAT record
            $numberFormatIndex = self::getUInt2d($recordData, 2);
            if (isset($this->formats[$numberFormatIndex])) {
                // then we have user-defined format code
                $numberFormat = ['formatCode' => $this->formats[$numberFormatIndex]];
            } elseif (($code = NumberFormat::builtInFormatCode($numberFormatIndex)) !== '') {
                // then we have built-in format code
                $numberFormat = ['formatCode' => $code];
            } else {
                // we set the general format code
                $numberFormat = ['formatCode' => NumberFormat::FORMAT_GENERAL];
            }
            /** @var string[] $numberFormat */
            $objStyle->getNumberFormat()
                ->setFormatCode($numberFormat['formatCode']);

            // offset:  4; size: 2; XF type, cell protection, and parent style XF
            // bit 2-0; mask 0x0007; XF_TYPE_PROT
            $xfTypeProt = self::getUInt2d($recordData, 4);
            // bit 0; mask 0x01; 1 = cell is locked
            $isLocked = (0x01 & $xfTypeProt) >> 0;
            $objStyle->getProtection()->setLocked($isLocked ? Protection::PROTECTION_INHERIT : Protection::PROTECTION_UNPROTECTED);

            // bit 1; mask 0x02; 1 = Formula is hidden
            $isHidden = (0x02 & $xfTypeProt) >> 1;
            $objStyle->getProtection()->setHidden($isHidden ? Protection::PROTECTION_PROTECTED : Protection::PROTECTION_UNPROTECTED);

            // bit 2; mask 0x04; 0 = Cell XF, 1 = Cell Style XF
            $isCellStyleXf = (0x04 & $xfTypeProt) >> 2;

            // offset:  6; size: 1; Alignment and text break
            // bit 2-0, mask 0x07; horizontal alignment
            $horAlign = (0x07 & ord($recordData[6])) >> 0;
            Xls\Style\CellAlignment::horizontal($objStyle->getAlignment(), $horAlign);

            // bit 3, mask 0x08; wrap text
            $wrapText = (0x08 & ord($recordData[6])) >> 3;
            Xls\Style\CellAlignment::wrap($objStyle->getAlignment(), $wrapText);

            // bit 6-4, mask 0x70; vertical alignment
            $vertAlign = (0x70 & ord($recordData[6])) >> 4;
            Xls\Style\CellAlignment::vertical($objStyle->getAlignment(), $vertAlign);

            if ($this->version == self::XLS_BIFF8) {
                // offset:  7; size: 1; XF_ROTATION: Text rotation angle
                $angle = ord($recordData[7]);
                $rotation = 0;
                if ($angle <= 90) {
                    $rotation = $angle;
                } elseif ($angle <= 180) {
                    $rotation = 90 - $angle;
                } elseif ($angle == Alignment::TEXTROTATION_STACK_EXCEL) {
                    $rotation = Alignment::TEXTROTATION_STACK_PHPSPREADSHEET;
                }
                $objStyle->getAlignment()->setTextRotation($rotation);

                // offset:  8; size: 1; Indentation, shrink to cell size, and text direction
                // bit: 3-0; mask: 0x0F; indent level
                $indent = (0x0F & ord($recordData[8])) >> 0;
                $objStyle->getAlignment()->setIndent($indent);

                // bit: 4; mask: 0x10; 1 = shrink content to fit into cell
                $shrinkToFit = (0x10 & ord($recordData[8])) >> 4;
                switch ($shrinkToFit) {
                    case 0:
                        $objStyle->getAlignment()->setShrinkToFit(false);

                        break;
                    case 1:
                        $objStyle->getAlignment()->setShrinkToFit(true);

                        break;
                }

                // offset:  9; size: 1; Flags used for attribute groups

                // offset: 10; size: 4; Cell border lines and background area
                // bit: 3-0; mask: 0x0000000F; left style
                if ($bordersLeftStyle = Xls\Style\Border::lookup((0x0000000F & self::getInt4d($recordData, 10)) >> 0)) {
                    $objStyle->getBorders()->getLeft()->setBorderStyle($bordersLeftStyle);
                }
                // bit: 7-4; mask: 0x000000F0; right style
                if ($bordersRightStyle = Xls\Style\Border::lookup((0x000000F0 & self::getInt4d($recordData, 10)) >> 4)) {
                    $objStyle->getBorders()->getRight()->setBorderStyle($bordersRightStyle);
                }
                // bit: 11-8; mask: 0x00000F00; top style
                if ($bordersTopStyle = Xls\Style\Border::lookup((0x00000F00 & self::getInt4d($recordData, 10)) >> 8)) {
                    $objStyle->getBorders()->getTop()->setBorderStyle($bordersTopStyle);
                }
                // bit: 15-12; mask: 0x0000F000; bottom style
                if ($bordersBottomStyle = Xls\Style\Border::lookup((0x0000F000 & self::getInt4d($recordData, 10)) >> 12)) {
                    $objStyle->getBorders()->getBottom()->setBorderStyle($bordersBottomStyle);
                }
                // bit: 22-16; mask: 0x007F0000; left color
                $objStyle->getBorders()->getLeft()->colorIndex = (0x007F0000 & self::getInt4d($recordData, 10)) >> 16;

                // bit: 29-23; mask: 0x3F800000; right color
                $objStyle->getBorders()->getRight()->colorIndex = (0x3F800000 & self::getInt4d($recordData, 10)) >> 23;

                // bit: 30; mask: 0x40000000; 1 = diagonal line from top left to right bottom
                $diagonalDown = (0x40000000 & self::getInt4d($recordData, 10)) >> 30 ? true : false;

                // bit: 31; mask: 0x800000; 1 = diagonal line from bottom left to top right
                $diagonalUp = (self::HIGH_ORDER_BIT & self::getInt4d($recordData, 10)) >> 31 ? true : false;

                if ($diagonalUp === false) {
                    if ($diagonalDown === false) {
                        $objStyle->getBorders()->setDiagonalDirection(Borders::DIAGONAL_NONE);
                    } else {
                        $objStyle->getBorders()->setDiagonalDirection(Borders::DIAGONAL_DOWN);
                    }
                } elseif ($diagonalDown === false) {
                    $objStyle->getBorders()->setDiagonalDirection(Borders::DIAGONAL_UP);
                } else {
                    $objStyle->getBorders()->setDiagonalDirection(Borders::DIAGONAL_BOTH);
                }

                // offset: 14; size: 4;
                // bit: 6-0; mask: 0x0000007F; top color
                $objStyle->getBorders()->getTop()->colorIndex = (0x0000007F & self::getInt4d($recordData, 14)) >> 0;

                // bit: 13-7; mask: 0x00003F80; bottom color
                $objStyle->getBorders()->getBottom()->colorIndex = (0x00003F80 & self::getInt4d($recordData, 14)) >> 7;

                // bit: 20-14; mask: 0x001FC000; diagonal color
                $objStyle->getBorders()->getDiagonal()->colorIndex = (0x001FC000 & self::getInt4d($recordData, 14)) >> 14;

                // bit: 24-21; mask: 0x01E00000; diagonal style
                if ($bordersDiagonalStyle = Xls\Style\Border::lookup((0x01E00000 & self::getInt4d($recordData, 14)) >> 21)) {
                    $objStyle->getBorders()->getDiagonal()->setBorderStyle($bordersDiagonalStyle);
                }

                // bit: 31-26; mask: 0xFC000000 fill pattern
                if ($fillType = FillPattern::lookup((self::FC000000 & self::getInt4d($recordData, 14)) >> 26)) {
                    $objStyle->getFill()->setFillType($fillType);
                }
                // offset: 18; size: 2; pattern and background colour
                // bit: 6-0; mask: 0x007F; color index for pattern color
                $objStyle->getFill()->startcolorIndex = (0x007F & self::getUInt2d($recordData, 18)) >> 0;

                // bit: 13-7; mask: 0x3F80; color index for pattern background
                $objStyle->getFill()->endcolorIndex = (0x3F80 & self::getUInt2d($recordData, 18)) >> 7;
            } else {
                // BIFF5

                // offset: 7; size: 1; Text orientation and flags
                $orientationAndFlags = ord($recordData[7]);

                // bit: 1-0; mask: 0x03; XF_ORIENTATION: Text orientation
                $xfOrientation = (0x03 & $orientationAndFlags) >> 0;
                switch ($xfOrientation) {
                    case 0:
                        $objStyle->getAlignment()->setTextRotation(0);

                        break;
                    case 1:
                        $objStyle->getAlignment()->setTextRotation(Alignment::TEXTROTATION_STACK_PHPSPREADSHEET);

                        break;
                    case 2:
                        $objStyle->getAlignment()->setTextRotation(90);

                        break;
                    case 3:
                        $objStyle->getAlignment()->setTextRotation(-90);

                        break;
                }

                // offset: 8; size: 4; cell border lines and background area
                $borderAndBackground = self::getInt4d($recordData, 8);

                // bit: 6-0; mask: 0x0000007F; color index for pattern color
                $objStyle->getFill()->startcolorIndex = (0x0000007F & $borderAndBackground) >> 0;

                // bit: 13-7; mask: 0x00003F80; color index for pattern background
                $objStyle->getFill()->endcolorIndex = (0x00003F80 & $borderAndBackground) >> 7;

                // bit: 21-16; mask: 0x003F0000; fill pattern
                $objStyle->getFill()->setFillType(FillPattern::lookup((0x003F0000 & $borderAndBackground) >> 16));

                // bit: 24-22; mask: 0x01C00000; bottom line style
                $objStyle->getBorders()->getBottom()->setBorderStyle(Xls\Style\Border::lookup((0x01C00000 & $borderAndBackground) >> 22));

                // bit: 31-25; mask: 0xFE000000; bottom line color
                $objStyle->getBorders()->getBottom()->colorIndex = (self::FE000000 & $borderAndBackground) >> 25;

                // offset: 12; size: 4; cell border lines
                $borderLines = self::getInt4d($recordData, 12);

                // bit: 2-0; mask: 0x00000007; top line style
                $objStyle->getBorders()->getTop()->setBorderStyle(Xls\Style\Border::lookup((0x00000007 & $borderLines) >> 0));

                // bit: 5-3; mask: 0x00000038; left line style
                $objStyle->getBorders()->getLeft()->setBorderStyle(Xls\Style\Border::lookup((0x00000038 & $borderLines) >> 3));

                // bit: 8-6; mask: 0x000001C0; right line style
                $objStyle->getBorders()->getRight()->setBorderStyle(Xls\Style\Border::lookup((0x000001C0 & $borderLines) >> 6));

                // bit: 15-9; mask: 0x0000FE00; top line color index
                $objStyle->getBorders()->getTop()->colorIndex = (0x0000FE00 & $borderLines) >> 9;

                // bit: 22-16; mask: 0x007F0000; left line color index
                $objStyle->getBorders()->getLeft()->colorIndex = (0x007F0000 & $borderLines) >> 16;

                // bit: 29-23; mask: 0x3F800000; right line color index
                $objStyle->getBorders()->getRight()->colorIndex = (0x3F800000 & $borderLines) >> 23;
            }

            // add cellStyleXf or cellXf and update mapping
            if ($isCellStyleXf) {
                // we only read one style XF record which is always the first
                if ($this->xfIndex == 0) {
                    $this->spreadsheet->addCellStyleXf($objStyle);
                    $this->mapCellStyleXfIndex[$this->xfIndex] = 0;
                }
            } else {
                // we read all cell XF records
                $this->spreadsheet->addCellXf($objStyle);
                $this->mapCellXfIndex[$this->xfIndex] = count($this->spreadsheet->getCellXfCollection()) - 1;
            }

            // update XF index for when we read next record
            ++$this->xfIndex;
        }
    }

    protected function readXfExt(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 2; 0x087D = repeated header

            // offset: 2; size: 2

            // offset: 4; size: 8; not used

            // offset: 12; size: 2; record version

            // offset: 14; size: 2; index to XF record which this record modifies
            $ixfe = self::getUInt2d($recordData, 14);

            // offset: 16; size: 2; not used

            // offset: 18; size: 2; number of extension properties that follow
            //$cexts = self::getUInt2d($recordData, 18);

            // start reading the actual extension data
            $offset = 20;
            while ($offset < $length) {
                // extension type
                $extType = self::getUInt2d($recordData, $offset);

                // extension length
                $cb = self::getUInt2d($recordData, $offset + 2);

                // extension data
                $extData = substr($recordData, $offset + 4, $cb);

                switch ($extType) {
                    case 4:        // fill start color
                        $xclfType = self::getUInt2d($extData, 0); // color type
                        $xclrValue = substr($extData, 4, 4); // color value (value based on color type)

                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue[0]), ord($xclrValue[1]), ord($xclrValue[2]));

                            // modify the relevant style property
                            if (isset($this->mapCellXfIndex[$ixfe])) {
                                $fill = $this->spreadsheet->getCellXfByIndex($this->mapCellXfIndex[$ixfe])->getFill();
                                $fill->getStartColor()->setRGB($rgb);
                                $fill->startcolorIndex = null; // normal color index does not apply, discard
                            }
                        }

                        break;
                    case 5:        // fill end color
                        $xclfType = self::getUInt2d($extData, 0); // color type
                        $xclrValue = substr($extData, 4, 4); // color value (value based on color type)

                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue[0]), ord($xclrValue[1]), ord($xclrValue[2]));

                            // modify the relevant style property
                            if (isset($this->mapCellXfIndex[$ixfe])) {
                                $fill = $this->spreadsheet->getCellXfByIndex($this->mapCellXfIndex[$ixfe])->getFill();
                                $fill->getEndColor()->setRGB($rgb);
                                $fill->endcolorIndex = null; // normal color index does not apply, discard
                            }
                        }

                        break;
                    case 7:        // border color top
                        $xclfType = self::getUInt2d($extData, 0); // color type
                        $xclrValue = substr($extData, 4, 4); // color value (value based on color type)

                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue[0]), ord($xclrValue[1]), ord($xclrValue[2]));

                            // modify the relevant style property
                            if (isset($this->mapCellXfIndex[$ixfe])) {
                                $top = $this->spreadsheet->getCellXfByIndex($this->mapCellXfIndex[$ixfe])->getBorders()->getTop();
                                $top->getColor()->setRGB($rgb);
                                $top->colorIndex = null; // normal color index does not apply, discard
                            }
                        }

                        break;
                    case 8:        // border color bottom
                        $xclfType = self::getUInt2d($extData, 0); // color type
                        $xclrValue = substr($extData, 4, 4); // color value (value based on color type)

                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue[0]), ord($xclrValue[1]), ord($xclrValue[2]));

                            // modify the relevant style property
                            if (isset($this->mapCellXfIndex[$ixfe])) {
                                $bottom = $this->spreadsheet->getCellXfByIndex($this->mapCellXfIndex[$ixfe])->getBorders()->getBottom();
                                $bottom->getColor()->setRGB($rgb);
                                $bottom->colorIndex = null; // normal color index does not apply, discard
                            }
                        }

                        break;
                    case 9:        // border color left
                        $xclfType = self::getUInt2d($extData, 0); // color type
                        $xclrValue = substr($extData, 4, 4); // color value (value based on color type)

                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue[0]), ord($xclrValue[1]), ord($xclrValue[2]));

                            // modify the relevant style property
                            if (isset($this->mapCellXfIndex[$ixfe])) {
                                $left = $this->spreadsheet->getCellXfByIndex($this->mapCellXfIndex[$ixfe])->getBorders()->getLeft();
                                $left->getColor()->setRGB($rgb);
                                $left->colorIndex = null; // normal color index does not apply, discard
                            }
                        }

                        break;
                    case 10:        // border color right
                        $xclfType = self::getUInt2d($extData, 0); // color type
                        $xclrValue = substr($extData, 4, 4); // color value (value based on color type)

                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue[0]), ord($xclrValue[1]), ord($xclrValue[2]));

                            // modify the relevant style property
                            if (isset($this->mapCellXfIndex[$ixfe])) {
                                $right = $this->spreadsheet->getCellXfByIndex($this->mapCellXfIndex[$ixfe])->getBorders()->getRight();
                                $right->getColor()->setRGB($rgb);
                                $right->colorIndex = null; // normal color index does not apply, discard
                            }
                        }

                        break;
                    case 11:        // border color diagonal
                        $xclfType = self::getUInt2d($extData, 0); // color type
                        $xclrValue = substr($extData, 4, 4); // color value (value based on color type)

                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue[0]), ord($xclrValue[1]), ord($xclrValue[2]));

                            // modify the relevant style property
                            if (isset($this->mapCellXfIndex[$ixfe])) {
                                $diagonal = $this->spreadsheet->getCellXfByIndex($this->mapCellXfIndex[$ixfe])->getBorders()->getDiagonal();
                                $diagonal->getColor()->setRGB($rgb);
                                $diagonal->colorIndex = null; // normal color index does not apply, discard
                            }
                        }

                        break;
                    case 13:    // font color
                        $xclfType = self::getUInt2d($extData, 0); // color type
                        $xclrValue = substr($extData, 4, 4); // color value (value based on color type)

                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue[0]), ord($xclrValue[1]), ord($xclrValue[2]));

                            // modify the relevant style property
                            if (isset($this->mapCellXfIndex[$ixfe])) {
                                $font = $this->spreadsheet->getCellXfByIndex($this->mapCellXfIndex[$ixfe])->getFont();
                                $font->getColor()->setRGB($rgb);
                                $font->colorIndex = null; // normal color index does not apply, discard
                            }
                        }

                        break;
                }

                $offset += $cb;
            }
        }
    }

    /**
     * Read STYLE record.
     */
    protected function readStyle(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 2; index to XF record and flag for built-in style
            $ixfe = self::getUInt2d($recordData, 0);

            // bit: 11-0; mask 0x0FFF; index to XF record
            //$xfIndex = (0x0FFF & $ixfe) >> 0;

            // bit: 15; mask 0x8000; 0 = user-defined style, 1 = built-in style
            $isBuiltIn = (bool) ((0x8000 & $ixfe) >> 15);

            if ($isBuiltIn) {
                // offset: 2; size: 1; identifier for built-in style
                $builtInId = ord($recordData[2]);

                switch ($builtInId) {
                    case 0x00:
                        // currently, we are not using this for anything
                        break;
                    default:
                        break;
                }
            }
            // user-defined; not supported by PhpSpreadsheet
        }
    }

    /**
     * Read PALETTE record.
     */
    protected function readPalette(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 2; number of following colors
            $nm = self::getUInt2d($recordData, 0);

            // list of RGB colors
            for ($i = 0; $i < $nm; ++$i) {
                $rgb = substr($recordData, 2 + 4 * $i, 4);
                $this->palette[] = self::readRGB($rgb);
            }
        }
    }

    /**
     * SHEET.
     *
     * This record is  located in the  Workbook Globals
     * Substream  and represents a sheet inside the workbook.
     * One SHEET record is written for each sheet. It stores the
     * sheet name and a stream offset to the BOF record of the
     * respective Sheet Substream within the Workbook Stream.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    protected function readSheet(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // offset: 0; size: 4; absolute stream position of the BOF record of the sheet
        // NOTE: not encrypted
        $rec_offset = self::getInt4d($this->data, $this->pos + 4);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 4; size: 1; sheet state
        $sheetState = match (ord($recordData[4])) {
            0x00 => Worksheet::SHEETSTATE_VISIBLE,
            0x01 => Worksheet::SHEETSTATE_HIDDEN,
            0x02 => Worksheet::SHEETSTATE_VERYHIDDEN,
            default => Worksheet::SHEETSTATE_VISIBLE,
        };

        // offset: 5; size: 1; sheet type
        $sheetType = ord($recordData[5]);

        // offset: 6; size: var; sheet name
        $rec_name = null;
        if ($this->version == self::XLS_BIFF8) {
            $string = self::readUnicodeStringShort(substr($recordData, 6));
            $rec_name = $string['value'];
        } elseif ($this->version == self::XLS_BIFF7) {
            $string = $this->readByteStringShort(substr($recordData, 6));
            $rec_name = $string['value'];
        }
        /** @var string $rec_name */
        $this->sheets[] = [
            'name' => $rec_name,
            'offset' => $rec_offset,
            'sheetState' => $sheetState,
            'sheetType' => $sheetType,
        ];
    }

    /**
     * Read EXTERNALBOOK record.
     */
    protected function readExternalBook(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset within record data
        $offset = 0;

        // there are 4 types of records
        if (strlen($recordData) > 4) {
            // external reference
            // offset: 0; size: 2; number of sheet names ($nm)
            $nm = self::getUInt2d($recordData, 0);
            $offset += 2;

            // offset: 2; size: var; encoded URL without sheet name (Unicode string, 16-bit length)
            $encodedUrlString = self::readUnicodeStringLong(substr($recordData, 2));
            $offset += $encodedUrlString['size'];

            // offset: var; size: var; list of $nm sheet names (Unicode strings, 16-bit length)
            $externalSheetNames = [];
            for ($i = 0; $i < $nm; ++$i) {
                $externalSheetNameString = self::readUnicodeStringLong(substr($recordData, $offset));
                $externalSheetNames[] = $externalSheetNameString['value'];
                $offset += $externalSheetNameString['size'];
            }

            // store the record data
            $this->externalBooks[] = [
                'type' => 'external',
                'encodedUrl' => $encodedUrlString['value'],
                'externalSheetNames' => $externalSheetNames,
            ];
        } elseif (substr($recordData, 2, 2) == pack('CC', 0x01, 0x04)) {
            // internal reference
            // offset: 0; size: 2; number of sheet in this document
            // offset: 2; size: 2; 0x01 0x04
            $this->externalBooks[] = [
                'type' => 'internal',
            ];
        } elseif (substr($recordData, 0, 4) == pack('vCC', 0x0001, 0x01, 0x3A)) {
            // add-in function
            // offset: 0; size: 2; 0x0001
            $this->externalBooks[] = [
                'type' => 'addInFunction',
            ];
        } elseif (substr($recordData, 0, 2) == pack('v', 0x0000)) {
            // DDE links, OLE links
            // offset: 0; size: 2; 0x0000
            // offset: 2; size: var; encoded source document name
            $this->externalBooks[] = [
                'type' => 'DDEorOLE',
            ];
        }
    }

    /**
     * Read EXTERNNAME record.
     */
    protected function readExternName(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // external sheet references provided for named cells
        if ($this->version == self::XLS_BIFF8) {
            // offset: 0; size: 2; options
            //$options = self::getUInt2d($recordData, 0);

            // offset: 2; size: 2;

            // offset: 4; size: 2; not used

            // offset: 6; size: var
            $nameString = self::readUnicodeStringShort(substr($recordData, 6));

            // offset: var; size: var; formula data
            $offset = 6 + $nameString['size'];
            $formula = $this->getFormulaFromStructure(substr($recordData, $offset));

            $this->externalNames[] = [
                'name' => $nameString['value'],
                'formula' => $formula,
            ];
        }
    }

    /**
     * Read EXTERNSHEET record.
     */
    protected function readExternSheet(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // external sheet references provided for named cells
        if ($this->version == self::XLS_BIFF8) {
            // offset: 0; size: 2; number of following ref structures
            $nm = self::getUInt2d($recordData, 0);
            for ($i = 0; $i < $nm; ++$i) {
                $this->ref[] = [
                    // offset: 2 + 6 * $i; index to EXTERNALBOOK record
                    'externalBookIndex' => self::getUInt2d($recordData, 2 + 6 * $i),
                    // offset: 4 + 6 * $i; index to first sheet in EXTERNALBOOK record
                    'firstSheetIndex' => self::getUInt2d($recordData, 4 + 6 * $i),
                    // offset: 6 + 6 * $i; index to last sheet in EXTERNALBOOK record
                    'lastSheetIndex' => self::getUInt2d($recordData, 6 + 6 * $i),
                ];
            }
        }
    }

    /**
     * DEFINEDNAME.
     *
     * This record is part of a Link Table. It contains the name
     * and the token array of an internal defined name. Token
     * arrays of defined names contain tokens with aberrant
     * token classes.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    protected function readDefinedName(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->version == self::XLS_BIFF8) {
            // retrieves named cells

            // offset: 0; size: 2; option flags
            $opts = self::getUInt2d($recordData, 0);

            // bit: 5; mask: 0x0020; 0 = user-defined name, 1 = built-in-name
            $isBuiltInName = (0x0020 & $opts) >> 5;

            // offset: 2; size: 1; keyboard shortcut

            // offset: 3; size: 1; length of the name (character count)
            $nlen = ord($recordData[3]);

            // offset: 4; size: 2; size of the formula data (it can happen that this is zero)
            // note: there can also be additional data, this is not included in $flen
            $flen = self::getUInt2d($recordData, 4);

            // offset: 8; size: 2; 0=Global name, otherwise index to sheet (1-based)
            $scope = self::getUInt2d($recordData, 8);

            // offset: 14; size: var; Name (Unicode string without length field)
            $string = self::readUnicodeString(substr($recordData, 14), $nlen);

            // offset: var; size: $flen; formula data
            $offset = 14 + $string['size'];
            $formulaStructure = pack('v', $flen) . substr($recordData, $offset);

            try {
                $formula = $this->getFormulaFromStructure($formulaStructure);
            } catch (PhpSpreadsheetException) {
                $formula = '';
                $isBuiltInName = 0;
            }

            $this->definedname[] = [
                'isBuiltInName' => $isBuiltInName,
                'name' => $string['value'],
                'formula' => $formula,
                'scope' => $scope,
            ];
        }
    }

    /**
     * Read MSODRAWINGGROUP record.
     */
    protected function readMsoDrawingGroup(): void
    {
        //$length = self::getUInt2d($this->data, $this->pos + 2);

        // get spliced record data
        $splicedRecordData = $this->getSplicedRecordData();
        /** @var string */
        $recordData = $splicedRecordData['recordData'];

        $this->drawingGroupData .= $recordData;
    }

    /**
     * SST - Shared String Table.
     *
     * This record contains a list of all strings used anywhere
     * in the workbook. Each string occurs only once. The
     * workbook uses indexes into the list to reference the
     * strings.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    protected function readSst(): void
    {
        // offset within (spliced) record data
        $pos = 0;

        // Limit global SST position, further control for bad SST Length in BIFF8 data
        $limitposSST = 0;

        // get spliced record data
        $splicedRecordData = $this->getSplicedRecordData();

        $recordData = $splicedRecordData['recordData'];
        /** @var mixed[] */
        $spliceOffsets = $splicedRecordData['spliceOffsets'];

        // offset: 0; size: 4; total number of strings in the workbook
        $pos += 4;

        // offset: 4; size: 4; number of following strings ($nm)
        /** @var string $recordData */
        $nm = self::getInt4d($recordData, 4);
        $pos += 4;

        // look up limit position
        foreach ($spliceOffsets as $spliceOffset) {
            // it can happen that the string is empty, therefore we need
            // <= and not just <
            if ($pos <= $spliceOffset) {
                $limitposSST = $spliceOffset;
            }
        }

        // loop through the Unicode strings (16-bit length)
        for ($i = 0; $i < $nm && $pos < $limitposSST; ++$i) {
            // number of characters in the Unicode string
            /** @var int $pos */
            $numChars = self::getUInt2d($recordData, $pos);
            /** @var int $pos */
            $pos += 2;

            // option flags
            /** @var string $recordData */
            $optionFlags = ord($recordData[$pos]);
            ++$pos;

            // bit: 0; mask: 0x01; 0 = compressed; 1 = uncompressed
            $isCompressed = (($optionFlags & 0x01) == 0);

            // bit: 2; mask: 0x02; 0 = ordinary; 1 = Asian phonetic
            $hasAsian = (($optionFlags & 0x04) != 0);

            // bit: 3; mask: 0x03; 0 = ordinary; 1 = Rich-Text
            $hasRichText = (($optionFlags & 0x08) != 0);

            $formattingRuns = 0;
            if ($hasRichText) {
                // number of Rich-Text formatting runs
                $formattingRuns = self::getUInt2d($recordData, $pos);
                $pos += 2;
            }

            $extendedRunLength = 0;
            if ($hasAsian) {
                // size of Asian phonetic setting
                $extendedRunLength = self::getInt4d($recordData, $pos);
                $pos += 4;
            }

            // expected byte length of character array if not split
            $len = ($isCompressed) ? $numChars : $numChars * 2;

            // look up limit position - Check it again to be sure that no error occurs when parsing SST structure
            $limitpos = null;
            foreach ($spliceOffsets as $spliceOffset) {
                // it can happen that the string is empty, therefore we need
                // <= and not just <
                if ($pos <= $spliceOffset) {
                    $limitpos = $spliceOffset;

                    break;
                }
            }

            /** @var int $limitpos */
            if ($pos + $len <= $limitpos) {
                // character array is not split between records

                $retstr = substr($recordData, $pos, $len);
                $pos += $len;
            } else {
                // character array is split between records

                // first part of character array
                $retstr = substr($recordData, $pos, $limitpos - $pos);

                $bytesRead = $limitpos - $pos;

                // remaining characters in Unicode string
                $charsLeft = $numChars - (($isCompressed) ? $bytesRead : ($bytesRead / 2));

                $pos = $limitpos;

                // keep reading the characters
                while ($charsLeft > 0) {
                    // look up next limit position, in case the string span more than one continue record
                    foreach ($spliceOffsets as $spliceOffset) {
                        if ($pos < $spliceOffset) {
                            $limitpos = $spliceOffset;

                            break;
                        }
                    }

                    // repeated option flags
                    // OpenOffice.org documentation 5.21
                    /** @var int $pos */
                    $option = ord($recordData[$pos]);
                    ++$pos;

                    /** @var int $limitpos */
                    if ($isCompressed && ($option == 0)) {
                        // 1st fragment compressed
                        // this fragment compressed
                        /** @var int */
                        $len = min($charsLeft, $limitpos - $pos);
                        $retstr .= substr($recordData, $pos, $len);
                        $charsLeft -= $len;
                        $isCompressed = true;
                    } elseif (!$isCompressed && ($option != 0)) {
                        // 1st fragment uncompressed
                        // this fragment uncompressed
                        /** @var int */
                        $len = min($charsLeft * 2, $limitpos - $pos);
                        $retstr .= substr($recordData, $pos, $len);
                        $charsLeft -= $len / 2;
                        $isCompressed = false;
                    } elseif (!$isCompressed && ($option == 0)) {
                        // 1st fragment uncompressed
                        // this fragment compressed
                        $len = min($charsLeft, $limitpos - $pos);
                        for ($j = 0; $j < $len; ++$j) {
                            $retstr .= $recordData[$pos + $j]
                                . chr(0);
                        }
                        $charsLeft -= $len;
                        $isCompressed = false;
                    } else {
                        // 1st fragment compressed
                        // this fragment uncompressed
                        $newstr = '';
                        $jMax = strlen($retstr);
                        for ($j = 0; $j < $jMax; ++$j) {
                            $newstr .= $retstr[$j] . chr(0);
                        }
                        $retstr = $newstr;
                        /** @var int */
                        $len = min($charsLeft * 2, $limitpos - $pos);
                        $retstr .= substr($recordData, $pos, $len);
                        $charsLeft -= $len / 2;
                        $isCompressed = false;
                    }

                    $pos += $len;
                }
            }

            // convert to UTF-8
            $retstr = self::encodeUTF16($retstr, $isCompressed);

            // read additional Rich-Text information, if any
            $fmtRuns = [];
            if ($hasRichText) {
                // list of formatting runs
                for ($j = 0; $j < $formattingRuns; ++$j) {
                    // first formatted character; zero-based
                    /** @var int $pos */
                    $charPos = self::getUInt2d($recordData, $pos + $j * 4);

                    // index to font record
                    $fontIndex = self::getUInt2d($recordData, $pos + 2 + $j * 4);

                    $fmtRuns[] = [
                        'charPos' => $charPos,
                        'fontIndex' => $fontIndex,
                    ];
                }
                $pos += 4 * $formattingRuns;
            }

            // read additional Asian phonetics information, if any
            if ($hasAsian) {
                // For Asian phonetic settings, we skip the extended string data
                $pos += $extendedRunLength;
            }

            // store the shared sting
            $this->sst[] = [
                'value' => $retstr,
                'fmtRuns' => $fmtRuns,
            ];
        }

        // getSplicedRecordData() takes care of moving current position in data stream
    }

    /**
     * Read PRINTGRIDLINES record.
     */
    protected function readPrintGridlines(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->version == self::XLS_BIFF8 && !$this->readDataOnly) {
            // offset: 0; size: 2; 0 = do not print sheet grid lines; 1 = print sheet gridlines
            $printGridlines = (bool) self::getUInt2d($recordData, 0);
            $this->phpSheet->setPrintGridlines($printGridlines);
        }
    }

    /**
     * Read DEFAULTROWHEIGHT record.
     */
    protected function readDefaultRowHeight(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; option flags
        // offset: 2; size: 2; default height for unused rows, (twips 1/20 point)
        $height = self::getUInt2d($recordData, 2);
        $this->phpSheet->getDefaultRowDimension()->setRowHeight($height / 20);
    }

    /**
     * Read SHEETPR record.
     */
    protected function readSheetPr(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2

        // bit: 6; mask: 0x0040; 0 = outline buttons above outline group
        $isSummaryBelow = (0x0040 & self::getUInt2d($recordData, 0)) >> 6;
        $this->phpSheet->setShowSummaryBelow((bool) $isSummaryBelow);

        // bit: 7; mask: 0x0080; 0 = outline buttons left of outline group
        $isSummaryRight = (0x0080 & self::getUInt2d($recordData, 0)) >> 7;
        $this->phpSheet->setShowSummaryRight((bool) $isSummaryRight);

        // bit: 8; mask: 0x100; 0 = scale printout in percent, 1 = fit printout to number of pages
        // this corresponds to radio button setting in page setup dialog in Excel
        $this->isFitToPages = (bool) ((0x0100 & self::getUInt2d($recordData, 0)) >> 8);
    }

    /**
     * Read HORIZONTALPAGEBREAKS record.
     */
    protected function readHorizontalPageBreaks(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->version == self::XLS_BIFF8 && !$this->readDataOnly) {
            // offset: 0; size: 2; number of the following row index structures
            $nm = self::getUInt2d($recordData, 0);

            // offset: 2; size: 6 * $nm; list of $nm row index structures
            for ($i = 0; $i < $nm; ++$i) {
                $r = self::getUInt2d($recordData, 2 + 6 * $i);
                $cf = self::getUInt2d($recordData, 2 + 6 * $i + 2);
                //$cl = self::getUInt2d($recordData, 2 + 6 * $i + 4);

                // not sure why two column indexes are necessary?
                $this->phpSheet->setBreak([$cf + 1, $r], Worksheet::BREAK_ROW);
            }
        }
    }

    /**
     * Read VERTICALPAGEBREAKS record.
     */
    protected function readVerticalPageBreaks(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->version == self::XLS_BIFF8 && !$this->readDataOnly) {
            // offset: 0; size: 2; number of the following column index structures
            $nm = self::getUInt2d($recordData, 0);

            // offset: 2; size: 6 * $nm; list of $nm row index structures
            for ($i = 0; $i < $nm; ++$i) {
                $c = self::getUInt2d($recordData, 2 + 6 * $i);
                $rf = self::getUInt2d($recordData, 2 + 6 * $i + 2);
                //$rl = self::getUInt2d($recordData, 2 + 6 * $i + 4);

                // not sure why two row indexes are necessary?
                $this->phpSheet->setBreak([$c + 1, ($rf > 0) ? $rf : 1], Worksheet::BREAK_COLUMN);
            }
        }
    }

    /**
     * Read HEADER record.
     */
    protected function readHeader(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: var
            // realized that $recordData can be empty even when record exists
            if ($recordData) {
                if ($this->version == self::XLS_BIFF8) {
                    $string = self::readUnicodeStringLong($recordData);
                } else {
                    $string = $this->readByteStringShort($recordData);
                }

                /** @var string[] $string */
                $this->phpSheet
                    ->getHeaderFooter()
                    ->setOddHeader($string['value']);
                $this->phpSheet
                    ->getHeaderFooter()
                    ->setEvenHeader($string['value']);
            }
        }
    }

    /**
     * Read FOOTER record.
     */
    protected function readFooter(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: var
            // realized that $recordData can be empty even when record exists
            if ($recordData) {
                if ($this->version == self::XLS_BIFF8) {
                    $string = self::readUnicodeStringLong($recordData);
                } else {
                    $string = $this->readByteStringShort($recordData);
                }
                /** @var string */
                $temp = $string['value'];
                $this->phpSheet
                    ->getHeaderFooter()
                    ->setOddFooter($temp);
                $this->phpSheet
                    ->getHeaderFooter()
                    ->setEvenFooter($temp);
            }
        }
    }

    /**
     * Read HCENTER record.
     */
    protected function readHcenter(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 2; 0 = print sheet left aligned, 1 = print sheet centered horizontally
            $isHorizontalCentered = (bool) self::getUInt2d($recordData, 0);

            $this->phpSheet->getPageSetup()->setHorizontalCentered($isHorizontalCentered);
        }
    }

    /**
     * Read VCENTER record.
     */
    protected function readVcenter(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 2; 0 = print sheet aligned at top page border, 1 = print sheet vertically centered
            $isVerticalCentered = (bool) self::getUInt2d($recordData, 0);

            $this->phpSheet->getPageSetup()->setVerticalCentered($isVerticalCentered);
        }
    }

    /**
     * Read LEFTMARGIN record.
     */
    protected function readLeftMargin(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 8
            $this->phpSheet->getPageMargins()->setLeft(self::extractNumber($recordData));
        }
    }

    /**
     * Read RIGHTMARGIN record.
     */
    protected function readRightMargin(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 8
            $this->phpSheet->getPageMargins()->setRight(self::extractNumber($recordData));
        }
    }

    /**
     * Read TOPMARGIN record.
     */
    protected function readTopMargin(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 8
            $this->phpSheet->getPageMargins()->setTop(self::extractNumber($recordData));
        }
    }

    /**
     * Read BOTTOMMARGIN record.
     */
    protected function readBottomMargin(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 8
            $this->phpSheet->getPageMargins()->setBottom(self::extractNumber($recordData));
        }
    }

    /**
     * Read PAGESETUP record.
     */
    protected function readPageSetup(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 2; paper size
            $paperSize = self::getUInt2d($recordData, 0);

            // offset: 2; size: 2; scaling factor
            $scale = self::getUInt2d($recordData, 2);

            // offset: 6; size: 2; fit worksheet width to this number of pages, 0 = use as many as needed
            $fitToWidth = self::getUInt2d($recordData, 6);

            // offset: 8; size: 2; fit worksheet height to this number of pages, 0 = use as many as needed
            $fitToHeight = self::getUInt2d($recordData, 8);

            // offset: 10; size: 2; option flags

            // bit: 0; mask: 0x0001; 0=down then over, 1=over then down
            $isOverThenDown = (0x0001 & self::getUInt2d($recordData, 10));

            // bit: 1; mask: 0x0002; 0=landscape, 1=portrait
            $isPortrait = (0x0002 & self::getUInt2d($recordData, 10)) >> 1;

            // bit: 2; mask: 0x0004; 1= paper size, scaling factor, paper orient. not init
            // when this bit is set, do not use flags for those properties
            $isNotInit = (0x0004 & self::getUInt2d($recordData, 10)) >> 2;

            if (!$isNotInit) {
                $this->phpSheet->getPageSetup()->setPaperSize($paperSize);
                $this->phpSheet->getPageSetup()->setPageOrder(((bool) $isOverThenDown) ? PageSetup::PAGEORDER_OVER_THEN_DOWN : PageSetup::PAGEORDER_DOWN_THEN_OVER);
                $this->phpSheet->getPageSetup()->setOrientation(((bool) $isPortrait) ? PageSetup::ORIENTATION_PORTRAIT : PageSetup::ORIENTATION_LANDSCAPE);

                $this->phpSheet->getPageSetup()->setScale($scale, false);
                $this->phpSheet->getPageSetup()->setFitToPage((bool) $this->isFitToPages);
                $this->phpSheet->getPageSetup()->setFitToWidth($fitToWidth, false);
                $this->phpSheet->getPageSetup()->setFitToHeight($fitToHeight, false);
            }

            // offset: 16; size: 8; header margin (IEEE 754 floating-point value)
            $marginHeader = self::extractNumber(substr($recordData, 16, 8));
            $this->phpSheet->getPageMargins()->setHeader($marginHeader);

            // offset: 24; size: 8; footer margin (IEEE 754 floating-point value)
            $marginFooter = self::extractNumber(substr($recordData, 24, 8));
            $this->phpSheet->getPageMargins()->setFooter($marginFooter);
        }
    }

    /**
     * PROTECT - Sheet protection (BIFF2 through BIFF8)
     *   if this record is omitted, then it also means no sheet protection.
     */
    protected function readProtect(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->readDataOnly) {
            return;
        }

        // offset: 0; size: 2;

        // bit 0, mask 0x01; 1 = sheet is protected
        $bool = (0x01 & self::getUInt2d($recordData, 0)) >> 0;
        $this->phpSheet->getProtection()->setSheet((bool) $bool);
    }

    /**
     * SCENPROTECT.
     */
    protected function readScenProtect(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->readDataOnly) {
            return;
        }

        // offset: 0; size: 2;

        // bit: 0, mask 0x01; 1 = scenarios are protected
        $bool = (0x01 & self::getUInt2d($recordData, 0)) >> 0;

        $this->phpSheet->getProtection()->setScenarios((bool) $bool);
    }

    /**
     * OBJECTPROTECT.
     */
    protected function readObjectProtect(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->readDataOnly) {
            return;
        }

        // offset: 0; size: 2;

        // bit: 0, mask 0x01; 1 = objects are protected
        $bool = (0x01 & self::getUInt2d($recordData, 0)) >> 0;

        $this->phpSheet->getProtection()->setObjects((bool) $bool);
    }

    /**
     * PASSWORD - Sheet protection (hashed) password (BIFF2 through BIFF8).
     */
    protected function readPassword(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 2; 16-bit hash value of password
            $password = strtoupper(dechex(self::getUInt2d($recordData, 0))); // the hashed password
            $this->phpSheet->getProtection()->setPassword($password, true);
        }
    }

    /**
     * Read DEFCOLWIDTH record.
     */
    protected function readDefColWidth(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; default column width
        $width = self::getUInt2d($recordData, 0);
        if ($width != 8) {
            $this->phpSheet->getDefaultColumnDimension()->setWidth($width);
        }
    }

    /**
     * Read COLINFO record.
     */
    protected function readColInfo(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 2; index to first column in range
            $firstColumnIndex = self::getUInt2d($recordData, 0);

            // offset: 2; size: 2; index to last column in range
            $lastColumnIndex = self::getUInt2d($recordData, 2);

            // offset: 4; size: 2; width of the column in 1/256 of the width of the zero character
            $width = self::getUInt2d($recordData, 4);

            // offset: 6; size: 2; index to XF record for default column formatting
            $xfIndex = self::getUInt2d($recordData, 6);

            // offset: 8; size: 2; option flags
            // bit: 0; mask: 0x0001; 1= columns are hidden
            $isHidden = (0x0001 & self::getUInt2d($recordData, 8)) >> 0;

            // bit: 10-8; mask: 0x0700; outline level of the columns (0 = no outline)
            $level = (0x0700 & self::getUInt2d($recordData, 8)) >> 8;

            // bit: 12; mask: 0x1000; 1 = collapsed
            $isCollapsed = (bool) ((0x1000 & self::getUInt2d($recordData, 8)) >> 12);

            // offset: 10; size: 2; not used

            for ($i = $firstColumnIndex + 1; $i <= $lastColumnIndex + 1; ++$i) {
                if ($lastColumnIndex == 255 || $lastColumnIndex == 256) {
                    $this->phpSheet->getDefaultColumnDimension()->setWidth($width / 256);

                    break;
                }
                $this->phpSheet->getColumnDimensionByColumn($i)->setWidth($width / 256);
                $this->phpSheet->getColumnDimensionByColumn($i)->setVisible(!$isHidden);
                $this->phpSheet->getColumnDimensionByColumn($i)->setOutlineLevel($level);
                $this->phpSheet->getColumnDimensionByColumn($i)->setCollapsed($isCollapsed);
                if (isset($this->mapCellXfIndex[$xfIndex])) {
                    $this->phpSheet->getColumnDimensionByColumn($i)->setXfIndex($this->mapCellXfIndex[$xfIndex]);
                }
            }
        }
    }

    /**
     * ROW.
     *
     * This record contains the properties of a single row in a
     * sheet. Rows and cells in a sheet are divided into blocks
     * of 32 rows.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    protected function readRow(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 2; index of this row
            $r = self::getUInt2d($recordData, 0);

            // offset: 2; size: 2; index to column of the first cell which is described by a cell record

            // offset: 4; size: 2; index to column of the last cell which is described by a cell record, increased by 1

            // offset: 6; size: 2;

            // bit: 14-0; mask: 0x7FFF; height of the row, in twips = 1/20 of a point
            $height = (0x7FFF & self::getUInt2d($recordData, 6)) >> 0;

            // bit: 15: mask: 0x8000; 0 = row has custom height; 1= row has default height
            $useDefaultHeight = (0x8000 & self::getUInt2d($recordData, 6)) >> 15;

            if (!$useDefaultHeight) {
                if (
                    $this->phpSheet->getDefaultRowDimension()->getRowHeight() > 0
                ) {
                    $this->phpSheet->getRowDimension($r + 1)
                        ->setCustomFormat(true, ($height === 255) ? -1 : ($height / 20));
                } else {
                    $this->phpSheet->getRowDimension($r + 1)->setRowHeight($height / 20);
                }
            }

            // offset: 8; size: 2; not used

            // offset: 10; size: 2; not used in BIFF5-BIFF8

            // offset: 12; size: 4; option flags and default row formatting

            // bit: 2-0: mask: 0x00000007; outline level of the row
            $level = (0x00000007 & self::getInt4d($recordData, 12)) >> 0;
            $this->phpSheet->getRowDimension($r + 1)->setOutlineLevel($level);

            // bit: 4; mask: 0x00000010; 1 = outline group start or ends here... and is collapsed
            $isCollapsed = (bool) ((0x00000010 & self::getInt4d($recordData, 12)) >> 4);
            $this->phpSheet->getRowDimension($r + 1)->setCollapsed($isCollapsed);

            // bit: 5; mask: 0x00000020; 1 = row is hidden
            $isHidden = (0x00000020 & self::getInt4d($recordData, 12)) >> 5;
            $this->phpSheet->getRowDimension($r + 1)->setVisible(!$isHidden);

            // bit: 7; mask: 0x00000080; 1 = row has explicit format
            $hasExplicitFormat = (0x00000080 & self::getInt4d($recordData, 12)) >> 7;

            // bit: 27-16; mask: 0x0FFF0000; only applies when hasExplicitFormat = 1; index to XF record
            $xfIndex = (0x0FFF0000 & self::getInt4d($recordData, 12)) >> 16;

            if ($hasExplicitFormat && isset($this->mapCellXfIndex[$xfIndex])) {
                $this->phpSheet->getRowDimension($r + 1)->setXfIndex($this->mapCellXfIndex[$xfIndex]);
            }
        }
    }

    /**
     * Read RK record
     * This record represents a cell that contains an RK value
     * (encoded integer or floating-point value). If a
     * floating-point value cannot be encoded to an RK value,
     * a NUMBER record will be written. This record replaces the
     * record INTEGER written in BIFF2.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    protected function readRk(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; index to row
        $row = self::getUInt2d($recordData, 0);

        // offset: 2; size: 2; index to column
        $column = self::getUInt2d($recordData, 2);
        $columnString = Coordinate::stringFromColumnIndex($column + 1);

        // Read cell?
        if ($this->getReadFilter()->readCell($columnString, $row + 1, $this->phpSheet->getTitle())) {
            // offset: 4; size: 2; index to XF record
            $xfIndex = self::getUInt2d($recordData, 4);

            // offset: 6; size: 4; RK value
            $rknum = self::getInt4d($recordData, 6);
            $numValue = self::getIEEE754($rknum);

            $cell = $this->phpSheet->getCell($columnString . ($row + 1));
            if (!$this->readDataOnly && isset($this->mapCellXfIndex[$xfIndex])) {
                // add style information
                $cell->setXfIndex($this->mapCellXfIndex[$xfIndex]);
            }

            // add cell
            $cell->setValueExplicit($numValue, DataType::TYPE_NUMERIC);
        }
    }

    /**
     * Read LABELSST record
     * This record represents a cell that contains a string. It
     * replaces the LABEL record and RSTRING record used in
     * BIFF2-BIFF5.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    protected function readLabelSst(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; index to row
        $row = self::getUInt2d($recordData, 0);

        // offset: 2; size: 2; index to column
        $column = self::getUInt2d($recordData, 2);
        $columnString = Coordinate::stringFromColumnIndex($column + 1);

        $cell = null;
        // Read cell?
        if ($this->getReadFilter()->readCell($columnString, $row + 1, $this->phpSheet->getTitle())) {
            // offset: 4; size: 2; index to XF record
            $xfIndex = self::getUInt2d($recordData, 4);

            // offset: 6; size: 4; index to SST record
            $index = self::getInt4d($recordData, 6);

            // add cell
            if (($fmtRuns = $this->sst[$index]['fmtRuns']) && !$this->readDataOnly) {
                // then we should treat as rich text
                $richText = new RichText();
                $charPos = 0;
                $sstCount = count($this->sst[$index]['fmtRuns']);
                for ($i = 0; $i <= $sstCount; ++$i) {
                    /** @var mixed[][] $fmtRuns */
                    if (isset($fmtRuns[$i])) {
                        /** @var int[] */
                        $temp = $fmtRuns[$i];
                        $temp = $temp['charPos'];
                        /** @var int $charPos */
                        $text = StringHelper::substring($this->sst[$index]['value'], $charPos, $temp - $charPos);
                        $charPos = $temp;
                    } else {
                        $text = StringHelper::substring($this->sst[$index]['value'], $charPos, StringHelper::countCharacters($this->sst[$index]['value']));
                    }

                    if (StringHelper::countCharacters($text) > 0) {
                        if ($i == 0) { // first text run, no style
                            $richText->createText($text);
                        } else {
                            $textRun = $richText->createTextRun($text);
                            /** @var int[][] $fmtRuns */
                            if (isset($fmtRuns[$i - 1])) {
                                if ($fmtRuns[$i - 1]['fontIndex'] < 4) {
                                    $fontIndex = $fmtRuns[$i - 1]['fontIndex'];
                                } else {
                                    // this has to do with that index 4 is omitted in all BIFF versions for some stra          nge reason
                                    // check the OpenOffice documentation of the FONT record
                                    /** @var int */
                                    $temp = $fmtRuns[$i - 1]['fontIndex'];
                                    $fontIndex = $temp - 1;
                                }
                                if (array_key_exists($fontIndex, $this->objFonts) === false) {
                                    $fontIndex = count($this->objFonts) - 1;
                                }
                                $textRun->setFont(clone $this->objFonts[$fontIndex]);
                            }
                        }
                    }
                }
                if ($this->readEmptyCells || trim($richText->getPlainText()) !== '') {
                    $cell = $this->phpSheet->getCell($columnString . ($row + 1));
                    $cell->setValueExplicit($richText, DataType::TYPE_STRING);
                }
            } else {
                if ($this->readEmptyCells || trim($this->sst[$index]['value']) !== '') {
                    $cell = $this->phpSheet->getCell($columnString . ($row + 1));
                    $cell->setValueExplicit($this->sst[$index]['value'], DataType::TYPE_STRING);
                }
            }

            if (!$this->readDataOnly && $cell !== null && isset($this->mapCellXfIndex[$xfIndex])) {
                // add style information
                $cell->setXfIndex($this->mapCellXfIndex[$xfIndex]);
            }
        }
    }

    /**
     * Read MULRK record
     * This record represents a cell range containing RK value
     * cells. All cells are located in the same row.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    protected function readMulRk(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; index to row
        $row = self::getUInt2d($recordData, 0);

        // offset: 2; size: 2; index to first column
        $colFirst = self::getUInt2d($recordData, 2);

        // offset: var; size: 2; index to last column
        $colLast = self::getUInt2d($recordData, $length - 2);
        $columns = $colLast - $colFirst + 1;

        // offset within record data
        $offset = 4;

        for ($i = 1; $i <= $columns; ++$i) {
            $columnString = Coordinate::stringFromColumnIndex($colFirst + $i);

            // Read cell?
            if ($this->getReadFilter()->readCell($columnString, $row + 1, $this->phpSheet->getTitle())) {
                // offset: var; size: 2; index to XF record
                $xfIndex = self::getUInt2d($recordData, $offset);

                // offset: var; size: 4; RK value
                $numValue = self::getIEEE754(self::getInt4d($recordData, $offset + 2));
                $cell = $this->phpSheet->getCell($columnString . ($row + 1));
                if (!$this->readDataOnly && isset($this->mapCellXfIndex[$xfIndex])) {
                    // add style
                    $cell->setXfIndex($this->mapCellXfIndex[$xfIndex]);
                }

                // add cell value
                $cell->setValueExplicit($numValue, DataType::TYPE_NUMERIC);
            }

            $offset += 6;
        }
    }

    /**
     * Read NUMBER record
     * This record represents a cell that contains a
     * floating-point value.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    protected function readNumber(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; index to row
        $row = self::getUInt2d($recordData, 0);

        // offset: 2; size 2; index to column
        $column = self::getUInt2d($recordData, 2);
        $columnString = Coordinate::stringFromColumnIndex($column + 1);

        // Read cell?
        if ($this->getReadFilter()->readCell($columnString, $row + 1, $this->phpSheet->getTitle())) {
            // offset 4; size: 2; index to XF record
            $xfIndex = self::getUInt2d($recordData, 4);

            $numValue = self::extractNumber(substr($recordData, 6, 8));

            $cell = $this->phpSheet->getCell($columnString . ($row + 1));
            if (!$this->readDataOnly && isset($this->mapCellXfIndex[$xfIndex])) {
                // add cell style
                $cell->setXfIndex($this->mapCellXfIndex[$xfIndex]);
            }

            // add cell value
            $cell->setValueExplicit($numValue, DataType::TYPE_NUMERIC);
        }
    }

    /**
     * Read FORMULA record + perhaps a following STRING record if formula result is a string
     * This record contains the token array and the result of a
     * formula cell.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    protected function readFormula(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; row index
        $row = self::getUInt2d($recordData, 0);

        // offset: 2; size: 2; col index
        $column = self::getUInt2d($recordData, 2);
        $columnString = Coordinate::stringFromColumnIndex($column + 1);

        // offset: 20: size: variable; formula structure
        $formulaStructure = substr($recordData, 20);

        // offset: 14: size: 2; option flags, recalculate always, recalculate on open etc.
        $options = self::getUInt2d($recordData, 14);

        // bit: 0; mask: 0x0001; 1 = recalculate always
        // bit: 1; mask: 0x0002; 1 = calculate on open
        // bit: 2; mask: 0x0008; 1 = part of a shared formula
        $isPartOfSharedFormula = (bool) (0x0008 & $options);

        // WARNING:
        // We can apparently not rely on $isPartOfSharedFormula. Even when $isPartOfSharedFormula = true
        // the formula data may be ordinary formula data, therefore we need to check
        // explicitly for the tExp token (0x01)
        $isPartOfSharedFormula = $isPartOfSharedFormula && ord($formulaStructure[2]) == 0x01;

        if ($isPartOfSharedFormula) {
            // part of shared formula which means there will be a formula with a tExp token and nothing else
            // get the base cell, grab tExp token
            $baseRow = self::getUInt2d($formulaStructure, 3);
            $baseCol = self::getUInt2d($formulaStructure, 5);
            $this->baseCell = Coordinate::stringFromColumnIndex($baseCol + 1) . ($baseRow + 1);
        }

        // Read cell?
        if ($this->getReadFilter()->readCell($columnString, $row + 1, $this->phpSheet->getTitle())) {
            if ($isPartOfSharedFormula) {
                // formula is added to this cell after the sheet has been read
                $this->sharedFormulaParts[$columnString . ($row + 1)] = $this->baseCell;
            }

            // offset: 16: size: 4; not used

            // offset: 4; size: 2; XF index
            $xfIndex = self::getUInt2d($recordData, 4);

            // offset: 6; size: 8; result of the formula
            if ((ord($recordData[6]) == 0) && (ord($recordData[12]) == 255) && (ord($recordData[13]) == 255)) {
                // String formula. Result follows in appended STRING record
                $dataType = DataType::TYPE_STRING;

                // read possible SHAREDFMLA record
                $code = self::getUInt2d($this->data, $this->pos);
                if ($code == self::XLS_TYPE_SHAREDFMLA) {
                    $this->readSharedFmla();
                }

                // read STRING record
                $value = $this->readString();
            } elseif (
                (ord($recordData[6]) == 1)
                && (ord($recordData[12]) == 255)
                && (ord($recordData[13]) == 255)
            ) {
                // Boolean formula. Result is in +2; 0=false, 1=true
                $dataType = DataType::TYPE_BOOL;
                $value = (bool) ord($recordData[8]);
            } elseif (
                (ord($recordData[6]) == 2)
                && (ord($recordData[12]) == 255)
                && (ord($recordData[13]) == 255)
            ) {
                // Error formula. Error code is in +2
                $dataType = DataType::TYPE_ERROR;
                $value = Xls\ErrorCode::lookup(ord($recordData[8]));
            } elseif (
                (ord($recordData[6]) == 3)
                && (ord($recordData[12]) == 255)
                && (ord($recordData[13]) == 255)
            ) {
                // Formula result is a null string
                $dataType = DataType::TYPE_NULL;
                $value = '';
            } else {
                // forumla result is a number, first 14 bytes like _NUMBER record
                $dataType = DataType::TYPE_NUMERIC;
                $value = self::extractNumber(substr($recordData, 6, 8));
            }

            $cell = $this->phpSheet->getCell($columnString . ($row + 1));
            if (!$this->readDataOnly && isset($this->mapCellXfIndex[$xfIndex])) {
                // add cell style
                $cell->setXfIndex($this->mapCellXfIndex[$xfIndex]);
            }

            // store the formula
            if (!$isPartOfSharedFormula) {
                // not part of shared formula
                // add cell value. If we can read formula, populate with formula, otherwise just used cached value
                try {
                    if ($this->version != self::XLS_BIFF8) {
                        throw new Exception('Not BIFF8. Can only read BIFF8 formulas');
                    }
                    $formula = $this->getFormulaFromStructure($formulaStructure); // get formula in human language
                    $cell->setValueExplicit('=' . $formula, DataType::TYPE_FORMULA);
                } catch (PhpSpreadsheetException) {
                    $cell->setValueExplicit($value, $dataType);
                }
            } else {
                if ($this->version == self::XLS_BIFF8) {
                    // do nothing at this point, formula id added later in the code
                } else {
                    $cell->setValueExplicit($value, $dataType);
                }
            }

            // store the cached calculated value
            $cell->setCalculatedValue($value, $dataType === DataType::TYPE_NUMERIC);
        }
    }

    /**
     * Read a SHAREDFMLA record. This function just stores the binary shared formula in the reader,
     * which usually contains relative references.
     * These will be used to construct the formula in each shared formula part after the sheet is read.
     */
    protected function readSharedFmla(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0, size: 6; cell range address of the area used by the shared formula, not used for anything
        //$cellRange = substr($recordData, 0, 6);
        //$cellRange = Xls\Biff5::readBIFF5CellRangeAddressFixed($cellRange); // note: even BIFF8 uses BIFF5 syntax

        // offset: 6, size: 1; not used

        // offset: 7, size: 1; number of existing FORMULA records for this shared formula
        //$no = ord($recordData[7]);

        // offset: 8, size: var; Binary token array of the shared formula
        $formula = substr($recordData, 8);

        // at this point we only store the shared formula for later use
        $this->sharedFormulas[$this->baseCell] = $formula;
    }

    /**
     * Read a STRING record from current stream position and advance the stream pointer to next record
     * This record is used for storing result from FORMULA record when it is a string, and
     * it occurs directly after the FORMULA record.
     *
     * @return string The string contents as UTF-8
     */
    protected function readString(): string
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->version == self::XLS_BIFF8) {
            $string = self::readUnicodeStringLong($recordData);
            $value = $string['value'];
        } else {
            $string = $this->readByteStringLong($recordData);
            $value = $string['value'];
        }
        /** @var string $value */

        return $value;
    }

    /**
     * Read BOOLERR record
     * This record represents a Boolean value or error value
     * cell.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    protected function readBoolErr(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; row index
        $row = self::getUInt2d($recordData, 0);

        // offset: 2; size: 2; column index
        $column = self::getUInt2d($recordData, 2);
        $columnString = Coordinate::stringFromColumnIndex($column + 1);

        // Read cell?
        if ($this->getReadFilter()->readCell($columnString, $row + 1, $this->phpSheet->getTitle())) {
            // offset: 4; size: 2; index to XF record
            $xfIndex = self::getUInt2d($recordData, 4);

            // offset: 6; size: 1; the boolean value or error value
            $boolErr = ord($recordData[6]);

            // offset: 7; size: 1; 0=boolean; 1=error
            $isError = ord($recordData[7]);

            $cell = $this->phpSheet->getCell($columnString . ($row + 1));
            switch ($isError) {
                case 0: // boolean
                    $value = (bool) $boolErr;

                    // add cell value
                    $cell->setValueExplicit($value, DataType::TYPE_BOOL);

                    break;
                case 1: // error type
                    $value = Xls\ErrorCode::lookup($boolErr);

                    // add cell value
                    $cell->setValueExplicit($value, DataType::TYPE_ERROR);

                    break;
            }

            if (!$this->readDataOnly && isset($this->mapCellXfIndex[$xfIndex])) {
                // add cell style
                $cell->setXfIndex($this->mapCellXfIndex[$xfIndex]);
            }
        }
    }

    /**
     * Read MULBLANK record
     * This record represents a cell range of empty cells. All
     * cells are located in the same row.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    protected function readMulBlank(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; index to row
        $row = self::getUInt2d($recordData, 0);

        // offset: 2; size: 2; index to first column
        $fc = self::getUInt2d($recordData, 2);

        // offset: 4; size: 2 x nc; list of indexes to XF records
        // add style information
        if (!$this->readDataOnly && $this->readEmptyCells) {
            for ($i = 0; $i < $length / 2 - 3; ++$i) {
                $columnString = Coordinate::stringFromColumnIndex($fc + $i + 1);

                // Read cell?
                if ($this->getReadFilter()->readCell($columnString, $row + 1, $this->phpSheet->getTitle())) {
                    $xfIndex = self::getUInt2d($recordData, 4 + 2 * $i);
                    if (isset($this->mapCellXfIndex[$xfIndex])) {
                        $this->phpSheet->getCell($columnString . ($row + 1))->setXfIndex($this->mapCellXfIndex[$xfIndex]);
                    }
                }
            }
        }

        // offset: 6; size 2; index to last column (not needed)
    }

    /**
     * Read LABEL record
     * This record represents a cell that contains a string. In
     * BIFF8 it is usually replaced by the LABELSST record.
     * Excel still uses this record, if it copies unformatted
     * text cells to the clipboard.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    protected function readLabel(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; index to row
        $row = self::getUInt2d($recordData, 0);

        // offset: 2; size: 2; index to column
        $column = self::getUInt2d($recordData, 2);
        $columnString = Coordinate::stringFromColumnIndex($column + 1);

        // Read cell?
        if ($this->getReadFilter()->readCell($columnString, $row + 1, $this->phpSheet->getTitle())) {
            // offset: 4; size: 2; XF index
            $xfIndex = self::getUInt2d($recordData, 4);

            // add cell value
            // todo: what if string is very long? continue record
            if ($this->version == self::XLS_BIFF8) {
                $string = self::readUnicodeStringLong(substr($recordData, 6));
                $value = $string['value'];
            } else {
                $string = $this->readByteStringLong(substr($recordData, 6));
                $value = $string['value'];
            }
            /** @var string $value */
            if ($this->readEmptyCells || trim($value) !== '') {
                $cell = $this->phpSheet->getCell($columnString . ($row + 1));
                $cell->setValueExplicit($value, DataType::TYPE_STRING);

                if (!$this->readDataOnly && isset($this->mapCellXfIndex[$xfIndex])) {
                    // add cell style
                    $cell->setXfIndex($this->mapCellXfIndex[$xfIndex]);
                }
            }
        }
    }

    /**
     * Read BLANK record.
     */
    protected function readBlank(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; row index
        $row = self::getUInt2d($recordData, 0);

        // offset: 2; size: 2; col index
        $col = self::getUInt2d($recordData, 2);
        $columnString = Coordinate::stringFromColumnIndex($col + 1);

        // Read cell?
        if ($this->getReadFilter()->readCell($columnString, $row + 1, $this->phpSheet->getTitle())) {
            // offset: 4; size: 2; XF index
            $xfIndex = self::getUInt2d($recordData, 4);

            // add style information
            if (!$this->readDataOnly && $this->readEmptyCells && isset($this->mapCellXfIndex[$xfIndex])) {
                $this->phpSheet->getCell($columnString . ($row + 1))->setXfIndex($this->mapCellXfIndex[$xfIndex]);
            }
        }
    }

    /**
     * Read MSODRAWING record.
     */
    protected function readMsoDrawing(): void
    {
        //$length = self::getUInt2d($this->data, $this->pos + 2);

        // get spliced record data
        $splicedRecordData = $this->getSplicedRecordData();
        $recordData = $splicedRecordData['recordData'];

        $this->drawingData .= StringHelper::convertToString($recordData);
    }

    /**
     * Read OBJ record.
     */
    protected function readObj(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->readDataOnly || $this->version != self::XLS_BIFF8) {
            return;
        }

        // recordData consists of an array of subrecords looking like this:
        //    ft: 2 bytes; ftCmo type (0x15)
        //    cb: 2 bytes; size in bytes of ftCmo data
        //    ot: 2 bytes; Object Type
        //    id: 2 bytes; Object id number
        //    grbit: 2 bytes; Option Flags
        //    data: var; subrecord data

        // for now, we are just interested in the second subrecord containing the object type
        $ftCmoType = self::getUInt2d($recordData, 0);
        $cbCmoSize = self::getUInt2d($recordData, 2);
        $otObjType = self::getUInt2d($recordData, 4);
        $idObjID = self::getUInt2d($recordData, 6);
        $grbitOpts = self::getUInt2d($recordData, 6);

        $this->objs[] = [
            'ftCmoType' => $ftCmoType,
            'cbCmoSize' => $cbCmoSize,
            'otObjType' => $otObjType,
            'idObjID' => $idObjID,
            'grbitOpts' => $grbitOpts,
        ];
        $this->textObjRef = $idObjID;
    }

    /**
     * Read WINDOW2 record.
     */
    protected function readWindow2(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; option flags
        $options = self::getUInt2d($recordData, 0);

        // offset: 2; size: 2; index to first visible row
        //$firstVisibleRow = self::getUInt2d($recordData, 2);

        // offset: 4; size: 2; index to first visible colum
        //$firstVisibleColumn = self::getUInt2d($recordData, 4);
        $zoomscaleInPageBreakPreview = 0;
        $zoomscaleInNormalView = 0;
        if ($this->version === self::XLS_BIFF8) {
            // offset:  8; size: 2; not used
            // offset: 10; size: 2; cached magnification factor in page break preview (in percent); 0 = Default (60%)
            // offset: 12; size: 2; cached magnification factor in normal view (in percent); 0 = Default (100%)
            // offset: 14; size: 4; not used
            if (!isset($recordData[10])) {
                $zoomscaleInPageBreakPreview = 0;
            } else {
                $zoomscaleInPageBreakPreview = self::getUInt2d($recordData, 10);
            }

            if ($zoomscaleInPageBreakPreview === 0) {
                $zoomscaleInPageBreakPreview = 60;
            }

            if (!isset($recordData[12])) {
                $zoomscaleInNormalView = 0;
            } else {
                $zoomscaleInNormalView = self::getUInt2d($recordData, 12);
            }

            if ($zoomscaleInNormalView === 0) {
                $zoomscaleInNormalView = 100;
            }
        }

        // bit: 1; mask: 0x0002; 0 = do not show gridlines, 1 = show gridlines
        $showGridlines = (bool) ((0x0002 & $options) >> 1);
        $this->phpSheet->setShowGridlines($showGridlines);

        // bit: 2; mask: 0x0004; 0 = do not show headers, 1 = show headers
        $showRowColHeaders = (bool) ((0x0004 & $options) >> 2);
        $this->phpSheet->setShowRowColHeaders($showRowColHeaders);

        // bit: 3; mask: 0x0008; 0 = panes are not frozen, 1 = panes are frozen
        $this->frozen = (bool) ((0x0008 & $options) >> 3);

        // bit: 6; mask: 0x0040; 0 = columns from left to right, 1 = columns from right to left
        $this->phpSheet->setRightToLeft((bool) ((0x0040 & $options) >> 6));

        // bit: 10; mask: 0x0400; 0 = sheet not active, 1 = sheet active
        $isActive = (bool) ((0x0400 & $options) >> 10);
        if ($isActive) {
            $this->spreadsheet->setActiveSheetIndex($this->spreadsheet->getIndex($this->phpSheet));
            $this->activeSheetSet = true;
        }

        // bit: 11; mask: 0x0800; 0 = normal view, 1 = page break view
        $isPageBreakPreview = (bool) ((0x0800 & $options) >> 11);

        //FIXME: set $firstVisibleRow and $firstVisibleColumn

        if ($this->phpSheet->getSheetView()->getView() !== SheetView::SHEETVIEW_PAGE_LAYOUT) {
            //NOTE: this setting is inferior to page layout view(Excel2007-)
            $view = $isPageBreakPreview ? SheetView::SHEETVIEW_PAGE_BREAK_PREVIEW : SheetView::SHEETVIEW_NORMAL;
            $this->phpSheet->getSheetView()->setView($view);
            if ($this->version === self::XLS_BIFF8) {
                $zoomScale = $isPageBreakPreview ? $zoomscaleInPageBreakPreview : $zoomscaleInNormalView;
                $this->phpSheet->getSheetView()->setZoomScale($zoomScale);
                $this->phpSheet->getSheetView()->setZoomScaleNormal($zoomscaleInNormalView);
            }
        }
    }

    /**
     * Read PLV Record(Created by Excel2007 or upper).
     */
    protected function readPageLayoutView(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; rt
        //->ignore
        //$rt = self::getUInt2d($recordData, 0);
        // offset: 2; size: 2; grbitfr
        //->ignore
        //$grbitFrt = self::getUInt2d($recordData, 2);
        // offset: 4; size: 8; reserved
        //->ignore

        // offset: 12; size 2; zoom scale
        $wScalePLV = self::getUInt2d($recordData, 12);
        // offset: 14; size 2; grbit
        $grbit = self::getUInt2d($recordData, 14);

        // decomprise grbit
        $fPageLayoutView = $grbit & 0x01;
        //$fRulerVisible = ($grbit >> 1) & 0x01; //no support
        //$fWhitespaceHidden = ($grbit >> 3) & 0x01; //no support

        if ($fPageLayoutView === 1) {
            $this->phpSheet->getSheetView()->setView(SheetView::SHEETVIEW_PAGE_LAYOUT);
            $this->phpSheet->getSheetView()->setZoomScale($wScalePLV); //set by Excel2007 only if SHEETVIEW_PAGE_LAYOUT
        }
        //otherwise, we cannot know whether SHEETVIEW_PAGE_LAYOUT or SHEETVIEW_PAGE_BREAK_PREVIEW.
    }

    /**
     * Read SCL record.
     */
    protected function readScl(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; numerator of the view magnification
        $numerator = self::getUInt2d($recordData, 0);

        // offset: 2; size: 2; numerator of the view magnification
        $denumerator = self::getUInt2d($recordData, 2);

        // set the zoom scale (in percent)
        $this->phpSheet->getSheetView()->setZoomScale($numerator * 100 / $denumerator);
    }

    /**
     * Read PANE record.
     */
    protected function readPane(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 2; position of vertical split
            $px = self::getUInt2d($recordData, 0);

            // offset: 2; size: 2; position of horizontal split
            $py = self::getUInt2d($recordData, 2);

            // offset: 4; size: 2; top most visible row in the bottom pane
            $rwTop = self::getUInt2d($recordData, 4);

            // offset: 6; size: 2; first visible left column in the right pane
            $colLeft = self::getUInt2d($recordData, 6);

            if ($this->frozen) {
                // frozen panes
                $cell = Coordinate::stringFromColumnIndex($px + 1) . ($py + 1);
                $topLeftCell = Coordinate::stringFromColumnIndex($colLeft + 1) . ($rwTop + 1);
                $this->phpSheet->freezePane($cell, $topLeftCell);
            }
            // unfrozen panes; split windows; not supported by PhpSpreadsheet core
        }
    }

    /**
     * Read SELECTION record. There is one such record for each pane in the sheet.
     */
    protected function readSelection(): string
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);
        $selectedCells = '';

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 1; pane identifier
            //$paneId = ord($recordData[0]);

            // offset: 1; size: 2; index to row of the active cell
            //$r = self::getUInt2d($recordData, 1);

            // offset: 3; size: 2; index to column of the active cell
            //$c = self::getUInt2d($recordData, 3);

            // offset: 5; size: 2; index into the following cell range list to the
            //  entry that contains the active cell
            //$index = self::getUInt2d($recordData, 5);

            // offset: 7; size: var; cell range address list containing all selected cell ranges
            $data = substr($recordData, 7);
            $cellRangeAddressList = Xls\Biff5::readBIFF5CellRangeAddressList($data); // note: also BIFF8 uses BIFF5 syntax

            $selectedCells = $cellRangeAddressList['cellRangeAddresses'][0];

            // first row '1' + last row '16384' indicates that full column is selected (apparently also in BIFF8!)
            if (preg_match('/^([A-Z]+1\:[A-Z]+)16384$/', $selectedCells)) {
                $selectedCells = (string) preg_replace('/^([A-Z]+1\:[A-Z]+)16384$/', '${1}1048576', $selectedCells);
            }

            // first row '1' + last row '65536' indicates that full column is selected
            if (preg_match('/^([A-Z]+1\:[A-Z]+)65536$/', $selectedCells)) {
                $selectedCells = (string) preg_replace('/^([A-Z]+1\:[A-Z]+)65536$/', '${1}1048576', $selectedCells);
            }

            // first column 'A' + last column 'IV' indicates that full row is selected
            if (preg_match('/^(A\d+\:)IV(\d+)$/', $selectedCells)) {
                $selectedCells = (string) preg_replace('/^(A\d+\:)IV(\d+)$/', '${1}XFD${2}', $selectedCells);
            }

            $this->phpSheet->setSelectedCells($selectedCells);
        }

        return $selectedCells;
    }

    private function includeCellRangeFiltered(string $cellRangeAddress): bool
    {
        $includeCellRange = false;
        $rangeBoundaries = Coordinate::getRangeBoundaries($cellRangeAddress);
        StringHelper::stringIncrement($rangeBoundaries[1][0]);
        for ($row = $rangeBoundaries[0][1]; $row <= $rangeBoundaries[1][1]; ++$row) {
            for ($column = $rangeBoundaries[0][0]; $column != $rangeBoundaries[1][0]; StringHelper::stringIncrement($column)) {
                if ($this->getReadFilter()->readCell($column, $row, $this->phpSheet->getTitle())) {
                    $includeCellRange = true;

                    break 2;
                }
            }
        }

        return $includeCellRange;
    }

    /**
     * MERGEDCELLS.
     *
     * This record contains the addresses of merged cell ranges
     * in the current sheet.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    protected function readMergedCells(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->version == self::XLS_BIFF8 && !$this->readDataOnly) {
            $cellRangeAddressList = Xls\Biff8::readBIFF8CellRangeAddressList($recordData);
            foreach ($cellRangeAddressList['cellRangeAddresses'] as $cellRangeAddress) {
                /** @var string $cellRangeAddress */
                if (
                    (str_contains($cellRangeAddress, ':'))
                    && ($this->includeCellRangeFiltered($cellRangeAddress))
                ) {
                    $this->phpSheet->mergeCells($cellRangeAddress, Worksheet::MERGE_CELL_CONTENT_HIDE);
                }
            }
        }
    }

    /**
     * Read HYPERLINK record.
     */
    protected function readHyperLink(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer forward to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 8; cell range address of all cells containing this hyperlink
            try {
                $cellRange = Xls\Biff8::readBIFF8CellRangeAddressFixed($recordData);
            } catch (PhpSpreadsheetException) {
                return;
            }

            // offset: 8, size: 16; GUID of StdLink

            // offset: 24, size: 4; unknown value

            // offset: 28, size: 4; option flags
            // bit: 0; mask: 0x00000001; 0 = no link or extant, 1 = file link or URL
            $isFileLinkOrUrl = (0x00000001 & self::getUInt2d($recordData, 28)) >> 0;

            // bit: 1; mask: 0x00000002; 0 = relative path, 1 = absolute path or URL
            //$isAbsPathOrUrl = (0x00000001 & self::getUInt2d($recordData, 28)) >> 1;

            // bit: 2 (and 4); mask: 0x00000014; 0 = no description
            $hasDesc = (0x00000014 & self::getUInt2d($recordData, 28)) >> 2;

            // bit: 3; mask: 0x00000008; 0 = no text, 1 = has text
            $hasText = (0x00000008 & self::getUInt2d($recordData, 28)) >> 3;

            // bit: 7; mask: 0x00000080; 0 = no target frame, 1 = has target frame
            $hasFrame = (0x00000080 & self::getUInt2d($recordData, 28)) >> 7;

            // bit: 8; mask: 0x00000100; 0 = file link or URL, 1 = UNC path (inc. server name)
            $isUNC = (0x00000100 & self::getUInt2d($recordData, 28)) >> 8;

            // offset within record data
            $offset = 32;

            if ($hasDesc) {
                // offset: 32; size: var; character count of description text
                $dl = self::getInt4d($recordData, 32);
                // offset: 36; size: var; character array of description text, no Unicode string header, always 16-bit characters, zero terminated
                //$desc = self::encodeUTF16(substr($recordData, 36, 2 * ($dl - 1)), false);
                $offset += 4 + 2 * $dl;
            }
            if ($hasFrame) {
                $fl = self::getInt4d($recordData, $offset);
                $offset += 4 + 2 * $fl;
            }

            // detect type of hyperlink (there are 4 types)
            $hyperlinkType = null;

            if ($isUNC) {
                $hyperlinkType = 'UNC';
            } elseif (!$isFileLinkOrUrl) {
                $hyperlinkType = 'workbook';
            } elseif (ord($recordData[$offset]) == 0x03) {
                $hyperlinkType = 'local';
            } elseif (ord($recordData[$offset]) == 0xE0) {
                $hyperlinkType = 'URL';
            }

            switch ($hyperlinkType) {
                case 'URL':
                    // section 5.58.2: Hyperlink containing a URL
                    // e.g. http://example.org/index.php

                    // offset: var; size: 16; GUID of URL Moniker
                    $offset += 16;
                    // offset: var; size: 4; size (in bytes) of character array of the URL including trailing zero word
                    $us = self::getInt4d($recordData, $offset);
                    $offset += 4;
                    // offset: var; size: $us; character array of the URL, no Unicode string header, always 16-bit characters, zero-terminated
                    $url = self::encodeUTF16(substr($recordData, $offset, $us - 2), false);
                    $nullOffset = strpos($url, chr(0x00));
                    if ($nullOffset) {
                        $url = substr($url, 0, $nullOffset);
                    }
                    $url .= $hasText ? '#' : '';
                    $offset += $us;

                    break;
                case 'local':
                    // section 5.58.3: Hyperlink to local file
                    // examples:
                    //   mydoc.txt
                    //   ../../somedoc.xls#Sheet!A1

                    // offset: var; size: 16; GUI of File Moniker
                    $offset += 16;

                    // offset: var; size: 2; directory up-level count.
                    $upLevelCount = self::getUInt2d($recordData, $offset);
                    $offset += 2;

                    // offset: var; size: 4; character count of the shortened file path and name, including trailing zero word
                    $sl = self::getInt4d($recordData, $offset);
                    $offset += 4;

                    // offset: var; size: sl; character array of the shortened file path and name in 8.3-DOS-format (compressed Unicode string)
                    $shortenedFilePath = substr($recordData, $offset, $sl);
                    $shortenedFilePath = self::encodeUTF16($shortenedFilePath, true);
                    $shortenedFilePath = substr($shortenedFilePath, 0, -1); // remove trailing zero

                    $offset += $sl;

                    // offset: var; size: 24; unknown sequence
                    $offset += 24;

                    // extended file path
                    // offset: var; size: 4; size of the following file link field including string lenth mark
                    $sz = self::getInt4d($recordData, $offset);
                    $offset += 4;

                    $extendedFilePath = '';
                    // only present if $sz > 0
                    if ($sz > 0) {
                        // offset: var; size: 4; size of the character array of the extended file path and name
                        $xl = self::getInt4d($recordData, $offset);
                        $offset += 4;

                        // offset: var; size 2; unknown
                        $offset += 2;

                        // offset: var; size $xl; character array of the extended file path and name.
                        $extendedFilePath = substr($recordData, $offset, $xl);
                        $extendedFilePath = self::encodeUTF16($extendedFilePath, false);
                        $offset += $xl;
                    }

                    // construct the path
                    $url = str_repeat('..\\', $upLevelCount);
                    $url .= ($sz > 0) ? $extendedFilePath : $shortenedFilePath; // use extended path if available
                    $url .= $hasText ? '#' : '';

                    break;
                case 'UNC':
                    // section 5.58.4: Hyperlink to a File with UNC (Universal Naming Convention) Path
                    // todo: implement
                    return;
                case 'workbook':
                    // section 5.58.5: Hyperlink to the Current Workbook
                    // e.g. Sheet2!B1:C2, stored in text mark field
                    $url = 'sheet://';

                    break;
                default:
                    return;
            }

            if ($hasText) {
                // offset: var; size: 4; character count of text mark including trailing zero word
                $tl = self::getInt4d($recordData, $offset);
                $offset += 4;
                // offset: var; size: var; character array of the text mark without the # sign, no Unicode header, always 16-bit characters, zero-terminated
                $text = self::encodeUTF16(substr($recordData, $offset, 2 * ($tl - 1)), false);
                $url .= $text;
            }

            // apply the hyperlink to all the relevant cells
            foreach (Coordinate::extractAllCellReferencesInRange($cellRange) as $coordinate) {
                $this->phpSheet->getCell($coordinate)->getHyperLink()->setUrl($url);
            }
        }
    }

    /**
     * Read DATAVALIDATIONS record.
     */
    protected function readDataValidations(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        //$recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer forward to next record
        $this->pos += 4 + $length;
    }

    /**
     * Read DATAVALIDATION record.
     */
    protected function readDataValidation(): void
    {
        (new Xls\DataValidationHelper())->readDataValidation2($this);
    }

    /**
     * Read SHEETLAYOUT record. Stores sheet tab color information.
     */
    protected function readSheetLayout(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 2; repeated record identifier 0x0862

            // offset: 2; size: 10; not used

            // offset: 12; size: 4; size of record data
            // Excel 2003 uses size of 0x14 (documented), Excel 2007 uses size of 0x28 (not documented?)
            $sz = self::getInt4d($recordData, 12);

            switch ($sz) {
                case 0x14:
                    // offset: 16; size: 2; color index for sheet tab
                    $colorIndex = self::getUInt2d($recordData, 16);
                    /** @var string[] */
                    $color = Xls\Color::map($colorIndex, $this->palette, $this->version);
                    $this->phpSheet->getTabColor()->setRGB($color['rgb']);

                    break;
                case 0x28:
                    // TODO: Investigate structure for .xls SHEETLAYOUT record as saved by MS Office Excel 2007
                    return;
            }
        }
    }

    /**
     * Read SHEETPROTECTION record (FEATHEADR).
     */
    protected function readSheetProtection(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->readDataOnly) {
            return;
        }

        // offset: 0; size: 2; repeated record header

        // offset: 2; size: 2; FRT cell reference flag (=0 currently)

        // offset: 4; size: 8; Currently not used and set to 0

        // offset: 12; size: 2; Shared feature type index (2=Enhanced Protetion, 4=SmartTag)
        $isf = self::getUInt2d($recordData, 12);
        if ($isf != 2) {
            return;
        }

        // offset: 14; size: 1; =1 since this is a feat header

        // offset: 15; size: 4; size of rgbHdrSData

        // rgbHdrSData, assume "Enhanced Protection"
        // offset: 19; size: 2; option flags
        $options = self::getUInt2d($recordData, 19);

        // bit: 0; mask 0x0001; 1 = user may edit objects, 0 = users must not edit objects
        // Note - do not negate $bool
        $bool = (0x0001 & $options) >> 0;
        $this->phpSheet->getProtection()->setObjects((bool) $bool);

        // bit: 1; mask 0x0002; edit scenarios
        // Note - do not negate $bool
        $bool = (0x0002 & $options) >> 1;
        $this->phpSheet->getProtection()->setScenarios((bool) $bool);

        // bit: 2; mask 0x0004; format cells
        $bool = (0x0004 & $options) >> 2;
        $this->phpSheet->getProtection()->setFormatCells(!$bool);

        // bit: 3; mask 0x0008; format columns
        $bool = (0x0008 & $options) >> 3;
        $this->phpSheet->getProtection()->setFormatColumns(!$bool);

        // bit: 4; mask 0x0010; format rows
        $bool = (0x0010 & $options) >> 4;
        $this->phpSheet->getProtection()->setFormatRows(!$bool);

        // bit: 5; mask 0x0020; insert columns
        $bool = (0x0020 & $options) >> 5;
        $this->phpSheet->getProtection()->setInsertColumns(!$bool);

        // bit: 6; mask 0x0040; insert rows
        $bool = (0x0040 & $options) >> 6;
        $this->phpSheet->getProtection()->setInsertRows(!$bool);

        // bit: 7; mask 0x0080; insert hyperlinks
        $bool = (0x0080 & $options) >> 7;
        $this->phpSheet->getProtection()->setInsertHyperlinks(!$bool);

        // bit: 8; mask 0x0100; delete columns
        $bool = (0x0100 & $options) >> 8;
        $this->phpSheet->getProtection()->setDeleteColumns(!$bool);

        // bit: 9; mask 0x0200; delete rows
        $bool = (0x0200 & $options) >> 9;
        $this->phpSheet->getProtection()->setDeleteRows(!$bool);

        // bit: 10; mask 0x0400; select locked cells
        // Note that this is opposite of most of above.
        $bool = (0x0400 & $options) >> 10;
        $this->phpSheet->getProtection()->setSelectLockedCells((bool) $bool);

        // bit: 11; mask 0x0800; sort cell range
        $bool = (0x0800 & $options) >> 11;
        $this->phpSheet->getProtection()->setSort(!$bool);

        // bit: 12; mask 0x1000; auto filter
        $bool = (0x1000 & $options) >> 12;
        $this->phpSheet->getProtection()->setAutoFilter(!$bool);

        // bit: 13; mask 0x2000; pivot tables
        $bool = (0x2000 & $options) >> 13;
        $this->phpSheet->getProtection()->setPivotTables(!$bool);

        // bit: 14; mask 0x4000; select unlocked cells
        // Note that this is opposite of most of above.
        $bool = (0x4000 & $options) >> 14;
        $this->phpSheet->getProtection()->setSelectUnlockedCells((bool) $bool);

        // offset: 21; size: 2; not used
    }

    /**
     * Read RANGEPROTECTION record
     * Reading of this record is based on Microsoft Office Excel 97-2000 Binary File Format Specification,
     * where it is referred to as FEAT record.
     */
    protected function readRangeProtection(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // local pointer in record data
        $offset = 0;

        if (!$this->readDataOnly) {
            $offset += 12;

            // offset: 12; size: 2; shared feature type, 2 = enhanced protection, 4 = smart tag
            $isf = self::getUInt2d($recordData, 12);
            if ($isf != 2) {
                // we only read FEAT records of type 2
                return;
            }
            $offset += 2;

            $offset += 5;

            // offset: 19; size: 2; count of ref ranges this feature is on
            $cref = self::getUInt2d($recordData, 19);
            $offset += 2;

            $offset += 6;

            // offset: 27; size: 8 * $cref; list of cell ranges (like in hyperlink record)
            $cellRanges = [];
            for ($i = 0; $i < $cref; ++$i) {
                try {
                    $cellRange = Xls\Biff8::readBIFF8CellRangeAddressFixed(substr($recordData, 27 + 8 * $i, 8));
                } catch (PhpSpreadsheetException) {
                    return;
                }
                $cellRanges[] = $cellRange;
                $offset += 8;
            }

            // offset: var; size: var; variable length of feature specific data
            //$rgbFeat = substr($recordData, $offset);
            $offset += 4;

            // offset: var; size: 4; the encrypted password (only 16-bit although field is 32-bit)
            $wPassword = self::getInt4d($recordData, $offset);
            $offset += 4;

            // Apply range protection to sheet
            if ($cellRanges) {
                $this->phpSheet->protectCells(implode(' ', $cellRanges), ($wPassword === 0) ? '' : strtoupper(dechex($wPassword)), true);
            }
        }
    }

    /**
     * Read a free CONTINUE record. Free CONTINUE record may be a camouflaged MSODRAWING record
     * When MSODRAWING data on a sheet exceeds 8224 bytes, CONTINUE records are used instead. Undocumented.
     * In this case, we must treat the CONTINUE record as a MSODRAWING record.
     */
    protected function readContinue(): void
    {
        $length = self::getUInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // check if we are reading drawing data
        // this is in case a free CONTINUE record occurs in other circumstances we are unaware of
        if ($this->drawingData == '') {
            // move stream pointer to next record
            $this->pos += 4 + $length;

            return;
        }

        // check if record data is at least 4 bytes long, otherwise there is no chance this is MSODRAWING data
        if ($length < 4) {
            // move stream pointer to next record
            $this->pos += 4 + $length;

            return;
        }

        // dirty check to see if CONTINUE record could be a camouflaged MSODRAWING record
        // look inside CONTINUE record to see if it looks like a part of an Escher stream
        // we know that Escher stream may be split at least at
        //        0xF003 MsofbtSpgrContainer
        //        0xF004 MsofbtSpContainer
        //        0xF00D MsofbtClientTextbox
        $validSplitPoints = [0xF003, 0xF004, 0xF00D]; // add identifiers if we find more

        $splitPoint = self::getUInt2d($recordData, 2);
        if (in_array($splitPoint, $validSplitPoints)) {
            // get spliced record data (and move pointer to next record)
            $splicedRecordData = $this->getSplicedRecordData();
            $this->drawingData .= StringHelper::convertToString($splicedRecordData['recordData']);

            return;
        }

        // move stream pointer to next record
        $this->pos += 4 + $length;
    }

    /**
     * Reads a record from current position in data stream and continues reading data as long as CONTINUE
     * records are found. Splices the record data pieces and returns the combined string as if record data
     * is in one piece.
     * Moves to next current position in data stream to start of next record different from a CONtINUE record.
     *
     * @return mixed[]
     */
    private function getSplicedRecordData(): array
    {
        $data = '';
        $spliceOffsets = [];

        $i = 0;
        $spliceOffsets[0] = 0;

        do {
            ++$i;

            // offset: 0; size: 2; identifier
            //$identifier = self::getUInt2d($this->data, $this->pos);
            // offset: 2; size: 2; length
            $length = self::getUInt2d($this->data, $this->pos + 2);
            $data .= $this->readRecordData($this->data, $this->pos + 4, $length);

            $spliceOffsets[$i] = $spliceOffsets[$i - 1] + $length;

            $this->pos += 4 + $length;
            $nextIdentifier = self::getUInt2d($this->data, $this->pos);
        } while ($nextIdentifier == self::XLS_TYPE_CONTINUE);

        return [
            'recordData' => $data,
            'spliceOffsets' => $spliceOffsets,
        ];
    }

    /**
     * Convert formula structure into human readable Excel formula like 'A3+A5*5'.
     *
     * @param string $formulaStructure The complete binary data for the formula
     * @param string $baseCell Base cell, only needed when formula contains tRefN tokens, e.g. with shared formulas
     *
     * @return string Human readable formula
     */
    protected function getFormulaFromStructure(string $formulaStructure, string $baseCell = 'A1'): string
    {
        // offset: 0; size: 2; size of the following formula data
        $sz = self::getUInt2d($formulaStructure, 0);

        // offset: 2; size: sz
        $formulaData = substr($formulaStructure, 2, $sz);

        // offset: 2 + sz; size: variable (optional)
        if (strlen($formulaStructure) > 2 + $sz) {
            $additionalData = substr($formulaStructure, 2 + $sz);
        } else {
            $additionalData = '';
        }

        return $this->getFormulaFromData($formulaData, $additionalData, $baseCell);
    }

    /**
     * Take formula data and additional data for formula and return human readable formula.
     *
     * @param string $formulaData The binary data for the formula itself
     * @param string $additionalData Additional binary data going with the formula
     * @param string $baseCell Base cell, only needed when formula contains tRefN tokens, e.g. with shared formulas
     *
     * @return string Human readable formula
     */
    private function getFormulaFromData(string $formulaData, string $additionalData = '', string $baseCell = 'A1'): string
    {
        // start parsing the formula data
        $tokens = [];

        while ($formulaData !== '' && $token = $this->getNextToken($formulaData, $baseCell)) {
            $tokens[] = $token;
            /** @var int[] $token */
            $formulaData = substr($formulaData, $token['size']);
        }

        $formulaString = $this->createFormulaFromTokens($tokens, $additionalData);

        return $formulaString;
    }

    /**
     * Take array of tokens together with additional data for formula and return human readable formula.
     *
     * @param mixed[][] $tokens
     * @param string $additionalData Additional binary data going with the formula
     *
     * @return string Human readable formula
     */
    private function createFormulaFromTokens(array $tokens, string $additionalData): string
    {
        // empty formula?
        if (empty($tokens)) {
            return '';
        }

        $formulaStrings = [];
        foreach ($tokens as $token) {
            // initialize spaces
            $space0 = $space0 ?? ''; // spaces before next token, not tParen
            $space1 = $space1 ?? ''; // carriage returns before next token, not tParen
            $space2 = $space2 ?? ''; // spaces before opening parenthesis
            $space3 = $space3 ?? ''; // carriage returns before opening parenthesis
            $space4 = $space4 ?? ''; // spaces before closing parenthesis
            $space5 = $space5 ?? ''; // carriage returns before closing parenthesis
            /** @var string */
            $tokenData = $token['data'] ?? '';
            switch ($token['name']) {
                case 'tAdd': // addition
                case 'tConcat': // addition
                case 'tDiv': // division
                case 'tEQ': // equality
                case 'tGE': // greater than or equal
                case 'tGT': // greater than
                case 'tIsect': // intersection
                case 'tLE': // less than or equal
                case 'tList': // less than or equal
                case 'tLT': // less than
                case 'tMul': // multiplication
                case 'tNE': // multiplication
                case 'tPower': // power
                case 'tRange': // range
                case 'tSub': // subtraction
                    $op2 = array_pop($formulaStrings);
                    $op1 = array_pop($formulaStrings);
                    $formulaStrings[] = "$op1$space1$space0{$tokenData}$op2";
                    unset($space0, $space1);

                    break;
                case 'tUplus': // unary plus
                case 'tUminus': // unary minus
                    $op = array_pop($formulaStrings);
                    $formulaStrings[] = "$space1$space0{$tokenData}$op";
                    unset($space0, $space1);

                    break;
                case 'tPercent': // percent sign
                    $op = array_pop($formulaStrings);
                    $formulaStrings[] = "$op$space1$space0{$tokenData}";
                    unset($space0, $space1);

                    break;
                case 'tAttrVolatile': // indicates volatile function
                case 'tAttrIf':
                case 'tAttrSkip':
                case 'tAttrChoose':
                    // token is only important for Excel formula evaluator
                    // do nothing
                    break;
                case 'tAttrSpace': // space / carriage return
                    // space will be used when next token arrives, do not alter formulaString stack
                    /** @var string[][] $token */
                    switch ($token['data']['spacetype']) {
                        case 'type0':
                            $space0 = str_repeat(' ', (int) $token['data']['spacecount']);

                            break;
                        case 'type1':
                            $space1 = str_repeat("\n", (int) $token['data']['spacecount']);

                            break;
                        case 'type2':
                            $space2 = str_repeat(' ', (int) $token['data']['spacecount']);

                            break;
                        case 'type3':
                            $space3 = str_repeat("\n", (int) $token['data']['spacecount']);

                            break;
                        case 'type4':
                            $space4 = str_repeat(' ', (int) $token['data']['spacecount']);

                            break;
                        case 'type5':
                            $space5 = str_repeat("\n", (int) $token['data']['spacecount']);

                            break;
                    }

                    break;
                case 'tAttrSum': // SUM function with one parameter
                    $op = array_pop($formulaStrings);
                    $formulaStrings[] = "{$space1}{$space0}SUM($op)";
                    unset($space0, $space1);

                    break;
                case 'tFunc': // function with fixed number of arguments
                case 'tFuncV': // function with variable number of arguments
                    /** @var string[] */
                    $temp1 = $token['data'];
                    $temp2 = $temp1['function'];
                    if ($temp2 != '') {
                        // normal function
                        $ops = []; // array of operators
                        $temp3 = (int) $temp1['args'];
                        for ($i = 0; $i < $temp3; ++$i) {
                            $ops[] = array_pop($formulaStrings);
                        }
                        $ops = array_reverse($ops);
                        $formulaStrings[] = "$space1$space0{$temp2}(" . implode(',', $ops) . ')';
                        unset($space0, $space1);
                    } else {
                        // add-in function
                        $ops = []; // array of operators
                        /** @var int[] */
                        $temp = $token['data'];
                        for ($i = 0; $i < $temp['args'] - 1; ++$i) {
                            $ops[] = array_pop($formulaStrings);
                        }
                        $ops = array_reverse($ops);
                        $function = array_pop($formulaStrings);
                        $formulaStrings[] = "$space1$space0$function(" . implode(',', $ops) . ')';
                        unset($space0, $space1);
                    }

                    break;
                case 'tParen': // parenthesis
                    $expression = array_pop($formulaStrings);
                    $formulaStrings[] = "$space3$space2($expression$space5$space4)";
                    unset($space2, $space3, $space4, $space5);

                    break;
                case 'tArray': // array constant
                    $constantArray = Xls\Biff8::readBIFF8ConstantArray($additionalData);
                    $formulaStrings[] = $space1 . $space0 . $constantArray['value'];
                    $additionalData = substr($additionalData, $constantArray['size']); // bite of chunk of additional data
                    unset($space0, $space1);

                    break;
                case 'tMemArea':
                    // bite off chunk of additional data
                    $cellRangeAddressList = Xls\Biff8::readBIFF8CellRangeAddressList($additionalData);
                    $additionalData = substr($additionalData, $cellRangeAddressList['size']);
                    $formulaStrings[] = "$space1$space0{$tokenData}";
                    unset($space0, $space1);

                    break;
                case 'tArea': // cell range address
                case 'tBool': // boolean
                case 'tErr': // error code
                case 'tInt': // integer
                case 'tMemErr':
                case 'tMemFunc':
                case 'tMissArg':
                case 'tName':
                case 'tNameX':
                case 'tNum': // number
                case 'tRef': // single cell reference
                case 'tRef3d': // 3d cell reference
                case 'tArea3d': // 3d cell range reference
                case 'tRefN':
                case 'tAreaN':
                case 'tStr': // string
                    $formulaStrings[] = "$space1$space0{$tokenData}";
                    unset($space0, $space1);

                    break;
            }
        }
        $formulaString = $formulaStrings[0];

        return $formulaString;
    }

    /**
     * Fetch next token from binary formula data.
     *
     * @param string $formulaData Formula data
     * @param string $baseCell Base cell, only needed when formula contains tRefN tokens, e.g. with shared formulas
     *
     * @return mixed[]
     */
    private function getNextToken(string $formulaData, string $baseCell = 'A1'): array
    {
        // offset: 0; size: 1; token id
        $id = ord($formulaData[0]); // token id
        $name = false; // initialize token name

        switch ($id) {
            case 0x03:
                $name = 'tAdd';
                $size = 1;
                $data = '+';

                break;
            case 0x04:
                $name = 'tSub';
                $size = 1;
                $data = '-';

                break;
            case 0x05:
                $name = 'tMul';
                $size = 1;
                $data = '*';

                break;
            case 0x06:
                $name = 'tDiv';
                $size = 1;
                $data = '/';

                break;
            case 0x07:
                $name = 'tPower';
                $size = 1;
                $data = '^';

                break;
            case 0x08:
                $name = 'tConcat';
                $size = 1;
                $data = '&';

                break;
            case 0x09:
                $name = 'tLT';
                $size = 1;
                $data = '<';

                break;
            case 0x0A:
                $name = 'tLE';
                $size = 1;
                $data = '<=';

                break;
            case 0x0B:
                $name = 'tEQ';
                $size = 1;
                $data = '=';

                break;
            case 0x0C:
                $name = 'tGE';
                $size = 1;
                $data = '>=';

                break;
            case 0x0D:
                $name = 'tGT';
                $size = 1;
                $data = '>';

                break;
            case 0x0E:
                $name = 'tNE';
                $size = 1;
                $data = '<>';

                break;
            case 0x0F:
                $name = 'tIsect';
                $size = 1;
                $data = ' ';

                break;
            case 0x10:
                $name = 'tList';
                $size = 1;
                $data = ',';

                break;
            case 0x11:
                $name = 'tRange';
                $size = 1;
                $data = ':';

                break;
            case 0x12:
                $name = 'tUplus';
                $size = 1;
                $data = '+';

                break;
            case 0x13:
                $name = 'tUminus';
                $size = 1;
                $data = '-';

                break;
            case 0x14:
                $name = 'tPercent';
                $size = 1;
                $data = '%';

                break;
            case 0x15:    //    parenthesis
                $name = 'tParen';
                $size = 1;
                $data = null;

                break;
            case 0x16:    //    missing argument
                $name = 'tMissArg';
                $size = 1;
                $data = '';

                break;
            case 0x17:    //    string
                $name = 'tStr';
                // offset: 1; size: var; Unicode string, 8-bit string length
                $string = self::readUnicodeStringShort(substr($formulaData, 1));
                $size = 1 + $string['size'];
                $data = self::UTF8toExcelDoubleQuoted($string['value']);

                break;
            case 0x19:    //    Special attribute
                // offset: 1; size: 1; attribute type flags:
                switch (ord($formulaData[1])) {
                    case 0x01:
                        $name = 'tAttrVolatile';
                        $size = 4;
                        $data = null;

                        break;
                    case 0x02:
                        $name = 'tAttrIf';
                        $size = 4;
                        $data = null;

                        break;
                    case 0x04:
                        $name = 'tAttrChoose';
                        // offset: 2; size: 2; number of choices in the CHOOSE function ($nc, number of parameters decreased by 1)
                        $nc = self::getUInt2d($formulaData, 2);
                        // offset: 4; size: 2 * $nc
                        // offset: 4 + 2 * $nc; size: 2
                        $size = 2 * $nc + 6;
                        $data = null;

                        break;
                    case 0x08:
                        $name = 'tAttrSkip';
                        $size = 4;
                        $data = null;

                        break;
                    case 0x10:
                        $name = 'tAttrSum';
                        $size = 4;
                        $data = null;

                        break;
                    case 0x40:
                    case 0x41:
                        $name = 'tAttrSpace';
                        $size = 4;
                        // offset: 2; size: 2; space type and position
                        $spacetype = match (ord($formulaData[2])) {
                            0x00 => 'type0',
                            0x01 => 'type1',
                            0x02 => 'type2',
                            0x03 => 'type3',
                            0x04 => 'type4',
                            0x05 => 'type5',
                            default => throw new Exception('Unrecognized space type in tAttrSpace token'),
                        };
                        // offset: 3; size: 1; number of inserted spaces/carriage returns
                        $spacecount = ord($formulaData[3]);

                        $data = ['spacetype' => $spacetype, 'spacecount' => $spacecount];

                        break;
                    default:
                        throw new Exception('Unrecognized attribute flag in tAttr token');
                }

                break;
            case 0x1C:    //    error code
                // offset: 1; size: 1; error code
                $name = 'tErr';
                $size = 2;
                $data = Xls\ErrorCode::lookup(ord($formulaData[1]));

                break;
            case 0x1D:    //    boolean
                // offset: 1; size: 1; 0 = false, 1 = true;
                $name = 'tBool';
                $size = 2;
                $data = ord($formulaData[1]) ? 'TRUE' : 'FALSE';

                break;
            case 0x1E:    //    integer
                // offset: 1; size: 2; unsigned 16-bit integer
                $name = 'tInt';
                $size = 3;
                $data = self::getUInt2d($formulaData, 1);

                break;
            case 0x1F:    //    number
                // offset: 1; size: 8;
                $name = 'tNum';
                $size = 9;
                $data = self::extractNumber(substr($formulaData, 1));
                $data = str_replace(',', '.', (string) $data); // in case non-English locale

                break;
            case 0x20:    //    array constant
            case 0x40:
            case 0x60:
                // offset: 1; size: 7; not used
                $name = 'tArray';
                $size = 8;
                $data = null;

                break;
            case 0x21:    //    function with fixed number of arguments
            case 0x41:
            case 0x61:
                $name = 'tFunc';
                $size = 3;
                // offset: 1; size: 2; index to built-in sheet function
                $mapping = Xls\Mappings::TFUNC_MAPPINGS[self::getUInt2d($formulaData, 1)] ?? null;
                if ($mapping === null) {
                    throw new Exception('Unrecognized function in formula');
                }
                $data = ['function' => $mapping[0], 'args' => $mapping[1]];

                break;
            case 0x22:    //    function with variable number of arguments
            case 0x42:
            case 0x62:
                $name = 'tFuncV';
                $size = 4;
                // offset: 1; size: 1; number of arguments
                $args = ord($formulaData[1]);
                // offset: 2: size: 2; index to built-in sheet function
                $index = self::getUInt2d($formulaData, 2);
                $function = Xls\Mappings::TFUNCV_MAPPINGS[$index] ?? null;
                if ($function === null) {
                    throw new Exception('Unrecognized function in formula');
                }
                $data = ['function' => $function, 'args' => $args];

                break;
            case 0x23:    //    index to defined name
            case 0x43:
            case 0x63:
                $name = 'tName';
                $size = 5;
                // offset: 1; size: 2; one-based index to definedname record
                $definedNameIndex = self::getUInt2d($formulaData, 1) - 1;
                // offset: 2; size: 2; not used
                /** @var string[] */
                $data = $this->definedname[$definedNameIndex]['name'] ?? ''; //* @phpstan-ignore-line

                break;
            case 0x24:    //    single cell reference e.g. A5
            case 0x44:
            case 0x64:
                $name = 'tRef';
                $size = 5;
                $data = Xls\Biff8::readBIFF8CellAddress(substr($formulaData, 1, 4));

                break;
            case 0x25:    //    cell range reference to cells in the same sheet (2d)
            case 0x45:
            case 0x65:
                $name = 'tArea';
                $size = 9;
                $data = Xls\Biff8::readBIFF8CellRangeAddress(substr($formulaData, 1, 8));

                break;
            case 0x26:    //    Constant reference sub-expression
            case 0x46:
            case 0x66:
                $name = 'tMemArea';
                // offset: 1; size: 4; not used
                // offset: 5; size: 2; size of the following subexpression
                $subSize = self::getUInt2d($formulaData, 5);
                $size = 7 + $subSize;
                $data = $this->getFormulaFromData(substr($formulaData, 7, $subSize));

                break;
            case 0x27:    //    Deleted constant reference sub-expression
            case 0x47:
            case 0x67:
                $name = 'tMemErr';
                // offset: 1; size: 4; not used
                // offset: 5; size: 2; size of the following subexpression
                $subSize = self::getUInt2d($formulaData, 5);
                $size = 7 + $subSize;
                $data = $this->getFormulaFromData(substr($formulaData, 7, $subSize));

                break;
            case 0x29:    //    Variable reference sub-expression
            case 0x49:
            case 0x69:
                $name = 'tMemFunc';
                // offset: 1; size: 2; size of the following sub-expression
                $subSize = self::getUInt2d($formulaData, 1);
                $size = 3 + $subSize;
                $data = $this->getFormulaFromData(substr($formulaData, 3, $subSize));

                break;
            case 0x2C: // Relative 2d cell reference reference, used in shared formulas and some other places
            case 0x4C:
            case 0x6C:
                $name = 'tRefN';
                $size = 5;
                $data = Xls\Biff8::readBIFF8CellAddressB(substr($formulaData, 1, 4), $baseCell);

                break;
            case 0x2D:    //    Relative 2d range reference
            case 0x4D:
            case 0x6D:
                $name = 'tAreaN';
                $size = 9;
                $data = Xls\Biff8::readBIFF8CellRangeAddressB(substr($formulaData, 1, 8), $baseCell);

                break;
            case 0x39:    //    External name
            case 0x59:
            case 0x79:
                $name = 'tNameX';
                $size = 7;
                // offset: 1; size: 2; index to REF entry in EXTERNSHEET record
                // offset: 3; size: 2; one-based index to DEFINEDNAME or EXTERNNAME record
                $index = self::getUInt2d($formulaData, 3);
                // assume index is to EXTERNNAME record
                $data = $this->externalNames[$index - 1]['name'] ?? '';

                // offset: 5; size: 2; not used
                break;
            case 0x3A:    //    3d reference to cell
            case 0x5A:
            case 0x7A:
                $name = 'tRef3d';
                $size = 7;

                try {
                    // offset: 1; size: 2; index to REF entry
                    $sheetRange = $this->readSheetRangeByRefIndex(self::getUInt2d($formulaData, 1));
                    // offset: 3; size: 4; cell address
                    $cellAddress = Xls\Biff8::readBIFF8CellAddress(substr($formulaData, 3, 4));

                    $data = "$sheetRange!$cellAddress";
                } catch (PhpSpreadsheetException) {
                    // deleted sheet reference
                    $data = '#REF!';
                }

                break;
            case 0x3B:    //    3d reference to cell range
            case 0x5B:
            case 0x7B:
                $name = 'tArea3d';
                $size = 11;

                try {
                    // offset: 1; size: 2; index to REF entry
                    $sheetRange = $this->readSheetRangeByRefIndex(self::getUInt2d($formulaData, 1));
                    // offset: 3; size: 8; cell address
                    $cellRangeAddress = Xls\Biff8::readBIFF8CellRangeAddress(substr($formulaData, 3, 8));

                    $data = "$sheetRange!$cellRangeAddress";
                } catch (PhpSpreadsheetException) {
                    // deleted sheet reference
                    $data = '#REF!';
                }

                break;
                // Unknown cases    // don't know how to deal with
            default:
                throw new Exception('Unrecognized token ' . sprintf('%02X', $id) . ' in formula');
        }

        return [
            'id' => $id,
            'name' => $name,
            'size' => $size,
            'data' => $data,
        ];
    }

    /**
     * Get a sheet range like Sheet1:Sheet3 from REF index
     * Note: If there is only one sheet in the range, one gets e.g Sheet1
     * It can also happen that the REF structure uses the -1 (FFFF) code to indicate deleted sheets,
     * in which case an Exception is thrown.
     */
    protected function readSheetRangeByRefIndex(int $index): string|false
    {
        if (isset($this->ref[$index])) {
            $type = $this->externalBooks[$this->ref[$index]['externalBookIndex']]['type'];

            switch ($type) {
                case 'internal':
                    // check if we have a deleted 3d reference
                    if ($this->ref[$index]['firstSheetIndex'] == 0xFFFF || $this->ref[$index]['lastSheetIndex'] == 0xFFFF) {
                        throw new Exception('Deleted sheet reference');
                    }

                    // we have normal sheet range (collapsed or uncollapsed)
                    $firstSheetName = $this->sheets[$this->ref[$index]['firstSheetIndex']]['name'];
                    $lastSheetName = $this->sheets[$this->ref[$index]['lastSheetIndex']]['name'];

                    if ($firstSheetName == $lastSheetName) {
                        // collapsed sheet range
                        $sheetRange = $firstSheetName;
                    } else {
                        $sheetRange = "$firstSheetName:$lastSheetName";
                    }

                    // escape the single-quotes
                    $sheetRange = str_replace("'", "''", $sheetRange);

                    // if there are special characters, we need to enclose the range in single-quotes
                    // todo: check if we have identified the whole set of special characters
                    // it seems that the following characters are not accepted for sheet names
                    // and we may assume that they are not present: []*/:\?
                    if (preg_match("/[ !\"@#£$%&{()}<>=+'|^,;-]/u", $sheetRange)) {
                        $sheetRange = "'$sheetRange'";
                    }

                    return $sheetRange;
                default:
                    // TODO: external sheet support
                    throw new Exception('Xls reader only supports internal sheets in formulas');
            }
        }

        return false;
    }

    /**
     * Read byte string (8-bit string length)
     * OpenOffice documentation: 2.5.2.
     *
     * @return array{value: mixed, size: int}
     */
    protected function readByteStringShort(string $subData): array
    {
        // offset: 0; size: 1; length of the string (character count)
        $ln = ord($subData[0]);

        // offset: 1: size: var; character array (8-bit characters)
        $value = $this->decodeCodepage(substr($subData, 1, $ln));

        return [
            'value' => $value,
            'size' => 1 + $ln, // size in bytes of data structure
        ];
    }

    /**
     * Read byte string (16-bit string length)
     * OpenOffice documentation: 2.5.2.
     *
     * @return array{value: mixed, size: int}
     */
    protected function readByteStringLong(string $subData): array
    {
        // offset: 0; size: 2; length of the string (character count)
        $ln = self::getUInt2d($subData, 0);

        // offset: 2: size: var; character array (8-bit characters)
        $value = $this->decodeCodepage(substr($subData, 2));

        //return $string;
        return [
            'value' => $value,
            'size' => 2 + $ln, // size in bytes of data structure
        ];
    }

    protected function parseRichText(string $is): RichText
    {
        $value = new RichText();
        $value->createText($is);

        return $value;
    }

    /**
     * Phpstan 1.4.4 complains that this property is never read.
     * So, we might be able to get rid of it altogether.
     * For now, however, this function makes it readable,
     * which satisfies Phpstan.
     *
     * @return mixed[]
     *
     * @codeCoverageIgnore
     */
    public function getMapCellStyleXfIndex(): array
    {
        return $this->mapCellStyleXfIndex;
    }

    /**
     * Parse conditional formatting blocks.
     *
     * @see https://www.openoffice.org/sc/excelfileformat.pdf Search for CFHEADER followed by CFRULE
     *
     * @return mixed[]
     */
    protected function readCFHeader(): array
    {
        return (new Xls\ConditionalFormatting())->readCFHeader2($this);
    }

    /** @param string[] $cellRangeAddresses */
    protected function readCFRule(array $cellRangeAddresses): void
    {
        (new Xls\ConditionalFormatting())->readCFRule2($cellRangeAddresses, $this);
    }

    public function getVersion(): int
    {
        return $this->version;
    }
}
