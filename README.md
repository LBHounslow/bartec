## London Borough of Hounslow - Bartec Collective

#### A library for the [Bartec Collective](https://www.bartecmunicipal.com/software/collective/) SOAP Web Service

This library includes both a [Client](src/Client/Client.php) and a [BartecService](src/Service/BartecService.php). The library can be used with [v15](https://collectiveapi.bartec-systems.com/API-R1531/CollectiveAPI.asmx?WSDL) or [v16](https://collectiveapi.bartec-systems.com/API-R1604/CollectiveAPI.asmx?WSDL) of the Bartec Collective.

The [BartecService](src/Service/BartecService.php) has several methods that are used regularly by LBHounslow which you are free to use. For any other call you can use the [Client](src/Client/Client.php) directly.

For more on how to use this client, see [usage documentation](docs/USAGE.md)

### Releases

- These are covered in [the Changelog](docs/CHANGELOG.md)

## Requirements

- [PHP 7.2+](https://www.php.net/downloads.php)
- [Git](https://git-scm.com/downloads)
- [Composer](https://getcomposer.org)

## Setup

- `composer install`

## Tests

Update [BartecServiceTest](tests/functional/Service/BartecServiceTest.php) with your Bartec TEST API credentials

Run all tests
 
`composer test`

```
Code Coverage Report:      
  2021-12-29 08:54:20      
                           
 Summary:                  
  Classes: 33.33% (2/6)    
  Methods: 51.28% (40/78)  
  Lines:   73.63% (335/455)
```

### Contributing

This repository is currently closed for contribution. Please [report an an issue](https://github.com/LBHounslow/bartec/issues) if you come across one.
