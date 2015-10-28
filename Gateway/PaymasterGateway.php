<?php

namespace SmartInformationSystems\PaymentBundle\Gateway;

use SmartInformationSystems\PaymentBundle\Entity\Payment;
use SmartInformationSystems\PaymentBundle\PaymentForm\PaymasterPaymentForm;

class PaymasterGateway extends AbstractGateway
{
    const NAME = 'paymaster';

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
            array('name' => 'LMI_MERCHANT_ID', 'value' => $this->getParam('shop_id')),
            array('name' => 'LMI_PAYMENT_AMOUNT', 'value' => $payment->getSum()),
            array('name' => 'LMI_CURRENCY', 'value' => 'RUB'),
            array('name' => 'LMI_PAYER_PHONE_NUMBER', 'value' => $payment->getBuyerPhone()),

            array('name' => 'LMI_PAYMENT_NO', 'value' => $payment->getOrderNumber()),
            array('name' => 'LMI_PAYMENT_DESC_BASE64', 'value' => base64_encode('Оплата заказа №' . $payment->getOrderNumber())),
            array('name' => 'paymentId', 'value' => $payment->getToken()),
        );

        $payment->setGatewayData(array(
            'LMI_MERCHANT_ID' => $this->getParam('shop_id'),
        ));

        $this->savePayment($payment);

        return new PaymasterPaymentForm(
            $this->getParam('url'),
            $fields
        );
    }
}
