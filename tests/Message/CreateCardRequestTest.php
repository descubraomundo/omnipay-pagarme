<?php

namespace Omnipay\Pagarme\Message;

use Omnipay\Tests\TestCase;

class CreateCardRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new CreateCardRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->setCard($this->getValidCard());
    }

    public function testEndpoint()
    {
        $this->request->setCustomerReference('');
        $this->assertSame('https://api.pagar.me/1/cards', $this->request->getEndpoint());
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The card_number parameter is required
     */
    public function testCard()
    {
        $this->request->setCard(null);
        $this->request->getData();
    }

    public function testDataWithCard()
    {
        $card = array(
            'billingName' => 'Foo Bar',
            'number' => '4111111111111111',
            'expiryMonth' => '01',
            'expiryYear' => '2016',
        );
        $this->request->setCard($card);
        $data = $this->request->getData();

        $this->assertSame('4111111111111111', $data['card_number']);
        $this->assertSame('012016', $data['card_expiration_date']);
        $this->assertSame('Foo Bar', $data['card_holder_name']);
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('CreateCardSuccess.txt');
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertSame('card_cicxb6pf700pvj96du2bo8zaj', $response->getCardReference());
        $this->assertNull($response->getMessage());
    }

    public function testSendFailure()
    {
        $this->setMockHttpResponse('CreateCardFailure.txt');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getCardReference());
        $this->assertSame('Data de expiração inválida', $response->getMessage());
    }
}
