<?php

namespace Omnipay\Pagarme\Message;

use Omnipay\Tests\TestCase;

class AuthorizeRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'amount' => '12.00',
                'card' => $this->getValidCard(),
                'payment_method' => 'credit_card',
                'postback_url' => 'http://requestb.in/pkt7pgpk',
                'installments' => 1,
                'soft_descriptor' => 'testeDeApi',
                'metadata' => array(
                    'name' => 'bar',
                    'email' => 'foo',
                ),
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame(1200, $data['amount']);
        $this->assertSame('credit_card', $data['payment_method']);
        $this->assertSame('http://requestb.in/pkt7pgpk', $data['postback_url']);
        $this->assertSame(1, $data['installments']);
        $this->assertSame('testeDeApi', $data['soft_descriptor']);
        $this->assertSame(array('name' => 'bar', 'email' => 'foo'), $data['metadata']);
        $this->assertSame('false', $data['capture']);
    }
    
    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The card parameter is required
     */
    public function testCardRequired()
    {
        $this->request->setCard(null);
        $this->request->getData();
    }
    
    public function testSendSuccess()
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');
        $response = $this->request->send();
        
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame(184220, $response->getTransactionReference());
        $this->assertSame('card_ci6l9fx8f0042rt16rtb477gj', $response->getCardReference());
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