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

Update the constants in [BartecServiceTest](tests/functional/Service/BartecServiceTest.php) for functional tests.

Run all tests
 
`composer test`

```
Code Coverage Report:      
  2022-02-02 15:46:14      
                           
 Summary:                  
  Classes: 22.22% (2/9)    
  Methods: 58.62% (68/116) 
  Lines:   73.12% (408/558)
```

### Contributing

This repository is currently closed for contribution. Please [report an an issue](https://github.com/LBHounslow/bartec/issues) if you come across one.
