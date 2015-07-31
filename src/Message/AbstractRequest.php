<?php

namespace Omnipay\Pagarme\Message;

use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;

/**
 * Abstract Request
 *
 */
abstract class AbstractRequest extends BaseAbstractRequest
{
    /**
     * Live or Test Endpoint URL
     *
     * @var string URL
     */
    protected $endpoint = 'https://api.pagar.me/1';
    
    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }
    
    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }
    
    public function getMetadata()
    {
        return $this->getParameter('metadata');
    }
    
    public function setMetadata($value)
    {
        return $this->setParameter('metadata', $value);
    }
    
    /**
     * Get HTTP Method.
     *
     * This is nearly always POST but can be over-ridden in sub classes.
     *
     * @return string
     */
    public function getHttpMethod()
    {
        return 'POST';
    }
    
    public function sendData($data)
    {
        // don't throw exceptions for 4xx errors
        $this->httpClient->getEventDispatcher()->addListener(
            'request.error',
            function ($event) {
                if ($event['response']->isClientError()) {
                    $event->stopPropagation();
                }
            }
        );

        $httpRequest = $this->httpClient->createRequest(
            $this->getHttpMethod(),
            $this->getEndpoint(),
            null,
            $data
        );
        $httpResponse = $httpRequest
            ->setHeader('Authorization', 'Basic '.base64_encode($this->getApiKey().':'))
            ->send();

        return $this->response = new Response($this, $httpResponse->json());
    }
    
    protected function getEndpoint()
    {
        return $this->endpoint; 
    }
    
    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }
}