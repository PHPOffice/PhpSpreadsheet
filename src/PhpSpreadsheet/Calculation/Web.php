<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use GuzzleHttp\Psr7\Request;
use PhpOffice\PhpSpreadsheet\Settings;
use Psr\Http\Client\ClientExceptionInterface;

class Web
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
    public static function WEBSERVICE(string $url)
    {
        $url = trim($url);
        if (strlen($url) > 2048) {
            return Functions::VALUE(); // Invalid URL length
        }

        if (!preg_match('/^http[s]?:\/\//', $url)) {
            return Functions::VALUE(); // Invalid protocol
        }

        // Get results from the the webservice
        $client = Settings::getHttpClient();
        $request = new Request('GET', $url);

        try {
            $response = $client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            return Functions::VALUE(); // cURL error
        }

        if ($response->getStatusCode() != 200) {
            return Functions::VALUE(); // cURL error
        }

        $output = (string) $response->getBody();
        if (strlen($output) > 32767) {
            return Functions::VALUE(); // Output not a string or too long
        }

        return $output;
    }
}
