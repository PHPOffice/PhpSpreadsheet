<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

interface IWriter
{
    public const SAVE_WITH_CHARTS = 1;

    /**
     * IWriter constructor.
     */
    public function __construct(Spreadsheet $spreadsheet);

    /**
     * Write charts in workbook?
     *        If this is true, then the Writer will write definitions for any charts that exist in the PhpSpreadsheet object.
     *        If false (the default) it will ignore any charts defined in the PhpSpreadsheet object.
     *
     * @return bool
     */
    public function getIncludeCharts();

    /**
     * Set write charts in workbook
     *        Set to true, to advise the Writer to include any charts that exist in the PhpSpreadsheet object.
     *        Set to false (the default) to ignore charts.
     *
     * @param bool $pValue
     *
     * @return IWriter
     */
    public function setIncludeCharts($pValue);

    /**
     * Get Pre-Calculate Formulas flag
     *     If this is true (the default), then the writer will recalculate all formulae in a workbook when saving,
     *        so that the pre-calculated values are immediately available to MS Excel or other office spreadsheet
     *        viewer when opening the file
     *     If false, then formulae are not calculated on save. This is faster for saving in PhpSpreadsheet, but slower
     *        when opening the resulting file in MS Excel, because Excel has to recalculate the formulae itself.
     *
     * @return bool
     */
    public function getPreCalculateFormulas();

    /**
     * Set Pre-Calculate Formulas
     *        Set to true (the default) to advise the Writer to calculate all formulae on save
     *        Set to false to prevent precalculation of formulae on save.
     *
     * @param bool $pValue Pre-Calculate Formulas?
     *
     * @return IWriter
     */
    public function setPreCalculateFormulas($pValue);

    /**
     * Save PhpSpreadsheet to file.
     *
     * @param resource|string $pFilename Name of the file to save
     */
    public function save($pFilename, int $flags = 0): void;

    /**
     * Get use disk caching where possible?
     *
     * @return bool
     */
    public function getUseDiskCaching();

    /**
     * Set use disk caching where possible?
     *
     * @param bool $pValue
     * @param string $pDirectory Disk caching directory
     *
     * @return IWriter
     */
    public function setUseDiskCaching($pValue, $pDirectory = null);

    /**
     * Get disk caching directory.
     *
     * @return string
     */
    public function getDiskCachingDirectory();
}
