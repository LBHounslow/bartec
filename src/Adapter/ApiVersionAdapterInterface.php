<?php

namespace LBHounslow\Bartec\Adapter;

use LBHounslow\Bartec\Client\Client as BartecClient;
use LBHounslow\Bartec\Exception\SoapException;
use LBHounslow\Bartec\Response\Response;

interface ApiVersionAdapterInterface
{
    /**
     * @return string
     */
    public function getVersion();

    /**
     * @return string
     */
    public function getCollectiveWsdl();

    /**
     * @return BartecClient
     */
    public function getBartecClient();

    /**
     * @param array $soapOptions
     * @return $this
     */
    public function setBartecClientSoapOptions(array $soapOptions);

    /**
     * @param array $data
     * @return Response
     * @throws SoapException
     */
    public function createServiceRequest(array $data);

    /**
     * @param string $serviceRequestCode
     * @param array $data
     * @return Response
     * @throws SoapException
     */
    public function updateServiceRequest(string $serviceRequestCode, array $data);

    /**
     * @param string $ServiceRequestCode
     * @return Response
     * @throws SoapException
     */
    public function getServiceRequestDetail(string $ServiceRequestCode);

    /**
     * @param \stdClass $ServiceRequest
     * @param \stdClass $ServiceRequestStatus
     * @return Response
     * @throws SoapException
     */
    public function setServiceRequestStatus(\stdClass $ServiceRequest, \stdClass $ServiceRequestStatus);

    /**
     * @param array $data
     * @return Response
     * @throws SoapException
     */
    public function createServiceRequestDocument(array $data);

    /**
     * @param int $ServiceRequestID
     * @param string $note
     * @param int $noteTypeID
     * @param int $sequenceNumber
     * @param string $comment
     * @return Response
     * @throws SoapException
     */
    public function createServiceRequestNote(int $ServiceRequestID, string $note, int $noteTypeID, $sequenceNumber = 1, $comment = '');
}