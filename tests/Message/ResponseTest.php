<?php

namespace Omnipay\Pagarme\Message;

use Omnipay\Tests\TestCase;

class ResponseTest extends TestCase
{

    public function createResponse($mock) {
        return new Response($this->getMockRequest(), json_decode($mock->getBody()->getContents(), true));
    }

    public function testPurchaseBoletoSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('PurchaseBoletoSuccess.txt');
        $response = $this->createResponse($httpResponse);

        $data = $response->getBoleto();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('https://pagar.me', $data['boleto_url']);
        $this->assertSame('1234 5678', $data['boleto_barcode']);
    }

    public function testAuthorizeBoletoSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('AuthorizeBoletoSuccess.txt');
        $response = $this->createResponse($httpResponse);

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getBoleto());
    }

    public function testPurchaseSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('PurchaseSuccess.txt');
        $response = $this->createResponse($httpResponse);

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame(184220, $response->getTransactionReference());
        $this->assertSame('card_ci6l9fx8f0042rt16rtb477gj', $response->getCardReference());
        $this->assertNull($response->getMessage());
    }

    public function testPurchaseFailure()
    {
        $httpResponse = $this->getMockHttpResponse('PurchaseFailure.txt');
        $response = $this->createResponse($httpResponse);

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame(243844, $response->getTransactionReference());
        $this->assertSame('card_cicq9age0005h4d6d7hxx7034', $response->getCardReference());
        $this->assertSame('acquirer', $response->getMessage());
    }

    public function testPurchaseError()
    {
        $httpResponse = $this->getMockHttpResponse('PurchaseError.txt');
        $response = $this->createResponse($httpResponse);

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getCardReference());
        $this->assertSame('api_key inválida', $response->getMessage());
    }
}

