<?php

require_once __DIR__ . '/Bootstrap.php';

use PhpOffice\PhpSpreadsheet\Helper\Sample;

$filename = basename($_GET['name']);
$filetype = $_GET['type'];

$sample = new Sample();
$filepath = "{$sample->getTemporaryFolder()}/{$filename}";

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
        'pdf' => 'application/pdf',
    ];

    public function __construct(string $filepath, string $filetype)
    {
        $this->filepath = $filepath;
        $this->filename = basename($filepath);
        if (file_exists($this->filepath) === false) {
            throw new Exception("{$this->filename} not found");
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
        if (array_key_exists($this->filetype, self::CONTENT_TYPES)) {
            header('Content-Type: ' . self::CONTENT_TYPES[$this->filetype]);

            return;
        }

        throw new Exception('Invalid Filetype');
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

try {
    $downloader = new Downloader($filepath, $filetype);
    $downloader->download();
} catch (Exception $e) {
    die($e->getMessage());
}
