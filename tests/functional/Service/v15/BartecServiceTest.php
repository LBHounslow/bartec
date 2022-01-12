<?php

namespace Tests\Functional\Service\v15;

use LBHounslow\Bartec\Client\Client as BartecClient;
use LBHounslow\Bartec\Client\SoapClient;
use LBHounslow\Bartec\Enum\BartecServiceEnum;
use LBHounslow\Bartec\Enum\DateEnum;
use LBHounslow\Bartec\Exception\SoapException;
use LBHounslow\Bartec\Response\Response;
use LBHounslow\Bartec\Service\BartecService;
use Tests\Functional\BartecTestCase;

class BartecServiceTest extends BartecTestCase
{
    /**
     * @var BartecService
     */
    private $bartecService;

    public function setUp(): void
    {
        $this->bartecService = new BartecService(
            new BartecClient(
                new SoapClient(BartecClient::WSDL_AUTH),
                new SoapClient(BartecClient::WSDL_COLLECTIVE_API_V15),
                'BARTEC_API_USERNAME',
                'BARTEC_API_PASSWORD',
                ['trace' => 1]
            )
        // no cache passed for testing
        );
        parent::setUp();
    }

    public function testItGetsServiceRequestClasses()
    {
        /** @var Response $response */
        $response = $this->bartecService->getServiceRequestClasses();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertFalse($response->hasErrors());
        $this->assertNotNull($response->getResult());
        $this->assertIsArray($response->getResult()->ServiceClass);
        $this->assertNotEmpty($response->getResult()->ServiceClass[0]->Name);
    }

    public function testItReturnsServiceRequestClassFromServiceRequestClassName()
    {
        /** @var \stdClass|null $ServiceRequestClass */
        $ServiceRequestClass = $this->bartecService->getServiceRequestClassFromServiceRequestClassName(BartecServiceEnum::SERVICE_REQUEST_CLASS_NAME_WASTE);
        $this->assertNotNull($ServiceRequestClass);
        $this->assertInstanceOf(\stdClass::class, $ServiceRequestClass);
        $this->assertTrue(isset($ServiceRequestClass->Name));
        $this->assertEquals(BartecServiceEnum::SERVICE_REQUEST_CLASS_NAME_WASTE, $ServiceRequestClass->Name);
    }

    public function testItReturnsNullIfServiceRequestClassDoesNotExist()
    {
        /** @var \stdClass|null $ServiceRequestClass */
        $ServiceRequestClass = $this->bartecService->getServiceRequestClassFromServiceRequestClassName('Invalid Name');
        $this->assertNull($ServiceRequestClass);
    }

    public function testItGetsServiceRequestTypes()
    {
        /** @var Response $response */
        $response = $this->bartecService->getServiceRequestTypes();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertFalse($response->hasErrors());
        $this->assertNotNull($response->getResult());
        $this->assertIsArray($response->getResult()->ServiceType);
        $this->assertNotEmpty($response->getResult()->ServiceType[0]->Name);
    }

    public function testItGetsServiceRequestTypesForServiceRequestClassId()
    {
        /** @var Response $response */
        $response = $this->bartecService->getServiceRequestClasses();
        $ServiceRequestClass = $response->getResult()->ServiceClass[0];

        /** @var Response $response */
        $response = $this->bartecService->getServiceRequestTypesByServiceRequestClassId($ServiceRequestClass->ID);
        $this->assertNotNull($response->getResult());
        $this->assertIsArray($response->getResult()->ServiceType);
        $this->assertNotEmpty($response->getResult()->ServiceType[0]->Name);
    }

    /**
     * @param string $status
     * @dataProvider serviceRequestStatusDataProvider
     */
    public function testItGetsServiceRequestStatusForServiceTypeIdByStatus(string $status)
    {
        /** @var Response $response */
        $response = $this->bartecService->getServiceRequestClasses();
        $ServiceRequestClass = $response->getResult()->ServiceClass[0];

        /** @var Response $response */
        $response = $this->bartecService->getServiceRequestTypesByServiceRequestClassId($ServiceRequestClass->ID);

        /** @var \stdClass|null $ServiceRequestStatus */
        $ServiceRequestStatus = $this->bartecService->getServiceRequestStatusForServiceTypeIdByStatus(
            $response->getResult()->ServiceType[0]->ID,
            $status
        );

        $this->assertInstanceOf(\stdClass::class, $ServiceRequestStatus);
        $this->assertNotEmpty($ServiceRequestStatus->ID);
        $this->assertEquals($ServiceRequestStatus->Status, $status);
    }

