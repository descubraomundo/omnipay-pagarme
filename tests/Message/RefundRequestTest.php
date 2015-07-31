<?php

namespace Omnipay\Pagarme\Message;

use Omnipay\Tests\TestCase;

class RefundRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->setTransactionReference(111111);
    }

    public function testEndpoint()
    {
        $this->assertSame('https://api.pagar.me/1/transactions/111111/refund', $this->request->getEndpoint());
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('RefundSuccess.txt');
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame(243899, $response->getTransactionReference());
        $this->assertSame('card_cicrliw3p002jxy6d80lm3b0e', $response->getCardReference());
        $this->assertNull($response->getMessage());
    }

    public function testSendError()
    {
        $this->setMockHttpResponse('RefundFailure.txt');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getCardReference());
        $this->assertSame('Transação já estornada', $response->getMessage());
    }
}
