<?php

namespace SmartInformationSystems\PaymentBundle;

final class PaymentEvents
{
    /**
     * Событие происходит каждый раз, когда платеж подтверждается
     * платежной системой.
     *
     * The event listener receives an
     * Acme\StoreBundle\Event\FilterOrderEvent instance.
     *
     * @var string
     */
    const PAYMENT_CONFIRM = 'sis_payments.payment.confirm';
}