    public function serviceRequestStatusDataProvider()
    {
        return [
            [BartecServiceEnum::STATUS_PENDING],
            [BartecServiceEnum::STATUS_OPEN]
        ];
    }

    public function testItGetsServiceRequestTypeFromServiceTypeName()
    {
        $result = $this->bartecService->getServiceRequestTypeFromServiceTypeName(BartecServiceEnum::SERVICE_REQUEST_TYPE_NAME_MISSED_BIN);
        $this->assertTrue(!empty($result->Name));
        $this->assertEquals(BartecServiceEnum::SERVICE_REQUEST_TYPE_NAME_MISSED_BIN, $result->Name);
    }

    public function testItGetsServiceRequestTypeFromServiceRequestClassIdAndServiceRequestTypeName()
    {
        /** @var \stdClass|null $ServiceRequestClass */
        $ServiceRequestClass = $this->bartecService->getServiceRequestClassFromServiceRequestClassName(BartecServiceEnum::SERVICE_REQUEST_CLASS_NAME_GARDEN_WASTE);

        /** @var \stdClass|null $ServiceRequestType */
        $ServiceRequestType = $this->bartecService->getServiceRequestTypeFromServiceRequestClassIdAndServiceRequestTypeName(
            $ServiceRequestClass->ID,
            BartecServiceEnum::SERVICE_REQUEST_TYPE_NAME_SUBSCRIPTION
        );

        $this->assertTrue(!empty($ServiceRequestType->ID));
        $this->assertEquals(BartecServiceEnum::SERVICE_REQUEST_TYPE_NAME_SUBSCRIPTION, $ServiceRequestType->Name);
    }

    /**
     * @param array $data
     * @param \stdClass $ServiceRequestType
     * @dataProvider createServiceRequestDataProvider
     */
    public function testItCreatesServiceRequest(array $data, \stdClass $ServiceRequestType)
    {
        /** @var \stdClass|null $ServiceRequest */
        $ServiceRequest = $this->bartecService->createServiceRequest($data);

        $this->assertInstanceOf(\stdClass::class, $ServiceRequest);
        $this->assertNotEmpty($ServiceRequest->ServiceCode);
        $this->assertEquals(BartecServiceEnum::SERVICE_REQUEST_CODE_PREFIX, substr($ServiceRequest->ServiceCode, 0, 2));
    }

    /**
     * @param array $data
     * @param \stdClass $ServiceRequestType
     * @dataProvider createServiceRequestDataProvider
     */
    public function testItGetsServiceRequestDetail(array $data, \stdClass $ServiceRequestType)
    {
        /** @var \stdClass|null $ServiceRequest */
        $ServiceRequest = $this->bartecService->createServiceRequest($data);

        /** @var \stdClass|null $ServiceRequestDetail */
        $ServiceRequestDetail = $this->bartecService->getServiceRequestDetail($ServiceRequest->ServiceCode);

        $this->assertNotEmpty($ServiceRequestDetail->ServiceRequest->ServiceCode);
        $this->assertEquals($ServiceRequest->ServiceCode, $ServiceRequestDetail->ServiceRequest->ServiceCode);
    }

    /**
     * @param array $data
     * @param \stdClass $ServiceRequestType
     * @dataProvider createServiceRequestDataProvider
     */
    public function testItSetsServiceRequestStatus(array $data, \stdClass $ServiceRequestType)
    {
        /** @var \stdClass|null $ServiceRequest */
        $ServiceRequest = $this->bartecService->createServiceRequest($data);

        /** @var \stdClass|null $ServiceRequestStatus */
        $ServiceRequestStatus = $this->bartecService->getServiceRequestStatusForServiceTypeIdByStatus(
            $ServiceRequestType->ID,
            BartecServiceEnum::STATUS_OPEN
        );

        /** @var Response $response */
        $response = $this->bartecService->setServiceRequestStatus($ServiceRequest, $ServiceRequestStatus);
        $this->assertFalse($response->hasErrors());
    }

