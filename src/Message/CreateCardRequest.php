<?php
/**
 * Pagarme Create Credit Card Request
 * 
 */
namespace Omnipay\Pagarme\Message;


/**
 * Pagarme Create Credit Card Request
 * 
 * Whenever you make a request through the Pagarme's API the 
 * cardholder information is stored, so that in future, 
 * you can use this information to new charges, or 
 * implementing features like one-click-buy.
 * 
 * Either a card object or card_id is required. Otherwise,
 * you must provide a card_hash, like the ones returned by Pagarme.js.
 * 
 * The customer_id is optional.
 * 
 * <code>
 *   // Create a credit card object
 *   // This card can be used for testing.
 *   $new_card = new CreditCard(array(
 *               'firstName'    => 'Example',
 *               'lastName'     => 'Customer',
 *               'number'       => '5555555555554444',
 *               'expiryMonth'  => '01',
 *               'expiryYear'   => '2020',
 *               'cvv'          => '456',
 *   ));
 *
 *   // Do a create card transaction on the gateway
 *   $response = $gateway->createCard(array(
 *       'card'              => $new_card,
 *       'customerReference' => $customer_id,
 *   ))->send();
 *   if ($response->isSuccessful()) {
 *       echo "Gateway createCard was successful.\n";
 *       // Find the card ID
 *       $card_id = $response->getCardReference();
 *       echo "Card ID = " . $card_id . "\n";
 *   }
 * </code>
 *
 * @link https://docs.pagar.me/api/?shell#cartes
 */
class CreateCardRequest extends AbstractRequest
{
    public function getData()
    {
        if ( $this->getCard() ) {
            $data = $this->getCardData();
            if ( $this->getCustomerReference() ) {
                $data['customer_id'] = $this->getCustomerReference();
            }
        } elseif ( $this->getCardHash() ) {
            $data['card_hash'] = $this->getCardHash();
        } else {
            $this->validate('card_number');
        }

        return $data;
    }
    
    public function getEndpoint() {
        return $this->endpoint . 'cards';
    }
}