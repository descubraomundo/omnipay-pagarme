<?php

namespace Omnipay\Pagarme\Message;

use Omnipay\Tests\TestCase;

class InstallmentsRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new InstallmentsRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
                array(
                    'max_installments' => 12,
                    'free_installments' => 3,
                    'interest_rate' => 1.12,
                    'amount' => '1200.00',
                )
        );
    }
    
    public function testGetData()
    {
        $data = $this->request->getData();
        
        $this->assertSame(array(), $data);
    }
    
    public function testInterestRate()
    {
        $this->assertSame($this->request, $this->request->setInterestRate(1.11));
        $this->assertSame(1.11, $this->request->getInterestRate());
    }
    
    public function testMaxInstallments()
    {
        $this->assertSame($this->request, $this->request->setMaxInstallments(10));
        $this->assertSame(10, $this->request->getMaxInstallments());
    }
    
    public function testFreeInstallments()
    {
        $this->assertSame($this->request, $this->request->setFreeInstallments(2));
        $this->assertSame(2, $this->request->getFreeInstallments());
    }
    
    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The amount parameter is required
     */
    public function testAmountRequired()
    {
        $this->request->setAmount(null);
        $this->request->getQuery();
    }
    
    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The interest_rate parameter is required
     */
    public function testInterestRateRequired()
    {
        $this->request->setInterestRate(null);
        $this->request->getQuery();
    }
    
    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The max_installments parameter is required
     */
    public function testMaxInstallmentsRequired()
    {
        $this->request->setMaxInstallments(null);
        $this->request->getQuery();
    }
    
    public function testGetHttpMethod()
    {
        $this->assertSame('GET', $this->request->getHttpMethod());
    }
    
    public function testGetQuery()
    {
        $data = $this->request->getQuery();
        
        $this->assertArrayHasKey('api_key', $data);
    }
    
    public function testSendSuccess()
    {
        $this->setMockHttpResponse('CalculateInstallmentsSuccess.txt');
        $response = $this->request->send();
        $calculatedInstallments = $response->getCalculatedInstallments();
        
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame(array("installment" => 1, "amount" => 120000, "installment_amount" => 120000), $calculatedInstallments[1] );
        $this->assertSame(array("installment" => 12, "amount" => 129422, "installment_amount" => 10785), $calculatedInstallments[12] );
        $this->assertNull($response->getMessage());
    }
    
    public function testSendFailure()
    {
        $this->setMockHttpResponse('CalculateInstallmentsFailure.txt');
        $response = $this->request->send();
        
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getCalculatedInstallments());
        $this->assertSame('Taxa de juros está faltando', $response->getMessage());
    }
    
    public function testEndpoint()
    {
        $this->assertSame('https://api.pagar.me/1/transactions/calculate_installments_amount', $this->request->getEndpoint());
    }
}