    /**
     * @param array $data
     * @param \stdClass $ServiceRequestType
     * @dataProvider createServiceRequestDataProvider
     */
    public function testItUpdatesServiceRequest(array $data, \stdClass $ServiceRequestType)
    {
        /** @var \stdClass|null $ServiceRequest */
        $ServiceRequest = $this->bartecService->createServiceRequest($data);

        /** @var \stdClass $result */
        $result = $this->bartecService->updateServiceRequest(
            $ServiceRequest->ServiceCode,
            [
                'reporterContact' => [
                    'Forename' => 'Tim',
                    'Surname' => 'Smith'
                ]
            ]
        );

        /** @var \stdClass|null $ServiceRequestDetail */
        $ServiceRequestDetail = $this->bartecService->getServiceRequestDetail($ServiceRequest->ServiceCode);

        $this->assertTrue(!empty($ServiceRequestDetail->ServiceRequest->ReporterContact));
        $this->assertEquals('Tim', $ServiceRequestDetail->ServiceRequest->ReporterContact->Forename);
        $this->assertEquals('Smith', $ServiceRequestDetail->ServiceRequest->ReporterContact->Surname);
    }

    public function testItGetServiceNoteTypeFromNoteTypeDescription()
    {
        /** @var \stdClass $ServiceRequestNoteType */
        $ServiceRequestNoteType = $this->bartecService->getServiceNoteTypeFromNoteTypeDescription(BartecServiceEnum::SERVICE_NOTE_DESCRIPTION_GENERAL_NOTE);
        $this->assertNotEmpty(!empty($ServiceRequestNoteType->ID));
        $this->assertEquals(BartecServiceEnum::SERVICE_NOTE_DESCRIPTION_GENERAL_NOTE, $ServiceRequestNoteType->Description);
    }

    /**
     * @param array $data
     * @param \stdClass $ServiceRequestType
     * @dataProvider createServiceRequestDataProvider
     */
    public function testItCreatesServiceRequestNote(array $data, \stdClass $ServiceRequestType)
    {
        /** @var \stdClass|null $ServiceRequest */
        $ServiceRequest = $this->bartecService->createServiceRequest($data);

        /** @var \stdClass|null $ServiceRequestDetail */
        $ServiceRequestDetail = $this->bartecService->getServiceRequestDetail($ServiceRequest->ServiceCode);

        /** @var \stdClass $ServiceRequestNoteType */
        $ServiceRequestNoteType = $this->bartecService->getServiceNoteTypeFromNoteTypeDescription(BartecServiceEnum::SERVICE_NOTE_DESCRIPTION_GENERAL_NOTE);

        /** @var Response $response */
        $response = $this->bartecService->createServiceRequestNote(
            $ServiceRequestDetail->ServiceRequest->id,
            self::TEST_NOTE,
            $ServiceRequestNoteType->ID,
            1,
            BartecServiceEnum::DEFAULT_NOTE_COMMENT
        );
        $this->assertFalse($response->hasErrors());

        /** @var \stdClass|null $ServiceRequestDetail */
        $ServiceRequestDetail = $this->bartecService->getServiceRequestDetail($ServiceRequest->ServiceCode);

        $this->assertTrue(!empty($ServiceRequestDetail->ServiceRequest->Notes->Note));
        $this->assertEquals(BartecServiceEnum::SERVICE_NOTE_DESCRIPTION_GENERAL_NOTE, $ServiceRequestDetail->ServiceRequest->Notes->Note->NoteType->Description);
        $this->assertEquals('Functional Test Note', $ServiceRequestDetail->ServiceRequest->Notes->Note->Note);
    }

