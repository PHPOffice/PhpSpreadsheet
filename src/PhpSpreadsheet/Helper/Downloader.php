<?php

namespace PhpOffice\PhpSpreadsheet\Helper;

use PhpOffice\PhpSpreadsheet\Exception;

/**
 * Assist downloading files when samples are run in browser.
 * Never run as part of unit tests, which are command line.
 *
 * @codeCoverageIgnore
 */
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
        clearstatcache();
        $filepath = realpath("{$folder}/{$filename}");
        if ($filepath === false || !is_file($filepath) || !is_readable($filepath)) {
            throw new Exception('File not found, or cannot be read');
        }
        $this->filepath = $filepath;
        $this->filename = basename($this->filepath);

        $filetype ??= pathinfo($this->filename, PATHINFO_EXTENSION);
        if (!array_key_exists(strtolower($filetype), self::CONTENT_TYPES)) {
            throw new Exception('Invalid filetype: file cannot be downloaded');
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
        // I cannot tell what this ob_clean is paired with.
        // I have never seen a problem with it, but someone has - issue 3739.
        // Perhaps it should be removed altogether,
        // but making it conditional seems harmless, and safer.
        if ((int) ob_get_length() > 0) {
            ob_clean();
        }

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
