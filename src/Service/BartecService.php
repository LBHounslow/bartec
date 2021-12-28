<?php

namespace LBHounslow\Bartec\Service;

use LBHounslow\Bartec\Client\Client as BartecClient;
use LBHounslow\Bartec\Enum\BartecServiceEnum;
use LBHounslow\Bartec\Exception\SoapException;
use LBHounslow\Bartec\Response\Response;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

/**
 * NOTE: This service has limited functionality with the Bartec Collective API.
 * For any other methods, use the BartecClient and call the API directly.
 */
class BartecService
{
    const CACHE_LIFETIME = 3600; // 1 hour
    const CACHE_NAMESPACE = 'lb-hounslow/bartec';

    /**
     * @var BartecClient
     */
    protected $client;

    /**
     * @var CacheItemPoolInterface|null
     */
    protected $cache;

    /**
     * @param BartecClient $client
     * @param CacheItemPoolInterface|null $cache
     */
    public function __construct(BartecClient $client, CacheItemPoolInterface $cache = null)
    {
        $this->client = $client;
        $this->cache = $cache;
    }

    /**
     * @return BartecClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param array $soapOptions
     * @return $this
     */
    public function setClientSoapOptions(array $soapOptions)
    {
        $this->client->setSoapOptions($soapOptions);
        return $this;
    }

    /**
     * @param array $data
     * @return \stdClass|null
     * @throws SoapException
     */
    public function createServiceRequest(array $data)
    {
        $response = $this->client->call(
            'ServiceRequests_Create',
            $data
        );

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response->getResult();
    }

    /**
     * @param string $serviceRequestCode
     * @param array $data
     * @return \stdClass|null
     * @throws SoapException
     */
    public function updateServiceRequest(string $serviceRequestCode, array $data)
    {
        $data['serviceCode'] = $serviceRequestCode;
        if (empty($data['serviceLocationDescription'])) { // workaround for issue in Bartec API
            $data['serviceLocationDescription'] = '';
        }

        $response = $this->client->call(
            'ServiceRequests_Update',
            $data
        );

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response->getResult();
    }

    /**
     * @param string $UPRN
     * @param string $minimumDate
     * @param string $maximumDate
     * @param int $serviceTypeId
     * @return mixed|null
     * @throws SoapException
     */
    public function getServiceRequests(
        string $UPRN,
        string $minimumDate,
        string $maximumDate,
        int $serviceTypeId
    ) {
        $response = $this->client->call(
            'ServiceRequests_Get',
            [
                'UPRNs' => ['decimal' => $UPRN],
                'RequestDate' => [
                    'MinimumDate' => $minimumDate,
                    'MaximumDate' => $maximumDate,
                ],
                'ServiceTypes' => ['int' => $serviceTypeId],
            ]
        );

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response->getResult();
    }

    /**
     * @param string $ServiceRequestCode
     * @return \stdClass|null
     * @throws SoapException
     */
    public function getServiceRequestDetail(string $ServiceRequestCode)
    {
        $response = $this->client->call(
            'ServiceRequests_Detail_Get',
            ['ServiceCode' => $ServiceRequestCode]
        );

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response->getResult();
    }

    /**
     * @param \stdClass $ServiceRequest
     * @param \stdClass $ServiceRequestStatus
     * @return Response
     * @throws SoapException
     */
    public function setServiceRequestStatus(\stdClass $ServiceRequest, \stdClass $ServiceRequestStatus)
    {
        /** @var Response $response */
        $response = $this->client->call(
            'ServiceRequests_Status_Set',
            [
                'ServiceCode' => $ServiceRequest->ServiceCode,
                'StatusID' => $ServiceRequestStatus->ID,
                'Comments' => BartecServiceEnum::DEFAULT_NOTE_COMMENT,
            ]
        );

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response;
    }

    /**
     * @param int $ServiceRequestID
     * @param string $note
     * @param int $noteTypeID
     * @param int $sequenceNumber
     * @param string $comment
     * @return Response
     * @throws SoapException
     */
    public function createServiceRequestNote(
        int $ServiceRequestID,
        string $note,
        int $noteTypeID,
        $sequenceNumber = 1,
        $comment = ''
    ) {
        /** @var Response $response */
        $response = $this->client->call(
            'ServiceRequests_Notes_Create',
            [
                'ServiceRequestID' => $ServiceRequestID,
                'ServiceCode' => null,
                'NoteTypeID' => $noteTypeID,
                'Note' => $note,
                'Comment' => $comment,
                'SequenceNumber' => $sequenceNumber
            ]
        );

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response;
    }

