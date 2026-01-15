<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Web;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class Service
{
    /**
     * WEBSERVICE.
     *
     * Returns data from a web service on the Internet or Intranet.
     *
     * Excel Function:
     *        Webservice(url)
     *
     * @return string the output resulting from a call to the webservice
     */
    public static function webService(mixed $url, ?Cell $cell = null): ?string
    {
        if (is_array($url)) {
            $url = Functions::flattenSingleValue($url);
        }
        $url = trim(StringHelper::convertToString($url, false));
        if (mb_strlen($url) > 2048) {
            return ExcelError::VALUE(); // Invalid URL length
        }
        $parsed = parse_url($url);
        $scheme = $parsed['scheme'] ?? '';
        if ($scheme !== 'http' && $scheme !== 'https') {
            return ExcelError::VALUE(); // Invalid protocol
        }
        $domainWhiteList = $cell?->getWorksheet()->getParent()?->getDomainWhiteList() ?? [];
        $host = $parsed['host'] ?? '';
        if (!in_array($host, $domainWhiteList, true)) {
            return ($cell === null) ? null : Functions::NOT_YET_IMPLEMENTED; // will be converted to oldCalculatedValue or null
        }

        // Get results from the webservice
        $ctxArray = [
            'http' => [
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            ],
        ];
        if ($scheme === 'https') {
            $ctxArray['ssl'] = ['crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT];
        }
        $ctx = stream_context_create($ctxArray);
        $output = @file_get_contents($url, false, $ctx);
        if ($output === false || mb_strlen($output) > 32767) {
            return ExcelError::VALUE(); // Output not a string or too long
        }

        return $output;
    }

    /**
     * URLENCODE.
     *
     * Returns data from a web service on the Internet or Intranet.
     *
     * Excel Function:
     *        urlEncode(text)
     *
     * @return string the url encoded output
     */
    public static function urlEncode(mixed $text): string
    {
        if (!is_string($text)) {
            return ExcelError::VALUE();
        }

        return str_replace('+', '%20', urlencode($text));
    }
}
