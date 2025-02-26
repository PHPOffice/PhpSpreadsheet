<?php

/**
 * Override to MPDF class to allow setting config options.
 */

namespace PhpOffice\PhpSpreadsheet\Writer\Pdf;

class Mpdf2 extends Mpdf
{
    /**
     * Gets the implementation of external PDF library that should be used.
     * This member extends Mpdf in order to allow specification of
     *     non-default configuration variables. In this case,
     *     it allows inclusion of a font which would not normally
     *     be used by Mpdf (which would instead use a default substitution).
     * Other configuration options may be specified here.
     *
     * @param array $config Configuration array
     */
    protected function createExternalWriterInstance($config): \Mpdf\Mpdf
    {
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $newFontDirectory = __DIR__;
        $config['fontDir'] = array_merge($fontDirs, [$newFontDirectory]);

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        // Note that Mpdf config uses lower-case fontdata
        //    even though it uses camel-case fontDir.
        $fontdata = $defaultFontConfig['fontdata'];
        $fontFile = 'ShadowsIntoLight-Regular.ttf';
        $config['fontdata'] = $fontdata + [ // lowercase letters only in font key
            'shadowsintolight' => [
                'R' => $fontFile,
            ],
        ];

        return new \Mpdf\Mpdf($config);
    }
}
