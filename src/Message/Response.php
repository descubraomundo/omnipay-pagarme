<?php

namespace Omnipay\Pagarme\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Pagarme Response
 *
 * This is the response class for all Pagarme requests.
 *
 * @see \Omnipay\PAgarme\Gateway
 */
class Response extends AbstractResponse
{
    /**
     * Is the transaction successful?
     *
     * @return bool
     */
    public function isSuccessful()
    {
        if (isset($this->data['object']) && 'transaction' === $this->data['object']) {
            return !($this->data['status'] == 'refused');
        }
        return !isset($this->data['errors']);
    }
    
    /**
     * Get the transaction reference.
     *
     * @return string|null
     */
    public function getTransactionReference()
    {
        if (isset($this->data['object']) && 'transaction' === $this->data['object']) {
            return $this->data['id'];
        }
        if (isset($this->data['errors']) && isset($this->data['errors']['id'])) {
            return $this->data['errors']['id'];
        }

        return null;
    }
    
    /**
     * Get a card reference, for createCard or createCustomer requests.
     *
     * @return string|null
     */
    public function getCardReference()
    {
        if (isset($this->data['object']) && 'card' === $this->data['object']) {
            if (! empty($this->data['id'])) {
                return $this->data['id'];
            }
        } elseif (isset($this->data['object']) && 'transaction' === $this->data['object']) {
            return $this->data['card']['id'];
        }

        return null;
    }
    
    /**
     * Get the error message from the response.
     *
     * Returns null if the request was successful.
     *
     * @return string|null
     */
    public function getMessage()
    {
        if (!$this->isSuccessful()) {
            if (isset($this->data['errors'])) {
                return $this->data['errors'][0]['message'];
            } else {
                return $this->data['refuse_reason'];
            }
        }

        return null;
    }


}