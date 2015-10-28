<?php

namespace SmartInformationSystems\PaymentBundle\Event;

use Symfony\Component\EventDispatcher\Event;

use SmartInformationSystems\PaymentBundle\Entity\Payment;

class PaymentConfirmEvent extends Event
{
    /**
     * @var Payment
     */
    private $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }
}
