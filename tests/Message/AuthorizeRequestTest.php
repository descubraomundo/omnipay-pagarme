<?php

namespace Omnipay\Pagarme\Message;

use Omnipay\Tests\TestCase;
use DateTime;
use DateInterval;

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
        $expiryDate = new DateTime();
        $expiryDate->add(new DateInterval("P1Y"));
        $card = array(
            'firstName' => 'John F',
            'lastName' => 'Doe',
            'number' => '4242424242424242',
            'expiryMonth' => $expiryDate->format('m'),
            'expiryYear' => $expiryDate->format('Y'),
            'cvv' => '123',
            'email' => 'jdoe@example.com',
            'address1' => 'Rua Alfonso F, 25, Torre A',
            'address2' => 'Alphaville',
            'postcode' => '05444040',
            'phone' => '(019)9 9988-7766',
            'birthday' => '1988-02-28',
            'gender' => 'M',
            'holder_document_number' => '214.278.589-40'
        );
        $this->request->setCard($card);
        $data = $this->request->getData();

        $this->assertSame(1200, $data['amount']);
        $this->assertSame('credit_card', $data['payment_method']);
        $this->assertSame('http://requestb.in/pkt7pgpk', $data['postback_url']);
        $this->assertSame(1, $data['installments']);
        $this->assertSame('testeDeApi', $data['soft_descriptor']);
        $this->assertSame(array('name' => 'bar', 'email' => 'foo'), $data['metadata']);
        $this->assertSame('John F Doe', $data['customer']['name']);
        $this->assertSame('jdoe@example.com', $data['customer']['email']);
        $this->assertSame('Rua Alfonso F', $data['customer']['address']['street']);
        $this->assertSame('05444040', $data['customer']['address']['zipcode']);
        $this->assertSame('Torre A', $data['customer']['address']['complementary']);
        $this->assertSame('Alphaville', $data['customer']['address']['neighborhood']);
        $this->assertSame('999887766', $data['customer']['phone']['number']);
        $this->assertSame('M', $data['customer']['sex']);
        $this->assertSame('02-28-1988', $data['customer']['born_at']);
        $this->assertSame('false', $data['capture']);
        $this->assertSame('21427858940', $data['customer']['document_number']);
    }

    public function testGetDataForBoletoPaymentMethod()
    {
        $this->request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'amount' => '12.00',
                'payment_method' => 'boleto',
                'postback_url' => 'http://requestb.in/pkt7pgpk',
                'boleto_expiration_date' => '25 August 2015',
            )
        );
        $data = $this->request->getData();

        $this->assertSame('2015-08-25T03:00:00', $data['boleto_expiration_date']);
        $this->assertArrayNotHasKey('card_id', $data);
        $this->assertArrayNotHasKey('card_hash', $data);
        $this->assertArrayNotHasKey('card_number', $data);
    }

    public function testGetDataUsingCardHash()
    {
        $this->request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'amount' => '12.00',
                'payment_method' => 'credit_card',
                'card_hash' => 'card_123',
            )
        );
        $data = $this->request->getData();

        $this->assertSame('card_123', $data['card_hash']);
        $this->assertArrayNotHasKey('card_id', $data);
        $this->assertArrayNotHasKey('card_number', $data);
    }

    public function testGetDataUsingCardReference()
    {
        $this->request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'amount' => '12.00',
                'payment_method' => 'credit_card',
                'cardReference' => 123456,
            )
        );
        $data = $this->request->getData();

        $this->assertSame(123456, $data['card_id']);
        $this->assertArrayNotHasKey('card_hash', $data);
        $this->assertArrayNotHasKey('card_number', $data);
    }

    public function testSetCustomerWithoutCard()
    {
        $customer = array(
            'firstName' => 'John F',
            'lastName' => 'Doe',
            'email' => 'jdoe@example.com',
            'address1' => 'Rua Alfonso F, 25, Torre A',
            'address2' => 'Alphaville',
            'postcode' => '05444040',
            'phone' => '(019)9 9988-7766',
            'birthday' => '1988-02-28',
            'gender' => 'M',
            'holder_document_number' => '214.278.589-40'
        );
        $this->request->initialize(array(
            'amount' => '12.34',
            'payment_method' => 'boleto',
            'customer' => $customer,
        ));
        $data = $this->request->getData();

        $this->assertSame(1234, $data['amount']);
        $this->assertSame('John F Doe', $data['customer']['name']);
        $this->assertSame('jdoe@example.com', $data['customer']['email']);
        $this->assertSame('Rua Alfonso F', $data['customer']['address']['street']);
        $this->assertSame('05444040', $data['customer']['address']['zipcode']);
        $this->assertSame('Torre A', $data['customer']['address']['complementary']);
        $this->assertSame('Alphaville', $data['customer']['address']['neighborhood']);
        $this->assertSame('999887766', $data['customer']['phone']['number']);
        $this->assertSame('M', $data['customer']['sex']);
        $this->assertSame('02-28-1988', $data['customer']['born_at']);
        $this->assertSame('21427858940', $data['customer']['document_number']);
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

    public function testSetBoletoExpirationDate()
    {
        $this->request->setPaymentMethod('boleto');
        $this->request->setBoletoExpirationDate('2 august 2015');

        $this->assertSame('2015-08-02T03:00:00', $this->request->getBoletoExpirationDate());
    }

    public function testSetBoletoExpirationDateWithNull()
    {
        $this->request->setPaymentMethod('boleto');
        $this->request->setBoletoExpirationDate(null);

        $this->assertNull($this->request->getBoletoExpirationDate());
    }

    public function testSetBoletoPaymentMethod()
    {
        $this->request->initialize(
            array(
                'amount' => '12.50',
                'card'   => array(
                    'name' => 'John Doe',
                    'email' => 'johndoe@example.com',
                )
            )
        );
        $this->request->setPaymentMethod('boleto');
        $data = $this->request->getData();

        $this->assertSame('boleto', $data['payment_method']);
        $this->assertSame('John Doe', $data['customer']['name']);
        $this->assertSame('johndoe@example.com', $data['customer']['email']);
        $this->assertArrayNotHasKey('card_id', $data);
        $this->assertArrayNotHasKey('card_hash', $data);
        $this->assertArrayNotHasKey('card_number', $data);
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

    public function testEndpoint()
    {
        $this->assertSame('https://api.pagar.me/1/transactions', $this->request->getEndpoint());
    }

}