    /**
     * @return Response
     * @throws InvalidArgumentException
     * @throws SoapException
     */
    public function getServiceRequestClasses()
    {
        if ($this->cache) {

            /** @var CacheItemInterface $cacheItem */
            $cacheItem = $this->cache->getItem(
                $this->generateCacheKey(__CLASS__ . __METHOD__)
            );

            if (!$cacheItem->isHit()) {
                /** @var Response $response */
                $response = $this->client->call('ServiceRequests_Classes_Get');

                if ($response->hasErrors()) {
                    throw new SoapException($response);
                }

                $cacheItem
                    ->set($response)
                    ->expiresAt($this->getCacheExpiry(self::CACHE_LIFETIME));
                $this->cache->save($cacheItem);
            }

            /** @var Response $response */
            $response = $cacheItem->get();
        } else {
            /** @var Response $response */
            $response = $this->client->call('ServiceRequests_Classes_Get');

            if ($response->hasErrors()) {
                throw new SoapException($response);
            }
        }

        return $response;
    }

    /**
     * @return Response
     * @throws InvalidArgumentException
     * @throws SoapException
     */
    public function getServiceRequestTypes()
    {
        if ($this->cache) {

            /** @var CacheItemInterface $cacheItem */
            $cacheItem = $this->cache->getItem(
                $this->generateCacheKey(__CLASS__ . __METHOD__)
            );

            if (!$cacheItem->isHit()) {
                /** @var Response $response */
                $response = $this->client->call('ServiceRequests_Types_Get');

                if ($response->hasErrors()) {
                    throw new SoapException($response);
                }

                $cacheItem
                    ->set($response)
                    ->expiresAt($this->getCacheExpiry(self::CACHE_LIFETIME));
                $this->cache->save($cacheItem);
            }

            /** @var Response $response */
            $response = $cacheItem->get();
        } else {
            /** @var Response $response */
            $response = $this->client->call('ServiceRequests_Types_Get');

            if ($response->hasErrors()) {
                throw new SoapException($response);
            }
        }

        return $response;
    }

    /**
     * @param int $serviceRequestClassId
     * @return Response
     * @throws InvalidArgumentException
     * @throws SoapException
     */
    public function getServiceRequestTypesByServiceRequestClassId(int $serviceRequestClassId)
    {
        if ($this->cache) {

            /** @var CacheItemInterface $cacheItem */
            $cacheItem = $this->cache->getItem(
                $this->generateCacheKey(__METHOD__.$serviceRequestClassId)
            );

            if (!$cacheItem->isHit()) {
                /** @var Response $response */
                $response = $this->client->call(
                    'ServiceRequests_Types_Get',
                    ['ServiceRequestClass' => $serviceRequestClassId]
                );

                if ($response->hasErrors()) {
                    throw new SoapException($response);
                }

                $cacheItem
                    ->set($response)
                    ->expiresAt($this->getCacheExpiry(self::CACHE_LIFETIME));
                $this->cache->save($cacheItem);
            }

            /** @var Response $response */
            $response = $cacheItem->get();
        } else {
            /** @var Response $response */
            $response = $this->client->call(
                'ServiceRequests_Types_Get',
                ['ServiceRequestClass' => $serviceRequestClassId]
            );

            if ($response->hasErrors()) {
                throw new SoapException($response);
            }
        }

        return $response;
    }

    /**
     * @param string $serviceRequestClassName
     * @return \stdClass|null
     * @throws InvalidArgumentException
     * @throws SoapException
     */
    public function getServiceRequestClassFromServiceRequestClassName(string $serviceRequestClassName)
    {
        /** @var Response $response */
        $response = $this->getServiceRequestClasses();

        if (empty($response->getResult()->ServiceClass)) {
            return null;
        }

        foreach ($response->getResult()->ServiceClass as $ServiceClass) {
            if (trim($ServiceClass->Name) === $serviceRequestClassName) {
                return $ServiceClass;
            }
        }
        return null;
    }

