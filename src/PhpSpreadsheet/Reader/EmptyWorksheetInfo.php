<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

/**
 * Empty listWorksheetInfo and listWorksheetNames methods,
 * facilitating migration from IReader to IReader2.
 */
class EmptyWorksheetInfo
{
    /**
     * Return empty array as worksheet info (Name, Last Column Letter, Last Column Index, Total Rows, Total Columns).
     *
     * @return array<int, array{worksheetName: string, lastColumnLetter: string, lastColumnIndex: int, totalRows: int, totalColumns: int, sheetState: string}>
     */
    public function listWorksheetInfo(string $filename): array
    {
        return [];
    }

    /**
     * Return empty array as names of the worksheets from a file,
     * possibly without parsing the whole file to a Spreadsheet object.
     *
     * @return string[]
     */
    public function listWorksheetNames(string $filename): array
    {
        return [];
    }
}