    /**
     * @param array $data
     * @param \stdClass $ServiceRequestType
     * @dataProvider createServiceRequestDataProvider
     */
    public function testItCreatesServiceRequestDocument(array $data, \stdClass $ServiceRequestType)
    {
        /** @var \stdClass|null $ServiceRequest */
        $ServiceRequest = $this->bartecService->createServiceRequest($data);

        /** @var \stdClass|null $ServiceRequestDetail */
        $ServiceRequestDetail = $this->bartecService->getServiceRequestDetail($ServiceRequest->ServiceCode);
        $this->assertTrue(!empty($ServiceRequestDetail->ServiceRequest->id));

        /** @var \SplFileInfo $file */
        $file = new \SplFileInfo(dirname(__FILE__, 2).DIRECTORY_SEPARATOR.'sample.pdf');

        $data = [
            'ServiceRequestID' => $ServiceRequestDetail->ServiceRequest->id,
            'Public' => '1',
            'AttachedDocument' => [
                'ID' => rand(1, 1000),
                'Name' => $file->getFilename(),
                'Document' => file_get_contents($file->getPathname()),
                'FileExtension' => $file->getExtension(),
            ],
        ];

        $this->bartecService->createServiceRequestDocument($data);

        /** @var \stdClass|null $ServiceRequestDetail */
        $ServiceRequestDetail = $this->bartecService->getServiceRequestDetail($ServiceRequest->ServiceCode);

        $this->assertTrue(!empty($ServiceRequestDetail->ServiceRequest->AttachedDocuments->AttachedDocument));
        $this->assertEquals($file->getFilename(), $ServiceRequestDetail->ServiceRequest->AttachedDocuments->AttachedDocument->DocumentName);
    }

    public function createServiceRequestDataProvider()
    {
        $this->setUp();

        /** @var \stdClass|null $ServiceRequestClass */
        $ServiceRequestClass = $this->bartecService->getServiceRequestClassFromServiceRequestClassName(BartecServiceEnum::SERVICE_REQUEST_CLASS_NAME_GARDEN_WASTE);

        /** @var \stdClass|null $ServiceRequestType */
        $ServiceRequestType = $this->bartecService->getServiceRequestTypeFromServiceRequestClassIdAndServiceRequestTypeName(
            $ServiceRequestClass->ID,
            BartecServiceEnum::SERVICE_REQUEST_TYPE_NAME_SUBSCRIPTION
        );

        /** @var \stdClass|null $ServiceRequestStatus */
        $ServiceRequestStatus = $this->bartecService->getServiceRequestStatusForServiceTypeIdByStatus(
            $ServiceRequestType->ID,
            BartecServiceEnum::STATUS_PENDING
        );

        return [
            [
                [
                    'UPRN' => self::RESIDENTIAL_UPRN,
                    'ServiceRequest_Location' => '',
                    'serviceLocationDescription' => '',
                    'DateRequested' => (new \DateTimeImmutable())->format(DateEnum::ISO8601_NO_TIMEZONE),
                    'ServiceTypeID' => $ServiceRequestType->ID,
                    'ServiceStatusID' => $ServiceRequestStatus->ID,
                    'reporterContact' => [
                        'Title' => self::REPORTER_TITLE,
                        'Forename' => self::REPORTER_FORENAME,
                        'OtherNames' => self::REPORTER_OTHERNAMES,
                        'Surname' => self::REPORTER_SURNAME,
                        'Email' => self::REPORTER_EMAIL,
                        'Telephone' => self::REPORTER_TELEPHONE,
                        'SpecialCommunicationNeeds' => self::REPORTER_SPECIAL_COMMUNICATION_NEEDS,
                        'ExternalReference' => self::REPORTER_EXTERNAL_REFERENCE,
                        'ReporterType' => BartecServiceEnum::REPORTER_TYPE_PUBLIC
                    ],
                    'reporterBusiness' => '',
                    'source' => BartecServiceEnum::DEFAULT_SERVICE_REQUEST_SOURCE,
                    'ExternalReference' => self::REPORTER_EXTERNAL_REFERENCE,
                    'LandTypeID' => $ServiceRequestType->DefaultLandType->ID,
                    'SLAID' => $ServiceRequestType->DefaultSLA->ID,
                    'CrewID' => $ServiceRequestType->DefaultCrew->ID,
                    'extendedData' => [
                        'ServiceRequests_CreateServiceRequests_CreateFields' => self::GARDEN_WASTE_SUBSCRIPTION_EXTENDED_DATA
                    ],
                    'relatedServiceRequests' => '',
                    'relatedPremises' => '',
                ],
                $ServiceRequestType
            ]
        ];
    }

