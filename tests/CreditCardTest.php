<?php
namespace Omnipay\Pagarme;

use Omnipay\Tests\TestCase;

class CreditCardTest extends TestCase
{
    public function setUp()
    {
        $this->card = new CreditCard;
        $this->card->setNumber('4111111111111111');
        $this->card->setFirstName('Example');
        $this->card->setLastName('Customer');
        $this->card->setExpiryMonth('4');
        $this->card->setExpiryYear(gmdate('Y')+2);
        $this->card->setCvv('123');
        $this->card->setHolderDocumentNumber('218.478.120-40');
    }

    public function testConstructWithParams()
    {
        $card = new CreditCard(array('name' => 'Test Customer'));
        $this->assertSame('Test Customer', $card->getName());
    }

    public function testInitializeWithParams()
    {
        $card = new CreditCard;
        $card->initialize(array('name' => 'Test Customer'));
        $this->assertSame('Test Customer', $card->getName());
    }

    public function testGetParameters()
    {
        $card = new CreditCard(array(
            'name' => 'Example Customer',
            'number' => '1234',
            'expiryMonth' => 6,
            'expiryYear' => 2016,
            'holder_document_number' => '218.478.120-40',
        ));

        $parameters = $card->getParameters();
        $this->assertSame('Example', $parameters['billingFirstName']);
        $this->assertSame('Customer', $parameters['billingLastName']);
        $this->assertSame('1234', $parameters['number']);
        $this->assertSame(6, $parameters['expiryMonth']);
        $this->assertSame(2016, $parameters['expiryYear']);
        $this->assertSame('21847812040', $parameters['holder_document_number']);
    }
    
    public function testHolderDocumentNumber()
    {
        $this->card->setHolderDocumentNumber('21847812040');
        $this->assertEquals('21847812040', $this->card->getHolderDocumentNumber());
    }
    
    public function testSetHolderDocumentNumberStripsNonDigits()
    {
        $this->card->setHolderDocumentNumber('218. 478.120 - 40');
        $this->assertEquals('21847812040', $this->card->getHolderDocumentNumber());
    }
}