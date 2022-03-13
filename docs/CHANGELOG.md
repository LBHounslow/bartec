## London Borough of Hounslow - Bartec Collective

## Changelog

### Release v2.0 `13/03/2022`

This release includes:

[APID-76](https://hounslow.atlassian.net/browse/APID-76) - Added Feature Type Name to Extended Data Field Mapping for Report a Missed Bin 2022

### Release v1.9.1 `18/02/2022`

This release includes a fix for the `Jobs_Detail_Get` Collective API method.

- Fixed bug in `AbstractApiVersionAdapter.getJobDetail` call.
- Added functional test coverage for this method.

### Release v1.9 `02/02/2022`

This release is in preparation for Hounslow switching from Bartec Collective v15 to v16.

- Added an API version adapter so that we can separate the version specific SOAP Service changes (differences between v15 and v16) into [Version15Adapter](../src/Adapter/Version15Adapter.php) and [Version16Adapter](../src/Adapter/Version16Adapter.php).
- The [BartecService](../src/Service/BartecService.php) now accepts **version** as an argument (eg. `v15` or `v16`) and will load the appropriate version adapter and WSDL. 
- You are able to override the version adapter WSDL by passing this into the `BartecService` if it changes due to a release.
- Updated functional test coverage to cover new functional in `BartecService`.

### Release v1.8 `24/01/2022`

- Adjusted php minimum version from >=7.4.2 to ^7.2 so it is compatible with Jadu.

### Release v1.7 `11/01/2022`

- Fix for `getEventsByUPRN` maximum date.
- Changed the return type for the relevant service calls to `\stdClass|null`
- Added optional arguments for `getEventsByUPRN` and `getJobs`
- Added/sorted more enumerated fields.

### Release v1.6 `29/12/2021`

Removed `symfony/cache` dependency - this was only ever used in the usage example.

### Release v1.5 `29/12/2021`

- Added [getFeatureSchedules](https://github.com/LBHounslow/bartec/blob/hotfix-get-feature-schedules/src/Service/BartecService.php#L940) to the BartecService along with [functional test coverage](https://github.com/LBHounslow/bartec/blob/hotfix-get-feature-schedules/tests/functional/Service/BartecServiceTest.php#L570).
- Updated [phpunit.xml.dist](https://github.com/LBHounslow/bartec/blob/hotfix-get-feature-schedules/phpunit.xml.dist#L1) and added [script to composer.json](https://github.com/LBHounslow/bartec/blob/hotfix-get-feature-schedules/composer.json#L25) to run all tests using `composer test`
- Added [CHANGELOG](https://github.com/LBHounslow/bartec/blob/hotfix-get-feature-schedules/docs/CHANGELOG.md) and moved usage to [USAGE](https://github.com/LBHounslow/bartec/blob/hotfix-get-feature-schedules/docs/USAGE.md) docs. Added phpunit code [test coverage report to README](https://github.com/LBHounslow/bartec/blob/hotfix-get-feature-schedules/README.md?plain=1#L30).

Fixes:
- Corrected namespace [case issue](https://github.com/LBHounslow/bartec/blob/hotfix-get-feature-schedules/tests/functional/Service/BartecServiceTest.php#L12) in `BartecServiceTest` and `BartecTestCase`.

### Release v1.4 `05/11/2021`

- Changed namespace throughout to include the prefix [LBHounslow\Bartec](https://github.com/LBHounslow/bartec/blob/v1.4/composer.json#L15) to be consistent with other lb-hounslow packages.

### Release v1.3 `19/09/2021`

- Updated the [BartecService PSR cache argument](https://github.com/LBHounslow/bartec/blob/v1.3/src/Service/BartecService.php#L36) to be **optional**. Updated methods to check for existence of cache.

### Release v1.2 `13/09/2021`

Updates:
- Upgrade PHP version from 7.2 to 7.4.2.
- The Hounslow API and Jadu XFP Custom Bundle were using 2 variations of a BartecService. I have consolidated these into the [BartecService](https://github.com/LBHounslow/bartec/blob/v1.2/src/Service/BartecService.php#L17) (added to this library) so that both applications can use one service. _It will allow us to add functional test coverage in preparation for the v16 upgrade._
- Added [BartecServiceEnum](https://github.com/LBHounslow/bartec/blob/v1.2/src/Enum/BartecServiceEnum.php#L5) which is used by the Bartec Service. This service [requires a PSR cache](https://github.com/LBHounslow/bartec/blob/v1.2/src/Service/BartecService.php#L33) to be passed into the constructor.
- Added [functional test coverage](https://github.com/LBHounslow/bartec/blob/v1.2/tests/functional/Service/BartecServiceTest.php#L15) for the BartecService.
- Updated [usage documentation](https://github.com/LBHounslow/bartec/blob/v1.2/example.php#L46) in example.php

Fixes:
- Added [check before overriding](https://github.com/LBHounslow/bartec/blob/v1.2/src/Client/Client.php#L66) both `SoapClient` options so that we don't always override with empty options.

### Release v1.1 `01/03/2021`

- Added [soapOptions](https://github.com/LBHounslow/bartec/blob/v1.1/src/Client/Client.php#L43) argument and [setSoapOptions](https://github.com/LBHounslow/bartec/blob/v1.1/src/Client/Client.php#L88) method so that we can override options for both `SoapClient` arguments.

### Release v1.0 `20/04/2020`

Initial application layout.
