<?php

namespace Omnipay\Przelewy24\Message\AbstractResponse;

use Omnipay\Przelewy24\Message\AbstractResponse;

class CompletePurchaseResponse extends AbstractResponse
{
    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return '0' === $this->getCode();
    }

    public function getCode()
    {
        return trim($this->data['error']);
    }

    public function getMessage()
    {
        if (true === $this->isSuccessful()) {
            return null;
        }

        return trim($this->data['errorMessage']);
    }
}