    /**
     * @param int $serviceRequestClassId
     * @param string $serviceRequestTypeName
     * @return \stdClass|null
     * @throws InvalidArgumentException
     * @throws SoapException
     */
    public function getServiceRequestTypeFromServiceRequestClassIdAndServiceRequestTypeName(int $serviceRequestClassId, string $serviceRequestTypeName)
    {
        /** @var Response $response */
        $response = $this->getServiceRequestTypesByServiceRequestClassId($serviceRequestClassId);

        foreach ($response->getResult()->ServiceType as $ServiceType) {
            if (trim($ServiceType->Name) === $serviceRequestTypeName) {
                return $ServiceType;
            }
        }
        return null;
    }

    /**
     * @param string $serviceTypeName
     * @return \stdClass|null
     * @throws InvalidArgumentException
     * @throws SoapException
     */
    public function getServiceRequestTypeFromServiceTypeName(string $serviceTypeName)
    {
        /** @var Response $response */
        $response = $this->getServiceRequestTypes();

        foreach ($response->getResult()->ServiceType as $ServiceType) {
            if (trim($ServiceType->Name) === $serviceTypeName) {
                return $ServiceType;
            }
        }
        return null;
    }

    /**
     * @param int $ServiceTypeID
     * @param string $status
     * @return \stdClass|null
     * @throws SoapException
     */
    public function getServiceRequestStatusForServiceTypeIdByStatus(int $ServiceTypeID, string $status)
    {
        /** @var Response $response */
        $response = $this->client->call(
            'ServiceRequests_Statuses_Get',
            ['ServiceTypeID' => $ServiceTypeID]
        );

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        foreach ($response->getResult()->ServiceStatus as $ServiceStatus) {
            if (trim($ServiceStatus->Status) === $status) {
                return $ServiceStatus;
            }
        }
        return null;
    }

    /**
     * @param string $noteTypeDescription
     * @return \stdClass|null
     * @throws SoapException
     */
    public function getServiceNoteTypeFromNoteTypeDescription(string $noteTypeDescription)
    {
        /** @var Response $response */
        $response = $this->client->call('ServiceRequests_Notes_Types_Get');

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        foreach ($response->getResult()->ServiceNoteType as $ServiceNoteType) {
            if (trim($ServiceNoteType->Description) === $noteTypeDescription) {
                return $ServiceNoteType;
            }
        }
        return null;
    }