    public function testItGetsServiceRequestsForUPRNDateRangeAndServiceTypeID()
    {
        $minimumDate = date(DateEnum::Y_m_d, strtotime(DateEnum::YESTERDAY));
        $maximumDate = date(DateEnum::Y_m_d, strtotime(DateEnum::TOMORROW));

        /** @var \stdClass|null $ServiceRequestClass */
        $ServiceRequestClass = $this->bartecService->getServiceRequestClassFromServiceRequestClassName(BartecServiceEnum::SERVICE_REQUEST_CLASS_NAME_GARDEN_WASTE);

        /** @var \stdClass|null $ServiceRequestType */
        $ServiceRequestType = $this->bartecService->getServiceRequestTypeFromServiceRequestClassIdAndServiceRequestTypeName(
            $ServiceRequestClass->ID,
            BartecServiceEnum::SERVICE_REQUEST_TYPE_NAME_SUBSCRIPTION
        );

        $result = $this->bartecService->getServiceRequests(
            self::RESIDENTIAL_UPRN,
            $minimumDate,
            $maximumDate,
            $ServiceRequestType->ID
        );

        $this->assertTrue(!empty($result->ServiceRequest));
        $this->assertIsArray($result->ServiceRequest);
        $this->assertTrue(!empty($result->ServiceRequest[0]->ServiceType->Name));
        $this->assertEquals(BartecServiceEnum::SERVICE_REQUEST_TYPE_NAME_SUBSCRIPTION, $result->ServiceRequest[0]->ServiceType->Name);
    }

    public function testGetPremisesByUPRNThrowsExceptionForInvalidString()
    {
        $this->expectException(SoapException::class);
        $this->bartecService->getPremisesByUPRN('Invalid String');
    }

    /**
     * @param string $UPRN
     * @param int $expectedRecordCount
     * @dataProvider getPremisesByUPRNDataProvider
     */
    public function testItGetsPremisesByUPRN(string $UPRN, int $expectedRecordCount)
    {
        $result = $this->bartecService->getPremisesByUPRN($UPRN);
        $this->assertEquals($expectedRecordCount, $result->RecordCount);
    }

    public function getPremisesByUPRNDataProvider()
    {
        return [
            ['99999999999', 0],
            [self::RESIDENTIAL_UPRN, 1]
        ];
    }

    public function testItGetPremisesDetailByUPRN()
    {
        $result = $this->bartecService->getPremisesDetailByUPRN(self::RESIDENTIAL_UPRN);
        $this->assertTrue(!empty($result->Premises));
        $this->assertEquals(self::RESIDENTIAL_UPRN, $result->Premises->UPRN);
        $this->assertTrue(isset($result->Premises->ParentUPRN));
    }

    public function testItGetsPremisesAttributes()
    {
        /** @var \stdClass $result */
        $result = $this->bartecService->getPremisesAttributes(self::RESIDENTIAL_UPRN);
        $this->assertTrue(!empty($result->Attribute));
        $this->assertIsArray($result->Attribute);
        $this->assertTrue(!empty($result->Attribute[0]->AttributeDefinition));
    }

    /**
     * @param string $UPRN
     * @param string $featureTypeName
     * @dataProvider getBinFeatureTypeNameFromUPRNAndServiceNameDataProvider
     */
    public function testItGetsBinFeatureTypeNameFromUPRNAndServiceName(string $UPRN, string $featureTypeName, string $expectedResult)
    {
        $result = $this->bartecService->getBinFeatureTypeNameFromUPRNAndServiceName($UPRN, $featureTypeName);
        $this->assertNotEmpty($result);
        $this->assertEquals($expectedResult, $result);
    }

