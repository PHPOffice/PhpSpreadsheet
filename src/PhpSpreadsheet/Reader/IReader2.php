<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

interface IReader2 extends IReader
{
    /**
     * Return worksheet info (Name, Last Column Letter, Last Column Index, Total Rows, Total Columns).
     *
     * @return array<int, array{worksheetName: string, lastColumnLetter: string, lastColumnIndex: int, totalRows: int, totalColumns: int, sheetState: string}>
     */
    public function listWorksheetInfo(string $filename): array;

    /**
     * Returns names of the worksheets from a file,
     * possibly without parsing the whole file to a Spreadsheet object.
     *
     * @return string[]
     */
    public function listWorksheetNames(string $filename): array;
}