    /**
     * @param array $data
     * @return null
     * @throws SoapException
     */
    public function createServiceRequestDocument(array $data)
    {
        /** @var Response $response */
        $response = $this->client->call('Service_Request_Document_Create', $data);

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }
        return null;
    }

    /**
     * @param string $UPRN
     * @return mixed|null
     * @throws SoapException
     */
    public function getPremisesByUPRN(string $UPRN)
    {
        $response = $this->client->call(
            'Premises_Get',
            ['UPRN' => $UPRN]
        );

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response->getResult();
    }

    /**
     * @param string $UPRN
     * @return mixed|null
     * @throws SoapException
     */
    public function getPremisesDetailByUPRN(string $UPRN)
    {
        $response = $this->client->call(
            'Premises_Detail_Get',
            ['UPRN' => $UPRN]
        );

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response->getResult();
    }

    /**
     * @param string $UPRN
     * @return mixed|null
     * @throws SoapException
     */
    public function getPremisesAttributes(string $UPRN)
    {
        $response = $this->client->call(
            'Premises_Attributes_Get',
            ['UPRN' => $UPRN]
        );

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response->getResult();
    }

    /**
     * @param string $UPRN
     * @param string $binServiceName
     * @return string
     * @throws SoapException
     */
    public function getBinFeatureTypeNameFromUPRNAndServiceName(string $UPRN, string $binServiceName)
    {
        /** @var \stdClass $result */
        $result = $this->getFeatures($UPRN);

        if (empty($result->RecordCount) || empty($result->Feature)) {
            return BartecServiceEnum::DEFAULT_BIN_NOT_FOUND;
        }

        $result->Feature = !is_array($result->Feature) ? [$result->Feature] : $result->Feature;

        foreach ($result->Feature as $feature) {

            if (
                (
                    $feature->Status->Name === BartecServiceEnum::FEATURE_STATE_IN_SERVICE
                    || $feature->Status->Name === BartecServiceEnum::FEATURE_STATE_DAMAGED
                    || $feature->Status->Name === BartecServiceEnum::FEATURE_STATE_ON_ORDER
                    || $feature->Status->Name === BartecServiceEnum::FEATURE_STATE_TO_BE_REMOVED
                )
                && strpos($feature->Name, $binServiceName) !== false
                && strpos($this->getExtendedDataFieldFromBinFeatureType($feature->FeatureType->Name), 'Mapping does not exist') === false
            ) {
                return $feature->FeatureType->Name;
            }
        }

        return BartecServiceEnum::DEFAULT_BIN_NOT_FOUND;
    }

    /**
     * @param string $binFeatureType
     * @return string
     */
    public function getExtendedDataFieldFromBinFeatureType(string $binFeatureType)
    {
        switch ($binFeatureType)
        {
            case BartecServiceEnum::FEATURE_RESIDUAL_SACK:
                return 'Household refuse sack collection';

            case BartecServiceEnum::FEATURE_RESIDUAL_140:
            case BartecServiceEnum::FEATURE_RESIDUAL_240:
                return 'Wheeled Bin refuse collection';

            case BartecServiceEnum::FEATURE_RECYCLING_RED:
            case BartecServiceEnum::FEATURE_RECYCLING_GREEN:
            case BartecServiceEnum::FEATURE_RECYCLING_BLUE:
                return 'Recycling Box collection';

            case BartecServiceEnum::FEATURE_GARDEN_240:
            case BartecServiceEnum::FEATURE_GARDENSACK:
                return 'Garden waste collection';

            case BartecServiceEnum::FEATURE_RESIDUAL_1100:
            case BartecServiceEnum::FEATURE_RESIDUAL_940:
            case BartecServiceEnum::FEATURE_RESIDUAL_660:
            case BartecServiceEnum::FEATURE_RESIDUAL_360:
                return 'Bulk Bin refuse';

            case BartecServiceEnum::FEATURE_RECYCLING_SACK:
                return 'Flats above shops recycling sack collection';

            case BartecServiceEnum::FEATURE_RESIDUAL_PURPLE_SACK:
                return 'Flats above shops Refuse Sack Collection';

            case BartecServiceEnum::FEATURE_RECYCLING_1280:
            case BartecServiceEnum::FEATURE_CARDBOARD_1280:
            case BartecServiceEnum::FEATURE_CARDBOARD_240:
            case BartecServiceEnum::FEATURE_PLASTIC_1280:
            case BartecServiceEnum::FEATURE_PLASTIC_240:
                return 'Bulk Bin recycling';

            case BartecServiceEnum::FEATURE_FOOD_23:
            case BartecServiceEnum::FEATURE_FOOD_140:
            case BartecServiceEnum::FEATURE_FOOD_240:
                return 'Food Waste';
        }

        return 'Mapping does not exist for ' . $binFeatureType;
    }

    /**
     * @param int $crewNumber
     * @param string $workGroupName
     * @return int|null
     * @throws InvalidArgumentException
     * @throws SoapException
     */
    public function getCrewIdFromCrewNumberAndWorkGroupName(int $crewNumber, string $workGroupName)
    {
        if ($this->cache) {

            /** @var CacheItemInterface $cacheItem */
            $cacheItem = $this->cache->getItem(
                $this->generateCacheKey(__METHOD__.$crewNumber.$workGroupName)
            );

            if (!$cacheItem->isHit()) {

                /** @var Response $response */
                $response = $this->client->call('Crews_Get');

                if ($response->hasErrors()) {
                    throw new SoapException($response);
                }

                $cacheItem
                    ->set($response)
                    ->expiresAt($this->getCacheExpiry(self::CACHE_LIFETIME));
                $this->cache->save($cacheItem);
            }

            /** @var Response $response */
            $response = $cacheItem->get();
        } else {
            /** @var Response $response */
            $response = $this->client->call('Crews_Get');

            if ($response->hasErrors()) {
                throw new SoapException($response);
            }
        }

        foreach ($response->getResult()->Crew as $crew) {
            if ($crew->CrewNumber === $crewNumber && $crew->WorkGroup->Name === $workGroupName) {
                return (int) $crew->ID;
            }
        }

        return null;
    }

    /**
     * @param string $SLAName
     * @return int|null
     * @throws InvalidArgumentException
     * @throws SoapException
     */
    public function getSlaIdFromSlaName(string $SLAName)
    {
        if ($this->cache) {

            /** @var CacheItemInterface $cacheItem */
            $cacheItem = $this->cache->getItem(
                $this->generateCacheKey(__METHOD__.$SLAName)
            );

            if (!$cacheItem->isHit()) {

                /** @var Response $response */
                $response = $this->client->call('ServiceRequests_SLAs_Get');

                if ($response->hasErrors()) {
                    throw new SoapException($response);
                }

                $cacheItem
                    ->set($response)
                    ->expiresAt($this->getCacheExpiry(self::CACHE_LIFETIME));
                $this->cache->save($cacheItem);
            }

            /** @var Response $response */
            $response = $cacheItem->get();
        } else {
            /** @var Response $response */
            $response = $this->client->call('ServiceRequests_SLAs_Get');

            if ($response->hasErrors()) {
                throw new SoapException($response);
            }
        }

        foreach ($response->getResult()->ServiceLevelAgreement as $SLA) {
            if ($SLA->Name === $SLAName) {
                return (int) $SLA->ID;
            }
        }

        return null;
    }

    /**
     * @param string $landName
     * @return int|null
     * @throws InvalidArgumentException
     * @throws SoapException
     */
    public function getLandTypeIdFromLandTypeName(string $landName)
    {
        if ($this->cache) {

            /** @var CacheItemInterface $cacheItem */
            $cacheItem = $this->cache->getItem(
                $this->generateCacheKey(__METHOD__.$landName)
            );

            if (!$cacheItem->isHit()) {

                /** @var Response $response */
                $response = $this->client->call('System_LandTypes_Get');

                if ($response->hasErrors()) {
                    throw new SoapException($response);
                }

                $cacheItem
                    ->set($response)
                    ->expiresAt($this->getCacheExpiry(self::CACHE_LIFETIME));
                $this->cache->save($cacheItem);
            }

            /** @var Response $response */
            $response = $cacheItem->get();
        } else {
            /** @var Response $response */
            $response = $this->client->call('System_LandTypes_Get');

            if ($response->hasErrors()) {
                throw new SoapException($response);
            }
        }

        foreach ($response->getResult()->LandType as $landType) {
            if ($landType->Name === $landName) {
                return (int) $landType->ID;
            }
        }

        return null;
    }

    /**
     * @param string $UPRN
     * @param string $minimumDate
     * @param string $maximumDate
     * @return mixed|null
     * @throws SoapException
     */
    public function getJobs(
        string $UPRN,
        string $minimumDate,
        string $maximumDate
    ) {
        $response = $this->client->call(
            'Jobs_Get',
            [
                'UPRN' => $UPRN,
                'WorkPackID' => null,
                'ScheduleStart' => [
                    'MinimumDate' => $minimumDate,
                    'MaximumDate' => $maximumDate,
                ],
                'IncludeRelated' => 1
            ]
        );

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response->getResult();
    }

    /**
     * @param int $jobId
     * @return mixed|null
     * @throws SoapException
     */
    public function getJobDetail(int $jobId) {
        $response = $this->client->call(
            'Jobs_Detail_Get',
            ['jobID' => $jobId]
        );

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response->getResult();
    }

    /**
     * @param string $UPRN
     * @param string $minimumDate
     * @param string $maximumDate
     * @return mixed|null
     * @throws SoapException
     */
    public function getEventsByUPRN(
        string $UPRN,
        string $minimumDate = '',
        string $maximumDate = ''
    ) {
        $data = ['UPRN' => $UPRN];

        if ($minimumDate) {
            $data['DateRange']['MinimumDate'] = $minimumDate;
        }

        if ($minimumDate) {
            $data['DateRange']['MaximumDate'] = $minimumDate;
        }

        $data['IncludeRelated'] = '';

        $response = $this->client->call(
            'Premises_Events_Get',
            $data
        );

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response->getResult();
    }

    /**
     * @param string $UPRN
     * @param string $subscriptionYear  // eg. 2021/22
     * @return bool
     * @throws SoapException
     */
    public function premisesHasGardenWasteSubscription(string $UPRN, string $subscriptionYear)
    {
        /** @var \stdClass $result */
        $result = $this->getPremisesAttributes($UPRN);

        if (!empty($result->Attribute) && is_array($result->Attribute)) {
            foreach ($result->Attribute as $attribute) {
                if (!empty($attribute->AttributeDefinition->Description)) {
                    $description = sprintf(BartecServiceEnum::HOUSEHOLD_SUBSCRIBED_FOR, $subscriptionYear);
                    if ($description === trim($attribute->AttributeDefinition->Description)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param string $UPRN
     * @param bool $includeRelated
     * @return \stdClass|null
     * @throws SoapException
     */
    public function getFeatures(string $UPRN, bool $includeRelated = true)
    {
        $data = [
            'UPRN' => $UPRN,
            'IncludeRelated' => '',
            'Types' => '',
            'Statuses' => '',
            'Manufacturers' => '',
            'Colours' => '',
            'Conditions' => '',
            'WasteTypes' => ''
        ];

        if ($includeRelated) {
            $data['IncludeRelated'] = 1;
        }

        $response = $this->client->call(
            'Features_Get',
            $data
        );

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response->getResult();
    }

    /**
     * @return mixed|null
     * @throws SoapException
     */
    public function getFeatureTypes()
    {
        $response = $this->client->call(
            'Features_Types_Get'
        );

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response->getResult();
    }

    /**
     * @param string $UPRN
     * @return mixed|null
     * @throws SoapException
     */
    public function getFeatureSchedules(string $UPRN)
    {
        $response = $this->client->call(
            'Features_Schedules_Get',
            ['UPRN' => $UPRN, 'Types' => '']
        );

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response->getResult();
    }

    /**
     * @param string $UPRN
     * @param array $featureTypeNames
     * @param array $featureStates
     * @param bool $includeRelated
     * @return null
     * @throws SoapException
     */
    public function getMostRecentFeature(
        string $UPRN,
        array $featureTypeNames = [],
        array $featureStates = [],
        bool $includeRelated = true
    ) {
        /** @var \stdClass $result */
        $result = $this->getFeatures($UPRN, $includeRelated);

        if (empty($result->Feature) || !is_array($result->Feature)) {
            return null;
        }

        /** @var \stdClass|null $mostRecentFeature */
        $mostRecentFeature = null;
        $mostRecentDateAdded = null;

        foreach ($result->Feature as $feature) {

            if ($featureStates && !in_array($feature->Status->Name, $featureStates)) {
                continue;
            }

            if ($featureTypeNames) {
                if (in_array($feature->FeatureType->Name, $featureTypeNames)) {
                    $dateAdded = (new \DateTime())
                        ->setTimestamp(strtotime($feature->RecordStamp->DateAdded));
                } else {
                    continue;
                }
            } else {
                $dateAdded = (new \DateTime())
                    ->setTimestamp(strtotime($feature->RecordStamp->DateAdded));
            }

            if (!$mostRecentDateAdded) {
                $mostRecentDateAdded = $dateAdded;
                $mostRecentFeature = $feature;
                continue;
            }

            if ($dateAdded->getTimestamp() > $mostRecentDateAdded->getTimestamp()) {
                $mostRecentDateAdded = $dateAdded;
                $mostRecentFeature = $feature;
            }
        }

        return $mostRecentFeature;
    }

    /**
     * @param array $featureTypeNames
     * @throws SoapException
     * @throws \InvalidArgumentException
     */
    public function validateWasteContainerFeatureTypeNames(array $featureTypeNames)
    {
        $result = $this->getFeatureTypes();

        $validBartecFeatureTypeNames = [];
        foreach ($result->FeatureType as $featureType) {
            if (trim($featureType->FeatureClass->FeatureCategory->Name) === BartecServiceEnum::FEATURE_CATEGORY_WASTE_CONTAINER) {
                $validBartecFeatureTypeNames[] = $featureType->Name;
            }
        }

        foreach ($featureTypeNames as $featureTypeName) {
            if (!in_array($featureTypeName, $validBartecFeatureTypeNames)) {
                throw new \InvalidArgumentException(sprintf("'%s' is an invalid Bartec Feature Type Name", $featureTypeName));
            }
        }
    }

    /**
     * @param int $seconds
     * @return \DateTime
     */
    public function getCacheExpiry(int $seconds)
    {
        return (new \DateTime())->add(new \DateInterval('PT'.$seconds.'S'));
    }

    /**
     * @param string $key
     * @return string
     */
    public function generateCacheKey(string $key)
    {
        return md5(self::CACHE_NAMESPACE . $key);
    }
}
