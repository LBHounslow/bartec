<?php

namespace Bartec\Client;

use Bartec\Exception\SoapException;
use Bartec\Response\Response;

class Client
{
    public const WSDL_AUTH = 'https://collectiveapi.bartec-systems.com/Auth/Authenticate.asmx?WSDL';
    public const WSDL_COLLECTIVE_API = 'https://collectiveapi.bartec-systems.com/API-R1531/CollectiveAPI.asmx?WSDL';

    /**
     * @var SoapClient
     */
    private $authSoapClient;

    /**
     * @var SoapClient
     */
    private $collectiveSoapClient;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @param SoapClient $authSoapClient
     * @param SoapClient $collectiveSoapClient
     * @param string $username
     * @param string $password
     */
    public function __construct(
        SoapClient $authSoapClient,
        SoapClient $collectiveSoapClient,
        string $username,
        string $password
    ) {
        $this->authSoapClient = $authSoapClient;
        $this->collectiveSoapClient = $collectiveSoapClient;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return SoapClient
     */
    public function getAuthSoapClient()
    {
        return $this->authSoapClient;
    }

    /**
     * @return SoapClient
     */
    public function getCollectiveSoapClient()
    {
        return $this->collectiveSoapClient;
    }

    /**
     * @return string|null
     * @throws SoapException
     */
    public function getAuthToken()
    {
        $response = (new Response())->hydrate(
            $this->getAuthSoapClient()->callOperation('Authenticate', [
                'user' => $this->username,
                'password' => $this->password
            ])
        );

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response->getToken();
    }

    /**
     * @return string|null
     * @throws SoapException
     */
    public function getIFrameAuthToken()
    {
        $response = (new Response())->hydrate(
            $this->getAuthSoapClient()->callOperation('AuthenticateIFrame', [
                'user' => $this->username,
                'password' => $this->password
            ])
        );

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response->getToken();
    }

    /**
     * @param string $operation
     * @param array $data
     * @return Response
     * @throws SoapException
     */
    public function call(string $operation, array $data = [])
    {
        $data = array_merge($data, ['token' => $this->getAuthToken()]);

        $response = (new Response())->hydrate(
            $this->getCollectiveSoapClient()->callOperation($operation, $data)
        );

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response;
    }
}