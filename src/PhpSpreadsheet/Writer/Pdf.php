<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Pdf implements IWriter
{
    /**
     * The wrapper for the requested PDF rendering engine.
     *
     * @var PDF\Core
     */
    private $renderer;

    /**
     * Instantiate a new renderer of the configured type within this container class.
     *
     * @param Spreadsheet $spreadsheet PhpSpreadsheet object
     *
     * @throws Exception when PDF library is not configured
     */
    public function __construct(Spreadsheet $spreadsheet)
    {
        $pdfLibraryName = Settings::getPdfRendererName();
        if ($pdfLibraryName === null) {
            throw new Exception('PDF Rendering library has not been defined.');
        }

        $rendererName = '\\PhpOffice\\PhpSpreadsheet\\Writer\\Pdf\\' . $pdfLibraryName;
        $this->renderer = new $rendererName($spreadsheet);
    }

    /**
     * Magic method to handle direct calls to the configured PDF renderer wrapper class.
     *
     * @param string $name Renderer library method name
     * @param mixed[] $arguments Array of arguments to pass to the renderer method
     *
     * @return mixed Returned data from the PDF renderer wrapper method
     */
    public function __call($name, $arguments)
    {
        if ($this->renderer === null) {
            throw new Exception('PDF Rendering library has not been defined.');
        }

        return call_user_func_array([$this->renderer, $name], $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function save($pFilename)
    {
        $this->renderer->save($pFilename);
    }
}
