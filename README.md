# London Borough of Hounslow

#### A client for the [Bartec Collective API v15](https://confluence.bartecautoid.com/display/COLLAPIR15/) SOAP Web  Service

## Requirements

- PHP 7.4.2+
- Required extensions: [SOAP](https://www.php.net/manual/en/soap.installation.php), [Json](https://www.php.net/manual/en/json.installation.php)

## Setup

- `composer install`

## Usage

### Bartec Client Usage
```
/** BartecClient $bartecClient **/
$bartecClient = new BartecClient(
    new SoapClient(BartecClient::WSDL_AUTH),
    new SoapClient(BartecClient::WSDL_COLLECTIVE_API_V15),
    'BARTEC_API_USERNAME',
    'BARTEC_API_PASSWORD'
);
```
### Bartec Service Usage

```
/** @var BartecService $bartecService */
$bartecService = new BartecService(
    $bartecClient,  // instance of BartecClient
    new FilesystemAdapter('Tests-functional-Service', BartecService::CACHE_LIFETIME)  // Optional PSR-6 cache library
);

```

See [example.php](example.php)

## Tests

Update [BartecServiceTest](tests/functional/Service/BartecServiceTest.php) with API credentials

Run Unit and Functional Tests
 
`./vendor/bin/phpunit tests`
