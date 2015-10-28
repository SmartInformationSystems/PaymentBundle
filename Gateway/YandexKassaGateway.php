<?php

namespace SmartInformationSystems\PaymentBundle\Gateway;

use SmartInformationSystems\PaymentBundle\Entity\Payment;
use SmartInformationSystems\PaymentBundle\PaymentForm\YandexKassaPaymentForm;

class YandexKassaGateway extends AbstractGateway
{
    const NAME = 'yandex_kassa';

    /**
     * {@inheritdoc}
     */
    public function getActionUrl()
    {
        return $this->getParam('url');
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentForm(Payment $payment)
    {
        $fields = array(
            array('name' => 'shopId', 'value' => $this->getParam('shop_id')),
            array('name' => 'scid', 'value' => $this->getParam('sc_id')),

            array('name' => 'sum', 'value' => $payment->getSum()),
            array('name' => 'orderNumber', 'value' => $payment->getOrderNumber()),

            array('name' => 'customerNumber', 'value' => $payment->getBuyerId()),
            array('name' => 'cps_phone', 'value' => $payment->getBuyerPhone()),

            array('name' => 'paymentId', 'value' => $payment->getToken()),
        );

        $fields[] = array('name' => 'paymentType', 'value' => $this->getParam('payment_type'));

        $payment->setGatewayData(array(
            'shopId' => $this->getParam('shop_id'),
            'scid' => $this->getParam('sc_id'),
        ));

        $this->savePayment($payment);

        return new YandexKassaPaymentForm(
            $this->getParam('url'),
            $fields
        );
    }
}
