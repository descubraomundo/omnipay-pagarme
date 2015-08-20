<?php
/**
 * Credit Card class
 */

namespace Omnipay\Pagarme;

use Omnipay\Common\CreditCard as Card;

/**
 * Credit Card class
 * 
 * This class extends the Omnipay's Credit Card 
 * allowing the addition of a new attribute "holder_document_number".
 * 
 * Example:
 *
 * <code>
 *   // Define credit card parameters, which should look like this
 *   $parameters = array(
 *       'firstName' => 'Bobby',
 *       'lastName' => 'Tables',
 *       'number' => '4444333322221111',
 *       'cvv' => '123',
 *       'expiryMonth' => '12',
 *       'expiryYear' => '2017',
 *       'email' => 'testcard@gmail.com',
 *       'holder_document_number' => '224.158.178-40' // CPF or CNPJ
 *   );
 *
 *   // Create a credit card object
 *   $card = new CreditCard($parameters);
 * </code>
 */

class CreditCard extends Card
{
    /**
     * Get Document number (CPF or CNPJ).
     *
     * @return string
     */
    public function getHolderDocumentNumber()
    {
        return $this->getParameter('holder_document_number');
    }

    /**
     * Set Document Number (CPF or CNPJ)
     *
     * Non-numeric characters are stripped out of the document number, so
     * it's safe to pass in strings such as "224.158.178-40" etc.
     *
     * @param string $value Parameter value
     * @return CreditCard provides a fluent interface.
     */
    public function setHolderDocumentNumber($value)
    {
        // strip non-numeric characters
        return $this->setParameter('holder_document_number', preg_replace('/\D/', '', $value));
    }
}