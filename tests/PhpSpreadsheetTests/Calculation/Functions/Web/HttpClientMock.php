<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Web;

use Closure;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpClientMock implements ClientInterface
{
    /** @var Closure|ResponseInterface */
    public $response;

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        if ($this->response instanceof Closure) {
            return ($this->response)($request);
        }

        return $this->response;
    }
}
