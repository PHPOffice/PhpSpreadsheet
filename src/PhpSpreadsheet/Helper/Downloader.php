<?php

namespace PhpOffice\PhpSpreadsheet\Helper;

use PhpOffice\PhpSpreadsheet\Exception;

class Downloader
{
    protected string $filepath;

    protected string $filename;

    protected string $filetype;

    protected const CONTENT_TYPES = [
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xls' => 'application/vnd.ms-excel',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'csv' => 'text/csv',
        'html' => 'text/html',
        'pdf' => 'application/pdf',
    ];

    public function __construct(string $folder, string $filename, ?string $filetype = null)
    {
        if ((is_dir($folder) === false) || (is_readable($folder) === false)) {
            throw new Exception("Folder {$folder} is not accessable");
        }
        $filepath = "{$folder}/{$filename}";
        $this->filepath = (string) realpath($filepath);
        $this->filename = basename($filepath);
        if ((file_exists($this->filepath) === false) || (is_readable($this->filepath) === false)) {
            throw new Exception("{$this->filename} not found, or cannot be read");
        }

        $filetype ??= pathinfo($filename, PATHINFO_EXTENSION);
        if (array_key_exists(strtolower($filetype), self::CONTENT_TYPES) === false) {
            throw new Exception("Invalid filetype: {$filetype} cannot be downloaded");
        }
        $this->filetype = strtolower($filetype);
    }

    public function download(): void
    {
        $this->headers();

        readfile($this->filepath);
    }

    public function headers(): void
    {
        ob_clean();

        $this->contentType();
        $this->contentDisposition();
        $this->cacheHeaders();
        $this->fileSize();

        flush();
    }

    protected function contentType(): void
    {
        header('Content-Type: ' . self::CONTENT_TYPES[$this->filetype]);
    }

    protected function contentDisposition(): void
    {
        header('Content-Disposition: attachment;filename="' . $this->filename . '"');
    }

    protected function cacheHeaders(): void
    {
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
    }

    protected function fileSize(): void
    {
        header('Content-Length: ' . filesize($this->filepath));
    }
}
