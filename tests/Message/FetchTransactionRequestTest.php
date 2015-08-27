<?php
namespace Omnipay\Pagarme\Message;

use Omnipay\Tests\TestCase;

class FetchTransactionRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new FetchTransactionRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
                array(
                    'transactionReference' => 123456,
                )
        );
    }
    
    public function testGetData()
    {
        $data = $this->request->getData();
        
        $this->assertSame(array(), $data);
    }
    
    public function testGetQuery()
    {
        $data = $this->request->getQuery();
        
        $this->assertArrayHasKey('api_key', $data);
    }
    
    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The transactionReference parameter is required
     */
    public function testAmountRequired()
    {
        $this->request->setTransactionReference(null);
        $this->request->getQuery();
    }
    
    public function testGetHttpMethod()
    {
        $this->assertSame('GET', $this->request->getHttpMethod());
    }
    
    public function testSendSuccess()
    {
        $this->setMockHttpResponse('FetchTransactionSuccess.txt');
        $response = $this->request->send();
        $transactionArray = $response->getData();
        
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('transaction', $transactionArray['object'] );
        $this->assertSame('paid', $transactionArray['status'] );
        $this->assertSame(1537, $transactionArray['amount']);
        $this->assertNull($response->getMessage());
    }
    
    public function testSendFailure()
    {
        $this->setMockHttpResponse('FetchTransactionFailure.txt');
        $response = $this->request->send();
        
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('Transaction não encontrado', $response->getMessage());
    }
    
    public function testEndpoint()
    {
        $this->assertSame('https://api.pagar.me/1/transactions/123456', $this->request->getEndpoint());
    }
}
