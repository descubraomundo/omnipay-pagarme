<?php

namespace Omnipay\Pagarme\Message;

/**
 * Pagarme Capture Request
 *
 * Use this request to capture and process a previously created authorization.
 *
 * Example -- note this example assumes that the authorization has been successful
 * and that the authorization ID returned from the authorization is held in $auth_id.
 * See AuthorizeRequest for the first part of this example transaction:
 *
 * <code>
 *   // Once the transaction has been authorized, we can capture it for final payment.
 *   $transaction = $gateway->capture();
 *   $transaction->setTransactionReference($auth_id);
 *   $response = $transaction->send();
 * </code>
 *
 * @see Omnipay\Pagarme\Message\AuthorizeRequest
 */
class CaptureRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('transactionReference');
        $data = array();
     
        return $data;
    }
    
    public function getEndpoint()
    {
        return $this->endpoint.'transactions/'.$this->getTransactionReference().'/capture';
    }
}
