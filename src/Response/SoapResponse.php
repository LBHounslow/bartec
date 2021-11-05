<?php

namespace LBHounslow\Bartec\Response;

/**
 * SOAP Response
 */
class SoapResponse
{
    /**
     * @var mixed|null
     */
    private $result;

    /**
     * @var \SoapFault|null
     */
    private $fault;

    /**
     * @var \Exception|null
     */
    private $exception;

    /**
     * @var array
     */
    private $debugInfo = [];

    /**
     * @param mixed|null $result
     */
    public function __construct($result = null)
    {
        $this->setResult($result);
    }

    /**
     * @return mixed|null
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed|null $result
     * @return $this
     */
    public function setResult($result): ?self
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @return \SoapFault|null
     */
    public function getFault()
    {
        return $this->fault;
    }

    /**
     * @param \SoapFault|null $fault
     * @return $this
     */
    public function setFault($fault): ?self
    {
        $this->fault = $fault;
        return $this;
    }

    /**
     * @return \Exception|null
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param \Exception|null $exception
     * @return $this
     */
    public function setException($exception): ?self
    {
        $this->exception = $exception;
        return $this;
    }

    /**
     * @return array
     */
    public function getDebugInfo(): ?array
    {
        return $this->debugInfo;
    }

    /**
     * @param array $debugInfo
     * @return $this
     */
    public function setDebugInfo(array $debugInfo): ?self
    {
        $this->debugInfo = $debugInfo;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasErrors(): ?bool
    {
        return $this->getFault() || $this->getException();
    }
}