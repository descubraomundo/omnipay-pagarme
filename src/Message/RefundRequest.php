<?php
/**
 * Pagarme Refund Request
 */

namespace Omnipay\Pagarme\Message;

/**
 * Pagarme Refund Request
 *
 * This route is used when you want to reverse a transaction 
 * performed by a charge via credit card. 
 * In case of reversal a transaction, only the id of the transaction
 * is required to effect the reversal.
 *
 * Example -- note this example assumes that the purchase has been successful
 * and that the transaction ID returned from the purchase is held in $sale_id.
 * See PurchaseRequest for the first part of this example transaction:
 *
 * <code>
 *   // Do a refund transaction on the gateway
 *   $transaction = $gateway->refund(array(
 *       'transactionReference'     => $sale_id,
 *   ));
 *   $response = $transaction->send();
 *   if ($response->isSuccessful()) {
 *       echo "Refund transaction was successful!\n";
 *       $refund_id = $response->getTransactionReference();
 *       echo "Transaction reference = " . $refund_id . "\n";
 *   }
 * </code>
 *
 * @see PurchaseRequest
 * @see Omnipay\Pagarme\Gateway
 * @link https://docs.pagar.me/api/?shell#estorno-de-transao
 */
class RefundRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('transactionReference');

        $data = array();

        return $data;
    }

    public function getEndpoint()
    {
        return $this->endpoint.'transactions/'.$this->getTransactionReference().'/refund';
    }
}
