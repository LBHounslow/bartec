<?php

namespace LBHounslow\Bartec\Adapter;

use LBHounslow\Bartec\Enum\BartecServiceEnum;

class Version16Adapter extends AbstractApiVersionAdapter
{
    const VERSION = 'v16';
    const WSDL_COLLECTIVE_API = 'https://collectiveapi.bartec-systems.com/API-R1604/CollectiveAPI.asmx?WSDL';

    /**
     * @inheritdoc
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * @inheritdoc
     */
    public function getCollectiveWsdl()
    {
        return self::WSDL_COLLECTIVE_API;
    }

    /**
     * @inheritdoc
     */
    public function createServiceRequest(array $data)
    {
        // Correct any v15 service requests with the old extended data field
        if (isset($data['extendedData']['ServiceRequests_CreateServiceRequests_CreateFields'])) {
            $data['extendedData']['ServiceRequest_CreateServiceRequest_CreateFields'] = $data['extendedData']['ServiceRequests_CreateServiceRequests_CreateFields'];
            unset($data['extendedData']['ServiceRequests_CreateServiceRequests_CreateFields']);
        }

        return $this->bartecClient->call(
            'ServiceRequest_Create',
            $data
        );
    }

    /**
     * @inheritdoc
     */
    public function updateServiceRequest(string $serviceRequestCode, array $data)
    {
        $data['serviceCode'] = $serviceRequestCode;
        $data['SR_ID'] = $this->getNumericIDFromServiceRequestCode($serviceRequestCode);

        if (empty($data['serviceLocationDescription'])) { // workaround for issue in Bartec API
            $data['serviceLocationDescription'] = '';
        }

        return $this->bartecClient->call(
            'ServiceRequest_Update',
            $data
        );
    }

    /**
     * @inheritdoc
     */
    public function getServiceRequestDetail(string $ServiceRequestCode)
    {
        return $this->bartecClient->call(
            'ServiceRequests_Detail_Get',
            ['ServiceCodes' => [$ServiceRequestCode]]
        );
    }

    /**
     * @inheritdoc
     */
    public function setServiceRequestStatus(\stdClass $ServiceRequest, \stdClass $ServiceRequestStatus)
    {
        return $this->bartecClient->call(
            'ServiceRequest_Status_Set',
            [
                'ServiceCode' => $ServiceRequest->ServiceCode,
                'StatusID' => $ServiceRequestStatus->ID,
                'Comments' => BartecServiceEnum::DEFAULT_NOTE_COMMENT,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function createServiceRequestDocument(array $data)
    {
        return $this->bartecClient->call('ServiceRequest_Document_Create', $data);
    }

    /**
     * @inheritdoc
     */
    public function createServiceRequestNote(
        int $ServiceRequestID,
        string $note,
        int $noteTypeID,
        $sequenceNumber = 1,
        $comment = ''
    ) {
        return $this->bartecClient->call(
            'ServiceRequest_Note_Create',
            [
                'ServiceRequestID' => $ServiceRequestID,
                'ServiceCode' => null,
                'NoteTypeID' => $noteTypeID,
                'Note' => $note,
                'Comment' => $comment,
                'SequenceNumber' => $sequenceNumber
            ]
        );
    }
}