    public function getBinFeatureTypeNameFromUPRNAndServiceNameDataProvider()
    {
        return [
            [self::RESIDENTIAL_UPRN, BartecServiceEnum::GARDEN, BartecServiceEnum::FEATURE_GARDEN_240],
            [self::RESIDENTIAL_UPRN, 'InvalidFeatureType', BartecServiceEnum::DEFAULT_BIN_NOT_FOUND]
        ];
    }

    public function testItGetsExtendedDataFieldFromBinFeatureType()
    {
        $featureTypeName = $this->bartecService->getBinFeatureTypeNameFromUPRNAndServiceName(self::RESIDENTIAL_UPRN, 'Garden');
        $result = $this->bartecService->getExtendedDataFieldFromBinFeatureType($featureTypeName);
        $this->assertEquals('Garden waste collection', $result);
    }

    /**
     * @param int $crewNumber
     * @param string $workGroupName
     * @param mixed $expectedResult
     * @dataProvider getCrewIdFromCrewNumberAndWorkGroupNameDataProvider
     */
    public function testItGetsCrewIdFromCrewNumberAndWorkGroupName(int $crewNumber, string $workGroupName, $expectedResult)
    {
        $result = $this->bartecService->getCrewIdFromCrewNumberAndWorkGroupName($crewNumber, $workGroupName);
        $this->assertEquals($expectedResult, $result);
    }

    public function getCrewIdFromCrewNumberAndWorkGroupNameDataProvider()
    {
        return [
            [BartecServiceEnum::CREW_NUMBER_900, BartecServiceEnum::CREW_WORK_GROUP_NAME, 3286],
            [123456, 'Invalid', null]
        ];
    }

    /**
     * @param string $slaName
     * @param $expectedResult
     * @dataProvider getSlaIdFromSlaNameDataProvider
     */
    public function testItGetsSlaIdFromSlaName(string $slaName, $expectedResult)
    {
        $result = $this->bartecService->getSlaIdFromSlaName($slaName);
        $this->assertEquals($expectedResult, $result);
    }

    public function getSlaIdFromSlaNameDataProvider()
    {
        return [
            [BartecServiceEnum::SLA_NAME_1WD, 41],
            ['Invalid', null]
        ];
    }

    /**
     * @param string $landTypeName
     * @param $expectedResult
     * @dataProvider getsLandTypeIdFromLandTypeNameDataProvider
     */
    public function testItGetsLandTypeIdFromLandTypeName(string $landTypeName, $expectedResult)
    {
        $result = $this->bartecService->getLandTypeIdFromLandTypeName($landTypeName);
        $this->assertEquals($expectedResult, $result);
    }

    public function getsLandTypeIdFromLandTypeNameDataProvider()
    {
        return [
            [BartecServiceEnum::LAND_NAME_HH, 19],
            ['Invalid', null],
        ];
    }

    public function testGetJobsReturnsJobs()
    {
        $minimumDate = date(DateEnum::Y_m_d, strtotime(DateEnum::YESTERDAY));
        $maximumDate = date(DateEnum::Y_m_d, strtotime(DateEnum::TOMORROW));

        $result = $this->bartecService->getJobs(self::RESIDENTIAL_UPRN, $minimumDate, $maximumDate);
        $this->assertTrue(isset($result->Errors->Result));
        $this->assertEquals(0, $result->Errors->Result);
    }

    public function testGetsEventsByUPRN()
    {
        $minimumDate = date(DateEnum::Y_m_d, strtotime(DateEnum::YESTERDAY));
        $maximumDate = date(DateEnum::Y_m_d, strtotime(DateEnum::TOMORROW));

        $result = $this->bartecService->getEventsByUPRN(self::RESIDENTIAL_UPRN, $minimumDate, $maximumDate);
        $this->assertTrue(isset($result->Errors->Result));
        $this->assertEquals(0, $result->Errors->Result);
    }

    /**
     * @param string $UPRN
     * @param string $subscriptionYear
     * @dataProvider premisesHasGardenWasteSubscriptionDataProvider
     */
    public function testPremisesHasGardenWasteSubscriptionDetectsSubscription(string $UPRN, string $subscriptionYear, $expectedResult)
    {
        $result = $this->bartecService->premisesHasGardenWasteSubscription($UPRN, $subscriptionYear);
        $this->assertEquals($expectedResult, $result);
    }

