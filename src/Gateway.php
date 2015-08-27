<?php

namespace Omnipay\Pagarme;

use Omnipay\Common\AbstractGateway;

/**
 * Pagarme Gateway
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
 *               'holder_document_number' => '214.278.589-40',
 *   ));
 *
 *   // Do an authorize transaction on the gateway
 *   $transaction = $gateway->authorize(array(
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
 *       echo "Authorize transaction was successful!\n";
 *       $sale_id = $response->getTransactionReference();
 *       $customer_id = $response->getCustomerReference();
 *       $card_id = $response->getCardReference();
 *       echo "Transaction reference = " . $sale_id . "\n";
 *   }
 * </code>
 *
 * Test modes:
 *
 * Pagarme accounts have test-mode API keys as well as live-mode
 * API keys. Data created with test-mode credentials will never 
 * hit the credit card networks and will never cost anyone money.
 *
 * Unlike some gateways, there is no test mode endpoint separate
 * to the live mode endpoint, the Pagarme API endpoint is the same
 * for test and for live.
 *
 * Setting the testMode flag on this gateway has no effect.  To
 * use test mode just use your test mode API key.
 *
 * Authentication:
 *
 * Authentication is by means of a single secret API key set as
 * the apiKey parameter when creating the gateway object.
 *
 * @see \Omnipay\Common\AbstractGateway
 * @see \Omnipay\Pagarme\Message\AbstractRequest
 * @link https://docs.pagar.me/
 */
class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'Pagarme';
    }
    
    /**
     * Get the gateway parameters
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return array(
            'apiKey' => '',
        );
    }
    
    /**
     * Get the gateway API Key
     *
     * Authentication is by means of a single secret API key set as
     * the apiKey parameter when creating the gateway object.
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }
    
    /**
     * Set the gateway API Key
     *
     * Authentication is by means of a single secret API key set as
     * the apiKey parameter when creating the gateway object.
     *
     * Pagarme accounts have test-mode API keys as well as live-mode
     * API keys. Data created with test-mode credentials will never 
     * hit the credit card networks and will never cost anyone money.
     *
     * Unlike some gateways, there is no test mode endpoint separate
     * to the live mode endpoint, the Stripe API endpoint is the same
     * for test and for live.
     *
     * Setting the testMode flag on this gateway has no effect.  To
     * use test mode just use your test mode API key.
     *
     * @param string $value
     * @return Gateway provides a fluent interface.
     */
    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }
    
    /**
     * Authorize Request
     *
     * An Authorize request is similar to a purchase request but the
     * charge issues an authorization (or pre-authorization), and no money
     * is transferred.  The transaction will need to be captured later
     * in order to effect payment. Uncaptured charges expire in 5 days.
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
     * @param array $parameters
     * @return \Omnipay\Pagarme\Message\AuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Pagarme\Message\AuthorizeRequest', $parameters);
    }
    
    /**
     * Capture Request
     *
     * Use this request to capture and process a previously created authorization.
     *
     * @param array $parameters
     * @return \Omnipay\Pagarme\Message\CaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Pagarme\Message\CaptureRequest', $parameters);
    }
    
    /**
     * Purchase request.
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
     * @param array $parameters
     * @return \Omnipay\Pagarme\Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Pagarme\Message\PurchaseRequest', $parameters);
    }
    
    /**
     * Refund Request
     *
     * When you refund, you must specify a transaction reference.
     *
     * Creating a new refund will refund a transaction that has
     * previously been created but not yet charged. Funds will
     * be refunded to the credit that was originally authorized.
     *
     * @param array $parameters
     * @return \Omnipay\Pagarme\Message\RefundRequest
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Pagarme\Message\RefundRequest', $parameters);
    }
    
    /**
     * Void Transaction Request
     * 
     * Pagarme does not support voiding, per se, but
     * we treat it as a full refund.
     *
     * See RefundRequest for additional information
     *
     * @param array $parameters
     * @return \Omnipay\Pagarme\Message\VoidRequest
     */
    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Pagarme\Message\VoidRequest', $parameters);
    }
    
    /**
     * Create Card
     *
     * This call can be used to create a new credit card.  
     * If a customerReference is passed in then
     * a card is added to an existing customer.  If there is no
     * customerReference passed in then a new customer is created.  The
     * response in that case will then contain both a customer reference
     * and a card reference, and is essentially the same as CreateCustomerRequest
     *
     * @param array $parameters
     * @return \Omnipay\Pagarme\Message\CreateCardRequest
     */
    public function createCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Pagarme\Message\CreateCardRequest', $parameters);
    }
    
    /**
     * Create Customer
     *
     * Customer objects allow you to perform recurring charges and
     * track multiple charges that are associated with the same customer.
     * The API allows you to create customers.
     *
     * @param array $parameters
     * @return \Omnipay\Pagarme\Message\CreateCustomerRequest
     */
    public function createCustomer(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Pagarme\Message\CreateCustomerRequest', $parameters);
    }
    
    /**
     * Pagarme Calculate Installments Request
     *
     * You can use Pagar.me API to calculate installments
     * for a purchase.
     * 
     * @param array $parameters
     * @return \Omnipay\Pagarme\Message\InstallmentsRequest
     */
    public function calculateInstallments(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Pagarme\Message\InstallmentsRequest', $parameters);
    }
    
    /**
     * Pagarme Fetch Transaction by Id.
     * 
     * @param array $parameters
     * @return \Omnipay\Pagarme\Message\FetchTransactionRequest
     */
    public function fetchTransaction(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Pagarme\Message\FetchTransactionRequest', $parameters);
    }
}
