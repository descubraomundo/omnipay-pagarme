<?php
/**
 * Pagarme Void Request
 */
namespace Omnipay\Pagarme\Message;

/**
 * Pagarme Void Request
 *
 * Pagarme does not support voiding, per se, but
 * we treat it as a full refund.
 *
 * See RefundRequest for additional information
 *
 * Example -- note this example assumes that the purchase has been successful
 * and that the transaction ID returned from the purchase is held in $sale_id.
 * See PurchaseRequest for the first part of this example transaction:
 *
 * <code>
 *   // Do a void transaction on the gateway
 *   $transaction = $gateway->void(array(
 *       'transactionReference' => $sale_id,
 *   ));
 *   $response = $transaction->send();
 *   if ($response->isSuccessful()) {
 *       echo "Void transaction was successful!\n";
 *       $void_id = $response->getTransactionReference();
 *       echo "Transaction reference = " . $void_id . "\n";
 *   }
 * </code>
 *
 * @see RefundRequest
 * @see Omnipay\Pagarme\Gateway
 * @link https://docs.pagar.me/api/?shell#estorno-de-transao
 */
class VoidRequest extends RefundRequest
{
    public function getData()
    {
        $this->validate('transactionReference');
        $data = array();

        return $data;
    }
}

