<?php
/**
 * Pagarme Authorize Request
 */

namespace Omnipay\Pagarme\Message;

/**
 * Pagarme Authorize Request
 *
 * An Authorize request is similar to a purchase request but the
 * charge issues an authorization (or pre-authorization), and no money
 * is transferred.  The transaction will need to be captured later
 * in order to effect payment. Uncaptured charges expire in 5 days.
 *
 * Either a customerReference or a card is required.  If a customerReference
 * is passed in then the cardReference must be the reference of a card
 * assigned to the customer.  Otherwise, if you do not pass a customer ID,
 * the card you provide must either be a token, like the ones returned by
 * Stripe.js, or a dictionary containing a user's credit card details.
 *
 * IN OTHER WORDS: You cannot just pass a card reference into this request,
 * you must also provide a customer reference if you want to use a stored
 * card.
 *
 * Example:
 *
 * <code>
 *   // Create a gateway for the Stripe Gateway
 *   // (routes to GatewayFactory::create)
 *   $gateway = Omnipay::create('Stripe');
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
 *               'email'                 => 'customer@example.com',
 *               'billingAddress1'       => '1 Scrubby Creek Road',
 *               'billingCountry'        => 'AU',
 *               'billingCity'           => 'Scrubby Creek',
 *               'billingPostcode'       => '4999',
 *               'billingState'          => 'QLD',
 *   ));
 *
 *   // Do an authorize transaction on the gateway
 *   $transaction = $gateway->authorize(array(
 *       'amount'                   => '10.00',
 *       'currency'                 => 'USD',
 *       'description'              => 'This is a test authorize transaction.',
 *       'card'                     => $card,
 *   ));
 *   $response = $transaction->send();
 *   if ($response->isSuccessful()) {
 *       echo "Authorize transaction was successful!\n";
 *       $sale_id = $response->getTransactionReference();
 *       echo "Transaction reference = " . $sale_id . "\n";
 *   }
 * </code>
 *
 * @see \Omnipay\Pagarme\Gateway
 * @link https://docs.pagar.me/api/?shell#objeto-transaction
 */
class AuthorizeRequest extends AbstractRequest
{
    /**
     * @return mixed
     */
    public function getPostbackUrl()
    {
        return $this->getParameter('postback_url');
    }
    
    /**
     * @param string $value
     * @return AbstractRequest provides a fluent interface.
     */
    public function setPostbackUrl($value)
    {
        return $this->setParameter('postback_url', $value);
    }
    
    /**
     * @return mixed
     */
    public function getInstallments()
    {
        return $this->getParameter('installments');
    }
    
    /**
     * @param string $value
     * @return AbstractRequest provides a fluent interface.
     */
    public function setInstallments($value)
    {
        return $this->setParameter('installments', $value);
    }
    
    /**
     * @return mixed
     */
    public function getSoftDescriptor()
    {
        return $this->getParameter('soft_descriptor');
    }
    
    /**
     * @param string $value
     * @return AbstractRequest provides a fluent interface.
     */
    public function setSoftDescriptor($value)
    {
        return $this->setParameter('soft_descriptor', $value);
    }
    
    public function getData()
    {
        $this->validate('amount');
        
        $data = array();
        
        $data['amount'] = $this->getAmountInteger();
        $data['payment_method'] = $this->getPaymentMethod();
        $data['postback_url'] = $this->getPostbackUrl();
        $data['installments'] = $this->getInstallments();
        $data['soft_descriptor'] = $this->getSoftDescriptor();
        $data['metadata'] = $this->getMetadata();
        $data['card'] = $this->getCard();
        $data['capture'] = 'false';
        
        if ($this->getPaymentMethod()) {
            $data['payment_method'] = $this->getPaymentMethod();
            if ($data['payment_method'] == 'credit_card') {
                $this->validate('card');
            }
        } else {
            // one of card or boleto is required
            $this->validate('payment_method');
        }
        
        return $data;
    }
    
    public function getEndpoint()
    {
        return $this->endpoint.'/transactions';
    }
}
