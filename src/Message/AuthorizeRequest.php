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
 * in order to effect payment. Uncaptured transactions expire in 5 days.
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
 * 
 * Optionally, you can provide the customer details to use the antifraude
 * feature. These details is passed using the following attributes available
 * on credit card object:
 * 
 * * firstName
 * * lastName
 * * name
 * * birthday
 * * gender
 * * address1 (must be in the format "street, street_number and neighborhood")
 * * address2 (used to specify the optional parameter "street_complementary")
 * * postcode
 * * phone (must be in the format "DDD PhoneNumber" e.g. "19 98888 5555")
 * * holder_document_number (CPF or CNPJ)
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
 *               'address1'     => 'Street name, Street number, Complementary',
 *               'address2'     => 'Neighborhood',
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
 * @see https://docs.pagar.me/capturing-card-data/
 * @see \Omnipay\Pagarme\Gateway
 * @see \Omnipay\Pagarme\Message\CaptureRequest
 * @link https://docs.pagar.me/api/?shell#objeto-transaction
 */
class AuthorizeRequest extends AbstractRequest
{
    /**
     * Get postback URL.
     * 
     * @return string
     */
    public function getPostbackUrl()
    {
        return $this->getParameter('postback_url');
    }
    
    /**
     * Set postback URL.
     * 
     * @param string $value
     * @return AuthorizeRequest provides a fluent interface.
     */
    public function setPostbackUrl($value)
    {
        return $this->setParameter('postback_url', $value);
    }
    
    /**
     * Get installments.
     * 
     * @return integer the number of installments
     */
    public function getInstallments()
    {
        return $this->getParameter('installments');
    }
    
    /**
     * Set Installments.
     * 
     * The number must be between 1 and 12. 
     * If the payment method is boleto defaults to 1.
     * 
     * @param integer $value
     * @return AuthorizeRequest provides a fluent interface.
     */
    public function setInstallments($value)
    {
        return $this->setParameter('installments', (int)$value);
    }
    
    /**
     * Get soft description.
     * 
     * @return string small description
     */
    public function getSoftDescriptor()
    {
        return $this->getParameter('soft_descriptor');
    }
    
    /**
     * Set soft description.
     * 
     * The Pagarme gateway allow 13 characters in the soft_descriptor.
     * The provided string will be truncated if lengh > 13.
     * 
     * @param string $value
     * @return AuthorizeRequest provides a fluent interface.
     */
    public function setSoftDescriptor($value)
    {
        return $this->setParameter('soft_descriptor', substr($value, 0, 13));
    }
    
    /**
     * Get the boleto expiration date
     * 
     * @return string boleto expiration date
     */
    public function getBoletoExpirationDate($format = 'Y-m-d\TH:i:s')
    {
        $value = $this->getParameter('boleto_expiration_date');
        
        return $value ? $value->format($format) : null;
    }
    
    /**
     * Set the boleto expiration date
     * 
     * @param string $value defaults to atual date + 7 days
     * @return AuthorizeRequest provides a fluent interface
     */
    public function setBoletoExpirationDate($value)
    {
        if ($value) {
            $value = new \DateTime($value, new \DateTimeZone('UTC'));
            $value = new \DateTime($value->format('Y-m-d\T03:00:00'), new \DateTimeZone('UTC'));
        } else {
            $value = null;
        }
        
        return $this->setParameter('boleto_expiration_date', $value);
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
        if ( $this->getPaymentMethod() && ($this->getPaymentMethod() == 'boleto') ) {
            if ( $this->getBoletoExpirationDate() ) {
                $data['boleto_expiration_date'] = $this->getBoletoExpirationDate();
            }
            $data['payment_method'] = $this->getPaymentMethod();
            if ( $this->getCard() ) {
                $data = array_merge($data, $this->getCustomerData());
            } elseif ($this->getCustomer()) {
                $this->setCard($this->getCustomer());
                $data = array_merge($data, $this->getCustomerData());
            }
        } else {
            if ( $this->getCard() ) {
                $data = array_merge($data, $this->getCardData(), $this->getCustomerData());
            } elseif ( $this->getCardHash() ) {
                $data['card_hash'] = $this->getCardHash();
            } elseif( $this->getCardReference() ) {
                $data['card_id'] = $this->getCardReference();
            } else {
                $this->validate('card');
            }
        }
        $data['capture'] = 'false';
        
        return $data;
    }
    
    public function getEndpoint()
    {
        return $this->endpoint.'transactions';
    }
}
