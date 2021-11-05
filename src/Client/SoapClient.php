<?php

namespace LBHounslow\Bartec\Client;

use LBHounslow\Bartec\Response\SoapResponse;

/**
 * SOAP Client
 * @link https://www.w3.org/TR/soap/
 */
class SoapClient
{
    /**
     * Valid SOAP options
     */
    public const VALID_OPTIONS = [
        'location',             // URL of SOAP server. Required in non-WSDL mode. Can be used in WSDL mode to override the URL.
        'uri',                  // Target namespace of SOAP service. Required in non-WSDL mode.
        'style',                // Possible values are SOAP_RPC or SOAP_DOCUMENT. Only valid in non-WSDL mode.
        'use',                  // Possible values are SOAP_ENCODED or SOAP_LITERAL. Only valid in non-WSDL mode.
        'soap_version',         // Possible values are SOAP_1_1 (default) or SOAP_1_2.
        'authentication',       // Enable HTTP authentication. Possible values are SOAP_AUTHENTICATION_BASIC (default) or SOAP_AUTHENTICATION_DIGEST.
        'login',                // Username for HTTP authentication
        'password',             // Password for HTTP authentication
        'proxy_host',           // URL of proxy server
        'proxy_port',           // Proxy server port
        'proxy_login',          // Username for proxy
        'proxy_password',       // Password for proxy
        'local_cert',           // Path to HTTPS client cert (for authentication)
        'passphrase',           // Passphrase for HTTPS client cert
        'compression',          // Compress request / response. Value is a bitmask of SOAP_COMPRESSION_ACCEPT with either SOAP_COMPRESSION_GZIP or SOAP_COMPRESSION_DEFLATE. For example: SOAP_COMPRESSION_ACCEPT \| SOAP_COMPRESSION_GZIP.
        'encoding',             // Internal character encoding
        'trace',                // Boolean, defaults to FALSE. Enables tracing of requests so faults can be backtraced. Enables use of __getLastRequest(), __getLastRequestHeaders(), __getLastResponse() and __getLastResponseHeaders().
        'classmap',             // Map WSDL types to PHP classes. Value should be an array with WSDL types as keys and PHP class names as values.
        'exceptions',           // Boolean value. Should SOAP errors exceptions (of type `SoapFault).
        'connection_timeout',   // Timeout (in seconds) for the connection to the SOAP service.
        'typemap',              // Array of type mappings. Array should be key/value pairs with the following keys: type_name, type_ns (namespace URI), from_xml (callback accepting one string parameter) and to_xml (callback accepting one object parameter).
        'from_xml',             // How (if at all) should the WSDL file be cached. Possible values are WSDL_CACHE_NONE, WSDL_CACHE_DISK, WSDL_CACHE_MEMORY or WSDL_CACHE_BOTH.
        'cache_wsdl',           // String to use in the User-Agent header.
        'user_agent',           // A resource for a context.
        'stream_context',       // Bitmask of SOAP_SINGLE_ELEMENT_ARRAYS, SOAP_USE_XSI_ARRAY_TYPE, SOAP_WAIT_ONE_WAY_CALLS.
        'keep_alive',           // Boolean value. Send either Connection: Keep-Alive header (TRUE) or Connection: Close header (FALSE).
        'ssl_method'            // (PHP version >= 5.5 only) Which SSL/TLS version to use. Possible values are SOAP_SSL_METHOD_TLS, SOAP_SSL_METHOD_SSLv2, SOAP_SSL_METHOD_SSLv3 or SOAP_SSL_METHOD_SSLv23.
    ];

    const DEFAULT_RESULT_SUFFIX = 'Result';

    /**
     * @var string
     */
    private $wsdl;

    /**
     * @var array
     */
    private $options;

    /**
     * @var null|\SoapClient
     */
    private $client;

    /**
     * @var SoapResponse
     */
    private $soapResponse;

    /**
     * @param string $wsdl
     * @param array $options
     */
    public function __construct(string $wsdl, array $options = []) {
        $this->setWsdl($wsdl);
        $this->setOptions($options);
        $this->soapResponse = new SoapResponse();
    }

    /**
     * @return \SoapClient|null
     * @throws \Exception
     * @throws \SoapFault
     */
    public function connect()
    {
        if ($this->client) {
            return $this->client;
        }

        if (empty($this->getWsdl())) {
            throw new \Exception('WSDL url is required');
        }

        foreach (array_keys($this->getOptions()) as $option) {
            if (!in_array($option, self::VALID_OPTIONS)) {
                throw new \Exception("Option $option is not a valid SOAP option");
            }
        }

        $this->client = $this->getInstance();

        return $this->client;
    }

    /**
     * @return \SoapClient
     * @throws \SoapFault
     */
    public function getInstance()
    {
        return new \SoapClient($this->getWsdl(), $this->getOptions());
    }

    /**
     * @param string $operation
     * @param array $data
     * @param string|null $operationResult
     * @return SoapResponse|null
     */
    public function callOperation(string $operation, array $data = [], string $operationResult = null)
    {
        // Build result property with argument or by using operation and 'Result' ie. {$operation}Result
        $operationResult = $operationResult ?? $operation.self::DEFAULT_RESULT_SUFFIX;

        try {
            $result = $this->connect()->{$operation}($data);
            $result = !empty($result->{$operationResult}) ? $result->{$operationResult} : $result;
        } catch (\SoapFault $fault) {
            $this->soapResponse->setFault($fault);
            $result = null;
        } catch (\Exception $e) {
            $this->soapResponse->setException($e);
            $result = null;
        }

        $this->soapResponse->setDebugInfo($this->getDebugInfo());

        return $this->soapResponse->setResult($result);
    }

    /**
     * @return string
     */
    public function getWsdl(): string
    {
        return $this->wsdl;
    }

    /**
     * @param string $wsdl
     * @return $this
     */
    public function setWsdl(string $wsdl): ?self
    {
        $this->wsdl = $wsdl;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options): ?self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param string $option
     * @return bool
     */
    public function hasOption(string $option)
    {
        return array_key_exists($option, $this->getOptions());
    }

    /**
     * @return $this
     */
    public function debug()
    {
        $this->options['trace'] = 1;
        return $this;
    }

    /**
     * @return array
     */
    public function getDebugInfo()
    {
        return [
            'request' => $this->getLastRequest(),
            'response' => $this->getLastResponse(),
            'requestHeaders' => $this->getLastRequestHeaders(),
            'responseHeaders' => $this->getLastResponseHeaders()
        ];
    }

    /**
     * @return string|null
     */
    private function getLastRequest()
    {
        return $this->client ? $this->client->__getLastRequest() : null;
    }

    /**
     * @return string|null
     */
    private function getLastResponse()
    {
        return $this->client ? $this->client->__getLastResponse() : null;
    }

    /**
     * @return string|null
     */
    private function getLastRequestHeaders()
    {
        return $this->client ? $this->client->__getLastRequestHeaders() : null;
    }

    /**
     * @return string|null
     */
    private function getLastResponseHeaders()
    {
        return $this->client ? $this->client->__getLastResponseHeaders() : null;
    }
}