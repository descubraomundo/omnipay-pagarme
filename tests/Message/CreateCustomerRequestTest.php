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
                'holder_document_number' => '238.654.289-50',
                'address1' => 'Rua Firmino, 23, Bloco A',
                'address2' => 'Vila Paraíso',
                'city' => 'Cidade',
            ),
        ));
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
        $this->request->getData();
    }
    
    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The document_number parameter is required
     */
    public function testDataExceptionWithName()
    {
        $this->request->setCard(array('name' => 'John Doe'));
        $this->request->getData();
    }

    public function testData()
    {
        $this->request->setCard(
                array(
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'email' => 'jdoe25@example.com',
                    'address1' => 'Rod. Anhanguera, km 25, Bloco A',
                    'city' => 'Campinas',
                    'state' => 'SP',
                    'country' => 'Brasil',
                    'postcode' => '05223100',
                    'phone' => '013 8564 2211',
                    'holder_document_number' => '238.654.289-50',
                )
            );
        $data = $this->request->getData();
        
        $this->assertSame('John Doe', $data['name']);
        $this->assertSame('jdoe25@example.com', $data['email']);
        $this->assertSame('23865428950', $data['document_number']);
        $this->assertArrayHasKey('address', $data);
        $this->assertArrayHasKey('phone', $data);
    }
    
    public function testDataWithCustomerKey()
    {
        $customer = array(
            'firstName' => 'John F',
            'lastName' => 'Doe',
            'email' => 'jdoe@example.com',
            'address1' => 'Rua Alfonso F, 25, Torre A',
            'address2' => 'Alphaville',
            'postcode' => '05444040',
            'phone' => '(019)9 9988-7766',
            'birthday' => '1988-02-28',
            'gender' => 'M',
            'holder_document_number' => '21437814860'
        );
        $this->request->initialize(array(
            'customer' => $customer,
        ));
        $data = $this->request->getData();

        $this->assertSame('John F Doe', $data['name']);
        $this->assertSame('jdoe@example.com', $data['email']);
        $this->assertSame('Rua Alfonso F', $data['address']['street']);
        $this->assertSame('05444040', $data['address']['zipcode']);
        $this->assertSame('Torre A', $data['address']['complementary']);
        $this->assertSame('999887766', $data['phone']['number']);
        $this->assertSame('M', $data['sex']);
        $this->assertSame('02-28-1988', $data['born_at']);
        $this->assertSame('21437814860', $data['document_number']);
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
