## London Borough of Hounslow - Bartec Collective

#### A client and service for the [Bartec Collective](https://www.bartecmunicipal.com/software/collective/) SOAP Web Service

The current version of the web service is [v16](https://confluence.bartecautoid.com/display/COLLAPIR16/).

The [v15](https://confluence.bartecautoid.com/display/COLLAPIR15/) service is still available [here](src/Service/v15/BartecService.php).

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
