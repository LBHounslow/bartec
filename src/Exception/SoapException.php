<?php

namespace LBHounslow\Bartec\Exception;

use LBHounslow\Bartec\Response\Response;
use Throwable;

class SoapException extends \Exception
{
    /**
     * @var Response
     */
    private $response;

    /**
     * @param Response $response
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(Response $response, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->response = $response;

        if (empty($message)) {
            if ($response->getErrorMessage()) {
                $message = $response->getErrorMessage();
            } elseif ($response->getFault()) {
                $message = $response->getFault()->getMessage();
            } elseif ($response->getException()) {
                $message = $response->getException()->getMessage();
                $code = $response->getException()->getCode();
            }
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}