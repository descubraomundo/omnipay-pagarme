<?php
/**
 * Pagarme Create Customer Request
 */

namespace Omnipay\Pagarme\Message;

/**
 * Pagarme Create Customer Request
 *
 * Customer objects allow you to perform recurring charges and
 * track multiple charges that are associated with the same customer.
 * The API allows you to create, delete, and update your customers.
 * You can retrieve individual customers as well as a list of all of
 * your customers.
 * 
 * Harnessing the Omnipay's CreditCard model, we can use the 
 * attributes listed below to create new customers. So it must 
 * pass the parameters for the card attribute or create a CreditCard
 * Object (see the code example below). Alternatively you can pass 
 * the data to the customer attribute.
 * 
 * * firstName
 * * lastName
 * * address1 (must be in the format "street, street_number and complementary")
 * * address2 (used to specify the parameter "address_neighborhood")
 * * city
 * * postcode
 * * state
 * * country
 * * phone (must be in the format "DDD PhoneNumber" e.g. "19 98888 5555")
 * * email
 * * birthday
 * * gender
 * 
 * 
 * Provided card's attributes will be ignored by Pagarme API.
 * 
 * Either a pair name|email or document_number (valid CPF or CNPJ) is required. 
 * 
 * 
 * 
 * Example:
 *
 * <code>
 *   // Create a gateway for the Pagarme Gateway
 *   // (routes to GatewayFactory::create)
 *   $gateway = Omnipay::create('Pagarme');
 *
 *   // Initialise the gateway
 *   $gateway->initialize(array(
 *       'apiKey' => 'MyApiKey',
 *   ));
 *
 *   // Create a credit card object or set the card
 *   // attribute
 *   // This card can be used for testing.
 *   $customer = array(
 *               'firstName'    => 'Example',
 *               'lastName'     => 'Customer',
 *               'email'        => 'customer@example.com',
 *               'address1'     => 'Street name, Street number, Complementary',
 *               'address2'     => 'Neighborhood',
 *               'postcode'     => '05443100',
 *               'phone'        => '19 3242 8855',
 *               'holder_document_number' => '21437814860',
 *   );
 *
 *   // Do a create customer transaction on the gateway
 *   $response = $gateway->createCustomer(array(
 *       'customer' => $customer,
 *   ))->send();
 * 
 *   if ($response->isSuccessful()) {
 *       echo "Gateway createCustomer was successful.\n";
 *       // Find the customer ID
 *       $customer_id = $response->getCustomerReference();
 *       echo "Customer ID = " . $customer_id . "\n";
 *       // Find the card ID
 *       $card_id = $response->getCardReference();
 *       echo "Card ID = " . $card_id . "\n";
 *   }
 * </code>
 *
 * @link https://docs.pagar.me/api/?shell#criando-um-cliente
 */
class CreateCustomerRequest extends AbstractRequest
{
    public function getData()
    {
        $data = array();
        //var_dump($this->getCustomer());
        //die;
        if ( $this->getCustomer() ) {
            $customerArray = $this->getCustomer();
            $this->setCard($customerArray);
        }
        
        if ( $this->getCard() ) {
            $customer = $this->getCustomerData();
            $data = $customer['customer'];
            if ( isset($data['address']) ) {
                $data['address']['city'] = $this->getCard()->getCity();
                $data['address']['state'] = $this->getCard()->getState();
                $data['address']['country'] = $this->getCard()->getCountry();
            }
        }
        
        // Validate Required Attributes
        if ( ! isset($data['document_number']) ) {
            if ( ! (isset($data['email']) && (isset($data['name']))) ) {
                $this->validate('document_number');
            }
        }
        
        return $data;
    }

    public function getEndpoint()
    {
        return $this->endpoint . 'customers';
    }
}
