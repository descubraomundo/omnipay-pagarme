<?php

namespace Omnipay\Pagarme\Message;

use Omnipay\Tests\TestCase;

class CreateCustomerRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new CreateCustomerRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(array(
            'card' => array(
                'name' => 'Example Customer User',
                'address1' => 'Rua Firmino, 23, Vila Paraíso',
                'address2' => 'Bloco A',
                'city' => 'Cidade',
            ),
        ));
        $this->request->setCustomerDocument('69264160108');
    }

    public function testEndpoint()
    {
        $this->assertSame('https://api.pagar.me/1/customers', $this->request->getEndpoint());
    }
    
    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The document_number parameter is required
     */
    public function testDataException()
    {
        $this->request->setCard(null);
        $this->request->setCustomerDocument(null);
        $this->request->getData();
    }
    
    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The document_number parameter is required
     */
    public function testDataExceptionWithName()
    {
        $this->request->setCard(array('name' => 'John Doe'));
        $this->request->setCustomerDocument(null);
        $this->request->getData();
    }

    public function testData()
    {
        $this->request->setCard(
                array(
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'email' => 'jdoe25@example.com',
                    'address1' => 'Rod. Anhanguera, km 25, Vila Alegre',
                    'city' => 'Campinas',
                    'state' => 'SP',
                    'country' => 'Brasil',
                    'postcode' => '05223100',
                    'phone' => '013 8564 2211',
                )
            );
        $data = $this->request->getData();
        
        $this->assertSame('John Doe', $data['name']);
        $this->assertSame('jdoe25@example.com', $data['email']);
        $this->assertArrayHasKey('address', $data);
        $this->assertArrayHasKey('phone', $data);
    }
    
    public function testDataOnlyWithDocumentNumber()
    {
        $this->request->setCard(null);
        $this->request->setCustomerDocument('77010168644');
        $data = $this->request->getData();
        
        $this->assertSame('77010168644', $data['document_number']);
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('CreateCustomerSuccess.txt');
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertSame(22063, $response->getCustomerReference());
        $this->assertNull($response->getCardReference());
        $this->assertNull($response->getMessage());
    }

    public function testSendFailure()
    {
        $this->setMockHttpResponse('CreateCustomerFailure.txt');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getCardReference());
        $this->assertSame('número do documento está faltando', $response->getMessage());
    }
}
