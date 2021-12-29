<?php

namespace Tests\Functional;

use PHPUnit\Framework\TestCase;

abstract class BartecTestCase extends TestCase
{
    const REPORTER_TITLE = 'Mr';
    const REPORTER_FORENAME = 'Joe';
    const REPORTER_OTHERNAMES = 'Functional Test';
    const REPORTER_SURNAME = 'Bloggs';
    const REPORTER_EMAIL = 'Joe.Bloggs@invalid-email-address.com';
    const REPORTER_TELEPHONE = '1234567891011';
    const REPORTER_SPECIAL_COMMUNICATION_NEEDS = '';
    const REPORTER_EXTERNAL_REFERENCE = '123456';

    const TEST_NOTE = 'Functional Test Note';

    const RESIDENTIAL_UPRN = '100021529122'; // 1 Oaks Avenue, Feltham, TW13 5JD

    const GARDEN_WASTE_SUBSCRIPTION_EXTENDED_DATA = [
        [
            'FieldName' => 'SackOrBin',
            'FieldValue' => 'Bin',
        ],
        [
            'FieldName' => 'CurrentBins',
            'FieldValue' => 0,
        ],
        [
            'FieldName' => 'CurrentSacks',
            'FieldValue' => 0,
        ],
        [
            'FieldName' => 'NewBins',
            'FieldValue' => 1,
        ],
        [
            'FieldName' => 'NewSacks',
            'FieldValue' => 0,
        ],
        [
            'FieldName' => 'CouncilTaxSupport',
            'FieldValue' => 'No',
        ],
        [
            'FieldName' => 'PaymentMethod',
            'FieldValue' => 'card',
        ],
        [
            'FieldName' => 'PaymentReferenceNumber',
            'FieldValue' => 'JADU0000012345',
        ]
    ];
}