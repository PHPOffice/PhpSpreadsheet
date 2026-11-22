<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;

class XlsSstProbe extends Xls
{
    public function readSstPayload(string $payload): int
    {
        return $this->readSstRecords($payload, '');
    }

    public function readSstRecords(string $first, string $second): int
    {
        return $this->readSstRecordList([$first, $second]);
    }

    /** @param string[] $records */
    public function readSstRecordList(array $records): int
    {
        $data = '';
        foreach ($records as $index => $record) {
            $type = ($index === 0) ? self::XLS_TYPE_SST : self::XLS_TYPE_CONTINUE;
            $data .= pack('vv', $type, strlen($record)) . $record;
        }
        $data .= pack('vv', 0x000A, 0);
        $this->data = $data;
        $this->pos = 0;
        $this->sst = [];
        $this->readSst();

        return count($this->sst);
    }

    /** @return array<array{value: string, fmtRuns: mixed[]}> */
    public function getSst(): array
    {
        return $this->sst;
    }
}
