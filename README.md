# London Borough of Hounslow

#### A client for the [Bartec Collective API v16](https://confluence.bartecautoid.com/display/COLLAPIR16/) SOAP Web  Service

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
    new SoapClient(BartecClient::WSDL_COLLECTIVE_API_V16),
    'BARTEC_API_USERNAME',
    'BARTEC_API_PASSWORD',
    ['trace' => 1] // optional for debugging
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

Update [BartecServiceTest](tests/functional/Service/BartecServiceTest.php) with your Bartec TEST API credentials

Run Unit and Functional Tests
 
`composer test`

```
Code Coverage Report:      
  2021-12-14 14:22:25      
                           
 Summary:                  
  Classes: 33.33% (2/6)    
  Methods: 53.25% (41/77)  
  Lines:   75.28% (338/449)
```
