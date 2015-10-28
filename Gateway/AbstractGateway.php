<?php

namespace SmartInformationSystems\PaymentBundle\Gateway;

use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;

use SmartInformationSystems\PaymentBundle\Entity\Payment;
use SmartInformationSystems\PaymentBundle\PaymentForm\AbstractPaymentForm;

use SmartInformationSystems\PaymentBundle\Exception\Gateway\Exception;

abstract class AbstractGateway
{
    const NAME = 'abstract';

    /**
     * @var Session
     */
    private $session;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var array
     */
    private $params = array();

    /**
     * @var array
     */
    private $options = array();

    /**
     * Возвращает action url для платежной формы.
     *
     * @return string
     */
    abstract public function getActionUrl();

    /**
     * Возвращает платежную форму.
     *
     * @param Payment $payment
     *
     * @return AbstractPaymentForm
     */
    abstract public function getPaymentForm(Payment $payment);

    /**
     * Конструктор.
     *
     * @param array $params
     */
    public function __construct(array $params = array(), Session $session, EntityManager $em)
    {
        $this->params = $params;
        $this->session = $session;
        $this->em = $em;
    }

    /**
     * Возвращает параметр.
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getParam($name)
    {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }

        if (isset($this->params[$name])) {
            return $this->params[$name];
        }

        throw new Exception('Нет настройки ' . $name);
    }

    /**
     * Создание платежа.
     *
     * @param double $sum Сумма платежа
     * @param string $orderId Идентифактор заказа
     * @param string $orderNumber Номер заказа
     * @param string $buyerId Идентификатор покупателя
     * @param string $buyerPhone Телефон покупателя
     *
     * @return Payment
     *
     * @throws Exception
     */
    public function createPayment($sum, $orderId, $orderNumber, $buyerId, $buyerPhone)
    {
        $token = md5($this->getName() . print_r($this->getOptions(), TRUE) . implode('|', func_get_args()));
        if (!($payment = $this->em->getRepository('SmartInformationSystemsPaymentBundle:Payment')->getByToken($token))) {
            $payment = new Payment();
            $payment->setToken($token);
            $payment->setGateway($this->getName());
            $payment->setGatewayOptions($this->getOptions());
            $payment->setSum($sum);
            $payment->setOrderId($orderId);
            $payment->setOrderNumber($orderNumber);
            $payment->setBuyerId($buyerId);
            $payment->setBuyerPhone($buyerPhone);

            $this->em->persist($payment);
            $this->em->flush($payment);
        }

        if ($payment->getConfirmed()) {
            throw new Exception('Платеж уже подтвержден');
        }

        return $payment;
    }

    protected function savePayment(Payment $payment)
    {
        $this->em->persist($payment);
        $this->em->flush($payment);
    }

    /**
     * Возвращает имя платежного сервиса.
     *
     * @return string
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * Установка пользовательских параметров.
     *
     * @param array $options
     *
     * @return AbstractGateway
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Возвращает пользовательские параметры.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