    public function premisesHasGardenWasteSubscriptionDataProvider()
    {
        return [
            [self::RESIDENTIAL_UPRN, date('Y') . '/' . date('y', strtotime('+1 year')), true],
            [self::RESIDENTIAL_UPRN, date('Y', strtotime('+1 year')) . '/' . date('y', strtotime('+2 years')), false],
            [self::RESIDENTIAL_UPRN, 'Invalid attribute', false],
        ];
    }

    public function testItGetFeatures()
    {
        /** @var \stdClass $result */
        $result = $this->bartecService->getFeatures(self::RESIDENTIAL_UPRN, true);
        $this->assertTrue(isset($result->Feature));
        $this->assertIsArray($result->Feature);
        $this->assertTrue(isset($result->Feature[0]->ID));
    }

    public function testItGetsFeatureTypes()
    {
        $result = $this->bartecService->getFeatureTypes();
        $this->assertTrue(isset($result->FeatureType));
        $this->assertIsArray($result->FeatureType);
        $this->assertTrue(isset($result->FeatureType[0]->ID));
    }

    public function testItGetFeatureSchedules()
    {
        /** @var \stdClass $result */
        $result = $this->bartecService->getFeatureSchedules(self::RESIDENTIAL_UPRN);
        $this->assertTrue(isset($result->FeatureSchedule));
        $this->assertIsArray($result->FeatureSchedule);
        $this->assertTrue(isset($result->FeatureSchedule[0]->ID));
    }

    /**
     * @param string $UPRN
     * @param array $featureTypeNames
     * @param array $featureStates
     * @param bool $includeRelated
     * @param $expectedResult
     * @dataProvider getMostRecentFeatureForNullDataProvider
     */
    public function testGetsMostRecentFeatureReturnsNull(string $UPRN, array $featureTypeNames, array $featureStates, bool $includeRelated, $expectedResult) {
        $result = $this->bartecService->getMostRecentFeature($UPRN, $featureTypeNames, $featureStates, $includeRelated);
        $this->assertEquals($expectedResult, $result);
    }

    public function getMostRecentFeatureForNullDataProvider()
    {
        return [
            [self::RESIDENTIAL_UPRN, [BartecServiceEnum::FEATURE_GARDEN_240, BartecServiceEnum::FEATURE_GARDENSACK], [BartecServiceEnum::FEATURE_STATE_IN_SERVICE], true, null],
            [self::RESIDENTIAL_UPRN, ['Invalid'], ['NON-EXISTANT STATUS'], true, null]
        ];
    }

    /**
     * @param string $UPRN
     * @param array $featureTypeNames
     * @param array $featureStates
     * @param bool $includeRelated
     * @param $expectedResult
     * @dataProvider getMostRecentFeatureForDataProvider
     */
    public function testGetsMostRecentFeature(string $UPRN, array $featureTypeNames, array $featureStates, bool $includeRelated, $expectedResult) {
        $result = $this->bartecService->getMostRecentFeature($UPRN, $featureTypeNames, $featureStates, $includeRelated);
        $this->assertTrue(!empty($result->FeatureType->Name));
        $this->assertEquals($expectedResult, $result->FeatureType->Name);
    }

    public function getMostRecentFeatureForDataProvider()
    {
        return [
            [self::RESIDENTIAL_UPRN, [BartecServiceEnum::FEATURE_RESIDUAL_140], [BartecServiceEnum::FEATURE_STATE_IN_SERVICE], true, BartecServiceEnum::FEATURE_RESIDUAL_140],
            [self::RESIDENTIAL_UPRN, [BartecServiceEnum::FEATURE_GARDEN_240], [BartecServiceEnum::FEATURE_STATE_ON_ORDER], true, BartecServiceEnum::FEATURE_GARDEN_240]
        ];
    }

    public function testItThrowsInvalidArgumentExceptionForInvalidFeatureTypeNames()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("'Invalid' is an invalid Bartec Feature Type Name");
        $this->bartecService->validateWasteContainerFeatureTypeNames(['Invalid']);
    }
}
