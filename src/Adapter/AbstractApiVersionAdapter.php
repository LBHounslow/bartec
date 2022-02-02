<?php

namespace LBHounslow\Bartec\Adapter;

use LBHounslow\Bartec\Client\Client as BartecClient;
use LBHounslow\Bartec\Exception\SoapException;
use LBHounslow\Bartec\Response\Response;

abstract class AbstractApiVersionAdapter implements ApiVersionAdapterInterface
{
    /**
     * @var BartecClient
     */
    protected $bartecClient;

    /**
     * @param BartecClient $bartecClient
     * @param string $WSDL // Collective WSDL override
     */
    public function __construct(BartecClient $bartecClient, string $WSDL = '')
    {
        $bartecClient
            ->getCollectiveSoapClient()
                ->setWsdl(
                $WSDL === '' ? $this->getCollectiveWsdl() : $WSDL
                );

        $this->bartecClient = $bartecClient;
    }

    /**
     * @return BartecClient
     */
    public function getBartecClient()
    {
        return $this->bartecClient;
    }

    /**
     * @param array $soapOptions
     * @return $this
     */
    public function setBartecClientSoapOptions(array $soapOptions)
    {
        $this->bartecClient->setSoapOptions($soapOptions);
        return $this;
    }

    /**
     * @param string $UPRN
     * @param string $minimumDate
     * @param string $maximumDate
     * @param int $serviceTypeId
     * @return Response
     * @throws SoapException
     */
    public function getServiceRequests(
        string $UPRN,
        string $minimumDate,
        string $maximumDate,
        int $serviceTypeId
    ) {
        return $this->bartecClient->call(
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
    }

    /**
     * @return Response
     * @throws SoapException
     */
    public function getServiceRequestClasses()
    {
        return $this->bartecClient->call('ServiceRequests_Classes_Get');
    }

    /**
     * @return Response
     * @throws SoapException
     */
    public function getServiceRequestTypes()
    {
        return $this->bartecClient->call('ServiceRequests_Types_Get');
    }

    /**
     * @param int $serviceRequestClassId
     * @return Response
     * @throws SoapException
     */
    public function getServiceRequestTypesByServiceRequestClassId(int $serviceRequestClassId)
    {
        return $this->bartecClient->call(
            'ServiceRequests_Types_Get',
            ['ServiceRequestClass' => $serviceRequestClassId]
        );
    }

    /**
     * @param int $ServiceTypeID
     * @return Response
     * @throws SoapException
     */
    public function getServiceRequestStatusForServiceTypeIdByStatus(int $ServiceTypeID)
    {
        return $this->bartecClient->call(
            'ServiceRequests_Statuses_Get',
            ['ServiceTypeID' => $ServiceTypeID]
        );
    }

    /**
     * @return Response
     * @throws SoapException
     */
    public function getServiceNoteTypeFromNoteTypeDescription()
    {
        return $this->bartecClient->call('ServiceRequests_Notes_Types_Get');
    }

    /**
     * @param string $UPRN
     * @return Response
     * @throws SoapException
     */
    public function getPremisesByUPRN(string $UPRN)
    {
        return $this->bartecClient->call(
            'Premises_Get',
            ['UPRN' => $UPRN]
        );
    }

    /**
     * @param string $UPRN
     * @return Response
     * @throws SoapException
     */
    public function getPremisesDetailByUPRN(string $UPRN)
    {
        return $this->bartecClient->call(
            'Premises_Detail_Get',
            ['UPRN' => $UPRN]
        );
    }

    /**
     * @param string $UPRN
     * @return Response
     * @throws SoapException
     */
    public function getPremisesAttributes(string $UPRN)
    {
        return $this->bartecClient->call(
            'Premises_Attributes_Get',
            ['UPRN' => $UPRN]
        );
    }

    /**
     * @return Response
     * @throws SoapException
     */
    public function getCrews()
    {
        return $this->bartecClient->call('Crews_Get');
    }

    /**
     * @return Response
     * @throws SoapException
     */
    public function getServiceRequestSLAs()
    {
        return $this->bartecClient->call('ServiceRequests_SLAs_Get');
    }

    /**
     * @return Response
     * @throws SoapException
     */
    public function getServiceLandTypes()
    {
        return $this->bartecClient->call('System_LandTypes_Get');
    }

    /**
     * @param string $UPRN
     * @param string $minimumDate
     * @param string $maximumDate
     * @param bool $includeRelated
     * @param null $workPackID
     * @return Response
     * @throws SoapException
     */
    public function getJobs(
        string $UPRN,
        string $minimumDate,
        string $maximumDate,
        bool $includeRelated = true,
               $workPackID = null
    ) {
        $data = [
            'UPRN' => $UPRN,
            'WorkPackID' => $workPackID,
            'ScheduleStart' => [
                'MinimumDate' => $minimumDate,
                'MaximumDate' => $maximumDate,
            ],
            'IncludeRelated' => ''
        ];

        if ($includeRelated) {
            $data['IncludeRelated'] = 1;
        }

        return $this->bartecClient->call(
            'Jobs_Get',
            $data
        );
    }

    /**
     * @param int $jobId
     * @return Response
     * @throws SoapException
     */
    public function getJobDetail(int $jobId)
    {
        return $this->bartecClient->call(
            'Jobs_Detail_Get',
            ['jobID' => $jobId]
        );
    }

    /**
     * @param string $UPRN
     * @param string $minimumDate
     * @param string $maximumDate
     * @param bool $includeRelated
     * @param null $workpack
     * @return Response
     * @throws SoapException
     */
    public function getEventsByUPRN(
        string $UPRN,
        string $minimumDate = '',
        string $maximumDate = '',
        bool $includeRelated = true,
               $workpack = null
    ) {
        $data = [
            'UPRN' => $UPRN,
            'WorkPack' => $workpack,
            'IncludeRelated' => ''
        ];

        if ($minimumDate) {
            $data['DateRange']['MinimumDate'] = $minimumDate;
        }

        if ($maximumDate) {
            $data['DateRange']['MaximumDate'] = $maximumDate;
        }

        if ($includeRelated) {
            $data['IncludeRelated'] = 1;
        }

        return $this->bartecClient->call(
            'Premises_Events_Get',
            $data
        );
    }

    /**
     * @param string $UPRN
     * @param bool $includeRelated
     * @return Response
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

        return $this->bartecClient->call(
            'Features_Get',
            $data
        );
    }

    /**
     * @return Response
     * @throws SoapException
     */
    public function getFeatureTypes()
    {
        return $this->bartecClient->call(
            'Features_Types_Get'
        );
    }

    /**
     * @param string $UPRN
     * @return Response
     * @throws SoapException
     */
    public function getFeatureSchedules(string $UPRN)
    {
        return $this->bartecClient->call(
            'Features_Schedules_Get',
            ['UPRN' => $UPRN, 'Types' => '']
        );
    }

    /**
     * @param string $serviceRequestCode
     * @return int
     */
    public function getNumericIDFromServiceRequestCode(string $serviceRequestCode)
    {
        return (int) str_replace('SR', '', $serviceRequestCode);
    }
}