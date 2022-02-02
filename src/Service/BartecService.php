<?php

namespace LBHounslow\Bartec\Service;

use LBHounslow\Bartec\Adapter\ApiVersionAdapterInterface;
use LBHounslow\Bartec\Adapter\Version15Adapter;
use LBHounslow\Bartec\Adapter\Version16Adapter;
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

    const VERSION_ADAPTERS = [
        Version15Adapter::VERSION => Version15Adapter::class,
        Version16Adapter::VERSION => Version16Adapter::class
    ];

    /**
     * @var ApiVersionAdapterInterface
     */
    protected $apiVersionAdapter;

    /**
     * @var CacheItemPoolInterface|null
     */
    protected $cache;

    /**
     * @param BartecClient $bartecClient
     * @param string $version
     * @param string $WSDL // Override for Collective WSDL
     * @param CacheItemPoolInterface|null $cache
     */
    public function __construct(
        BartecClient $bartecClient,
        string $version,
        $WSDL = '',
        CacheItemPoolInterface $cache = null
    ) {
        $this->apiVersionAdapter = $this->factoryApiVersionAdapter($bartecClient, $version, $WSDL);
        $this->cache = $cache;
    }

    /**
     * @param BartecClient $bartecClient
     * @param string $version
     * @param string $WSDL
     * @return ApiVersionAdapterInterface
     * @throws \InvalidArgumentException
     */
    public function factoryApiVersionAdapter(BartecClient $bartecClient, string $version, string $WSDL)
    {
        if (!in_array($version, array_keys(self::VERSION_ADAPTERS))) {
            throw new \InvalidArgumentException(sprintf("Version '%s' is not supported", $version));
        }

        $versionAdapterClass = self::VERSION_ADAPTERS[$version];

        return new $versionAdapterClass($bartecClient, $WSDL);
    }

    /**
     * @return BartecClient
     */
    public function getClient()
    {
        return $this->apiVersionAdapter->getBartecClient();
    }

    /**
     * @param array $soapOptions
     * @return $this
     */
    public function setClientSoapOptions(array $soapOptions)
    {
        $this->apiVersionAdapter->getBartecClient()
            ->setSoapOptions($soapOptions);
        return $this;
    }

    /**
     * @param array $data
     * @return \stdClass|null
     * @throws SoapException
     */
    public function createServiceRequest(array $data)
    {
        $response = $this->apiVersionAdapter
            ->createServiceRequest($data);

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response->getResult();
    }

    /**
     * @param string $serviceRequestCode
     * @param array $data
     * @return mixed|null
     * @throws SoapException
     */
    public function updateServiceRequest(string $serviceRequestCode, array $data)
    {
        $response = $this->apiVersionAdapter
            ->updateServiceRequest($serviceRequestCode, $data);

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
     * @return \stdClass|null
     * @throws SoapException
     */
    public function getServiceRequests(
        string $UPRN,
        string $minimumDate,
        string $maximumDate,
        int $serviceTypeId
    ) {
        $response = $this->apiVersionAdapter
            ->getServiceRequests(
                $UPRN,
                $minimumDate,
                $maximumDate,
                $serviceTypeId
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
        $response = $this->apiVersionAdapter
            ->getServiceRequestDetail($ServiceRequestCode);

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
        $response = $this->apiVersionAdapter
            ->setServiceRequestStatus($ServiceRequest, $ServiceRequestStatus);

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
        $response = $this->apiVersionAdapter
            ->createServiceRequestNote(
                $ServiceRequestID,
                $note,
                $noteTypeID,
                $sequenceNumber,
                $comment
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
                $response = $this->apiVersionAdapter
                    ->getServiceRequestClasses();

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
            $response = $this->apiVersionAdapter
                ->getServiceRequestClasses();

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
                $response = $this->apiVersionAdapter
                    ->getServiceRequestTypes();

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
            $response = $this->apiVersionAdapter
                ->getServiceRequestTypes();

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
                $response = $this->apiVersionAdapter
                    ->getServiceRequestTypesByServiceRequestClassId($serviceRequestClassId);

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
            $response = $this->apiVersionAdapter
                ->getServiceRequestTypesByServiceRequestClassId($serviceRequestClassId);

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
        $response = $this->apiVersionAdapter
            ->getServiceRequestStatusForServiceTypeIdByStatus($ServiceTypeID);

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
        $response = $this->apiVersionAdapter
            ->getServiceNoteTypeFromNoteTypeDescription();

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
        $response = $this->apiVersionAdapter
            ->createServiceRequestDocument($data);

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }
        return null;
    }

    /**
     * @param string $UPRN
     * @return \stdClass|null
     * @throws SoapException
     */
    public function getPremisesByUPRN(string $UPRN)
    {
        /** @var Response $response */
        $response = $this->apiVersionAdapter
            ->getPremisesByUPRN($UPRN);

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response->getResult();
    }

    /**
     * @param string $UPRN
     * @return \stdClass|null
     * @throws SoapException
     */
    public function getPremisesDetailByUPRN(string $UPRN)
    {
        /** @var Response $response */
        $response = $this->apiVersionAdapter
            ->getPremisesDetailByUPRN($UPRN);

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response->getResult();
    }

    /**
     * @param string $UPRN
     * @return \stdClass|null
     * @throws SoapException
     */
    public function getPremisesAttributes(string $UPRN)
    {
        /** @var Response $response */
        $response = $this->apiVersionAdapter
            ->getPremisesAttributes($UPRN);

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
                $response = $this->apiVersionAdapter->getCrews();

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
            $response = $this->apiVersionAdapter->getCrews();

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
                $response = $this->apiVersionAdapter
                    ->getServiceRequestSLAs();

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
            $response = $this->apiVersionAdapter
                ->getServiceRequestSLAs();

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
                $response = $this->apiVersionAdapter
                    ->getServiceLandTypes();

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
            $response = $this->apiVersionAdapter
                ->getServiceLandTypes();

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
     * @param bool $includeRelated
     * @param int|null $workPackID
     * @return \stdClass|null
     * @throws SoapException
     */
    public function getJobs(
        string $UPRN,
        string $minimumDate,
        string $maximumDate,
        bool $includeRelated = true,
        $workPackID = null
    ) {
        /** @var Response $response */
        $response = $this->apiVersionAdapter
            ->getJobs(
                $UPRN,
                $minimumDate,
                $maximumDate,
                $includeRelated,
                $workPackID
            );

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response->getResult();
    }

    /**
     * @param int $jobId
     * @return \stdClass|null
     * @throws SoapException
     */
    public function getJobDetail(int $jobId) {

        /** @var Response $response */
        $response = $this->apiVersionAdapter
            ->getJobDetail($jobId);

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response->getResult();
    }

    /**
     * @param string $UPRN
     * @param string $minimumDate
     * @param string $maximumDate
     * @param bool $includeRelated
     * @param string|null $workpack
     * @return \stdClass|null
     * @throws SoapException
     */
    public function getEventsByUPRN(
        string $UPRN,
        string $minimumDate = '',
        string $maximumDate = '',
        bool $includeRelated = true,
        $workpack = null
    ) {
        /** @var Response $response */
        $response = $this->apiVersionAdapter
            ->getEventsByUPRN(
                $UPRN,
                $minimumDate,
                $maximumDate,
                $includeRelated,
                $workpack
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
        /** @var Response $response */
        $response = $this->apiVersionAdapter
            ->getFeatures($UPRN, $includeRelated);

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response->getResult();
    }

    /**
     * @return \stdClass|null
     * @throws SoapException
     */
    public function getFeatureTypes()
    {
        /** @var Response $response */
        $response = $this->apiVersionAdapter
            ->getFeatureTypes();

        if ($response->hasErrors()) {
            throw new SoapException($response);
        }

        return $response->getResult();
    }

    /**
     * @param string $UPRN
     * @return \stdClass|null
     * @throws SoapException
     */
    public function getFeatureSchedules(string $UPRN)
    {
        /** @var Response $response */
        $response = $this->apiVersionAdapter
            ->getFeatureSchedules($UPRN);

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
