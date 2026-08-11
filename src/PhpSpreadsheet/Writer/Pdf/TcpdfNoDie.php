<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Pdf;

class TcpdfNoDie extends Tcpdf
{
    /**
     * By default, Tcpdf will die sometimes rather than throwing exception.
     * And this is controlled by a defined constant in the global namespace,
     * not by an instance property. Ugh!
     * Using this class instead of the class which it extends will probably
     * be suitable for most users. But not for those who have customized
     * their config file. Which is why this isn't the default, so that
     * there is no breaking change for those users.
     * Note that if both Tcpdf and TcpdfNoDie are used in the same process,
     * the first one used "wins" the battle of the defines.
     */
    protected function defines(): void
    {
        if (!defined('K_TCPDF_EXTERNAL_CONFIG')) {
            define('K_TCPDF_EXTERNAL_CONFIG', true);
        }
        if (!defined('K_TCPDF_THROW_EXCEPTION_ERROR')) {
            define('K_TCPDF_THROW_EXCEPTION_ERROR', true);
        }
    }
}
