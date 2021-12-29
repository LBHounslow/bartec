## London Borough of Hounslow - Bartec Collective

## Usage


### Bartec Client Usage
```
/** BartecClient $bartecClient **/
$bartecClient = new BartecClient(
    new SoapClient(BartecClient::WSDL_AUTH),
    new SoapClient(BartecClient::WSDL_COLLECTIVE_API_V15),
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
