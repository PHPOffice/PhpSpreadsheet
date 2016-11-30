<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use PhpOffice\PhpSpreadsheet\Writer\PDF\Core;

/**
 *  Copyright (c) 2006 - 2015 PhpSpreadsheet.
 *
 *  This library is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU Lesser General Public
 *  License as published by the Free Software Foundation; either
 *  version 2.1 of the License, or (at your option) any later version.
 *
 *  This library is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *  Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public
 *  License along with this library; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *  @category    PhpSpreadsheet
 *
 *  @copyright   Copyright (c) 2006 - 2015 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 *  @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 *
 *  @version     ##VERSION##, ##DATE##
 */
class PDF implements IWriter
{
    /**
     * The wrapper for the requested PDF rendering engine.
     *
     * @var Core
     */
    private $renderer = null;

    /**
     *  Instantiate a new renderer of the configured type within this container class.
     *
     *  @param  Spreadsheet   $spreadsheet         PhpSpreadsheet object
     *
     *  @throws WriteException    when PDF library is not configured
     */
    public function __construct(Spreadsheet $spreadsheet)
    {
        $pdfLibraryName = Settings::getPdfRendererName();
        if (is_null($pdfLibraryName)) {
            throw new WriteException('PDF Rendering library has not been defined.');
        }

        $pdfLibraryPath = Settings::getPdfRendererPath();
        if (is_null($pdfLibraryName)) {
            throw new WriteException('PDF Rendering library path has not been defined.');
        }
        $includePath = str_replace('\\', '/', get_include_path());
        $rendererPath = str_replace('\\', '/', $pdfLibraryPath);
        if (strpos($rendererPath, $includePath) === false) {
            set_include_path(get_include_path().PATH_SEPARATOR.$pdfLibraryPath);
        }

        $rendererName = '\\PhpOffice\\PhpSpreadsheet\\Writer\\PDF\\'.$pdfLibraryName;
        $this->renderer = new $rendererName($spreadsheet);
    }

    /**
     *  Magic method to handle direct calls to the configured PDF renderer wrapper class.
     *
     *  @param   string   $name        Renderer library method name
     *  @param   mixed[]  $arguments   Array of arguments to pass to the renderer method
     *
     *  @return  mixed    Returned data from the PDF renderer wrapper method
     */
    public function __call($name, $arguments)
    {
        if ($this->renderer === null) {
            throw new WriteException('PDF Rendering library has not been defined.');
        }

        return call_user_func_array([$this->renderer, $name], $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function save($pFilename = null)
    {
        $this->renderer->save($pFilename);
    }
}
