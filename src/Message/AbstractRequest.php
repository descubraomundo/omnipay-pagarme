<?php

namespace Omnipay\Pagarme\Message;

use Omnipay\Pagarme\CreditCard;
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
    protected $endpoint = 'https://api.pagar.me/1/';
    
    /**
     * Get the card.
     *
     * @return CreditCard
     */
    public function getCard()
    {
        return $this->getParameter('card');
    }

    /**
     * Sets the card.
     *
     * @param CreditCard $value
     * @return AbstractRequest Provides a fluent interface
     */
    public function setCard($value)
    {
        if ($value && !$value instanceof CreditCard) {
            $value = new CreditCard($value);
        }

        return $this->setParameter('card', $value);
    }
    
    /**
     * Get API key
     * 
     * @return string API key
     */
    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }
    
    /**
     * Set API key
     * 
     * @param string $value
     * @return AbstractRequest provides a fluent interface.
     */
    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }
    
    /**
     * Get Customer Data
     * 
     * @return array customer data
     */
    public function getCustomer()
    {
        return $this->getParameter('customer');
    }
    
    /**
     * Set Customer data
     * 
     * @param array $value
     * @return AbstractRequest provides a fluent interface.
     */
    public function setCustomer($value)
    {
        return $this->setParameter('customer', $value);
    }
    
    /**
     * Get the customer reference
     *
     * @return string customer reference
     */
    public function getCustomerReference()
    {
        return $this->getParameter('customerReference');
    }

    /**
     * Set the customer reference
     *
     * Used when calling CreateCardRequest on an existing customer. If this
     * parameter is not set then a new customer is created.
     *
     * @return AbstractRequest provides a fluent interface.
     */
    public function setCustomerReference($value)
    {
        return $this->setParameter('customerReference', $value);
    }
    
    /**
     * Get the card hash
     *
     * @return string card hash
     */
    public function getCardHash()
    {
        return $this->getParameter('card_hash');
    }

    /**
     * Set the card hash
     *
     * Must be a card hash like the ones returned by Pagarme.js.
     *
     * @return AbstractRequest provides a fluent interface.
     */
    public function setCardHash($value)
    {
        return $this->setParameter('card_hash', $value);
    }
    
    /**
     * Get Metadata
     * 
     * @return array metadata
     */
    public function getMetadata()
    {
        return $this->getParameter('metadata');
    }
    
    /**
     * 
     * @param array $value
     * @return AbstractRequest provides a fluent interface.
     */
    public function setMetadata($value)
    {
        return $this->setParameter('metadata', $value);
    }
    
    /**
     * Insert the API key into de array.
     * 
     * @param array $data
     * @return array The data with the API key to be used in all Requests
     */
    protected function insertApiKeyToData($data)
    {
        $data['api_key'] = $this->getApiKey();
        
        return $data;
    }
     
    /**
     * Get HTTP Method.
     *
     * This is nearly always POST but can be over-ridden in sub classes.
     *
     * @return string the HTTP method
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
            $this->insertApiKeyToData($data),
            $this->getOptions()
        );
        $httpResponse = $httpRequest->send();

        return $this->response = new Response($this, $httpResponse->json());
    }
    
    /**
     * Get Query Options.
     *
     * Must be over-ridden in sub classes that make GET requests
     * with query parameters.
     *
     * @return array The query Options
     */
    protected function getOptions()
    {
        return array();
    }
    
    protected function getEndpoint()
    {
        return $this->endpoint; 
    }
    
    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }
    
    /**
     * Get the card data.
     *
     * Because the pagarme gateway uses a common format for passing
     * card data to the API, this function can be called to get the
     * data from the associated card object in the format that the
     * API requires.
     *
     * @return array card data
     */
    protected function getCardData()
    {
        $card = $this->getCard();
        $data = array();
        
        $card->validate();
        $data['object'] = 'card';
        $data['card_number'] = $card->getNumber();
        $data['card_expiration_date'] = sprintf('%02d',$card->getExpiryMonth()).(string)$card->getExpiryYear();
        if ( $card->getCvv() ) {
            $data['card_cvv'] = $card->getCvv();
        }
        $data['card_holder_name'] = $card->getName();
        
        return $data;
    }
    
    /**
     * Get the Customer data.
     * 
     * Because the pagarme gateway uses a common format for passing
     * customer data to the API, this function can be called to get the
     * data from the card object in the format that the API requires.
     * 
     * @return array customer data
     */
    protected function getCustomerData() 
    {
        $card = $this->getCard();
        $data = array();
        
        $data['customer']['name'] = $card->getName();
        $data['customer']['email'] = $card->getEmail();
        $data['customer']['sex'] = $card->getGender();
        $data['customer']['born_at'] = $card->getBirthday('m-d-Y');
        $data['customer']['document_number'] = $card->getHolderDocumentNumber();
        
        $arrayAddress = $this->extractAddress($card->getAddress1());
        if ( ! empty($arrayAddress['street']) ) {
            $data['customer']['address'] = $arrayAddress;
            $data['customer']['address']['zipcode'] = $card->getPostcode();
            if ( $card->getAddress2() ) {
                $data['customer']['address']['neighborhood'] = $card->getAddress2();
            }
        }
        
        $arrayPhone = $this->extractDddPhone($card->getPhone());
        if ( ! empty($arrayPhone['ddd']) ) {
            $data['customer']['phone'] = $arrayPhone;
        }
        
        return $data;
    }
    
    /**
     * Separate DDD from phone numbers in an array 
     * containing two keys:
     * 
     * * ddd
     * * number
     * 
     * @param string $phoneNumber phone number with DDD (byref)
     * @return array the Phone number and the DDD with two digits
     */
    protected function extractDddPhone($phoneNumber)
    {
        $arrayPhone = array();
        $phone = preg_replace("/[^0-9]/", "", $phoneNumber);
        if(substr( $phone, 0, 1 ) === "0"){
            $arrayPhone['ddd'] = substr($phone, 1, 2);
            $arrayPhone['number'] = substr($phone, 3);
        } elseif (strlen($phone) < 10 ) {
            $arrayPhone['ddd'] = '';
            $arrayPhone['number'] = $phone;
        } else {
            $arrayPhone['ddd'] = substr($phone, 0, 2);
            $arrayPhone['number'] = substr($phone, 2);
        }
        
        return $arrayPhone;
    }
    
    /**
     * Separate data from the credit card Address in an 
     * array containing the keys:
     * * street
     * * street_number
     * * complementary
     * 
     * It's important to provide the parameter $address
     * with the information in the given order and separated 
     * by commas.
     * 
     * @param string $address
     * @return array containing the street, street_number and complementary
     */
    protected function extractAddress($address)
    {
        $result = array();
        $explode = array_map('trim', explode(',', $address));
        
        $result['street'] = $explode[0];
        $result['street_number'] = isset($explode[1]) ? $explode[1] : '';
        $result['complementary'] = isset($explode[2]) ? $explode[2] : '';
        
        return $result;
    }
}