<?php

namespace SmartInformationSystems\PaymentBundle\PaymentForm;

abstract class AbstractPaymentForm
{
    const METHOD = 'POST';

    /**
     * @var string
     */
    private $url;

    /**
     * @var array
     */
    private $hiddenFields;

    public function __construct($url, array $hiddenFields = array())
    {
        $this->url = $url;
        $this->hiddenFields = $hiddenFields;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return array
     */
    public function getHiddenFields()
    {
        return $this->hiddenFields;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return static::METHOD;
    }
}