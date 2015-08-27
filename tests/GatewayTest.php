<?php

namespace Omnipay\Pagarme;

use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
	
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
    }
        
    public function testAuthorize()
    {
        $request = $this->gateway->authorize(array('amount' => '10.00'));
        
        $this->assertInstanceOf('Omnipay\Pagarme\Message\AuthorizeRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }
    
    public function testCapture()
    {
        $request = $this->gateway->capture();

        $this->assertInstanceOf('Omnipay\Pagarme\Message\CaptureRequest', $request);
    }

    public function testPurchase()
    {
        $request = $this->gateway->purchase(array('amount' => '10.00'));

        $this->assertInstanceOf('Omnipay\Pagarme\Message\PurchaseRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }
    
    public function testRefund()
    {
        $request = $this->gateway->refund(array('amount' => '10.00'));

        $this->assertInstanceOf('Omnipay\Pagarme\Message\RefundRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    
    public function testVoid()
    {
        $request = $this->gateway->void();

        $this->assertInstanceOf('Omnipay\Pagarme\Message\VoidRequest', $request);
    }
    
    public function testCreateCustomer()
    {
        $request = $this->gateway->createCustomer();

        $this->assertInstanceOf('Omnipay\Pagarme\Message\CreateCustomerRequest', $request);
    }
    
    public function testCalculateInstallments()
    {
        $request = $this->gateway->calculateInstallments();

        $this->assertInstanceOf('Omnipay\Pagarme\Message\InstallmentsRequest', $request);
    }
    
    public function testFetchTransaction()
    {
        $request = $this->gateway->fetchTransaction();

        $this->assertInstanceOf('Omnipay\Pagarme\Message\FetchTransactionRequest', $request);
    }
}