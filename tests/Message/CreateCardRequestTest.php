<?php

namespace Omnipay\Pagarme\Message;

use Omnipay\Tests\TestCase;
use DateTime;
use DateInterval;

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
        $expiryDate = new DateTime();
        $expiryDate->add(new DateInterval("P1Y"));
        $card = array(
            'billingName' => 'Foo Bar',
            'number' => '4111111111111111',
            'expiryMonth' => $expiryDate->format('m'),
            'expiryYear' => $expiryDate->format('Y'),
        );
        $this->request->setCard($card);
        $this->request->setCustomerReference(123456);
        $data = $this->request->getData();

        $this->assertSame('4111111111111111', $data['card_number']);
        $this->assertSame($expiryDate->format('mY'), $data['card_expiration_date']);
        $this->assertSame('Foo Bar', $data['card_holder_name']);
        $this->assertSame(123456, $data['customer_id']);
    }

    public function testDataWithCardHash()
    {
        $this->request->setCard(null);
        $this->request->setCardHash('card_1111');
        $data = $this->request->getData();

        $this->assertSame('card_1111', $data['card_hash']);
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('CreateCardSuccess.txt');
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getTransactionReference());
        $this->assertSame('card_cicxb6pf700pvj96du2bo8zaj', $response->getCardReference());
        $this->assertSame(22382, $response->getCustomerReference());
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
        $this->assertNull($response->getCustomerReference());
        $this->assertSame('Data de expiração inválida', $response->getMessage());
    }
}
