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
    
    /**
     * Refund Request (Estorno de Transação)
     *
     *
     * Creating a new refund will refund a charge that has
     * previously been created but not yet refunded. Funds will
     * be refunded to the credit or debit card that was originally
     * charged. The fees you were originally charged are also
     * refunded.
     *
     * @param array $parameters
     * @return \Omnipay\Pagarme\Message\RefundRequest
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Pagarme\Message\RefundRequest', $parameters);
    }

    
    public function testVoid()
    {
        $request = $this->gateway->void();

        $this->assertInstanceOf('Omnipay\Pagarme\Message\VoidRequest', $request);
    }

}