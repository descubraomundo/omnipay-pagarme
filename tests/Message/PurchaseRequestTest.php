<?php

namespace Omnipay\Pagarme\Message;

use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'amount' => '10.00',
                'payment_method' => 'credit_card',
                'card' => $this->getValidCard(),
            )
        );
    }

    public function testCaptureIsTrue()
    {
        $data = $this->request->getData();
        $this->assertSame('true', $data['capture']);
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame(184220, $response->getTransactionReference());
        $this->assertSame('card_ci6l9fx8f0042rt16rtb477gj', $response->getCardReference());
        $this->assertSame(22382, $response->getCustomerReference());
        $this->assertNull($response->getBoleto());
        $this->assertNull($response->getMessage());
    }

    public function testSendFailure()
    {
        $this->setMockHttpResponse('PurchaseFailure.txt');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame(243844, $response->getTransactionReference());
        $this->assertSame('card_cicq9age0005h4d6d7hxx7034', $response->getCardReference());
        $this->assertSame('acquirer', $response->getMessage());
    }
    
    public function testSendError()
    {
        $this->setMockHttpResponse('PurchaseError.txt');
        $response = $this->request->send();
        
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getCardReference());
        $this->assertSame('api_key inválida', $response->getMessage());
    }
}




