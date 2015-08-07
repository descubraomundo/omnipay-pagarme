<?php

namespace Omnipay\Pagarme\Message;

class PurchaseRequest extends AuthorizeRequest
{
    /**
     * Pagarme Purchase Request
     *
     * To charge a credit card or generate a boleto you create a new transaction 
     * object. If your API key is in test mode, the supplied card won't actually 
     * be charged, though everything else will occur as if in live mode.
     *
     * Either a card object or card_id is required by default. Otherwise,
     * you must provide a card_hash, like the ones returned by Pagarme.js
     * or use the boleto's payment method.
     * 
     * Pagarme gateway supports only two types of "payment_method":
     * 
     * * credit_card
     * * boleto
     * 
     * @see https://docs.pagar.me/capturing-card-data/
     * 
     * Optionally, you can provide the customer details to use the antifraude
     * feature. These details is passed using the following attributes available
     * on credit card object:
     * 
     * * firstName
     * * lastName
     * * address1 (must be in the format "street, street_number and neighborhood")
     * * address2 (used to specify the optional parameter "street_complementary")
     * * postcode
     * * phone (must be in the format "DDD PhoneNumber" e.g. "19 98888 5555")
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
     *   // Create a credit card object
     *   // This card can be used for testing.
     *   $card = new CreditCard(array(
     *               'firstName'    => 'Example',
     *               'lastName'     => 'Customer',
     *               'number'       => '4242424242424242',
     *               'expiryMonth'  => '01',
     *               'expiryYear'   => '2020',
     *               'cvv'          => '123',
     *               'email'        => 'customer@example.com',
     *               'address1'     => 'Street name, Street number, Neighborhood',
     *               'address2'     => 'address complementary',
     *               'postcode'     => '05443100',
     *               'phone'        => '19 3242 8855',
     *   ));
     *
     *   // Do a purchase transaction on the gateway
     *   $transaction = $gateway->purchase(array(
     *       'amount'           => '10.00',
     *       'soft_descriptor'  => 'test',
     *       'payment_method'   => 'credit_card',
     *       'card'             => $card,
     *       'metadata'         => array(
     *                                 'product_id' => 'ID1111',
     *                                 'invoice_id' => 'IV2222',
     *                             ),
     *   ));
     *   $response = $transaction->send();
     *   if ($response->isSuccessful()) {
     *       echo "Purchase transaction was successful!\n";
     *       $sale_id = $response->getTransactionReference();
     *       $customer_id = $response->getCustomerReference();
     *       $card_id = $response->getCardReference();
     *       echo "Transaction reference = " . $sale_id . "\n";
     *   }
     * </code>
     *
     * Because a purchase request in Pagarme looks similar to an
     * Authorize request, this class simply extends the AuthorizeRequest
     * class and over-rides the getData method setting capture = true.
     *
     * @see \Omnipay\Pagarme\Gateway
     * @link https://docs.pagar.me/api/?shell#criando-uma-transao
     */
    public function getData()
    {
        $data = parent::getData();
        $data['capture'] = 'true';
        return $data;
    }
}