<?php
require_once 'vendor/autoload.php';

use Bartec\Client\Client as BartecClient;
use Bartec\Client\SoapClient;
use Bartec\Exception\SoapException;

$bartecClient = new BartecClient(
    new SoapClient(BartecClient::WSDL_AUTH),
    new SoapClient(BartecClient::WSDL_COLLECTIVE_API),
    'BARTEC_USERNAME',
    'BARTEC_PASSWORD'
);

// Fetch a token
try {
    $token = $bartecClient->getAuthToken();
} catch (SoapException $e) {
    // handle $e (see further down)
}

// Fetch an IFRAME token
try {
    $token = $bartecClient->getIFrameAuthToken();
} catch (SoapException $e) {
    // handle $e (see further down)
}

// Service call to retrieve service classes
try {
    $response = $bartecClient->call('ServiceRequests_Classes_Get');
    // get the SOAP result
    $result = $response->getResult();
} catch (SoapException $e) {
    // You have the following available in $e
    $debug = $e->getResponse()->getDebugInfo();     // SOAP debug ie. __getLastRequest(), __getLastResponse() etc if you set 'trace' => 1
    $fault = $e->getResponse()->getFault();         // \SoapFault if it was thrown or NULL
    $exception = $e->getResponse()->getException(); // SoapClients \Exception if thrown  or NULL
    $error = $e->getResponse()->getErrorMessage();  // Bartec's $result->Errors->Error->Message OR $result->Errors->Message if available or NULL
}
