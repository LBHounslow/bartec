<?php

namespace LBHounslow\Bartec\Response;

class Response extends SoapResponse
{
    /**
     * @param SoapResponse $soapResponse
     * @return $this
     */
    public function hydrate(SoapResponse $soapResponse)
    {
        $this->setResult($soapResponse->getResult());
        $this->setFault($soapResponse->getFault());
        $this->setException($soapResponse->getException());
        $this->setDebugInfo($soapResponse->getDebugInfo());
        return $this;
    }

    /**
     * @return int
     */
    public function getErrorResult()
    {
        return isset($this->getResult()->Errors->Result)
            ? $this->getResult()->Errors->Result
            : 0;
    }

    /**
     * @return string|null
     */
    public function getErrorMessage()
    {
        $message = null;
        if (isset($this->getResult()->Errors->Message)) {
            $message = $this->getResult()->Errors->Message;
        } elseif (isset($this->getResult()->Errors->Error->Message)) {
            $message = $this->getResult()->Errors->Error->Message;
        }
        return $message;
    }

    /**
     * @return string|null
     */
    public function getToken()
    {
        return isset($this->getResult()->Token->TokenString)
            ? $this->getResult()->Token->TokenString
            : null;
    }

    /**
     * @return bool
     */
    public function hasErrors(): ?bool
    {
        return parent::hasErrors()
            || $this->getErrorResult()
            || !empty($this->getErrorMessage());
    }
}