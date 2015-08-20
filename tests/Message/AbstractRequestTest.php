<?php

namespace Omnipay\Pagarme\Message;

use Mockery;
use Omnipay\Tests\TestCase;

class AbstractRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = Mockery::mock('\Omnipay\Pagarme\Message\AbstractRequest')->makePartial();
        $this->request->initialize();
    }
    
    public function testGetEndpoint()
    {
        $this->assertStringStartsWith('https://api.pagar.me/1', $this->request->getEndpoint());
    }
    
    public function testSetApiKeyToData()
    {
        $data = array();
        $this->request->setApiKey('123abc');
        $data = $this->request->insertApiKeyToData($data);
        
        $this->assertArrayHasKey('api_key', $data);
        $this->assertSame('123abc', $data['api_key']);
    }
    
    public function testPagarmeCardInstance()
    {
        $card = $this->getValidCard();
        $this->request->setCard($card);
        
        $this->assertInstanceOf('Omnipay\Pagarme\CreditCard', $this->request->getCard());
    }
    
    public function testCardReference()
    {
        $this->assertSame($this->request, $this->request->setCardReference('abc123'));
        $this->assertSame('abc123', $this->request->getCardReference());
    }
    
    public function testCardToken()
    {
        $this->assertSame($this->request, $this->request->setToken('abc123'));
        $this->assertSame('abc123', $this->request->getToken());
    }
    
    public function testCustomer()
    {
        $this->assertSame($this->request, $this->request->setCustomer(array('name' => 'Foo', 'email' => 'foo@example.com')));
        $this->assertSame(array('name' => 'Foo', 'email' => 'foo@example.com'), $this->request->getCustomer());
    }
    
    public function testCardData()
    {
        $card = $this->getValidCard();
        $this->request->setCard($card);
        $data = $this->request->getCardData();

        $this->assertSame($card['number'], $data['card_number']);
        $this->assertSame($this->request->getCard()->getName(), $data['card_holder_name']);
        $this->assertSame(sprintf('%02d',$card['expiryMonth']).(string)$card['expiryYear'], $data['card_expiration_date']);
        $this->assertSame($card['cvv'], $data['card_cvv']);
    }
    
    public function testExtractValidDddPhone()
    {
        $result1 = $this->request->extractDddPhone('019992989946');
        $result2 = $this->request->extractDddPhone('01632522869');
        $result3 = $this->request->extractDddPhone('9 9198-9956');
        $result4 = $this->request->extractDddPhone('3261-2749');
        $result5 = $this->request->extractDddPhone(15991989953);
        
        $this->assertSame('19', $result1['ddd']);
        $this->assertsame('992989946', $result1['number']);
        $this->assertSame('16', $result2['ddd']);
        $this->assertSame('32522869', $result2['number']);
        $this->assertSame('', $result3['ddd']);
        $this->assertsame('991989956', $result3['number']);
        $this->assertSame('', $result4['ddd']);
        $this->assertsame('32612749', $result4['number']);
        $this->assertSame('15', $result5['ddd']);
        $this->assertsame('991989953', $result5['number']);
    }
    
    public function testExtractInvalidDddPhone()
    {
        $result1 = $this->request->extractDddPhone('');
        $result2 = $this->request->extractDddPhone('522869');
        $result3 = $this->request->extractDddPhone(null);
        
        $this->assertSame('', $result1['ddd']);
        $this->assertSame('', $result1['number']);
        $this->assertSame('', $result2['ddd']);
        $this->assertSame('522869', $result2['number']);
        $this->assertSame('', $result3['ddd']);
        $this->assertSame('', $result3['number']);
    }
    
    public function testExtractAddressWithValidString()
    {
        $result = $this->request->extractAddress(' Rua Foo Bar, 12 , Complementary ');
        
        $this->assertSame('Rua Foo Bar', $result['street']);
        $this->assertSame('12', $result['street_number']);
        $this->assertSame('Complementary', $result['complementary']);
    }
    
    public function testExtractAddressWithInsufficientParameters()
    {
        $result = $this->request->extractAddress('Rua Foo Bar, 30');
        
        $this->assertSame('Rua Foo Bar', $result['street']);
        $this->assertSame('30', $result['street_number']);
        $this->assertSame('', $result['complementary']);
    }
}