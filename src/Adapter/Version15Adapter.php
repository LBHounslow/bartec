<?php

namespace LBHounslow\Bartec\Adapter;

use LBHounslow\Bartec\Enum\BartecServiceEnum;

class Version15Adapter extends AbstractApiVersionAdapter
{
    const VERSION = 'v15';
    const WSDL_COLLECTIVE_API = 'https://collectiveapi.bartec-systems.com/API-R1531/CollectiveAPI.asmx?WSDL';

    /**
     * @inheritdoc
     */
    public function createServiceRequest(array $data)
    {
        return $this->bartecClient->call(
            'ServiceRequests_Create',
            $data
        );
    }

    /**
     * @inheritdoc
     */
    public function updateServiceRequest(string $serviceRequestCode, array $data)
    {
        $data['serviceCode'] = $serviceRequestCode;

        if (empty($data['serviceLocationDescription'])) { // workaround for issue in Bartec API
            $data['serviceLocationDescription'] = '';
        }

        return $this->bartecClient->call(
            'ServiceRequests_Update',
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
            ['ServiceCode' => $ServiceRequestCode]
        );
    }

    /**
     * @inheritdoc
     */
    public function setServiceRequestStatus(\stdClass $ServiceRequest, \stdClass $ServiceRequestStatus)
    {
        return $this->bartecClient->call(
            'ServiceRequests_Status_Set',
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
        return $this->bartecClient->call('Service_Request_Document_Create', $data);
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
    }
}