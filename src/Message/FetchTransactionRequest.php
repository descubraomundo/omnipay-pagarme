<?php
/**
 * Pagarme Fetch Transaction Request
 */

namespace Omnipay\Pagarme\Message;

/**
 * Pagarme Fetch Transaction Request
 *
 * Example -- note this example assumes that the purchase has been successful
 * and that the transaction ID returned from the purchase is held in $sale_id.
 * See PurchaseRequest for the first part of this example transaction:
 *
 * <code>
 *   // Fetch the transaction so that details can be found for refund, etc.
 *   $transaction = $gateway->fetchTransaction();
 *   $transaction->setTransactionReference($sale_id);
 *   $response = $transaction->send();
 *   $data = $response->getData();
 *   echo "Gateway fetchTransaction response data == " . print_r($data, true) . "\n";
 * </code>
 *
 * @see PurchaseRequest
 * @see Omnipay\Pagarme\Gateway
 * @link https://docs.pagar.me/api/#retornando-uma-transao
 */
class FetchTransactionRequest extends AbstractRequest
{
    public function getData()
    {
        $data = array();

        return $data;
    }
    
    public function getQuery()
    {
        $this->validate('transactionReference');

        $data = array();
        $data['api_key'] = $this->getApiKey();

        return $data;
    }
    
    protected function getOptions() {         
        $options['query'] = $this->getQuery();
         
        return $options;
    }

    public function getEndpoint()
    {
        return $this->endpoint.'transactions/'.$this->getTransactionReference();
    }

    public function getHttpMethod()
    {
        return 'GET';
    }
}
